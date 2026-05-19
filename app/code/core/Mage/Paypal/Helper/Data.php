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
     * Currencies PayPal does not accept decimal amounts for.
     *
     * @var string[]
     */
    private const ZERO_DECIMAL_CURRENCIES = ['HUF', 'JPY', 'TWD'];

    /**
     * Retrieves the PayPal configuration model, optionally for a specific store.
     *
     * @param mixed $store the store ID or object
     */
    public function getConfig(mixed $store = null): Mage_Paypal_Model_Config
    {
        return Mage::getSingleton('paypal/config')->setStoreId($store);
    }

    /**
     * Checks if the PayPal payment method is active and available, optionally for a specific store.
     *
     * @param mixed $store the store ID or object
     */
    public function isAvailable(mixed $store = null): bool
    {
        return $this->getConfig($store)->isActive();
    }

    /**
     * Retrieves the configuration settings for the PayPal button, optionally for a specific store.
     *
     * @param  mixed                      $store the store ID or object
     * @return array<string, bool|string>
     */
    public function getButtonConfig(mixed $store = null): array
    {
        return $this->getConfig($store)->getButtonConfiguration();
    }

    /**
     * Checks whether the product-page PayPal shortcut may render.
     */
    public function isShortcutVisibleOnProduct(mixed $store = null): bool
    {
        return Mage::getStoreConfigFlag('payment/paypal/visible_on_product', $store)
            && $this->isPaymentMethodAvailable($store);
    }

    /**
     * Checks whether the cart-page PayPal shortcut may render.
     */
    public function isShortcutVisibleOnCart(mixed $store = null): bool
    {
        return Mage::getStoreConfigFlag('payment/paypal/visible_on_cart', $store)
            && $this->isPaymentMethodAvailable($store);
    }

    /**
     * Checks PayPal method availability against the current checkout quote.
     */
    private function isPaymentMethodAvailable(mixed $store = null): bool
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($store !== null) {
            $storeModel = Mage::app()->getStore($store);
            if ($storeModel !== null) {
                $quote->setStore($storeModel);
            }
        }

        return Mage::getModel('paypal/paypal')->isAvailable($quote);
    }

    /**
     * Returns the number of decimal places PayPal expects for the given currency.
     *
     * PayPal rejects amounts with decimals for HUF, JPY and TWD.
     */
    public function getCurrencyDecimals(string $currency): int
    {
        return in_array(strtoupper($currency), self::ZERO_DECIMAL_CURRENCIES, true) ? 0 : 2;
    }

    /**
     * Formats a numeric price into a string suitable for the PayPal API.
     *
     * When a currency code is supplied the precision matches what PayPal
     * expects for that currency; otherwise two decimal places are used.
     *
     * @param float  $amount   the price amount to format
     * @param string $currency optional ISO currency code controlling precision
     */
    public function formatPrice(float $amount, string $currency = ''): string
    {
        $decimals = $currency === '' ? 2 : $this->getCurrencyDecimals($currency);
        return number_format($amount, $decimals, '.', '');
    }

    /**
     * Constructs the URL to view a specific transaction in the PayPal merchant dashboard.
     *
     * @param string $transactionId the transaction ID
     * @param bool   $sandbox       whether to use the sandbox environment
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
