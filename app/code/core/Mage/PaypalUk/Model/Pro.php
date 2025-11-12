<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * PayPal Website Payments Pro (Payflow Edition) implementation for payment method instances
 * This model was created because right now PayPal Direct and PayPal Express payment
 * (Payflow Edition) methods cannot have same abstract
 *
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Pro extends Mage_Paypal_Model_Pro
{
    /**
     * Api model type
     *
     * @var string
     */
    protected $_apiType = 'paypaluk/api_nvp';

    /**
     * Config model type
     *
     * @var string
     */
    protected $_configType = 'paypal/config';

    /**
     * Payflow trx_id key in transaction info
     *
     * @var string
     */
    public const TRANSPORT_PAYFLOW_TXN_ID = 'payflow_trxid';

    /**
     * Refund a capture transaction
     *
     * @param float $amount
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($captureTxnId = $this->_getParentTransactionId($payment)) {
            $api = $this->getApi();
            $api->setAuthorizationId($captureTxnId);
        }

        parent::refund($payment, $amount);
    }

    /**
     * Is capture request needed on this transaction
     *
     * @return true
     */
    protected function _isCaptureNeeded()
    {
        return true;
    }

    /**
     * Get payflow transaction id from parent transaction
     *
     * @return string
     */
    protected function _getParentTransactionId(Varien_Object $payment)
    {
        if ($payment->getParentTransactionId()) {
            return $payment->getTransaction($payment->getParentTransactionId())
                ->getAdditionalInformation(self::TRANSPORT_PAYFLOW_TXN_ID);
        }

        return $payment->getParentTransactionId();
    }

    /**
     * Import capture results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp $api
     * @param Mage_Sales_Model_Order_Payment $payment
     */
    protected function _importCaptureResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(false)
            ->setTransactionAdditionalInfo(
                self::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId(),
            );
        $payment->setPreparedMessage(
            Mage::helper('paypaluk')->__('Payflow PNREF: #%s.', $api->getTransactionId()),
        );
        Mage::getModel('paypal/info')->importToPayment($api, $payment);
    }

    /**
     * Fetch transaction details info method does not exists in PaypalUK
     *
     * @param string $transactionId
     * @return void
     * @throws Mage_Core_Exception
     */
    public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId)
    {
        Mage::throwException(
            Mage::helper('paypaluk')->__('Fetch transaction details method does not exists in PaypalUK'),
        );
    }

    /**
     * Import refund results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp $api
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param bool $canRefundMore
     */
    protected function _importRefundResultToPayment($api, $payment, $canRefundMore)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(1) // refund initiated by merchant
            ->setShouldCloseParentTransaction(!$canRefundMore)
            ->setTransactionAdditionalInfo(
                self::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId(),
            );
        $payment->setPreparedMessage(
            Mage::helper('paypaluk')->__('Payflow PNREF: #%s.', $api->getTransactionId()),
        );
        Mage::getModel('paypal/info')->importToPayment($api, $payment);
    }
}
