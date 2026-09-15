<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\Address;
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
use PaypalServerSdkLib\Models\Builders\PaypalWalletBuilder;
use PaypalServerSdkLib\Models\Builders\PaypalWalletExperienceContextBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingNameBuilder;
use PaypalServerSdkLib\Models\ExperienceContextShippingPreference;
use PaypalServerSdkLib\Models\ShippingDetails;

/**
 * PayPal Order Creation Handler
 * Handles PayPal order creation and building logic
 */
class Mage_Paypal_Model_Order extends Mage_Core_Model_Abstract
{
    /**
     * Create PayPal order via API
     *
     * @param  Mage_Sales_Model_Quote                            $quote                   Customer quote
     * @param  null|string                                       $fundingSource           Funding source for the order, e.g., 'mybank'
     * @param  bool                                              $useQuoteShippingAddress Ship to the address entered at checkout instead of one picked on PayPal
     * @return array{success: bool, id?: string, error?: string}
     * @throws Mage_Paypal_Model_Exception
     */
    public function createOrder(Mage_Sales_Model_Quote $quote, ?string $fundingSource, bool $useQuoteShippingAddress = false): array
    {
        // Normalize to a defined funding source; 'paypal' is the wallet default.
        $fundingSource = $fundingSource ?: 'paypal';

        try {
            $this->getHelper()->validateQuoteForPayment($quote);
            $quote->reserveOrderId()->save();
            $api = $this->getHelper()->getApi();
            $orderRequest = $this->buildOrderRequest($quote, $quote->getReservedOrderId(), $fundingSource, $useQuoteShippingAddress);
            // Every attempt is a new PayPal order. Reusing the id left on the quote by an earlier, captured
            // attempt made PayPal reject the new order with ORDER_ALREADY_CAPTURED.
            $paypalRequestId = bin2hex(random_bytes(16));
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
    public function buildOrderRequest(
        Mage_Sales_Model_Quote $quote,
        ?string $referenceId = null,
        ?string $fundingSource = null,
        bool $useQuoteShippingAddress = false,
    ): object {
        $purchaseUnit = $this->buildPurchaseUnit($quote, $referenceId, $useQuoteShippingAddress);
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
            $paymentSource = $this->_buildPaymentSource(
                $fundingSource,
                $payer,
                $this->getShippingPreference($quote, $useQuoteShippingAddress),
            );
            if ($paymentSource !== null) {
                $orderRequestBuilder->paymentSource($paymentSource);
            }
        }

        return $orderRequestBuilder->build();
    }

    /**
     * Build a PayPal payment source based on funding source type
     *
     * @param  string      $fundingSource      The funding source type
     * @param  null|object $payer              The payer object containing customer information
     * @param  null|string $shippingPreference PayPal wallet shipping preference; null keeps PayPal's default
     * @return null|object The built payment source object, or null for default payment methods
     */
    private function _buildPaymentSource(string $fundingSource, ?object $payer, ?string $shippingPreference = null): ?object
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

            case 'paypal':
                // The wallet itself needs no payer details, but set return/cancel URLs so the
                // order also works as a redirect flow: when a post-approval patch raises the total
                // beyond PayPal's tolerance, the capture fails with PAYER_ACTION_REQUIRED and the
                // buyer must re-approve on paypal.com — without a return_url that page has nowhere
                // to send them back to. Harmless for the JS popup flow, which never redirects.
                return $paymentSourceBuilder->paypal(
                    PaypalWalletBuilder::init()->experienceContext(
                        PaypalWalletExperienceContextBuilder::init()
                            ->returnUrl(Mage::getUrl('paypal/express/return', ['_secure' => true]))
                            ->cancelUrl(Mage::getUrl('paypal/express/cancelReturn', ['_secure' => true]))
                            ->shippingPreference($shippingPreference)
                            ->build(),
                    )->build(),
                )->build();

                // Card fields render inline; no explicit payment source needed.
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
    public function buildPurchaseUnit(Mage_Sales_Model_Quote $quote, ?string $referenceId = null, bool $useQuoteShippingAddress = false): object
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

        if ($this->getShippingPreference($quote, $useQuoteShippingAddress) === ExperienceContextShippingPreference::SET_PROVIDED_ADDRESS) {
            $purchaseUnitBuilder->shipping($this->buildShippingDetails($quote->getShippingAddress()));
        }

        return $purchaseUnitBuilder->build();
    }

    /**
     * Without a preference PayPal ships to an address the buyer picks on PayPal, which the in-checkout flow
     * ignores: the order ships to the checkout address, so PayPal's transaction would record a different one.
     */
    public function getShippingPreference(Mage_Sales_Model_Quote $quote, bool $useQuoteShippingAddress): ?string
    {
        if (!$useQuoteShippingAddress) {
            return null;
        }

        if ($quote->isVirtual()) {
            return ExperienceContextShippingPreference::NO_SHIPPING;
        }

        $address = $quote->getShippingAddress();
        if ((string) $address->getCountryId() === '' || trim($address->getFirstname() . ' ' . $address->getLastname()) === '') {
            return null;
        }

        return ExperienceContextShippingPreference::SET_PROVIDED_ADDRESS;
    }

    public function buildShippingDetails(Mage_Sales_Model_Quote_Address $address): ShippingDetails
    {
        $name = ShippingNameBuilder::init()
            ->fullName(mb_substr(trim($address->getFirstname() . ' ' . $address->getLastname()), 0, 300))
            ->build();

        return ShippingDetailsBuilder::init()
            ->name($name)
            ->address($this->buildAddress($address))
            ->build();
    }

    /**
     * Field lengths are PayPal's maximums; a longer value fails the whole order request.
     */
    public function buildAddress(Mage_Customer_Model_Address_Abstract $address): Address
    {
        return AddressBuilder::init((string) $address->getCountryId())
            ->addressLine1(mb_substr((string) $address->getStreet1(), 0, 300))
            ->addressLine2($this->truncateOptional((string) $address->getStreet2(), 300))
            ->adminArea2(mb_substr((string) $address->getCity(), 0, 120))
            ->adminArea1($this->truncateOptional((string) $address->getRegionCode(), 300))
            ->postalCode($this->truncateOptional((string) $address->getPostcode(), 60))
            ->build();
    }

    private function truncateOptional(string $value, int $length): ?string
    {
        $value = trim($value);

        return $value === '' ? null : mb_substr($value, 0, $length);
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

        return PayerBuilder::init()
            ->emailAddress($this->getPayerEmail($quote, $billingAddress))
            ->name($name)
            ->address($this->buildAddress($billingAddress))
            ->build();
    }

    /**
     * Guest quotes get customer_email only when the order is placed, so the billing address carries it before that.
     * PayPal rejects the whole order request for an address it cannot parse, including internationalized ones
     * Magento accepts; such an address is left out instead.
     */
    private function getPayerEmail(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Quote_Address $billingAddress): ?string
    {
        $email = trim((string) $quote->getCustomerEmail());
        if ($email === '') {
            $email = trim((string) $billingAddress->getEmail());
        }

        if ($email === '' || strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        return $email;
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
        $payment->setData(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID, $result->getId())
            ->setPaypalCorrelationId($result->getId())
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
