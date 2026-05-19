<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\Payer;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\PayerBuilder;
use PaypalServerSdkLib\Models\Builders\AddressBuilder;
use PaypalServerSdkLib\Models\Builders\NameBuilder;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentSourceBuilder;
use PaypalServerSdkLib\Models\Builders\MybankPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\BancontactPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\BlikPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\EpsPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\GiropayPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\IdealPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\P24PaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\SofortPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\TrustlyPaymentRequestBuilder;
use PaypalServerSdkLib\Models\Builders\ApplePayRequestBuilder;
use PaypalServerSdkLib\Models\Builders\GooglePayRequestBuilder;
use PaypalServerSdkLib\Models\Builders\VenmoWalletRequestBuilder;

/**
 * PayPal Order Creation Handler
 * Handles PayPal order creation and building logic
 */
class Mage_Paypal_Model_Order extends Mage_Core_Model_Abstract
{
    /**
     * Create PayPal order via API
     *
     * @param  Mage_Sales_Model_Quote                            $quote         Customer quote
     * @param  null|string                                       $fundingSource Funding source for the order, e.g., 'mybank'
     * @return array{success: bool, id?: string, error?: string}
     * @throws Mage_Paypal_Model_Exception
     */
    public function createOrder(Mage_Sales_Model_Quote $quote, ?string $fundingSource): array
    {
        // Normalize to a defined funding source; 'paypal' is the wallet default.
        $fundingSource = $fundingSource ?: 'paypal';

        try {
            $this->getHelper()->validateQuoteForPayment($quote);
            $quote->reserveOrderId()->save();
            $api = $this->getHelper()->getApi();
            $orderRequest = $this->buildOrderRequest($quote, $quote->getReservedOrderId(), $fundingSource);
            $paypalRequestId = $this->getHelper()->getPaypalRequestId($quote) ?? bin2hex(random_bytes(16));
            $response = $api->createOrder($quote, $orderRequest, $paypalRequestId);
            $result = $response->getResult();

            if ($response->isError()) {
                $this->getHelper()->handleApiError($response, 'Error creating PayPal order');
            }

            $this->updatePaymentWithOrderInfo($quote->getPayment(), $response, $paypalRequestId, $fundingSource);

            return [
                'success' => true,
                'id' => $result->getId(),
            ];
        } catch (Exception $exception) {
            Mage::logException($exception);
            return [
                'success' => false,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Build a complete PayPal order request using SDK builders
     *
     * @param  null|string $referenceId   Optional reference ID
     * @param  null|string $fundingSource Optional funding source for the order
     * @return object      The built order request object
     */
    public function buildOrderRequest(Mage_Sales_Model_Quote $quote, ?string $referenceId = null, ?string $fundingSource = null): object
    {
        $purchaseUnit = $this->buildPurchaseUnit($quote, $referenceId);
        $orderRequestBuilder = OrderRequestBuilder::init(
            $this->getHelper()->getPaymentIntent(),
            [$purchaseUnit],
        );

        /**
         * @var null|Payer $payer
         */
        $payer = $this->buildPayerFromBillingAddress($quote);
        if ($payer !== null) {
            $orderRequestBuilder->payer($payer);
        }

        if ($fundingSource) {
            $paymentSource = $this->_buildPaymentSource($fundingSource, $payer);
            if ($paymentSource !== null) {
                $orderRequestBuilder->paymentSource($paymentSource);
            }
        }

        return $orderRequestBuilder->build();
    }

    /**
     * Build a PayPal payment source based on funding source type
     *
     * @param  string      $fundingSource The funding source type
     * @param  null|object $payer         The payer object containing customer information
     * @return null|object The built payment source object, or null for default payment methods
     */
    private function _buildPaymentSource(string $fundingSource, ?object $payer): ?object
    {
        $paymentSourceBuilder = PaymentSourceBuilder::init();

        // Wallet methods that need no payer details.
        switch ($fundingSource) {
            case 'applepay':
                return $paymentSourceBuilder->applePay(ApplePayRequestBuilder::init()->build())->build();
            case 'googlepay':
                return $paymentSourceBuilder->googlePay(GooglePayRequestBuilder::init()->build())->build();
            case 'venmo':
                return $paymentSourceBuilder->venmo(VenmoWalletRequestBuilder::init()->build())->build();

                // Default payment methods (PayPal, card) don't need an explicit payment source
            case 'paypal':
            case 'card':
                return null;
        }

        // Remaining methods (local bank redirects) require payer details.
        if ($payer === null) {
            return null;
        }

        $nameObj = $payer->getName();
        $addressObj = $payer->getAddress();
        $name = $nameObj->getGivenName() . ' ' . $nameObj->getSurname();
        $country = $addressObj->getCountryCode();
        $email = $payer->getEmailAddress() ?? 'noreply@example.com';
        switch ($fundingSource) {
            // Payment methods requiring name and country
            case 'mybank':
                $paymentSourceBuilder->mybank(MybankPaymentRequestBuilder::init($name, $country)->build());
                break;
            case 'bancontact':
                $paymentSourceBuilder->bancontact(BancontactPaymentRequestBuilder::init($name, $country)->build());
                break;
            case 'blik':
                $paymentSourceBuilder->blik(BlikPaymentRequestBuilder::init($name, $country)->build());
                break;
            case 'eps':
                $paymentSourceBuilder->eps(EpsPaymentRequestBuilder::init($name, $country)->build());
                break;
            case 'giropay':
                $paymentSourceBuilder->giropay(GiropayPaymentRequestBuilder::init($name, $country)->build());
                break;
            case 'ideal':
                $paymentSourceBuilder->ideal(IdealPaymentRequestBuilder::init($name, $country)->build());
                break;
            case 'sofort':
                $paymentSourceBuilder->sofort(SofortPaymentRequestBuilder::init($name, $country)->build());
                break;

                // Payment methods requiring name, email, and country
            case 'p24':
                $paymentSourceBuilder->p24(P24PaymentRequestBuilder::init($name, $email, $country)->build());
                break;
            case 'trustly':
                $paymentSourceBuilder->trustly(TrustlyPaymentRequestBuilder::init($name, $country, $email)->build());
                break;

            default:
                return null;
        }

        return $paymentSourceBuilder->build();
    }

    /**
     * Build a purchase unit using PayPal SDK builders
     */
    public function buildPurchaseUnit(Mage_Sales_Model_Quote $quote, ?string $referenceId = null): object
    {
        $cart = Mage::getModel('paypal/cart', [$quote]);
        $currency = $quote->getOrderCurrencyCode() ?: $quote->getQuoteCurrencyCode();

        $reconciled = $this->reconcileCartForPaypal($cart, $currency);

        $breakdown = $this->buildAmountBreakdown($reconciled['totals']);
        $amount    = $this->buildAmountWithBreakdown($currency, (float) $quote->getGrandTotal(), $breakdown);

        $purchaseUnitBuilder = PurchaseUnitRequestBuilder::init($amount)
            ->items($reconciled['items'])
            ->referenceId($referenceId ?: (string) $quote->getId())
            ->invoiceId($referenceId ?: (string) $quote->getId());

        return $purchaseUnitBuilder->build();
    }

    /**
     * Build amount breakdown from cart totals
     *
     * @param  array<string, mixed> $totals Cart totals
     * @return object               Built breakdown object
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

        if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_HANDLING])) {
            $breakdownBuilder->handling($totals[Mage_Paypal_Model_Cart::TOTAL_HANDLING]);
        }

        if (
            isset($totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT])
            && (float) $totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT]->getValue() > 0
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
        $formattedTotal = Mage::helper('paypal')->formatPrice($totalAmount, $currency);

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
     * Reconcile cart items and totals so the PayPal purchase unit satisfies the
     * two amount invariants PayPal validates:
     *
     *  - sum(item.unit_amount * qty) == breakdown.item_total
     *  - item_total + tax_total + shipping + handling - discount == amount.value
     *
     * item_total and tax_total are derived directly from the (already rounded)
     * line items, so the first invariant always holds. Any remaining difference
     * against the order grand total — coupon discounts, per-line rounding, fees
     * — is absorbed by a single field: the discount when the itemised parts
     * exceed the grand total, or the handling field when the grand total is
     * higher. No synthetic "Rounding Adjustment" line items are added.
     *
     * @return array{totals: array, items: array}
     */
    public function reconcileCartForPaypal(Mage_Paypal_Model_Cart $cart, string $currency): array
    {
        $helper = Mage::helper('paypal');
        $totals = $cart->getAmounts();
        $items  = $cart->getAllItems();

        $itemsTotal    = 0.00;
        $itemsTaxTotal = 0.00;
        foreach ($items as $item) {
            /** @var Item $item */
            $qty = (int) $item->getQuantity();
            $itemsTotal    += (float) $item->getUnitAmount()->getValue() * $qty;
            $itemsTaxTotal += ($item->getTax() ? (float) $item->getTax()->getValue() : 0.00) * $qty;
        }

        $shippingTotal = isset($totals[Mage_Paypal_Model_Cart::TOTAL_SHIPPING])
            ? (float) $totals[Mage_Paypal_Model_Cart::TOTAL_SHIPPING]->getValue()
            : 0.00;
        $grandTotal = (float) $cart->getQuote()->getGrandTotal();

        // Derive item_total / tax_total from the line items themselves.
        $totals[Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL] = MoneyBuilder::init(
            $currency,
            $helper->formatPrice($itemsTotal, $currency),
        )->build();
        $totals[Mage_Paypal_Model_Cart::TOTAL_TAX] = MoneyBuilder::init(
            $currency,
            $helper->formatPrice($itemsTaxTotal, $currency),
        )->build();

        // Solve for the single field that balances the breakdown.
        $decimals   = $helper->getCurrencyDecimals($currency);
        $difference = round($itemsTotal + $itemsTaxTotal + $shippingTotal - $grandTotal, $decimals);

        unset($totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT], $totals[Mage_Paypal_Model_Cart::TOTAL_HANDLING]);
        if ($difference > 0) {
            $totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT] = MoneyBuilder::init(
                $currency,
                $helper->formatPrice($difference, $currency),
            )->build();
        } elseif ($difference < 0) {
            $totals[Mage_Paypal_Model_Cart::TOTAL_HANDLING] = MoneyBuilder::init(
                $currency,
                $helper->formatPrice(abs($difference), $currency),
            )->build();
        }

        return ['totals' => $totals, 'items' => $items];
    }

    /**
     * Update payment object with PayPal order information
     */
    public function updatePaymentWithOrderInfo(Mage_Sales_Model_Quote_Payment $payment, ApiResponse $response, string $paypalRequestId, string $fundingSource): void
    {
        $result = $response->getResult();
        $payment->setPaypalCorrelationId($result->getId())
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->getHelper()->prepareRawDetails($response->getBody()),
            )->setAdditionalInformation(
                Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID,
                $paypalRequestId,
            )->setAdditionalInformation(
                Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_SOURCE,
                $fundingSource,
            )->save();
    }

    /**
     * Get PayPal Helper instance
     */
    private function getHelper(): Mage_Paypal_Model_Helper
    {
        return Mage::getSingleton('paypal/helper');
    }
}
