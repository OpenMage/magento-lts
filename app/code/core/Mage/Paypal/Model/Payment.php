<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\{
    Refund,
    PaypalWalletResponse
};

/**
 * PayPal Payment Processing Handler
 * Handles authorization, capture, refund and void operations
 */
class Mage_Paypal_Model_Payment extends Mage_Core_Model_Abstract
{
    // Payment information transport keys
    public const PAYPAL_REQUEST_ID = 'paypal_request_id';

    public const PAYPAL_PAYMENT_SOURCE = 'paypal_payment_source';

    public const PAYPAL_PAYMENT_AUTHORIZATION_ID = 'paypal_payment_authorization_id';

    public const PAYPAL_PAYMENT_AUTHORIZATION_REAUTHORIZED = 'paypal_payment_authorization_reauthorized';

    /**
     * Payment additional_information key holding the amount PayPal actually captured.
     */
    public const PAYPAL_CAPTURED_AMOUNT = 'paypal_captured_amount';

    /**
     * Payment additional_information key tracking the cumulative online refunded amount.
     */
    public const PAYPAL_REFUNDED_AMOUNT = 'paypal_refunded_amount';

    // Error messages
    private const ERROR_NO_AUTHORIZATION_ID = 'No authorization ID found. Cannot capture payment.';

    /**
     * Capture PayPal payment via API
     *
     * @param  string                      $orderId PayPal order ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function captureOrder(string $orderId, Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote): void
    {
        $api = $this->getHelper()->getApi();
        $requestId = $this->getHelper()->getPaypalRequestId($quote);
        $response = $api->captureOrder($orderId, $quote, $requestId);
        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Capture order failed');
        }

        $captureId = $this->getHelper()->extractCaptureId($response->getResult());
        $this->getTransactionManager()->updatePaymentAfterCapture($quote->getPayment(), $response, $captureId);
        $quote->collectTotals()->save();
    }

    /**
     * Authorize PayPal payment via API
     *
     * @param  string                      $orderId PayPal order ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function authorizePayment(string $orderId, Mage_Sales_Model_Quote $quote): void
    {
        $api = $this->getHelper()->getApi();
        $response = $api->authorizeOrder($orderId, $quote);
        $result = $response->getResult();

        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Authorization failed');
        }

        $this->getTransactionManager()->updatePaymentAfterAuthorization($quote->getPayment(), $response);
        $quote->collectTotals()->save();

        $authorization = $result->getPurchaseUnits()[0]->getPayments()->getAuthorizations()[0];
        $this->getHelper()->addOrderComment($quote, 'authorized', $authorization->getId());
    }

    /**
     * Reauthorize a payment after the initial authorization has expired
     *
     * @param string                 $orderId PayPal order ID
     * @param Mage_Sales_Model_Order $order   Magento order
     */
    public function reauthorizePayment(string $orderId, Mage_Sales_Model_Order $order): string
    {
        $api = $this->getHelper()->getApi();
        $response = $this->_requireApiResponse(
            $api->reAuthorizeOrder($orderId, $order),
            'Reauthorization failed',
        );

        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Reauthorization failed');
        }

        $payment = $order->getPayment();
        $payment->setAdditionalInformation(
            self::PAYPAL_PAYMENT_AUTHORIZATION_REAUTHORIZED,
            true,
        );
        $payment->save();

