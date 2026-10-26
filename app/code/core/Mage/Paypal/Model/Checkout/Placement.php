<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use Monolog\Level;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

/**
 * Pays for an approved PayPal order and places the Magento order for its quote in one server-side step.
 *
 * Capture and placement used to be two browser requests. When the browser lost the checkout page in
 * between, the buyer was charged and no order existed. Every path that charges a buyer or turns a charge
 * into an order goes through here: the checkout button, the shortcut review page, a retried checkout and
 * the webhook fallback.
 */
class Mage_Paypal_Model_Checkout_Placement
{
    public const LOCK_TIMEOUT_SECONDS = 30;

    private const ERROR_PAYMENT_RECEIVED = 'We have received your PayPal payment, but your order could not be completed. Please do not pay again: we have been notified and will contact you shortly.';

    private const ERROR_PAYMENT_MISMATCH = 'The PayPal payment does not match this quote. Please restart PayPal checkout.';

    /**
     * Charge the approved PayPal order, unless PayPal already has, and place the order for the quote.
     *
     * Safe to repeat: once the PayPal order has a Magento order, that order is returned. With $allowCharge
     * off, only a PayPal order that is already charged is placed. A caller that has already prepared the
     * quote's customer (the shortcut review page, before it patches the PayPal order) passes the result as
     * $isNewCustomer so it is not prepared twice.
     *
     * @throws Mage_Core_Exception
     * @throws Mage_Paypal_Model_Checkout_UnplacedPaymentException
     */
    public function placeApprovedOrder(
        Mage_Sales_Model_Quote $quote,
        string $paypalOrderId,
        bool $allowCharge = true,
        bool $notifyOnFailure = true,
        ?bool $isNewCustomer = null,
        bool $saveCustomerAddresses = true
    ): Mage_Sales_Model_Order {
        if ($paypalOrderId === '') {
            Mage::throwException(Mage::helper('paypal')->__('PayPal order ID is required'));
        }

        $helper = $this->getHelper();
        if (!$helper->acquirePaypalOrderLock($paypalOrderId, self::LOCK_TIMEOUT_SECONDS)) {
            Mage::throwException(
                Mage::helper('paypal')->__('Your PayPal payment is still being processed. Please wait a moment and try again.'),
            );
        }

        try {
            $order = $this->findOrderByPaypalOrderId($paypalOrderId);
            if ($order instanceof Mage_Sales_Model_Order) {
                return $this->completePlacedOrder($quote, $order);
            }

            $this->assertQuoteAwaitsPaypalOrder($quote, $paypalOrderId);

            $response = $this->fetchPaypalOrder($quote, $paypalOrderId);
            $details = $this->decodePaypalOrder($response);
            $status = strtoupper((string) ($details['status'] ?? ''));
            $isAuthorize = strtoupper((string) ($details['intent'] ?? '')) === CheckoutPaymentIntent::AUTHORIZE;

            if ($status === 'COMPLETED') {
                $isNewCustomer ??= $this->getFinalizer()->prepareQuoteForCheckout($quote, $saveCustomerAddresses);
                $this->recordCompletedPayment($quote, $response, $details, $paypalOrderId, $isAuthorize, true);
            } elseif ($status === 'APPROVED' && $allowCharge) {
                // The customer is prepared before the charge: registering a new account can move the quote to
                // another customer group, and so to other prices, which must not happen after the buyer paid.
                $isNewCustomer ??= $this->getFinalizer()->prepareQuoteForCheckout($quote, $saveCustomerAddresses);
                $this->validateQuoteBeforeCharge($quote, $details, $paypalOrderId);
                $this->chargeApprovedOrderOnce($quote, $paypalOrderId, $isAuthorize);
            } else {
                Mage::throwException(
                    Mage::helper('paypal')->__('Your PayPal payment was not approved. Please complete the PayPal checkout and try again.'),
                );
            }

            return $this->placeChargedQuote($quote, $paypalOrderId, $isAuthorize, $isNewCustomer, $notifyOnFailure);
        } finally {
            $helper->releasePaypalOrderLock($paypalOrderId);
        }
    }

