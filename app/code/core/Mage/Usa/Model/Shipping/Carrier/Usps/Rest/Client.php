<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Client
 *
 * Handles all HTTP communication with the USPS REST API.
 * Provides methods for rate quotes, tracking, labels, and authentication.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
{
    /**
     * Production API base URL
     */
    public const URL_PRODUCTION = 'https://apis.usps.com/';

    /**
     * Sandbox/Test API base URL
     */
    public const URL_SANDBOX = 'https://apis-tem.usps.com/';

    /**
     * Default request timeout in seconds
     */
    public const DEFAULT_TIMEOUT = 30;

    /**
     * Base URL for API requests
     */
    protected string $_baseUrl;

    /**
     * OAuth access token
     */
    protected ?string $_accessToken = null;

    /**
     * Debug mode flag
     */
    protected bool $_debug = false;

    /**
     * Debug log array
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $_debugLog = [];

    /**
     * Constructor
     *
     * @param string $baseUrl Base API URL (production or sandbox)
     * @param bool   $debug   Enable debug logging
     */
    public function __construct(?string $baseUrl = null, bool $debug = false)
    {
        $this->_baseUrl = $baseUrl ?: self::URL_PRODUCTION;
        $this->_debug = $debug;
    }

    /**
     * Set the OAuth access token for authenticated requests
     *
     * @param string $token Access token
     */
    public function setAccessToken(string $token): self
    {
        $this->_accessToken = $token;
        return $this;
    }

    /**
     * Get the current access token
     */
    public function getAccessToken(): ?string
    {
        return $this->_accessToken;
    }

    /**
     * Set base URL
     *
     * @param string $url Base API URL
     */
    public function setBaseUrl(string $url): self
    {
        $this->_baseUrl = rtrim($url, '/') . '/';
        return $this;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl(): string
    {
        return $this->_baseUrl;
    }

    /**
     * Get rate quotes for a package
     *
     * @param  array<string, mixed> $request Rate request parameters
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function getRates(array $request): array
    {
        $endpoint = 'prices/v3/total-rates/search';
        return $this->_post($endpoint, $request, true);
    }

    /**
     * Get tracking information for a tracking number
     *
     * @param  string               $trackingNumber  Tracking number
     * @param  bool                 $expandedDetails Include expanded tracking details
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function getTracking(string $trackingNumber, bool $expandedDetails = true): array
    {
        $endpoint = 'tracking/v3/tracking/' . urlencode($trackingNumber);
        $query = $expandedDetails ? '?expand=DETAIL' : '';
        return $this->_get($endpoint . $query, true);
    }

    /**
     * Create a domestic shipping label
     *
     * @param  array<string, mixed> $labelRequest Label request parameters
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function createDomesticLabel(array $labelRequest): array
    {
        $endpoint = 'labels/v3/label';
        return $this->_post($endpoint, $labelRequest, true);
    }

    /**
     * Create an international shipping label
     *
     * @param  array<string, mixed> $labelRequest Label request parameters
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function createInternationalLabel(array $labelRequest): array
    {
        $endpoint = 'international-labels/v3/label';
        return $this->_post($endpoint, $labelRequest, true);
    }

    /**
     * Cancel a shipping label
     *
     * @param  string               $trackingNumber Tracking number of label to cancel
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function cancelLabel(string $trackingNumber): array
    {
        $endpoint = 'labels/v3/label/' . urlencode($trackingNumber);
        return $this->_delete($endpoint, true);
    }

    /**
     * Get payment authorization token for label creation
     *
     * @param  array<string, mixed> $payload Payment authorization request
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function getPaymentAuthorization(array $payload): array
    {
        $endpoint = 'payments/v3/payment-authorization';
        return $this->_post($endpoint, $payload, true);
    }

    /**
     * Verify an address using USPS Address API
     *
     * Note: USPS Address API uses GET with query parameters, not POST.
     *
     * @param  array<string, mixed> $address Address to verify with keys: streetAddress, secondaryAddress, city, state, ZIPCode, ZIPPlus4
     * @return array<string, mixed> Response array with 'success', 'data', 'error'
     */
    public function verifyAddress(array $address): array
    {
        $queryParams = http_build_query(array_filter([
            'streetAddress' => $address['streetAddress'] ?? '',
            'secondaryAddress' => $address['secondaryAddress'] ?? '',
            'city' => $address['city'] ?? '',
            'state' => $address['state'] ?? '',
            'ZIPCode' => $address['ZIPCode'] ?? '',
            'ZIPPlus4' => $address['ZIPPlus4'] ?? '',
        ]));
        $endpoint = 'addresses/v3/address?' . $queryParams;
        return $this->_get($endpoint, true);
    }

    /**
     * Perform HTTP GET request
     *
     * @param  string               $endpoint      API endpoint
     * @param  bool                 $authenticated Include auth header
     * @return array<string, mixed>
     */
    protected function _get(string $endpoint, bool $authenticated = true): array
    {
        return $this->_request('GET', $endpoint, null, $authenticated);
    }

    /**
     * Perform HTTP POST request
     *
     * @param  string               $endpoint      API endpoint
     * @param  array<string, mixed> $data          Request body data
     * @param  bool                 $authenticated Include auth header
     * @return array<string, mixed>
     */
    protected function _post(string $endpoint, array $data, bool $authenticated = true): array
    {
        return $this->_request('POST', $endpoint, $data, $authenticated);
    }

    /**
     * Perform HTTP DELETE request
     *
     * @param  string               $endpoint      API endpoint
     * @param  bool                 $authenticated Include auth header
     * @return array<string, mixed>
     */
    protected function _delete(string $endpoint, bool $authenticated = true): array
    {
        return $this->_request('DELETE', $endpoint, null, $authenticated);
    }

    /**
     * Perform HTTP request
     *
     * @param  string                    $method        HTTP method (GET, POST, DELETE)
     * @param  string                    $endpoint      API endpoint
     * @param  null|array<string, mixed> $data          Request body data
     * @param  bool                      $authenticated Include auth header
     * @return array<string, mixed>      Response array with 'success', 'data', 'http_code', 'error'
     */
    protected function _request(string $method, string $endpoint, ?array $data = null, bool $authenticated = true): array
    {
        $url = $this->_baseUrl . ltrim($endpoint, '/');

        $headers = ['Content-Type: application/json'];

        if ($authenticated && $this->_accessToken) {
            $headers[] = 'Authorization: Bearer ' . $this->_accessToken;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data) ?: '');
                }

                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'GET':
            default:
                // Default is GET
                break;
        }

        $this->_log('request', [
            'method' => $method,
            'url' => $url,
            'data' => $data,
        ]);

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            $this->_log('error', ['curl_error' => $curlError]);
            return [
                'success' => false,
                'http_code' => 0,
                'error' => 'cURL error: ' . $curlError,
                'data' => null,
            ];
        }

        $responseData = json_decode((string) $responseBody, true);

        $this->_log('response', [
            'http_code' => $httpCode,
            'data' => $responseData,
        ]);

        $isSuccess = $httpCode >= 200 && $httpCode < 300;

        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'data' => $responseData,
            'error' => $isSuccess ? null : $this->_extractError($responseData, $httpCode),
            'raw' => $responseBody,
        ];
    }

    /**
     * Extract error message from response
     *
     * @param  null|array<string, mixed> $responseData Decoded response
     * @param  int                       $httpCode     HTTP status code
     * @return string                    Error message
     */
    protected function _extractError(?array $responseData, int $httpCode): string
    {
        if (is_array($responseData)) {
            // Check various error formats
            if (isset($responseData['error']['message'])) {
                return $responseData['error']['message'];
            }

            if (isset($responseData['error_description'])) {
                return $responseData['error_description'];
            }

            if (isset($responseData['errors'][0]['message'])) {
                return $responseData['errors'][0]['message'];
            }

            if (isset($responseData['message'])) {
                return $responseData['message'];
            }
        }

        return 'HTTP ' . $httpCode . ' error';
    }

    /**
     * Log debug information
     *
     * @param string               $type Log entry type
     * @param array<string, mixed> $data Log data
     */
    protected function _log(string $type, array $data): void
    {
        if (!$this->_debug) {
            return;
        }

        $this->_debugLog[] = [
            'type' => $type,
            'time' => microtime(true),
            'data' => $data,
        ];
    }

    /**
     * Get debug log
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDebugLog(): array
    {
        return $this->_debugLog;
    }

    /**
     * Clear debug log
     */
    public function clearDebugLog(): self
    {
        $this->_debugLog = [];
        return $this;
    }

    /**
     * Check if response indicates a transient error that can be retried
     *
     * @param int $httpCode HTTP status code
     */
    public function isTransientError(int $httpCode): bool
    {
        return in_array($httpCode, [429, 500, 502, 503, 504], true);
    }

    /**
     * Perform HTTP request with automatic retry for transient errors
     *
     * Uses exponential backoff: 200ms, 400ms, 800ms delays between retries.
     *
     * @param  string                    $method        HTTP method (GET, POST, DELETE)
     * @param  string                    $endpoint      API endpoint
     * @param  null|array<string, mixed> $data          Request body data
     * @param  bool                      $authenticated Include auth header
     * @param  int                       $maxRetries    Maximum retry attempts (default 3)
     * @return array<string, mixed>      Response array with 'success', 'data', 'http_code', 'error'
     */
    public function requestWithRetry(string $method, string $endpoint, ?array $data = null, bool $authenticated = true, int $maxRetries = 3): array
    {
        $maxRetries = max(1, $maxRetries);
        $lastResponse = ['success' => false, 'http_code' => 0, 'error' => 'Max retries exceeded', 'data' => null];

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $response = $this->_request($method, $endpoint, $data, $authenticated);

            if ($response['success'] || !$this->isTransientError($response['http_code'])) {
                return $response;
            }

            $lastResponse = $response;

            // Log retry attempt
            $this->_log('retry', [
                'attempt' => $attempt,
                'max_retries' => $maxRetries,
                'http_code' => $response['http_code'],
                'error' => $response['error'],
            ]);

            // Don't sleep after the last attempt
            if ($attempt < $maxRetries) {
                // Exponential backoff: 200ms, 400ms, 800ms
                usleep(2 ** $attempt * 100000);
            }
        }

        return $lastResponse;
    }

    /**
     * Perform GET request with retry
     *
     * @param  string               $endpoint      API endpoint
     * @param  bool                 $authenticated Include auth header
     * @param  int                  $maxRetries    Maximum retry attempts
     * @return array<string, mixed>
     */
    public function getWithRetry(string $endpoint, bool $authenticated = true, int $maxRetries = 3): array
    {
        return $this->requestWithRetry('GET', $endpoint, null, $authenticated, $maxRetries);
    }

    /**
     * Perform POST request with retry
     *
     * @param  string               $endpoint      API endpoint
     * @param  array<string, mixed> $data          Request body data
     * @param  bool                 $authenticated Include auth header
     * @param  int                  $maxRetries    Maximum retry attempts
     * @return array<string, mixed>
     */
    public function postWithRetry(string $endpoint, array $data, bool $authenticated = true, int $maxRetries = 3): array
    {
        return $this->requestWithRetry('POST', $endpoint, $data, $authenticated, $maxRetries);
    }
}
