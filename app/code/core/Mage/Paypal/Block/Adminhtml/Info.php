<?php

/**
 * PayPal payment information block for admin order view
 */
class Mage_Paypal_Block_Adminhtml_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/info.phtml');
    }

    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getInfo()->getLastTransId();
    }

    /**
     * Get PayPal transaction URL
     *
     * @return string|null
     */
    public function getTransactionUrl()
    {
        $transactionId = $this->getTransactionId();
        if (!$transactionId) {
            return null;
        }

        $isSandbox = Mage::getSingleton('paypal/config')->isSandbox();
        return Mage::helper('paypal')->getTransactionUrl($transactionId, $isSandbox);
    }

    /**
     * Get additional information from payment
     *
     * @return array
     */
    public function getPaymentInfo()
    {
        $payment = $this->getInfo();
        $info = [];
        if ($payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_STATUS)) {
            $info[Mage::helper('paypal')->__('Status')] = $payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_STATUS);
        }
        if ($payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_ID)) {
            $info[Mage::helper('paypal')->__('Authorization ID')] = $payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_ID);
            if ($payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME)) {
                $expirationTime = $payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);
                $info[Mage::helper('paypal')->__('Authorization Expires')] = $this->formatExpirationDate($expirationTime);
            }
        }

        return $info;
    }

    /**
     * Format expiration date to local timezone
     *
     * @param string $expirationTime
     * @return string
     */
    protected function formatExpirationDate($expirationTime)
    {
        $storeTimezone = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        $date = new DateTime($expirationTime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($storeTimezone));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Check if payment can be reauthorized
     *
     * @return bool
     */
    public function canReauthorize()
    {
        $payment = $this->getInfo();

        if ($payment->getMethod() !== 'paypal' || 
            !$payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_ID)) {
            return false;
        }
        
        $expirationTime = $payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);
        if ($expirationTime) {
            $now = new DateTime();
            $expDate = new DateTime($expirationTime);
            $daysDiff = $now->diff($expDate)->days;
            return $daysDiff <= 26;
        }
        
        $authTime = $payment->getCreatedAt();
        $now = new DateTime();
        $authDate = new DateTime($authTime);
        $daysDiff = $now->diff($authDate)->days;
        
        return $daysDiff >= 3;
    }
    
    /**
     * Get reauthorize URL
     *
     * @return string
     */
    public function getReauthorizeUrl()
    {
        return Mage::getUrl('*/paypal_transaction/reauthorize', [
            'order_id' => $this->getInfo()->getOrder()->getId(),
            '_secure' => true
        ]);
    }
}
