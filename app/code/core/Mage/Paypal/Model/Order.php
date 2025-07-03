<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\PayerBuilder;
use PaypalServerSdkLib\Models\Builders\AddressBuilder;
use PaypalServerSdkLib\Models\Builders\NameBuilder;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;

/**
 * PayPal Order Creation Handler
 * Handles PayPal order creation and building logic
 */
class Mage_Paypal_Model_Order extends Mage_Core_Model_Abstract
{
    /**
     * Create PayPal order via API
     *
     * @param Mage_Sales_Model_Quote $quote Customer quote
     * @return array{success: bool, id?: string, error?: string}
     * @throws Mage_Core_Exception
     */
    public function createOrder(Mage_Sales_Model_Quote $quote): array
    {
        try {
            $this->getHelper()->validateQuoteForPayment($quote);
            if ($quote->getReservedOrderId()) {
                $existingPayment = $quote->getPayment();
                if ($existingPayment && $existingPayment->getPaypalCorrelationId()) {
                    $quote->setReservedOrderId('');
                }
            }

            $quote->reserveOrderId()->save();
            $api = $this->getHelper()->getApi();
            $orderRequest = $this->buildOrderRequest($quote, $quote->getReservedOrderId());

            $response = $api->createOrder($quote, $orderRequest);
            $result = $response->getResult();

            if ($response->isError()) {
                throw new Mage_Core_Exception($result['message'] ?? 'Error creating PayPal order');
            }

            $this->updatePaymentWithOrderInfo($quote->getPayment(), $response);

            return [
                'success' => true,
                'id' => $result->getId(),
            ];
        } catch (Exception $e) {
            Mage::logException($e);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build a complete PayPal order request using SDK builders
     *
     * @param string|null $referenceId Optional reference ID
     * @return object The built order request object
     */
    public function buildOrderRequest(Mage_Sales_Model_Quote $quote, ?string $referenceId = null): object
    {
        $purchaseUnit = $this->buildPurchaseUnit($quote, $referenceId);
        $orderRequestBuilder = OrderRequestBuilder::init(
            $this->getHelper()->getPaymentIntent(),
            [$purchaseUnit],
        );

        $payer = $this->buildPayerFromBillingAddress($quote);
        if ($payer !== null) {
            $orderRequestBuilder->payer($payer);
        }

        return $orderRequestBuilder->build();
    }

    /**
     * Build a purchase unit using PayPal SDK builders
     */
    public function buildPurchaseUnit(Mage_Sales_Model_Quote $quote, ?string $referenceId = null): object
    {
        $cart = Mage::getModel('paypal/cart', [$quote]);
        $currency = $quote->getOrderCurrencyCode() ?: $quote->getQuoteCurrencyCode();

        $adjustedCartData = $this->adjustCartTotalsForTaxDiscrepancy($cart, $currency);
        $totals = $adjustedCartData['totals'];
        $items  = $adjustedCartData['items'];

        $breakdown = $this->buildAmountBreakdown($totals);
        $amount    = $this->buildAmountWithBreakdown($currency, $quote->getGrandTotal(), $breakdown);

        $purchaseUnitBuilder = PurchaseUnitRequestBuilder::init($amount)
            ->items($items)
            ->referenceId($referenceId ?: (string) $quote->getId())
            ->invoiceId($referenceId ?: (string) $quote->getId());

        return $purchaseUnitBuilder->build();
    }

    /**
     * Build amount breakdown from cart totals
     *
     * @param array<string, mixed> $totals Cart totals
     * @return object Built breakdown object
     */
    public function buildAmountBreakdown(array $totals): object
    {
        $breakdownBuilder = AmountBreakdownBuilder::init();

        if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL])) {
            $breakdownBuilder->itemTotal($totals[Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL]);
        }

