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
use PaypalServerSdkLib\Models\{
    Refund,
    PaypalWalletResponse
};

/**
 * PayPal Payment Method Model
 *
 * Handles PayPal order creation, authorization, capture, refund and void operations
 *  *
 */
class Mage_Paypal_Model_Paypal extends Mage_Payment_Model_Method_Abstract
{
    // Payment method configuration
    protected $_code = 'paypal';
    protected $_formBlockType = 'paypal/form';
    protected $_infoBlockType = 'paypal/adminhtml_info';
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = false;
    protected $_isGateway = true;
    protected $_canUseCheckout = true;

    // Payment information transport keys
    public const PAYPAL_PAYMENT_STATUS = 'paypal_payment_status';
    public const PAYPAL_PAYMENT_AUTHORIZATION_ID = 'paypal_payment_authorization_id';
    public const PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME = 'paypal_payment_authorization_expires_time';

    // Error messages
    private const ERROR_ZERO_AMOUNT = 'PayPal does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.';
    private const ERROR_NO_AUTHORIZATION_ID = 'No authorization ID found. Cannot capture payment.';

    /**
     * Create PayPal order via API
     *
     * @param Mage_Sales_Model_Quote $quote Customer quote
     * @return array{success: bool, id?: string, error?: string}
     * @throws Mage_Core_Exception
     */
    public function create(Mage_Sales_Model_Quote $quote): array
    {
        try {
            $this->_validateQuoteForPayment($quote);

            $quote->reserveOrderId()->save();
            $api = $this->_getApi();
            $orderRequest = $this->_buildOrderRequest($quote, $quote->getReservedOrderId());

            $response = $api->createOrder($quote, $orderRequest);
            $result = $response->getResult();

            if ($response->isError()) {
                throw new Mage_Core_Exception($result['message'] ?? 'Error creating PayPal order');
            }

            $this->_updatePaymentWithOrderInfo($quote->getPayment(), $response);

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
     * Capture PayPal payment via API
     *
     * @param string $orderId PayPal order ID
     * @throws Mage_Core_Exception
     */
    public function captureOrder(string $orderId, Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quote): void
    {
        $api = $this->_getApi();
        $response = $api->captureOrder($orderId, $quote);
        $result = $response->getResult();

        if ($response->isError()) {
            $this->_handleApiError($response, 'Capture failed');
        }
        $captureId = $this->_extractCaptureId($result);
        $this->_updatePaymentAfterCapture($quote->getPayment(), $response, $captureId);
        $quote->collectTotals()->save();
    }

    /**
     * Authorize PayPal payment via API
     *
     * @param string $orderId PayPal order ID
     * @throws Mage_Core_Exception
     */
    public function authorizePayment(string $orderId, Mage_Sales_Model_Quote $quote): void
    {
        $api = $this->_getApi();
        $response = $api->authorizeOrder($orderId, $quote);
        $result = $response->getResult();

        if ($response->isError()) {
            $this->_handleApiError($response, 'Authorization failed');
        }

        $this->_updatePaymentAfterAuthorization($quote->getPayment(), $response);
        $quote->collectTotals()->save();

        $authorization = $result->getPurchaseUnits()[0]->getPayments()->getAuthorizations()[0];
        $this->_addOrderComment($quote, 'authorized', $authorization->getId());
    }

    /**
     * Reauthorize a payment after the initial authorization has expired
     *
     * @param string $orderId PayPal order ID
     * @param Mage_Sales_Model_Order $order Magento order
     * @return array{success: bool, authorization_id?: string, error?: string}
     */
    public function reauthorizePayment(string $orderId, Mage_Sales_Model_Order $order): array
    {
        try {
            $api = $this->_getApi();
            $response = $api->reAuthorizeOrder($orderId, $order);
            $result = $response->getResult();

            if ($response->isError()) {
                $this->_handleApiError($response, 'Reauthorization failed');
            }

            $authorization = $result->getPurchaseUnits()[0]->getPayments()->getAuthorizations()[0];
            $authorizationId = $authorization->getId();
            $expirationTime = $authorization->getExpirationTime();

            $payment = $order->getPayment();
            $payment->setAdditionalInformation([
                self::PAYPAL_PAYMENT_STATUS => $authorization->getStatus(),
                self::PAYPAL_PAYMENT_AUTHORIZATION_ID => $authorizationId,
                self::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME => $expirationTime,
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->_prepareRawDetails($response->getBody()),
            ])->getShouldCloseParentTransaction();
            $payment->save();

            $transaction = Mage::getModel('sales/order_payment_transaction');
            $transaction->setOrderPaymentObject($payment)
                ->setTxnId($authorizationId)
                ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
                ->setIsClosed(0)
                ->setAdditionalInformation(
                    Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                    $this->_prepareRawDetails($response->getBody()),
                );
            $transaction->save();

            $storeTimezone = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
            $date = new DateTime($expirationTime, new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone($storeTimezone));

            $order->addStatusHistoryComment(
                Mage::helper('paypal')->__(
                    'PayPal payment has been reauthorized. New authorization ID: %s. Expires on: %s',
                    $authorizationId,
                    $date->format('Y-m-d H:i:s'),
                ),
                false,
            )->save();

            return [
                'success' => true,
                'authorization_id' => $authorizationId,
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
     * Validate payment method information object
     */
    public function validate(): static
    {
        parent::validate();
        return $this;
    }

    /**
     * Refund payment method
     *
     * @param Varien_Object $payment Payment object
     * @param float $amount Refund amount
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount): static
    {
        try {
            if (is_string($amount)) {
                $amount = (float) $amount;
            }
            $response = $this->_getApi()->refundCapturedPayment(
                $payment->getParentTransactionId(),
                $amount,
                $payment->getOrder()->getOrderCurrencyCode(),
                $payment->getOrder(),
            );

            if ($response->isError()) {
                throw new Mage_Core_Exception($response->getResult()->getMessage());
            }

            $result = $response->getResult();
            $this->_updatePaymentAfterRefund($payment, $result);
            $this->_createRefundTransaction($payment, $response, $amount);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception(
                Mage::helper('paypal')->__('Refund error: %s', $e->getMessage()),
            );
        }

        return $this;
    }

    /**
     * Capture payment method
     *
     * @param Varien_Object $payment Payment object
     * @param float $amount Capture amount
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount): static
    {
        $addInfo = $payment->getAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS);
        if (is_array($addInfo) && isset($addInfo['intent'])) {
            if ($addInfo['intent'] === CheckoutPaymentIntent::CAPTURE) {
                return $this;
            }
        }
        $order = $payment->getOrder();
        $authorizationId = $payment->getAdditionalInformation(self::PAYPAL_PAYMENT_AUTHORIZATION_ID);

        if (!$authorizationId) {
            throw new Mage_Core_Exception(
                Mage::helper('paypal')->__(self::ERROR_NO_AUTHORIZATION_ID),
            );
        }

        $api = $this->_getApi();
        $response = $api->captureAuthorizedPayment($authorizationId, $order);

        if ($response->isError()) {
            $this->_handleApiError($response, 'Capture failed');
        }

        $this->_updatePaymentAfterAuthorizedCapture($payment, $response, $authorizationId);
        $this->_createCaptureTransaction($payment, $response, $authorizationId);

        return $this;
    }

    /**
     * Void payment method
     *
     * @param Varien_Object $payment Payment object
     * @throws Mage_Core_Exception
     */
    public function void(Varien_Object $payment): static
    {
        // PayPal will handle void transaction
        $transactionId = str_contains($payment->getTransactionId(), '-void')
            ? str_replace('-void', '', $payment->getTransactionId())
            : $payment->getTransactionId();

        try {
            $response = $this->_getApi()->voidPayment($transactionId, $payment->getOrder());

            if ($response->isError()) {
                throw new Mage_Core_Exception($response->getResult()->getMessage());
            }

            $this->_updatePaymentAfterVoid($payment);
            $this->_createVoidTransaction($payment, $response);
            $payment->getOrder()->addStatusHistoryComment(
                Mage::helper('paypal')->__('PayPal payment voided successfully. Transaction ID: %s', $transactionId),
                false,
            )->save();
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception(
                Mage::helper('paypal')->__('Void error: %s', $e->getMessage()),
            );
        }

        return $this;
    }

    /**
     * Cancel payment method
     *
     * @param Varien_Object $payment Payment object
     * @throws Mage_Core_Exception
     */
    public function cancel(Varien_Object $payment): static
    {
        if ($payment->getIsTransactionClosed() && $payment->getShouldCloseParentTransaction()) {
            return $this;
        }
        $this->void($payment);
        return $this;
    }

    /**
     * Check if payment method is available
     *
     * @param Mage_Sales_Model_Quote|null $quote
     */
    public function isAvailable($quote = null): bool
    {
        if (!$this->getConfigData('client_id') || !$this->getConfigData('client_secret')) {
            return false;
        }

        $this->_handleMultishippingNotification($quote);

        return parent::isAvailable($quote);
    }

    /**
     * Get PayPal API instance
     */
    private function _getApi(): Mage_Paypal_Model_Api
    {
        return Mage::getSingleton('paypal/api');
    }

    /**
     * Handle API error response
     *
     * @param ApiResponse $response API response
     * @param string $defaultMessage Default error message
     * @throws Mage_Core_Exception
     */
    private function _handleApiError(ApiResponse $response, string $defaultMessage): never
    {
        $errorMsg = $this->_extractErrorMessage($response, $defaultMessage);
        throw new Mage_Core_Exception($errorMsg);
    }

    /**
     * Get current quote
     */
    private function _getQuote(): Mage_Sales_Model_Quote
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Validate quote for PayPal payment processing
     *
     * @throws Mage_Core_Exception
     */
    private function _validateQuoteForPayment(Mage_Sales_Model_Quote $quote): void
    {
        $quote->collectTotals();

        if (!$quote->getGrandTotal() && !$quote->hasNominalItems()) {
            throw new Mage_Core_Exception(
                Mage::helper('paypal')->__(self::ERROR_ZERO_AMOUNT),
            );
        }
    }

    /**
     * Update payment object with PayPal order information
     */
    private function _updatePaymentWithOrderInfo(Mage_Sales_Model_Quote_Payment $payment, ApiResponse $response): void
    {
        $result = $response->getResult();
        $payment->setPaypalCorrelationId($result->getId())
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->_prepareRawDetails($response->getBody()),
            )
            ->save();
    }

    /**
     * Build a purchase unit using PayPal SDK builders
     *
     * @param string|null $referenceId Optional reference ID
     * @return object The built purchase unit object
     */
    private function _buildPurchaseUnit(Mage_Sales_Model_Quote $quote, ?string $referenceId = null): object
    {
        $cart = Mage::getModel('paypal/cart', [$quote]);
        $totals = $cart->getAmounts();
        $currency = $quote->getOrderCurrencyCode() ?: $quote->getQuoteCurrencyCode();
        $items = $cart->getAllItems();
        // Amount check
        $taxCalculated = 0.00;
        $taxDifference = 0.00;
        $taxAmount = 0.00;
        if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_TAX])) {
            $taxAmount = (float) $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->getValue();
        }
        foreach ($items as $item) {
            /**
             * @var PaypalServerSdkLib\Models\Item $item
             */
            if ($item->getTax()) {
                $qty = (int) $item->getQuantity();
                $taxValue = (float) $item->getTax()->getValue();
                $taxCalculated += $taxValue * $qty;
            }
        }
        if ($taxCalculated !== $taxAmount) {
            $taxDifference = round($taxAmount - $taxCalculated, 2);
            $taxAmount = $taxCalculated;
        }
        if ($taxDifference < 0) {
            if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_TAX])) {
                $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->setValue(
                    number_format($taxCalculated, 2, '.', ''),
                );
            }
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
        } else {
            $moneyBuilder = MoneyBuilder::init(
                $currency,
                number_format(abs($taxDifference), 2, '.', ''),
            );
            $itemBuilder = ItemBuilder::init(Mage::helper('paypal')->__('Rounding'), $moneyBuilder->build(), '1')
                ->sku(Mage::helper('paypal')->__('Rounding'))
                ->category(ItemCategory::DIGITAL_GOODS);
            $items[] = $itemBuilder->build();
            if (isset($totals[Mage_Paypal_Model_Cart::TOTAL_TAX])) {
                $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->setValue(
                    number_format($taxCalculated, 2, '.', ''),
                );
            }
        }
        $breakdown = $this->_buildAmountBreakdown($totals);
        $amount = $this->_buildAmountWithBreakdown($currency, $quote->getGrandTotal(), $breakdown);
        $purchaseUnitBuilder = PurchaseUnitRequestBuilder::init($amount);
        if (!empty($items)) {
            $purchaseUnitBuilder->items($items);
        }
        $purchaseUnitBuilder->referenceId($referenceId ?: (string) $quote->getId());
        $purchaseUnitBuilder->invoiceId($referenceId ?: (string) $quote->getId());
        return $purchaseUnitBuilder->build();
    }

    /**
     * Build amount breakdown from cart totals
     *
     * @param array<string, mixed> $totals Cart totals
     * @return object Built breakdown object
     */
    private function _buildAmountBreakdown(array $totals): object
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
    private function _buildAmountWithBreakdown(string $currency, float $totalAmount, object $breakdown): object
    {
        $formattedTotal = number_format($totalAmount, 2, '.', '');

        return AmountWithBreakdownBuilder::init($currency, $formattedTotal)
            ->breakdown($breakdown)
            ->build();
    }

    /**
     * Build a complete PayPal order request using SDK builders
     *
     * @param string|null $referenceId Optional reference ID
     * @return object The built order request object
     */
    private function _buildOrderRequest(Mage_Sales_Model_Quote $quote, ?string $referenceId = null): object
    {
        $purchaseUnit = $this->_buildPurchaseUnit($quote, $referenceId);
        $orderRequestBuilder = OrderRequestBuilder::init(
            $this->_getPaymentIntent(),
            [$purchaseUnit],
        );

        $payer = $this->_buildPayerFromBillingAddress($quote);
        if ($payer !== null) {
            $orderRequestBuilder->payer($payer);
        }

        return $orderRequestBuilder->build();
    }

    /**
     * Build payer object from billing address
     */
    private function _buildPayerFromBillingAddress(Mage_Sales_Model_Quote $quote): ?object
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
     * Get payment intent based on payment action configuration
     */
    private function _getPaymentIntent(): string
    {
        // Always use capture for multishipping orders
        if ($this->_getQuote()->getIsMultiShipping()) {
            return CheckoutPaymentIntent::CAPTURE;
        }

        $paymentAction = Mage::getSingleton('paypal/config')->getPaymentAction();

        return $paymentAction === strtolower(CheckoutPaymentIntent::AUTHORIZE)
            ? CheckoutPaymentIntent::AUTHORIZE
            : CheckoutPaymentIntent::CAPTURE;
    }

    /**
     * Extract capture ID from API result
     *
     * @param mixed $result API result
     */
    private function _extractCaptureId(mixed $result): ?string
    {
        if (
            !method_exists($result, 'getPurchaseUnits') ||
            !is_array($result->getPurchaseUnits()) ||
            empty($result->getPurchaseUnits())
        ) {
            return null;
        }

        $purchaseUnit = $result->getPurchaseUnits()[0];
        $payments = $purchaseUnit->getPayments();

        if (
            !method_exists($payments, 'getCaptures') ||
            !is_array($payments->getCaptures()) ||
            empty($payments->getCaptures())
        ) {
            return null;
        }

        return $payments->getCaptures()[0]->getId();
    }

    /**
     * Update payment object after successful capture
     *
     * @param ApiResponse $response API result
     * @param string $captureId Capture ID
     */
    private function _updatePaymentAfterCapture(
        Mage_Sales_Model_Quote_Payment|Mage_Sales_Model_Order_Payment $payment,
        ApiResponse $response,
        string $captureId
    ): void {
        $result = $response->getResult();
        $payment->setMethod($this->_code)
            ->setPaypalCorrelationId($captureId)
            ->setTransactionId($captureId)
            ->setIsTransactionClosed(true)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->_prepareRawDetails($response->getBody()),
            );
        $paymentSource = $result->getPaymentSource();
        if ($paymentSource->getPaypal() instanceof PaypalWalletResponse) {
            $paypalWallet = $paymentSource->getPaypal();
            $payment->setPaypalPayerId($paypalWallet->getAccountId())
                ->setPaypalPayerStatus($paypalWallet->getAccountStatus());
        }

        $payment->save();
    }

    /**
     * Update payment object after successful authorization
     *
     * @param ApiResponse $response API result
     */
    private function _updatePaymentAfterAuthorization(Mage_Sales_Model_Quote_Payment $payment, ApiResponse $response): void
    {
        $result = $response->getResult();
        $authorization = $result->getPurchaseUnits()[0]->getPayments()->getAuthorizations()[0];
        $paymentSource = $result->getPaymentSource();

        if ($paymentSource->getPaypal() instanceof PaypalWalletResponse) {
            $paypalWallet = $paymentSource->getPaypal();
            $payment->setPaypalPayerId($paypalWallet->getAccountId())
                ->setPaypalPayerStatus($paypalWallet->getAccountStatus());
        }

        $payment->setAdditionalInformation([
            self::PAYPAL_PAYMENT_STATUS => $authorization->getStatus(),
            self::PAYPAL_PAYMENT_AUTHORIZATION_ID => $authorization->getId(),
            self::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME => $authorization->getExpirationTime(),
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->_prepareRawDetails($response->getBody()),
        ]);
        $payment->save();
    }

    /**
     * Add order status comment
     *
     * @param string $action Action performed (captured, authorized, etc.)
     * @param string|null $transactionId Transaction ID
     */
    private function _addOrderComment(
        Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quote,
        string $action,
        ?string $transactionId
    ): void {
        if ($quote instanceof Mage_Sales_Model_Order && $transactionId) {
            $message = match ($action) {
                'captured' => 'PayPal payment captured successfully. Capture ID: %s',
                'authorized' => 'PayPal payment authorized successfully. Authorization ID: %s',
                default => 'PayPal payment %s successfully. Transaction ID: %s'
            };

            $quote->addStatusHistoryComment(
                Mage::helper('paypal')->__($message, $transactionId),
                false,
            );
        }
    }

    /**
     * Update payment after refund
     *
     * @param Varien_Object $payment Payment object
     * @param Refund $result API result
     */
    private function _updatePaymentAfterRefund(Varien_Object $payment, Refund $result): void
    {
        $payment->setTransactionId($result->getId())
            ->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1)
            ->setSkipTransactionCreation(true);
    }

    /**
     * Create refund transaction record
     *
     * @param Varien_Object $payment Payment object
     * @param ApiResponse $response API result
     * @param float $amount Refund amount
     */
    private function _createRefundTransaction(Varien_Object $payment, ApiResponse $response, float $amount): void
    {
        $result = $response->getResult();
        $transaction = Mage::getModel('sales/order_payment_transaction');
        /**
         * @var Mage_Sales_Model_Order_Payment $payment
         */
        $transaction->setOrderPaymentObject($payment)
            ->setTxnId($result->getId())
            ->setParentTxnId($payment->getParentTransactionId())
            ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND)
            ->setIsClosed(1)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->_prepareRawDetails($response->getBody()),
            );

        $transaction->save();
    }

    /**
     * Update payment after authorized capture
     *
     * @param Varien_Object $payment Payment object
     * @param string $authorizationId Authorization ID
     */
    private function _updatePaymentAfterAuthorizedCapture(Varien_Object $payment, ApiResponse $response, string $authorizationId): void
    {
        $result = $response->getResult();
        $captureId = $result->getId();
        $payment->setTransactionId($captureId)
            ->setParentTransactionId($authorizationId)
            ->setIsTransactionClosed(true)
            ->setShouldCloseParentTransaction(true)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->_prepareRawDetails($response->getBody()),
            );
    }

    /**
     * Create capture transaction record
     *
     * @param Varien_Object $payment Payment object
     * @param string $authorizationId Authorization ID
     */
    private function _createCaptureTransaction(Varien_Object $payment, ApiResponse $response, string $authorizationId): void
    {
        $result = $response->getResult();
        $transaction = Mage::getModel('sales/order_payment_transaction');
        /**
         * @var Mage_Sales_Model_Order_Payment $payment
         */
        $transaction->setOrderPaymentObject($payment)
            ->setTxnId($result->getId())
            ->setParentTxnId($authorizationId)
            ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE)
            ->setIsClosed(1)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->_prepareRawDetails($response->getBody()),
            );

        $transaction->save();
    }

    /**
     * Update payment after void
     *
     * @param Varien_Object $payment Payment object
     */
    private function _updatePaymentAfterVoid(Varien_Object $payment): void
    {
        $payment->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1)
            ->setSkipTransactionCreation(true);
    }

    /**
     * Create void transaction record
     *
     * @param Varien_Object $payment Payment object
     * @param ApiResponse $response API response
     */
    private function _createVoidTransaction(Varien_Object $payment, ApiResponse $response): void
    {
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $result = $response->getResult();
        /**
         * @var Mage_Sales_Model_Order_Payment $payment
         */
        $transaction->setOrderPaymentObject($payment)
            ->setTxnId($payment->getTransactionId())
            ->setParentTxnId($result->getId())
            ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID)
            ->setIsClosed(1)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->_prepareRawDetails($response->getBody()),
            );
        $transaction->save();

        $parentTxn = $transaction->loadByTxnId($result->getId());
        if ($parentTxn->getId()) {
            $parentTxn->setIsClosed(1);
            $parentTxn->save();
        }
    }

    /**
     * Handle multishipping notification
     */
    private function _handleMultishippingNotification(?Mage_Sales_Model_Quote $quote): void
    {
        if (
            $quote?->getIsMultiShipping() &&
            Mage::getSingleton('paypal/config')->getPaymentAction() === strtolower(CheckoutPaymentIntent::AUTHORIZE)
        ) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('paypal')->__(
                    'PayPal will process multishipping orders as immediate capture regardless of your authorization setting.',
                ),
            );
        }
    }

    /**
     * Extract error message from API response
     *
     * @param ApiResponse $response API response
     * @param string $defaultMessage Default error message
     */
    private function _extractErrorMessage(ApiResponse $response, string $defaultMessage): string
    {
        $result = $response->getResult();

        return match (true) {
            is_array($result) && isset($result['message']) => $result['message'] ?: $defaultMessage,
            is_object($result) && method_exists($result, 'getMessage') => $result->getMessage() ?: $defaultMessage,
            is_string($result) => $result ?: $defaultMessage,
            default => $defaultMessage
        };
    }

    /**
     * Prepare raw details for storage
     *
     * @param string $details JSON details object
     * @return array<string, mixed>
     */
    private function _prepareRawDetails(string $details): array
    {
        $decoded = json_decode($details, true);
        if (isset($decoded['links'])) {
            unset($decoded['links']);
        }
        return array_map(
            fn($v) => is_array($v) ? json_encode($v) : $v,
            $decoded ?? [],
        );
    }
}
