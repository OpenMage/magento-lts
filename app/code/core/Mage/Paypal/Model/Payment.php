<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Http\ApiResponse;
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
    public const PAYPAL_PAYMENT_STATUS = 'paypal_payment_status';
    public const PAYPAL_PAYMENT_AUTHORIZATION_ID = 'paypal_payment_authorization_id';
    public const PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME = 'paypal_payment_authorization_expires_time';
    public const PAYPAL_PAYMENT_AUTHORIZATION_REAUTHROIZED = 'paypal_payment_authorization_reauthorized';


    // Error messages
    private const ERROR_NO_AUTHORIZATION_ID = 'No authorization ID found. Cannot capture payment.';

    /**
     * Capture PayPal payment via API
     *
     * @param string $orderId PayPal order ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function captureOrder(string $orderId, Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quote): void
    {
        $api = $this->getHelper()->getApi();
        $response = $api->captureOrder($orderId, $quote);
        $result = $response->getResult();

        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Capture failed');
        }
        $captureId = $this->getHelper()->extractCaptureId($result);
        $this->getTransactionManager()->updatePaymentAfterCapture($quote->getPayment(), $response, $captureId);
        $quote->collectTotals()->save();
    }

    /**
     * Authorize PayPal payment via API
     *
     * @param string $orderId PayPal order ID
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
     * @param string $orderId PayPal order ID
     * @param Mage_Sales_Model_Order $order Magento order
     */
    public function reauthorizePayment(string $orderId, Mage_Sales_Model_Order $order): string
    {
        $api = $this->getHelper()->getApi();
        $response = $api->reAuthorizeOrder($orderId, $order);

        if ($response->isError()) {
            $this->getHelper()->handleApiError($response, 'Reauthorization failed');
        }

        $payment = $order->getPayment();
        $payment->setAdditionalInformation(
            self::PAYPAL_PAYMENT_AUTHORIZATION_REAUTHROIZED,
            true,
        );
        $payment->save();

        $order->addStatusHistoryComment(
            Mage::helper('paypal')->__(
                'PayPal payment has been reauthorized. Capture is required.',
            ),
            false,
        )->save();
        return $response->getResult()->getBody();
    }

    /**
     * Process refund payment method
     *
     * @param Varien_Object $payment Payment object
     * @param float $amount Refund amount
     * @throws Mage_Paypal_Model_Exception
     */
    public function processRefund(Varien_Object $payment, $amount): static
    {
        try {
            if (is_string($amount)) {
                $amount = (float) $amount;
            }
            $response = $this->getHelper()->getApi()->refundCapturedPayment(
                $payment->getParentTransactionId(),
                $amount,
                $payment->getOrder()->getOrderCurrencyCode(),
                $payment->getOrder(),
            );

            if ($response->isError()) {
                throw new Mage_Paypal_Model_Exception($response->getResult()->getMessage());
            }

            $result = $response->getResult();
            $this->getTransactionManager()->updatePaymentAfterRefund($payment, $result);
            $this->getTransactionManager()->createRefundTransaction($payment, $response);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__('Refund error: %s', $e->getMessage()),
            );
        }

        return $this;
    }

    /**
     * Process capture payment method
     *
     * @param Varien_Object $payment Payment object
     * @param float $amount Capture amount
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
     * @param Varien_Object $payment Payment object
     * @throws Mage_Paypal_Model_Exception
     */
    public function processVoid(Varien_Object $payment): static
    {
        // PayPal will handle void transaction
        $transactionId = str_contains($payment->getTransactionId(), '-void')
            ? str_replace('-void', '', $payment->getTransactionId())
            : $payment->getTransactionId();

        try {
            $response = $this->getHelper()->getApi()->voidPayment($transactionId, $payment->getOrder());

            if ($response->isError()) {
                throw new Mage_Paypal_Model_Exception($response->getResult()->getMessage());
            }

            $this->getTransactionManager()->updatePaymentAfterVoid($payment);
            $this->getTransactionManager()->createVoidTransaction($payment, $response);
            $payment->getOrder()->addStatusHistoryComment(
                Mage::helper('paypal')->__('PayPal payment voided successfully. Transaction ID: %s', $transactionId),
                false,
            )->save();
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__('Void error: %s', $e->getMessage()),
            );
        }

        return $this;
    }

    /**
     * Process cancel payment method
     *
     * @param Varien_Object $payment Payment object
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
}
