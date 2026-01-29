<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Authentication and Access Token handling
 *
 * OAuth2 client credentials flow for USPS REST API
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_UspsAuth extends Mage_Usa_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Cache key prefix for USPS API token
     */
    public const CACHE_KEY_PREFIX = 'usps_rest_api_token';

    /**
     * OAuth scopes required for USPS REST API operations
     * 
     * Note: USPS OAuth automatically grants all authorized scopes when empty.
     * Requesting specific scopes causes authentication issues.
     */
    public const OAUTH_SCOPES = '';

    /**
     * Content type for OAuth request
     */
    public const CONTENT_TYPE_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    /**
     * Error log message prefix
     */
    public const ERROR_LOG_MESSAGE = '---USPS REST API Auth Exception---';

    /**
     * Get OAuth2 access token for USPS REST API
     *
     * Retrieves cached token if available, otherwise requests new token
     * from USPS OAuth endpoint and caches it with appropriate TTL.
     *
     * @param string $clientId USPS Consumer Key
     * @param string $clientSecret USPS Consumer Secret
     * @param string $clientUrl OAuth token endpoint URL
     * @return string|false|null Access token on success, false on API error, null on exception
     * @throws Exception
     */
    public function getAccessToken(string $clientId, string $clientSecret, string $clientUrl)
    {
        $storeId = Mage::app()->getStore()->getId();
        $cacheKey = self::CACHE_KEY_PREFIX . '_store_' . $storeId;
        $cache = Mage::app()->getCache();
        $result = $cache->load($cacheKey);

        if ($result) {
            return $result;
        }

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $authPayload = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        // Log OAuth request (mask sensitive data)
        $debugData = [
            'request' => [
                'endpoint' => $clientUrl . 'oauth2/v3/token',
                'method' => 'POST',
                'content_type' => 'application/x-www-form-urlencoded',
                'grant_type' => 'client_credentials',
                'client_id' => substr($clientId, 0, 8) . '...',
            ],
            '__pid' => getmypid(),
        ];
        $this->_debug($debugData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $clientUrl . 'oauth2/v3/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authPayload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        try {
            $responseData = curl_exec($ch);

            if ($responseData === false) {
                $code = curl_errno($ch);
                $description = curl_strerror($code);
                $message = curl_error($ch);
                
                // Direct diagnostic logging
                
                $this->_debug([
                    'error' => "cURL Error: ($code) $description - \"$message\"",
                    '__pid' => getmypid(),
                ]);
                return null;
            }

            // Direct diagnostic logging of response
            
            $debugData = [
                'result' => $responseData,
                '__pid' => getmypid(),
            ];
            $this->_debug($debugData);

            $response = json_decode($responseData, true);

            if (isset($response['error'])) {
                
                $this->_debug([
                    'error' => $response['error'] . ': ' . ($response['error_description'] ?? 'Unknown error'),
                    '__pid' => getmypid(),
                ]);
                return false;
            }

            if (!isset($response['access_token'])) {
                
                $this->_debug([
                    'error' => 'No access_token in response',
                    'response' => $response,
                    '__pid' => getmypid(),
                ]);
                return false;
            }

            $accessToken = $response['access_token'];
            $expiresIn = $response['expires_in'] ?? 10000;

            // Trim token to remove any whitespace that could cause invalid signature errors
            $accessToken = trim($accessToken);
            
            // Cache the token with TTL slightly less than expiry to avoid edge cases
            $cacheTtl = max(1, $expiresIn - 60);
            $cache->save($accessToken, $cacheKey, [], $cacheTtl);

            return $accessToken;
        } catch (Exception $e) {
            $this->_debug(self::ERROR_LOG_MESSAGE . ' ' . $e->getMessage());
            return null;
        } finally {
            curl_close($ch);
        }
    }

    /**
     * Clear cached access token
     *
     * Useful when token is invalid or credentials have changed
     *
     * @return void
     */
    public function clearCachedToken()
    {
        $storeId = Mage::app()->getStore()->getId();
        $cacheKey = self::CACHE_KEY_PREFIX . '_store_' . $storeId;
        $cache = Mage::app()->getCache();
        $cache->remove($cacheKey);
    }

    /**
     * @inheritDoc
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        return new Varien_Object();
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods(): array
    {
        return [];
    }
}
