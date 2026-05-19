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

    protected $_cachedCredentials = null;

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
        if ($this->_cachedCredentials === null) {
            $this->_cachedCredentials = [
                'client_id' => $this->getConfigData('client_id'),
                'client_secret' => Mage::helper('core')->decrypt($this->getConfigData('client_secret')),
            ];
        }

        return $this->_cachedCredentials;
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
     * @return array<string, bool|string>
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
     * Retrieves the SDK API timeout in seconds.
     */
    public function getApiTimeout(): int
    {
        return max(0, (int) $this->getConfigData('api_timeout'));
    }

    /**
     * Retrieves the SDK retry configuration.
     *
     * @return array{
     *     enabled: bool,
     *     number_of_retries: int,
     *     retry_interval: float,
     *     backoff_factor: float,
     *     maximum_retry_wait_time: int,
     *     retry_on_timeout: bool,
     *     http_status_codes: int[],
     *     http_methods: string[]
     * }
     */
    public function getRetryConfiguration(): array
    {
        return [
            'enabled' => (bool) $this->getConfigData('retry_enabled'),
            'number_of_retries' => max(0, (int) $this->getConfigData('retry_count')),
            'retry_interval' => max(0.0, (float) $this->getConfigData('retry_interval')),
            'backoff_factor' => max(0.0, (float) $this->getConfigData('retry_backoff_factor')),
            'maximum_retry_wait_time' => max(0, (int) $this->getConfigData('retry_max_wait_time')),
            'retry_on_timeout' => (bool) $this->getConfigData('retry_on_timeout'),
            'http_status_codes' => $this->parseRetryStatusCodes($this->getConfigData('retry_status_codes')),
            'http_methods' => $this->parseRetryHttpMethods($this->getConfigData('retry_http_methods')),
        ];
    }

    /**
     * Checks if SDK HTTP debug logging is enabled.
     */
    public function isSdkHttpDebugEnabled(): bool
    {
        return (bool) $this->getConfigData('sdk_http_debug');
    }

    /**
     * Checks if PayPal webhook handling is enabled.
     */
    public function isWebhookEnabled(): bool
    {
        return (bool) $this->getConfigData('webhook_enabled');
    }

    /**
     * Retrieves the PayPal REST webhook ID.
     */
    public function getWebhookId(): string
    {
        return trim((string) $this->getConfigData('webhook_id'));
    }

    /**
     * Checks if webhook business effects should be processed from cron.
     */
    public function shouldProcessWebhooksAsync(): bool
    {
        return (bool) $this->getConfigData('webhook_process_async');
    }

    /**
     * Retrieves the webhook processing retry limit.
     */
    public function getWebhookRetryLimit(): int
    {
        return max(0, (int) $this->getConfigData('webhook_retry_limit'));
    }

    /**
     * Retrieves the processed webhook retention period in days.
     */
    public function getWebhookRetentionDays(): int
    {
        return max(1, (int) $this->getConfigData('webhook_retention_days'));
    }

    /**
     * Checks if the PayPal payment method is active for the given store.
     *
     * @param null|mixed $store the store ID or object to check for
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
     * @param string     $field the configuration field to retrieve
     * @param null|mixed $store the store ID or object
     */
    protected function getConfigData(string $field, mixed $store = null): mixed
    {
        return Mage::getStoreConfig('payment/paypal/' . $field, $store);
    }

    /**
     * @return int[]
     */
    private function parseRetryStatusCodes(mixed $value): array
    {
        $statusCodes = [];

        foreach (explode(',', (string) $value) as $rawStatusCode) {
            $rawStatusCode = trim($rawStatusCode);
            if ($rawStatusCode === '') {
                continue;
            }

            if (!ctype_digit($rawStatusCode)) {
                continue;
            }

            $statusCode = (int) $rawStatusCode;
            if ($statusCode < 100) {
                continue;
            }

            if ($statusCode > 599) {
                continue;
            }

            if (in_array($statusCode, $statusCodes, true)) {
                continue;
            }

            $statusCodes[] = $statusCode;
        }

        return $statusCodes;
    }

    /**
     * @return string[]
     */
    private function parseRetryHttpMethods(mixed $value): array
    {
        $methods = [];

        foreach (explode(',', (string) $value) as $rawMethod) {
            $method = strtoupper(trim($rawMethod));
            if ($method === '') {
                continue;
            }

            if (!in_array($method, Mage_Paypal_Model_System_Config_Source_RetryHttpMethods::METHODS, true)) {
                continue;
            }

            if (in_array($method, $methods, true)) {
                continue;
            }

            $methods[] = $method;
        }

        return $methods;
    }
}
