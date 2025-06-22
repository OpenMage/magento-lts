<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Config extends Varien_Object
{
    public const BUTTON_SHAPE_RECT = 'rect';
    public const BUTTON_SHAPE_PILL = 'pill';
    public const BUTTON_SHAPE_SHARP = 'sharp';

    public const BUTTON_COLOR_GOLD = 'gold';
    public const BUTTON_COLOR_BLUE = 'blue';
    public const BUTTON_COLOR_SILVER = 'silver';
    public const BUTTON_COLOR_WHITE = 'white';
    public const BUTTON_COLOR_BLACK = 'black';

    public const BUTTON_LAYOUT_VERTICAL = 'vertical';
    public const BUTTON_LAYOUT_HORIZONTAL = 'horizontal';

    public const BUTTON_LABEL_PAYPAL = 'paypal';
    public const BUTTON_LABEL_CHECKOUT = 'checkout';
    public const BUTTON_LABEL_BUYNOW = 'buynow';
    public const BUTTON_LABEL_PAY = 'pay';
    public const BUTTON_LABEL_INSTALLMENT = 'installment';

    /**
     * Supported currencies for PayPal transactions
     * https://developer.paypal.com/docs/reports/reference/paypal-supported-currencies/
     *
     * @var array
     */
    protected $_supportedCurrencies = [
        'AUD',
        'BRL',
        'CAD',
        'CNY',
        'CZK',
        'DKK',
        'EUR',
        'HKD',
        'HUF',
        'ILS',
        'JPY',
        'MYR',
        'MXN',
        'TWD',
        'NZD',
        'NOK',
        'PHP',
        'PLN',
        'GBP',
        'SGD',
        'SEK',
        'CHF',
        'THB',
        'USD',
    ];

    /**
     * Retrieves the API credentials, including the client ID and decrypted client secret.
     *
     * @return array<string, string>
     */
    public function getApiCredentials(): array
    {
        return [
            'client_id' => $this->getConfigData('client_id'),
            'client_secret' => Mage::helper('core')->decrypt(
                $this->getConfigData('client_secret'),
            ),
        ];
    }

    /**
     * Checks if debug mode is enabled.
     */
    public function isDebugEnabled(): bool
    {
        return (bool) $this->getConfigData('debug');
    }

    /**
     * Checks if sandbox mode is enabled.
     */
    public function isSandbox(): bool
    {
        return (bool) $this->getConfigData('sandbox_mode');
    }

    /**
     * Retrieves the configured payment action (e.g., 'authorize', 'capture').
     */
    public function getPaymentAction(): string
    {
        return (string) $this->getConfigData('payment_action');
    }

    /**
     * Retrieves the PayPal API endpoint URL based on the sandbox mode.
     */
    public function getEndpoint(): string
    {
        return $this->isSandbox() ? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com';
    }

    /**
     * Retrieves an array of configuration settings for the PayPal button.
     *
     * @return array<string, string|bool>
     */
    public function getButtonConfiguration(): array
    {
        return [
            'shape' => (string) $this->getConfigData('button_shape'),
            'color' => (string) $this->getConfigData('button_color'),
            'layout' => (string) $this->getConfigData('button_layout'),
            'label' => (string) $this->getConfigData('button_label'),
            'message' => (bool) $this->getConfigData('button_message'),
        ];
    }


    /**
     * Checks if the PayPal payment method is active for the given store.
     *
     * @param mixed|null $store The store ID or object to check for.
     */
    public function isActive(mixed $store = null): bool
    {
        return (bool) $this->getConfigData('active', $store);
    }

    /**
     * Retrieves a list of supported currency codes.
     *
     * @return string[]
     */
    public function getAllowedCurrencyCodes(): array
    {
        return $this->_supportedCurrencies;
    }

    /**
     * Retrieves a specific configuration value from the store config.
     *
     * @param string $field The configuration field to retrieve.
     * @param mixed|null $store The store ID or object.
     */
    protected function getConfigData(string $field, mixed $store = null): mixed
    {
        return Mage::getStoreConfig('payment/paypal/' . $field, $store);
    }
}