        $order->addStatusHistoryComment(
            Mage::helper('paypal')->__(
                'PayPal payment has been reauthorized. Capture is required.',
            ),
            false,
        )->save();
        return (string) $response->getBody();
    }

    /**
     * Process refund payment method
     *
     * @param  Varien_Object               $payment Payment object
     * @param  float                       $amount  Refund amount
     * @throws Mage_Paypal_Model_Exception
     */
    public function processRefund(Varien_Object $payment, $amount): static
    {
        try {
            $amount = (float) $amount;
            $order  = $payment->getOrder();

            $this->_assertRefundable($payment, $amount);

            $response = $this->getHelper()->getApi()->refundCapturedPayment(
                $payment->getParentTransactionId(),
                $amount,
                $order->getOrderCurrencyCode(),
                $order,
            );

            if ($response->isError()) {
                throw new Mage_Paypal_Model_Exception($response->getResult()->getMessage());
            }

            $result = $response->getResult();
            $this->getTransactionManager()->updatePaymentAfterRefund($payment, $result);
            $this->getTransactionManager()->createRefundTransaction($payment, $response);

            // Track the cumulative online-refunded amount for the next refund's guard.
            $refundedToDate = (float) $payment->getAdditionalInformation(self::PAYPAL_REFUNDED_AMOUNT);
            $payment->setAdditionalInformation(self::PAYPAL_REFUNDED_AMOUNT, $refundedToDate + $amount)->save();
        } catch (Exception $exception) {
            Mage::logException($exception);
            throw new Mage_Paypal_Model_Exception(Mage::helper('paypal')->__('Refund error: %s', $exception->getMessage()), [], $exception);
        }

        return $this;
    }

    /**
     * Guard against refunding more than was captured.
     *
     * PayPal would reject an over-refund, but a local pre-check fails fast with
     * a clear message and protects against the running total drifting across
     * multiple partial refunds.
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _assertRefundable(Varien_Object $payment, float $amount): void
    {
        $helper = Mage::helper('paypal');
        if ($amount <= 0) {
            throw new Mage_Paypal_Model_Exception($helper->__('Refund amount must be greater than zero.'));
        }

        $order = $payment->getOrder();

        // The amount PayPal actually captured is the authoritative ceiling;
        // fall back to Magento's paid total only if it was never recorded.
        $capturedInfo = $payment->getAdditionalInformation(self::PAYPAL_CAPTURED_AMOUNT);
        $captured = ($capturedInfo !== null && $capturedInfo !== '')
            ? (float) $capturedInfo
            : (float) $order->getTotalPaid();

        $alreadyRefunded = (float) $payment->getAdditionalInformation(self::PAYPAL_REFUNDED_AMOUNT);
        $refundable      = round($captured - $alreadyRefunded, 4);

        if ($amount > $refundable + 0.0001) {
            throw new Mage_Paypal_Model_Exception($helper->__(
                'Refund amount (%s) exceeds the refundable amount (%s).',
                $helper->formatPrice($amount),
                $helper->formatPrice(max($refundable, 0.0)),
            ));
        }
    }

    /**
     * Process capture payment method
     *
     * @param  Varien_Object               $payment Payment object
     * @param  float                       $amount  Capture amount
     * @throws Mage_Paypal_Model_Exception
     */
    public function processCapture(Varien_Object $payment, $amount): static
    {
        $addInfo = $payment->getAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS);
        if (($addInfo['intent'] ?? null) === CheckoutPaymentIntent::CAPTURE) {
            return $this;
        }

        $order = $payment->getOrder();
        $authorizationId = $payment->getAdditionalInformation(self::PAYPAL_PAYMENT_AUTHORIZATION_ID);

        if (!$authorizationId) {
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__(self::ERROR_NO_AUTHORIZATION_ID),
            );
        }

        $api = $this->getHelper()->getApi();
        $response = $api->captureAuthorizedPayment($authorizationId, $order);

        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Capture failed');
        }

        $this->getTransactionManager()->updatePaymentAfterAuthorizedCapture($payment, $response, $authorizationId);
        $this->getTransactionManager()->createCaptureTransaction($payment, $response, $authorizationId);

        return $this;
    }

    /**
     * Process void payment method
     *
     * @param  Varien_Object               $payment Payment object
     * @throws Mage_Paypal_Model_Exception
     */
    public function processVoid(Varien_Object $payment): static
    {
        $transactionId = str_contains($payment->getTransactionId(), '-void')
            ? str_replace('-void', '', $payment->getTransactionId())
            : $payment->getTransactionId();

        try {
            $response = $this->getHelper()->getApi()->voidPayment($transactionId, $payment->getOrder());

            if ($response->isError()) {
                throw new Mage_Paypal_Model_Exception($response->getResult()->getMessage());
            }

            $this->getTransactionManager()->createVoidTransaction($payment, $response, $transactionId);
            $this->getTransactionManager()->updatePaymentAfterVoid($payment);
            $payment->getOrder()->addStatusHistoryComment(
                Mage::helper('paypal')->__('PayPal payment voided successfully. Transaction ID: %s', $transactionId),
                false,
            )->save();
        } catch (Exception $exception) {
            Mage::logException($exception);
            throw new Mage_Paypal_Model_Exception(Mage::helper('paypal')->__('Void error: %s', $exception->getMessage()), [], $exception);
        }

        return $this;
    }

    /**
     * Process cancel payment method
     *
     * @param  Varien_Object               $payment Payment object
     * @throws Mage_Paypal_Model_Exception
     */
    public function processCancel(Varien_Object $payment): static
    {
        if ($payment->getIsTransactionClosed() && $payment->getShouldCloseParentTransaction()) {
            return $this;
        }

        $this->processVoid($payment);
        return $this;
    }

    /**
     * Get PayPal Helper instance
     */
    private function getHelper(): Mage_Paypal_Model_Helper
    {
        return Mage::getSingleton('paypal/helper');
    }

    /**
     * Get PayPal Transaction Manager instance
     */
    private function getTransactionManager(): Mage_Paypal_Model_Transaction
    {
        return Mage::getSingleton('paypal/transaction');
    }

    /**
     * @throws Mage_Paypal_Model_Exception
     */
    private function _requireApiResponse(?ApiResponse $response, string $message): ApiResponse
    {
        if (!$response instanceof ApiResponse) {
            throw new Mage_Paypal_Model_Exception(Mage::helper('paypal')->__($message));
        }

        return $response;
    }
}
