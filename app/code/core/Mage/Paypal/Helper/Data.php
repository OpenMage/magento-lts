<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal module helper
 */
class Mage_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieves the PayPal configuration model, optionally for a specific store.
     *
     * @param mixed $store The store ID or object.
     */
    public function getConfig(mixed $store = null): Mage_Paypal_Model_Config
    {
        return Mage::getSingleton('paypal/config')->setStoreId($store);
    }

    /**
     * Checks if the PayPal payment method is active and available, optionally for a specific store.
     *
     * @param mixed $store The store ID or object.
     */
    public function isAvailable(mixed $store = null): bool
    {
        return $this->getConfig($store)->isActive();
    }

    /**
     * Retrieves the configuration settings for the PayPal button, optionally for a specific store.
     *
     * @param mixed $store The store ID or object.
     */
    public function getButtonConfig(mixed $store = null): array
    {
        return $this->getConfig($store)->getButtonConfiguration();
    }

    /**
     * Formats a numeric price into a string with two decimal places, suitable for the PayPal API.
     *
     * @param float $amount The price amount to format.
     */
    public function formatPrice(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Constructs the URL to view a specific transaction in the PayPal merchant dashboard.
     *
     * @param string $transactionId The transaction ID.
     * @param bool $sandbox Whether to use the sandbox environment.
     */
    public function getTransactionUrl(string $transactionId, bool $sandbox = false): string
    {
        if (str_contains($transactionId, '-')) {
            $parts = explode('-', $transactionId);
            $transactionId = $parts[0];
        }

        $baseUrl = $sandbox
            ? 'https://www.sandbox.paypal.com/activity/payment/'
            : 'https://www.paypal.com/activity/payment/';
        return $baseUrl . $transactionId;
    }
}