        if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_TAX])) {
            $breakdownBuilder->taxTotal($totals[Mage_Paypal_Model_Cart::TOTAL_TAX]);
        }

        if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_SHIPPING])) {
            $breakdownBuilder->shipping($totals[Mage_Paypal_Model_Cart::TOTAL_SHIPPING]);
        }

        if (
            isset($totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT]) &&
            $totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT]->getValue() > 0
        ) {
            $breakdownBuilder->discount($totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT]);
        }

        return $breakdownBuilder->build();
    }

    /**
     * Build amount with breakdown
     *
     * @param object $breakdown Built breakdown object
     */
    public function buildAmountWithBreakdown(string $currency, float $totalAmount, object $breakdown): object
    {
        $formattedTotal = number_format($totalAmount, 2, '.', '');

        return AmountWithBreakdownBuilder::init($currency, $formattedTotal)
            ->breakdown($breakdown)
            ->build();
    }

    /**
     * Build payer object from billing address
     */
    public function buildPayerFromBillingAddress(Mage_Sales_Model_Quote $quote): ?object
    {
        $billingAddress = $quote->getBillingAddress();

        if ($billingAddress->validate() !== true) {
            return null;
        }

        $name = NameBuilder::init()
            ->givenName($billingAddress->getFirstname())
            ->surname($billingAddress->getLastname())
            ->build();

        $address = AddressBuilder::init($billingAddress->getCountryId())
            ->addressLine1($billingAddress->getStreetLine(1))
            ->addressLine2($billingAddress->getStreetLine(2))
            ->adminArea2($billingAddress->getCity())
            ->adminArea1($billingAddress->getRegionCode())
            ->postalCode($billingAddress->getPostcode())
            ->build();

        return PayerBuilder::init()
            ->emailAddress($quote->getEmail())
            ->name($name)
            ->address($address)
            ->build();
    }

    /**
     * Adjust tax totals and items for rounding discrepancies
     *
     * @return array{totals: array, items: array}
     */
    public function adjustCartTotalsForTaxDiscrepancy(Mage_Paypal_Model_Cart $cart, string $currency): array
    {
        $totals = $cart->getAmounts();
        $items  = $cart->getAllItems();

        $taxCalculated = 0.00;
        $taxAmount     = isset($totals[Mage_Paypal_Model_Cart::TOTAL_TAX])
            ? (float) $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->getValue()
            : 0.00;

        foreach ($items as $item) {
            /** @var PaypalServerSdkLib\Models\Item $item */
            if ($item->getTax()) {
                $qty        = (int) $item->getQuantity();
                $taxValue   = (float) $item->getTax()->getValue();
                $taxCalculated += $taxValue * $qty;
            }
        }

        $taxDifference = round($taxAmount - $taxCalculated, 2);

        if ($taxDifference < 0) {
            $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->setValue(number_format($taxCalculated, 2, '.', ''));

            if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT])) {
                $totalDiscount = (float) $totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT]->getValue();
                $totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT]->setValue(
                    number_format(abs($taxDifference) + $totalDiscount, 2, '.', ''),
                );
            } else {
                $totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT] = MoneyBuilder::init(
                    $currency,
                    number_format(abs($taxDifference), 2, '.', ''),
                )->build();
            }
        } elseif ($taxDifference > 0) {
            $moneyBuilder = MoneyBuilder::init($currency, number_format(abs($taxDifference), 2, '.', ''));
            $roundingItem = ItemBuilder::init(
                Mage::helper('paypal')->__('Rounding'),
                $moneyBuilder->build(),
                '1',
            )
                ->sku(Mage::helper('paypal')->__('Rounding'))
                ->category(ItemCategory::DIGITAL_GOODS)
                ->build();

            $items[] = $roundingItem;
            $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->setValue(number_format($taxCalculated, 2, '.', ''));
        }

        return ['totals' => $totals, 'items' => $items];
    }

    /**
     * Update payment object with PayPal order information
     */
    public function updatePaymentWithOrderInfo(Mage_Sales_Model_Quote_Payment $payment, ApiResponse $response): void
    {
        $result = $response->getResult();
        $payment->setPaypalCorrelationId($result->getId())
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->getHelper()->prepareRawDetails($response->getBody()),
            )
            ->save();
    }

    /**
     * Get PayPal Helper instance
     */
    private function getHelper(): Mage_Paypal_Model_Helper
    {
        return Mage::getSingleton('paypal/helper');
    }
}
