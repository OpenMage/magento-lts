<?php

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
    const URL_PRODUCTION = 'https://apis.usps.com/';

    /**
     * Sandbox/Test API base URL
     */
    const URL_SANDBOX = 'https://apis-tem.usps.com/';

    /**
     * Default request timeout in seconds
     */
    const DEFAULT_TIMEOUT = 30;

    /**
     * Base URL for API requests
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * OAuth access token
     *
     * @var string|null
     */
    protected $_accessToken;

    /**
     * Debug mode flag
     *
     * @var bool
     */
    protected $_debug = false;

    /**
     * Debug log array
     *
     * @var array
     */
    protected $_debugLog = array();

    /**
     * Constructor
     *
     * @param string $baseUrl Base API URL (production or sandbox)
     * @param bool $debug Enable debug logging
     */
    public function __construct($baseUrl = null, $debug = false)
    {
        $this->_baseUrl = $baseUrl ?: self::URL_PRODUCTION;
        $this->_debug = $debug;
    }

    /**
     * Set the OAuth access token for authenticated requests
     *
     * @param string $token Access token
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    public function setAccessToken($token)
    {
        $this->_accessToken = $token;
        return $this;
    }

    /**
     * Get the current access token
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * Set base URL
     *
     * @param string $url Base API URL
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    public function setBaseUrl($url)
    {
        $this->_baseUrl = rtrim($url, '/') . '/';
        return $this;
    }

    /**
     * Get base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Authenticate and retrieve OAuth token
     *
     * @param string $clientId Client ID
     * @param string $clientSecret Client Secret
     * @return array Response array with 'success', 'access_token', 'expires_in', 'error'
     */
    public function authenticate($clientId, $clientSecret)
    {
        $endpoint = 'oauth2/v3/token';
        $payload = array(
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        );

        $response = $this->_post($endpoint, $payload, false);

        if ($response['success'] && isset($response['data']['access_token'])) {
            $this->_accessToken = $response['data']['access_token'];
            return array(
                'success' => true,
                'access_token' => $response['data']['access_token'],
                'expires_in' => isset($response['data']['expires_in']) ? $response['data']['expires_in'] : 3600,
                'token_type' => isset($response['data']['token_type']) ? $response['data']['token_type'] : 'Bearer',
            );
        }

        return array(
            'success' => false,
            'error' => isset($response['error']) ? $response['error'] : 'Authentication failed',
            'error_description' => isset($response['data']['error_description']) ? $response['data']['error_description'] : null,
        );
    }

    /**
     * Get rate quotes for a package
     *
     * @param array $request Rate request parameters
     * @return array Response array with 'success', 'data', 'error'
     */
    public function getRates(array $request)
    {
        $endpoint = 'prices/v3/total-rates/search';
        return $this->_post($endpoint, $request, true);
    }

    /**
     * Get tracking information for a tracking number
     *
     * @param string $trackingNumber Tracking number
     * @param bool $expandedDetails Include expanded tracking details
     * @return array Response array with 'success', 'data', 'error'
     */
    public function getTracking($trackingNumber, $expandedDetails = true)
    {
        $endpoint = 'tracking/v3/tracking/' . urlencode($trackingNumber);
        $query = $expandedDetails ? '?expand=DETAIL' : '';
        return $this->_get($endpoint . $query, true);
    }

    /**
     * Create a domestic shipping label
     *
     * @param array $labelRequest Label request parameters
     * @return array Response array with 'success', 'data', 'error'
     */
    public function createDomesticLabel(array $labelRequest)
    {
        $endpoint = 'labels/v3/label';
        return $this->_post($endpoint, $labelRequest, true);
    }

    /**
     * Create an international shipping label
     *
     * @param array $labelRequest Label request parameters
     * @return array Response array with 'success', 'data', 'error'
     */
    public function createInternationalLabel(array $labelRequest)
    {
        $endpoint = 'international-labels/v3/label';
        return $this->_post($endpoint, $labelRequest, true);
    }

    /**
     * Cancel a shipping label
     *
     * @param string $trackingNumber Tracking number of label to cancel
     * @return array Response array with 'success', 'data', 'error'
     */
    public function cancelLabel($trackingNumber)
    {
        $endpoint = 'labels/v3/label/' . urlencode($trackingNumber);
        return $this->_delete($endpoint, true);
    }

    /**
     * Get payment authorization token for label creation
     *
     * @param array $payload Payment authorization request
     * @return array Response array with 'success', 'data', 'error'
     */
    public function getPaymentAuthorization(array $payload)
    {
        $endpoint = 'payments/v3/payment-authorization';
        return $this->_post($endpoint, $payload, true);
    }

    /**
     * Verify an address using USPS Address API
     *
     * Note: USPS Address API uses GET with query parameters, not POST.
     *
     * @param array $address Address to verify with keys: streetAddress, secondaryAddress, city, state, ZIPCode, ZIPPlus4
     * @return array Response array with 'success', 'data', 'error'
     */
    public function verifyAddress(array $address)
    {
        $queryParams = http_build_query(array_filter(array(
            'streetAddress' => isset($address['streetAddress']) ? $address['streetAddress'] : '',
            'secondaryAddress' => isset($address['secondaryAddress']) ? $address['secondaryAddress'] : '',
            'city' => isset($address['city']) ? $address['city'] : '',
            'state' => isset($address['state']) ? $address['state'] : '',
            'ZIPCode' => isset($address['ZIPCode']) ? $address['ZIPCode'] : '',
            'ZIPPlus4' => isset($address['ZIPPlus4']) ? $address['ZIPPlus4'] : '',
        )));
        $endpoint = 'addresses/v3/address?' . $queryParams;
        return $this->_get($endpoint, true);
    }

    /**
     * Perform HTTP GET request
     *
     * @param string $endpoint API endpoint
     * @param bool $authenticated Include auth header
     * @return array
     */
    protected function _get($endpoint, $authenticated = true)
    {
        return $this->_request('GET', $endpoint, null, $authenticated);
    }

    /**
     * Perform HTTP POST request
     *
     * @param string $endpoint API endpoint
     * @param array $data Request body data
     * @param bool $authenticated Include auth header
     * @return array
     */
    protected function _post($endpoint, array $data, $authenticated = true)
    {
        return $this->_request('POST', $endpoint, $data, $authenticated);
    }

    /**
     * Perform HTTP DELETE request
     *
     * @param string $endpoint API endpoint
     * @param bool $authenticated Include auth header
     * @return array
     */
    protected function _delete($endpoint, $authenticated = true)
    {
        return $this->_request('DELETE', $endpoint, null, $authenticated);
    }

    /**
     * Perform HTTP request
     *
     * @param string $method HTTP method (GET, POST, DELETE)
     * @param string $endpoint API endpoint
     * @param array|null $data Request body data
     * @param bool $authenticated Include auth header
     * @return array Response array with 'success', 'data', 'http_code', 'error'
     */
    protected function _request($method, $endpoint, $data = null, $authenticated = true)
    {
        $url = $this->_baseUrl . ltrim($endpoint, '/');

        $headers = array('Content-Type: application/json');

        if ($authenticated && $this->_accessToken) {
            $headers[] = 'Authorization: Bearer ' . $this->_accessToken;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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

        $this->_log('request', array(
            'method' => $method,
            'url' => $url,
            'data' => $data,
        ));

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            $this->_log('error', array('curl_error' => $curlError));
            return array(
                'success' => false,
                'http_code' => 0,
                'error' => 'cURL error: ' . $curlError,
                'data' => null,
            );
        }

        $responseData = json_decode($responseBody, true);

        $this->_log('response', array(
            'http_code' => $httpCode,
            'data' => $responseData,
        ));

        $isSuccess = $httpCode >= 200 && $httpCode < 300;

        return array(
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'data' => $responseData,
            'error' => $isSuccess ? null : $this->_extractError($responseData, $httpCode),
            'raw' => $responseBody,
        );
    }

    /**
     * Extract error message from response
     *
     * @param array|null $responseData Decoded response
     * @param int $httpCode HTTP status code
     * @return string Error message
     */
    protected function _extractError($responseData, $httpCode)
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
     * @param string $type Log entry type
     * @param array $data Log data
     * @return void
     */
    protected function _log($type, array $data)
    {
        if (!$this->_debug) {
            return;
        }

        $this->_debugLog[] = array(
            'type' => $type,
            'time' => microtime(true),
            'data' => $data,
        );
    }

    /**
     * Get debug log
     *
     * @return array
     */
    public function getDebugLog()
    {
        return $this->_debugLog;
    }

    /**
     * Clear debug log
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    public function clearDebugLog()
    {
        $this->_debugLog = array();
        return $this;
    }

    /**
     * Check if response indicates a transient error that can be retried
     *
     * @param int $httpCode HTTP status code
     * @return bool
     */
    public function isTransientError($httpCode)
    {
        return in_array($httpCode, array(429, 500, 502, 503, 504));
    }

    /**
     * Perform HTTP request with automatic retry for transient errors
     *
     * Uses exponential backoff: 200ms, 400ms, 800ms delays between retries.
     *
     * @param string $method HTTP method (GET, POST, DELETE)
     * @param string $endpoint API endpoint
     * @param array|null $data Request body data
     * @param bool $authenticated Include auth header
     * @param int $maxRetries Maximum retry attempts (default 3)
     * @return array Response array with 'success', 'data', 'http_code', 'error'
     */
    public function requestWithRetry($method, $endpoint, $data = null, $authenticated = true, $maxRetries = 3)
    {
        $lastResponse = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $response = $this->_request($method, $endpoint, $data, $authenticated);

            if ($response['success'] || !$this->isTransientError($response['http_code'])) {
                return $response;
            }

            $lastResponse = $response;

            // Log retry attempt
            $this->_log('retry', array(
                'attempt' => $attempt,
                'max_retries' => $maxRetries,
                'http_code' => $response['http_code'],
                'error' => $response['error'],
            ));

            // Don't sleep after the last attempt
            if ($attempt < $maxRetries) {
                // Exponential backoff: 200ms, 400ms, 800ms
                usleep(pow(2, $attempt) * 100000);
            }
        }

        return $lastResponse;
    }

    /**
     * Perform GET request with retry
     *
     * @param string $endpoint API endpoint
     * @param bool $authenticated Include auth header
     * @param int $maxRetries Maximum retry attempts
     * @return array
     */
    public function getWithRetry($endpoint, $authenticated = true, $maxRetries = 3)
    {
        return $this->requestWithRetry('GET', $endpoint, null, $authenticated, $maxRetries);
    }

    /**
     * Perform POST request with retry
     *
     * @param string $endpoint API endpoint
     * @param array $data Request body data
     * @param bool $authenticated Include auth header
     * @param int $maxRetries Maximum retry attempts
     * @return array
     */
    public function postWithRetry($endpoint, array $data, $authenticated = true, $maxRetries = 3)
    {
        return $this->requestWithRetry('POST', $endpoint, $data, $authenticated, $maxRetries);
    }
}
