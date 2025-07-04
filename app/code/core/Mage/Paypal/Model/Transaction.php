<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\{
    Refund,
    PaypalWalletResponse
};

/**
 * PayPal Transaction Management Handler
 * Handles transaction creation and payment updates
 */
class Mage_Paypal_Model_Transaction extends Mage_Core_Model_Abstract
{
    // Payment information transport keys
    public const PAYPAL_PAYMENT_STATUS = 'paypal_payment_status';
    public const PAYPAL_PAYMENT_AUTHORIZATION_ID = 'paypal_payment_authorization_id';
    public const PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME = 'paypal_payment_authorization_expires_time';

    /**
     * Create refund transaction record
     *
     * @param Varien_Object $payment Payment object
     * @param ApiResponse $response API result
     */
    public function createRefundTransaction(Varien_Object $payment, ApiResponse $response): void
    {
        $result = $response->getResult();
        $transaction = $this->getTransaction();
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
                $this->getHelper()->prepareRawDetails($response->getBody()),
            );

        $transaction->save();
    }

    /**
     * Create capture transaction record
     *
     * @param Varien_Object $payment Payment object
     * @param ApiResponse $response API response
     * @param string $authorizationId Authorization ID
     */
    public function createCaptureTransaction(Varien_Object $payment, ApiResponse $response, string $authorizationId): void
    {
        $result = $response->getResult();
        $transaction = $this->getTransaction();
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
                $this->getHelper()->prepareRawDetails($response->getBody()),
            );

        $transaction->save();
    }

    /**
     * Create void transaction record
     *
     * @param Varien_Object $payment Payment object
     * @param ApiResponse $response API response
     */
    public function createVoidTransaction(Varien_Object $payment, ApiResponse $response): void
    {
        $transaction = $this->getTransaction();
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
                $this->getHelper()->prepareRawDetails($response->getBody()),
            );
        $transaction->save();

        $parentTxn = $transaction->loadByTxnId($result->getId());
        if ($parentTxn->getId()) {
            $parentTxn->setIsClosed(1);
            $parentTxn->save();
        }
    }

    /**
     * Update payment object after successful capture
     *
     * @param ApiResponse $response API result
     * @param string $captureId Capture ID
     */
    public function updatePaymentAfterCapture(
        Mage_Sales_Model_Quote_Payment|Mage_Sales_Model_Order_Payment $payment,
        ApiResponse $response,
        string $captureId
    ): void {
        $result = $response->getResult();
        $payment->setMethod('paypal')
            ->setPaypalCorrelationId($captureId)
            ->setTransactionId($captureId)
            ->setIsTransactionClosed(true)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->getHelper()->prepareRawDetails($response->getBody()),
            );
        $paymentSource = $result->getPaymentSource();
        if (isset($paymentSource) && $paymentSource->getPaypal() instanceof PaypalWalletResponse) {
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
    public function updatePaymentAfterAuthorization(Mage_Sales_Model_Quote_Payment $payment, ApiResponse $response): void
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
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->getHelper()->prepareRawDetails($response->getBody()),
        ]);
        $payment->save();
    }

    /**
     * Update payment after refund
     *
     * @param Varien_Object $payment Payment object
     * @param Refund $result API result
     */
    public function updatePaymentAfterRefund(Varien_Object $payment, Refund $result): void
    {
        $payment->setTransactionId($result->getId())
            ->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1)
            ->setSkipTransactionCreation(true);
    }

    /**
     * Update payment after authorized capture
     *
     * @param Varien_Object $payment Payment object
     * @param ApiResponse $response API response
     * @param string $authorizationId Authorization ID
     */
    public function updatePaymentAfterAuthorizedCapture(Varien_Object $payment, ApiResponse $response, string $authorizationId): void
    {
        /**
         * @var Mage_Sales_Model_Order_Payment $payment
         */
        $result = $response->getResult();
        $captureId = $result->getId();
        $additionalInfo = $payment->getAdditionalInformation();
        unset($additionalInfo[self::PAYPAL_PAYMENT_STATUS]);
        unset($additionalInfo[self::PAYPAL_PAYMENT_AUTHORIZATION_ID]);
        unset($additionalInfo[self::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME]);
        $payment->setAdditionalInformation($additionalInfo);
        $payment->setTransactionId($captureId)
            ->setParentTransactionId($authorizationId)
            ->setIsTransactionClosed(true)
            ->setShouldCloseParentTransaction(true)
            ->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                $this->getHelper()->prepareRawDetails($response->getBody()),
            );
    }

    /**
     * Update payment after void
     *
     * @param Varien_Object $payment Payment object
     */
    public function updatePaymentAfterVoid(Varien_Object $payment): void
    {
        $additionalInfo = $payment->getAdditionalInformation();
        unset($additionalInfo[self::PAYPAL_PAYMENT_STATUS]);
        unset($additionalInfo[self::PAYPAL_PAYMENT_AUTHORIZATION_ID]);
        unset($additionalInfo[self::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME]);
        $payment->setAdditionalInformation($additionalInfo);
        $payment->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1)
            ->setSkipTransactionCreation(true);
    }

    /**
     * Update expired transaction record
     *
     * @param Varien_Object $payment Payment object
     */
    public function updateExpiredTransaction(Varien_Object $payment): void
    {
        $transaction = $this->getTransaction();
        $transactionId = $payment->getLastTransId() . '-expired';

        /**
         * @var Mage_Sales_Model_Order_Payment $payment
         */
        $transaction->setOrderPaymentObject($payment)
            ->setTxnId($transactionId)
            ->setParentTxnId($payment->getTransactionId())
            ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID)
            ->setIsClosed(1);
        $transaction->save();
        $payment->setLastTransId($transactionId)
            ->setAdditionalInformation(self::PAYPAL_PAYMENT_STATUS, 'EXPIRED')
            ->save();

        $parentTxn = $transaction->loadByTxnId($payment->getLastTransId());
        if ($parentTxn->getId()) {
            $parentTxn->setIsClosed(1);
            $parentTxn->save();
        }
    }

    /**
     * Get order payment transaction model
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function getTransaction(): Mage_Sales_Model_Order_Payment_Transaction
    {
        return Mage::getModel('sales/order_payment_transaction');
    }

    /**
     * Get PayPal Helper instance
     */
    private function getHelper(): Mage_Paypal_Model_Helper
    {
        return Mage::getSingleton('paypal/helper');
    }
}
