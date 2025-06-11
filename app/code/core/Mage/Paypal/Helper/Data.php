<?php

/**
 * PayPal module helper
 */
class Mage_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get PayPal configuration model
     *
     * @param mixed $store
     * @return Mage_Paypal_Model_Config
     */
    public function getConfig($store = null)
    {
        return Mage::getSingleton('paypal/config')->setStoreId($store);
    }

    /**
     * Check if PayPal is available
     *
     * @param mixed $store
     * @return bool
     */
    public function isAvailable($store = null)
    {
        return $this->getConfig($store)->isActive();
    }

    /**
     * Get PayPal button configuration
     *
     * @param mixed $store
     * @return array
     */
    public function getButtonConfig($store = null)
    {
        return $this->getConfig($store)->getButtonConfiguration();
    }

    /**
     * Format price for PayPal API
     *
     * @param float $amount
     * @return string
     */
    public function formatPrice($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Get transaction URL in PayPal merchant dashboard
     *
     * @param string $transactionId
     * @param bool $sandbox
     * @return string
     */
    public function getTransactionUrl($transactionId, $sandbox = false)
    {
        if (strpos($transactionId, '-') !== false) {
            $parts = explode('-', $transactionId);
            $transactionId = $parts[0];
        }

        $baseUrl = $sandbox
            ? 'https://www.sandbox.paypal.com/activity/payment/'
            : 'https://www.paypal.com/activity/payment/';
        return $baseUrl . $transactionId;
    }
}