    /**
     * Settle a quote that may already be paid before the buyer is allowed to start another PayPal order.
     *
     * Returns the placed order when PayPal did charge. Returns null, and forgets the earlier attempt, when
     * PayPal confirms it did not, so a new PayPal order can be started.
     *
     * @throws Mage_Core_Exception
     * @throws Mage_Paypal_Model_Checkout_UnplacedPaymentException
     */
    public function resolvePendingCharge(Mage_Sales_Model_Quote $quote): ?Mage_Sales_Model_Order
    {
        $helper = $this->getHelper();
        $paypalOrderId = $helper->getPendingChargePaypalOrderId($quote);
        if ($paypalOrderId === '') {
            return null;
        }

        if (!$helper->hasUnplacedPaypalPayment($quote)) {
            if (!$helper->acquirePaypalOrderLock($paypalOrderId, self::LOCK_TIMEOUT_SECONDS)) {
                Mage::throwException(
                    Mage::helper('paypal')->__('Your PayPal payment is still being processed. Please wait a moment and try again.'),
                );
            }

            try {
                $order = $this->findOrderByPaypalOrderId($paypalOrderId);
                if ($order instanceof Mage_Sales_Model_Order) {
                    return $this->completePlacedOrder($quote, $order);
                }

                $details = $this->decodePaypalOrder($this->fetchPaypalOrder($quote, $paypalOrderId));
                if (strtoupper((string) ($details['status'] ?? '')) !== 'COMPLETED') {
                    $quote->getPayment()->unsAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID)->save();
                    return null;
                }
            } finally {
                $helper->releasePaypalOrderLock($paypalOrderId);
            }
        }

