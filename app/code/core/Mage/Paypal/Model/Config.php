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
    public const BUTTON_LABEL_PAYPAL = 'paypal';

    public const BUTTON_LABEL_CHECKOUT = 'checkout';

    public const BUTTON_LABEL_BUYNOW = 'buynow';

    public const BUTTON_LABEL_PAY = 'pay';

    public const PAGE_TYPE_CHECKOUT = 'checkout';

    public const PAGE_TYPE_CART = 'cart';

    public const PAGE_TYPE_PRODUCT = 'product-details';

    /**
     * API credentials cache, keyed by store id so website-scoped
     * credentials are not leaked across stores.
     *
     * @var array<string, array<string, string>>
     */
    protected $_cachedCredentials = [];

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
        $storeId = $this->getStoreId();
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }

        $storeKey = (string) $storeId;
        $this->_cachedCredentials[$storeKey] ??= [
            'client_id' => (string) $this->getConfigData('client_id'),
            'client_secret' => (string) Mage::helper('core')->decrypt($this->getConfigData('client_secret')),
        ];

        return $this->_cachedCredentials[$storeKey];
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
     * URL of the PayPal JavaScript SDK v6 core script.
     */
    public function getSdkUrl(): string
    {
        return rtrim($this->getEndpoint(), '/') . '/web-sdk/v6/core' . ($this->isDebugEnabled() ? '?debug=true' : '');
    }

    /**
     * The SDK button type for the configured label; empty for the plain PayPal logo.
     */
    public function getButtonType(): string
    {
        $label = (string) $this->getConfigData('button_label');

        return in_array($label, [self::BUTTON_LABEL_CHECKOUT, self::BUTTON_LABEL_BUYNOW, self::BUTTON_LABEL_PAY], true)
            ? $label
            : '';
    }

    /**
     * Settings the storefront passes to PayPalSdk.renderButtons().
     *
     * @return array{sdkUrl: string, clientId: string, pageType: string, locale: string, currencyCode: string,
     *               amount: string, buttonType: string, payLaterEnabled: bool, creditEnabled: bool}
     */
    public function getSdkConfig(string $pageType, string $currencyCode, ?float $amount = null, ?string $buttonType = null): array
    {
        return [
            'sdkUrl' => $this->getSdkUrl(),
            'clientId' => $this->getApiCredentials()['client_id'],
            'pageType' => $pageType,
            'locale' => str_replace('_', '-', Mage::app()->getLocale()->getLocaleCode()),
            'currencyCode' => $currencyCode,
            'amount' => $amount !== null && $amount > 0 ? number_format($amount, 2, '.', '') : '',
            'buttonType' => $buttonType ?? $this->getButtonType(),
            'payLaterEnabled' => (bool) $this->getConfigData('show_paylater_button'),
            'creditEnabled' => (bool) $this->getConfigData('show_credit_button'),
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
        return Mage::getStoreConfig('payment/paypal/' . $field, $store ?? $this->getStoreId());
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