        return $this->placeApprovedOrder($quote, $paypalOrderId, false, false);
    }

    /**
     * Place the missing order for a PayPal order reported by a webhook.
     *
     * An approval is only charged for an in-checkout order the storefront never took over. A shortcut order
     * is confirmed by the buyer on the store's review page after PayPal approval, and a checkout that did
     * handle the approval already charged it or told the buyer it failed; charging either from a webhook
     * would overrule the buyer.
     *
     * @throws Mage_Core_Exception
     * @throws Mage_Paypal_Model_Checkout_UnplacedPaymentException
     */
    public function recoverOrder(string $paypalOrderId, bool $isApproval, bool $notifyOnFailure): ?Mage_Sales_Model_Order
    {
        if ($paypalOrderId === '' || $this->findOrderByPaypalOrderId($paypalOrderId) instanceof Mage_Sales_Model_Order) {
            return null;
        }

        $quote = $this->findQuoteByPaypalOrderId($paypalOrderId);
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            return null;
        }

        $payment = $quote->getPayment();
        $isShortcut = (string) $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID) === $paypalOrderId;
        $isTakenOver = (string) $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID) === $paypalOrderId;
        if ($isApproval && ($isShortcut || $isTakenOver)) {
            return null;
        }

        $emulation = Mage::getSingleton('core/app_emulation');
        $environment = $emulation->startEnvironmentEmulation((int) $quote->getStoreId());
        try {
            return $this->placeApprovedOrder(
                $quote,
                $paypalOrderId,
                $isApproval,
                $notifyOnFailure,
                null,
                !$isShortcut,
            );
        } finally {
            $emulation->stopEnvironmentEmulation($environment);
        }
    }

    /**
     * Record that the storefront has handled the buyer's approval of this PayPal order, even though it did
     * not charge it, so the webhook fallback does not charge an approval the buyer was told had failed.
     */
    public function markCheckoutAttempt(Mage_Sales_Model_Quote $quote, string $paypalOrderId): void
    {
        if ($paypalOrderId === '' || (int) $quote->getId() === 0
            || $this->getHelper()->getQuotePaypalOrderId($quote) !== $paypalOrderId
        ) {
            return;
        }

        $quote->getPayment()
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID, $paypalOrderId)
            ->save();
    }

    /**
     * Record a charged PayPal payment whose order could not be placed, and return the message for the buyer.
     *
     * The quote keeps the payment, so the order can still be placed from it later.
     */
    public function reportUnplacedPayment(
        Mage_Sales_Model_Quote $quote,
        string $paypalOrderId,
        Throwable $throwable,
        bool $notify = true
    ): string {
        Mage::logException($throwable);

        $message = sprintf(
            'PayPal order %s was charged for quote %d, but the Magento order could not be placed: %s',
            $paypalOrderId,
            (int) $quote->getId(),
            $throwable->getMessage(),
        );
        Mage::log($message, Level::Critical, 'paypal.log', true);

        if ($notify) {
            $this->notifyStore($quote, $message);
        }

        return Mage::helper('paypal')->__(self::ERROR_PAYMENT_RECEIVED);
    }

    /**
     * Email the store's payment-failed recipients. A quote is optional: a charge whose quote is gone still has
     * to reach someone.
     */
    public function notifyStore(?Mage_Sales_Model_Quote $quote, string $message): void
    {
        $quote ??= Mage::getModel('sales/quote')->setStoreId((int) Mage::app()->getDefaultStoreView()?->getId());

        try {
            Mage::helper('checkout')->sendPaymentFailedEmail($quote, $message);
        } catch (Throwable $throwable) {
            Mage::logException($throwable);
        }
    }

    public function findOrderByPaypalOrderId(string $paypalOrderId): ?Mage_Sales_Model_Order
    {
        $payment = Mage::getResourceModel('sales/order_payment_collection')
            ->addFieldToFilter(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID, $paypalOrderId)
            ->setPageSize(1)
            ->getFirstItem();
        if ((int) $payment->getId() === 0) {
            return null;
        }

        $order = Mage::getModel('sales/order')->load($payment->getParentId());

        return (int) $order->getId() > 0 ? $order : null;
    }

    public function findQuoteByPaypalOrderId(string $paypalOrderId): ?Mage_Sales_Model_Quote
    {
        $payment = Mage::getResourceModel('sales/quote_payment_collection')
            ->addFieldToFilter(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID, $paypalOrderId)
            ->setPageSize(1)
            ->getFirstItem();
        if ((int) $payment->getId() === 0) {
            return null;
        }

        $quote = $this->loadQuote((int) $payment->getQuoteId());

        return (int) $quote->getId() > 0 && (int) $quote->getIsActive() === 1 ? $quote : null;
    }

    protected function loadQuote(int $quoteId): Mage_Sales_Model_Quote
    {
        return Mage::getModel('sales/quote')->loadByIdWithoutStore($quoteId);
    }

    /**
     * @throws Mage_Core_Exception
     */
    protected function fetchPaypalOrder(Mage_Sales_Model_Quote $quote, string $paypalOrderId): ApiResponse
    {
        $response = $this->getHelper()->getApi()->setStore($quote->getStore())->getOrderDetails($paypalOrderId);
        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Unable to read PayPal order details.');
        }

        return $response;
    }

    /**
     * @throws Mage_Core_Exception
     */
    protected function chargeApprovedOrder(Mage_Sales_Model_Quote $quote, string $paypalOrderId, bool $isAuthorize): void
    {
        $paypal = Mage::getModel('paypal/paypal');
        if ($isAuthorize) {
            $paypal->authorizePayment($paypalOrderId, $quote);
            return;
        }

        $paypal->captureOrder($paypalOrderId, $quote);
    }

    protected function createQuoteService(Mage_Sales_Model_Quote $quote): Mage_Sales_Model_Service_Quote
    {
        return Mage::getModel('sales/service_quote', $quote);
    }

    protected function getTransactionManager(): Mage_Paypal_Model_Transaction
    {
        return Mage::getSingleton('paypal/transaction');
    }

    protected function getFinalizer(): Mage_Paypal_Model_Checkout_Finalizer
    {
        return Mage::getModel('paypal/checkout_finalizer');
    }

    protected function getHelper(): Mage_Paypal_Model_Helper
    {
        return Mage::getSingleton('paypal/helper');
    }

    /**
     * Checked against the stored quote, not the caller's copy: another request may have placed or changed it
     * while this one waited for the lock.
     *
     * @throws Mage_Core_Exception
     */
    private function assertQuoteAwaitsPaypalOrder(Mage_Sales_Model_Quote $quote, string $paypalOrderId): void
    {
        $storedQuote = (int) $quote->getId() > 0 ? $this->loadQuote((int) $quote->getId()) : null;
        if (!$storedQuote instanceof Mage_Sales_Model_Quote || (int) $storedQuote->getIsActive() !== 1) {
            throw new Mage_Paypal_Model_Exception(Mage::helper('paypal')->__(self::ERROR_PAYMENT_MISMATCH));
        }

        $storedPaypalOrderId = $this->getHelper()->getQuotePaypalOrderId($storedQuote);
        if ($storedPaypalOrderId === '' || !hash_equals($storedPaypalOrderId, $paypalOrderId)) {
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__('The PayPal order does not match this checkout session.'),
            );
        }
    }

    /**
     * @return array<string, mixed>
     * @throws Mage_Paypal_Model_Exception
     */
    private function decodePaypalOrder(ApiResponse $response): array
    {
        $details = json_decode((string) $response->getBody(), true);
        if (!is_array($details)) {
            throw new Mage_Paypal_Model_Exception(Mage::helper('paypal')->__('PayPal order details were not valid JSON.'));
        }

        return $details;
    }

    /**
     * Everything that would stop the order from being placed must fail here, before the buyer is charged.
     *
     * Placement re-collects totals and compares them with the charged amount anyway, so a total that moved
     * since the PayPal order was created is refused now instead of surfacing after the capture.
     *
     * @param  array<string, mixed> $details
     * @throws Mage_Core_Exception
     */
    private function validateQuoteBeforeCharge(Mage_Sales_Model_Quote $quote, array $details, string $paypalOrderId): void
    {
        if (!$quote->hasItems() || $quote->getHasError()) {
            Mage::throwException(Mage::helper('paypal')->__('Your cart has changed. Please review it and restart PayPal checkout.'));
        }

        $helper = $this->getHelper();
        $this->getFinalizer()->ignoreAddressValidation($quote);
        $quote->collectTotals();
        $helper->validateShippingMethodForQuote($quote, false);
        $helper->assertPaypalOrderMatchesQuote($details, $quote, $paypalOrderId);
    }

    /**
     * @throws Mage_Core_Exception
     */
    private function chargeApprovedOrderOnce(Mage_Sales_Model_Quote $quote, string $paypalOrderId, bool $isAuthorize): void
    {
        // Saved before the call: if its response is lost, the quote still shows that PayPal may have charged.
        $quote->getPayment()
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID, $paypalOrderId)
            ->save();

        try {
            $this->chargeApprovedOrder($quote, $paypalOrderId, $isAuthorize);
        } catch (Throwable $throwable) {
            // A failed call does not prove PayPal did not charge (a timeout after PayPal captured, a failure while
            // recording the result), so ask PayPal before reporting a failure the buyer could answer by paying again.
            try {
                $response = $this->fetchPaypalOrder($quote, $paypalOrderId);
                $details = $this->decodePaypalOrder($response);
            } catch (Throwable $readException) {
                Mage::logException($readException);
                throw $throwable;
            }

            if (strtoupper((string) ($details['status'] ?? '')) !== 'COMPLETED') {
                throw $throwable;
            }

            Mage::logException($throwable);
            $this->recordCompletedPayment($quote, $response, $details, $paypalOrderId, $isAuthorize, false);
        }
    }

    /**
     * Bring the quote payment up to date with a PayPal order that is already charged.
     *
     * With $requireSettled on, a capture that PayPal still holds as pending (e.g. an eCheck) is refused; its
     * PAYMENT.CAPTURE.COMPLETED webhook places the order once the money has actually moved.
     *
     * @param  array<string, mixed> $details
     * @throws Mage_Core_Exception
     */
    private function recordCompletedPayment(
        Mage_Sales_Model_Quote $quote,
        ApiResponse $response,
        array $details,
        string $paypalOrderId,
        bool $isAuthorize,
        bool $requireSettled
    ): void {
        $payments = is_array($details['purchase_units'][0]['payments'] ?? null) ? $details['purchase_units'][0]['payments'] : [];
        $transactions = $this->getTransactionManager();

        if ($isAuthorize) {
            $authorization = is_array($payments['authorizations'][0] ?? null) ? $payments['authorizations'][0] : [];
            if (($authorization['id'] ?? '') === '') {
                throw new Mage_Paypal_Model_Exception(sprintf('PayPal order %s is completed without an authorization.', $paypalOrderId));
            }

            if ($requireSettled && !in_array(strtoupper((string) ($authorization['status'] ?? '')), ['CREATED', 'CAPTURED'], true)) {
                Mage::throwException(Mage::helper('paypal')->__('Your PayPal payment is still being processed by PayPal. Your order will be placed once it completes.'));
            }

            $transactions->updatePaymentAfterAuthorization($quote->getPayment(), $response);
        } else {
            $capture = is_array($payments['captures'][0] ?? null) ? $payments['captures'][0] : [];
            $captureId = (string) ($capture['id'] ?? '');
            if ($captureId === '') {
                throw new Mage_Paypal_Model_Exception(sprintf('PayPal order %s is completed without a capture.', $paypalOrderId));
            }

            if ($requireSettled && strtoupper((string) ($capture['status'] ?? '')) !== 'COMPLETED') {
                Mage::throwException(Mage::helper('paypal')->__('Your PayPal payment is still being processed by PayPal. Your order will be placed once it completes.'));
            }

            $transactions->updatePaymentAfterCapture($quote->getPayment(), $response, $captureId);
        }

        $quote->collectTotals()->save();
    }

    /**
     * @throws Mage_Paypal_Model_Checkout_UnplacedPaymentException
     */
    private function placeChargedQuote(
        Mage_Sales_Model_Quote $quote,
        string $paypalOrderId,
        bool $isAuthorize,
        bool $isNewCustomer,
        bool $notifyOnFailure
    ): Mage_Sales_Model_Order {
        try {
            $finalizer = $this->getFinalizer();
            $finalizer->ignoreAddressValidation($quote);
            $quote->collectTotals();
            $this->getHelper()->validateProcessedPaymentForQuote($quote, $isAuthorize, $paypalOrderId);

            $service = $this->createQuoteService($quote);
            $service->submitAll();
            $order = $finalizer->finalizeSubmittedOrder($quote, $service, $isAuthorize, $isNewCustomer);
            if (!$order instanceof Mage_Sales_Model_Order) {
                throw new Mage_Paypal_Model_Exception('Submitting the quote produced no order.');
            }

            return $order;
        } catch (Throwable $throwable) {
            // The order is committed before the submit_success observers and finalizing run; a failure in either
            // still leaves a real, paid order, found here by its PayPal order id.
            $placedOrder = $this->findOrderByPaypalOrderId($paypalOrderId);
            if ($placedOrder instanceof Mage_Sales_Model_Order) {
                $this->reportUnfinalizedOrder($placedOrder, $paypalOrderId, $throwable);
                return $this->completePlacedOrder($quote, $placedOrder);
            }

            throw new Mage_Paypal_Model_Checkout_UnplacedPaymentException(
                $this->reportUnplacedPayment($quote, $paypalOrderId, $throwable, $notifyOnFailure),
                [],
                $throwable,
            );
        }
    }

    /**
     * @throws Mage_Paypal_Model_Exception
     */
    private function completePlacedOrder(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Order $order): Mage_Sales_Model_Order
    {
        if ((int) $order->getQuoteId() !== (int) $quote->getId()) {
            throw new Mage_Paypal_Model_Exception(Mage::helper('paypal')->__(self::ERROR_PAYMENT_MISMATCH));
        }

        // A placement interrupted after the order was committed can leave its quote active, which would keep
        // offering a payment that is already an order.
        $storedQuote = $this->loadQuote((int) $quote->getId());
        if ((int) $storedQuote->getId() > 0 && (int) $storedQuote->getIsActive() === 1) {
            $storedQuote->setIsActive(0)->save();
        }

        $quote->setIsActive(0);

        Mage::getSingleton('checkout/session')
            ->setLastQuoteId($quote->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());

        return $order;
    }

    private function reportUnfinalizedOrder(Mage_Sales_Model_Order $order, string $paypalOrderId, Throwable $throwable): void
    {
        Mage::logException($throwable);
        Mage::log(
            sprintf(
                'Order %s was placed for PayPal order %s, but finishing it failed; check its payment transaction and invoice: %s',
                $order->getIncrementId(),
                $paypalOrderId,
                $throwable->getMessage(),
            ),
            Level::Critical,
            'paypal.log',
            true,
        );
    }
}
