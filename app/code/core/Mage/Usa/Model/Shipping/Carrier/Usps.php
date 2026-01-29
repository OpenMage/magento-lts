<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS shipping rates estimation
 *
 * @link       http://www.usps.com/webtools/htm/Development-Guide-v3-0b.htm
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps extends Mage_Usa_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * USPS containers
     */
    public const CONTAINER_VARIABLE           = 'VARIABLE';

    public const CONTAINER_FLAT_RATE_BOX      = 'FLAT RATE BOX';

    public const CONTAINER_FLAT_RATE_ENVELOPE = 'FLAT RATE ENVELOPE';

    public const CONTAINER_RECTANGULAR        = 'RECTANGULAR';

    public const CONTAINER_NONRECTANGULAR     = 'NONRECTANGULAR';

    /**
     * USPS size
     */
    public const SIZE_REGULAR = 'REGULAR';

    public const SIZE_LARGE   = 'LARGE';

    /**
     * Default api revision
     *
     * @var int
     */
    public const DEFAULT_REVISION = 2;

    /**
     * Code of the carrier
     *
     * @var string
     */
    public const CODE = 'usps';

    /**
     * Ounces in one pound for conversion
     */
    public const OUNCES_POUND = 16;

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Destination Zip Code required flag
     *
     * @var bool
     * @deprecated since 1.7.0 functionality implemented in Mage_Usa_Model_Shipping_Carrier_Abstract
     */
    protected $_isZipCodeRequired;

    /**
     * Rate request data
     *
     * @var Mage_Shipping_Model_Rate_Request|null
     */
    protected $_request = null;

    /**
     * Raw rate request data
     *
     * @var Varien_Object|null
     */
    protected $_rawRequest = null;


    /**
     * Raw rate tracking request data
     *
     * @var Varien_Object|null
     */
    protected $_rawTrackRequest = null;

    /**
     * Rate result data
     *
     * @var Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Tracking_Result|null
     */
    protected $_result = null;

    /**
     * Container types that could be customized for USPS carrier
     *
     * @var array
     */
    protected $_customizableContainerTypes = ['VARIABLE', 'RECTANGULAR', 'NONRECTANGULAR'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Collect and get rates
     *
     * @return Mage_Shipping_Model_Rate_Result|bool|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag($this->_activeFlag)) {
            return false;
        }

        $this->setRequest($request);

        $this->_result = $this->_getQuotes();

        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

    /**
     * Prepare and set request to this instance
     *
     * @return $this
     */
    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        } else {
            $r->setService('ALL');
        }

        if ($request->getUspsContainer()) {
            $container = $request->getUspsContainer();
        } else {
            $container = $this->getConfigData('container');
        }

        $r->setContainer($container);

        if ($request->getUspsSize()) {
            $size = $request->getUspsSize();
        } else {
            $size = $this->getConfigData('size');
        }

        $r->setSize($size);

        if ($request->getGirth()) {
            $girth = $request->getGirth();
        } else {
            $girth = $this->getConfigData('girth');
        }

        $r->setGirth($girth);

        // @customization Vitasalus: Calculate dimensions from product attributes
        // instead of using request overrides. Falls back to config if no product
        // dimensions found. See _calculatePackageDimensions() method below.
        $dimensions = $this->_calculatePackageDimensions($request);
        $r->setHeight($dimensions['height']);
        $r->setLength($dimensions['length']);
        $r->setWidth($dimensions['width']);
        // End @customization

        if ($request->getUspsMachinable()) {
            $machinable = $request->getUspsMachinable();
        } else {
            $machinable = $this->getConfigData('machinable');
        }

        $r->setMachinable($machinable);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $request->getStoreId(),
            ));
        }

        if ($request->getOrigCountryId()) {
            $r->setOrigCountryId($request->getOrigCountryId());
        } else {
            $r->setOrigCountryId(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $request->getStoreId(),
            ));
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        $r->setDestCountryId($destCountry);

        if (!$this->_isUSCountry($destCountry)) {
            $r->setDestCountryName($this->_getCountryName($destCountry));
        }

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeightPounds(floor($weight));
        $r->setWeightOunces(round(($weight - floor($weight)) * self::OUNCES_POUND, 1));
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $this->_rawRequest = $r;

        return $this;
    }

    /**
     * Get result of request
     *
     * @return Mage_Shipping_Model_Rate_Result|null
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Check if carrier is active - override to add logging
     *
     * @return bool
     */
    public function isActive()
    {
        $active = parent::isActive();
        return $active;
    }

    /**
     * @inheritdoc
     * Returns true if label generation is enabled and all required credentials are configured.
     * Requires: enable_labels=Yes, CRID, MID, and EPS Account Number.
     */
    public function isShippingLabelsAvailable()
    {
        return (bool) $this->getConfigData('enable_labels')
            && $this->getConfigData('crid')
            && $this->getConfigData('mid')
            && $this->getConfigData('eps_account_number');
    }

    /**
     * Get quotes
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        return $this->_getRestQuotes();
    }

    /**
     * Get USPS error dictionary instance
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_ErrorDictionary
     */
    protected function _getErrorDictionary()
    {
        return Mage::getSingleton('usa/shipping_carrier_usps_error_dictionary');
    }

    /**
     * Get USPS REST API gateway URL
     *
     * Returns the configured gateway URL. In admin, this field auto-populates
     * based on the rest_environment selection via JavaScript.
     *
     * @return string
     */
    protected function _getRestGatewayUrl()
    {
        $url = $this->getConfigData('gateway_rest_url');
        
        // If no URL configured, determine from environment setting
        if (!$url) {
            $environment = $this->getConfigData('rest_environment');
            /** @var Mage_Usa_Model_Shipping_Carrier_Usps_Source_Environment $envSource */
            $envSource = Mage::getSingleton('usa/shipping_carrier_usps_source_environment');
            $url = $envSource->getUrlForEnvironment($environment ?: 'sandbox');
        }
        
        return rtrim($url, '/') . '/';
    }

    /**
     * Get OAuth access token for REST API
     *
     * @return string|null
     */
    protected function _getOAuthToken()
    {
        $clientId = $this->getConfigData('client_id');
        $clientSecret = $this->getConfigData('client_secret');
        $baseUrl = $this->_getRestGatewayUrl();
        $environment = $this->getConfigData('rest_environment') ?: 'sandbox';

        // Diagnostic logging for credential validation
        
        // Check if credentials contain non-printable characters (sign of decryption failure)
        $clientIdClean = preg_match('/^[a-zA-Z0-9_-]+$/', $clientId);
        $clientSecretClean = preg_match('/^[a-zA-Z0-9_-]+$/', $clientSecret);

        if (!$clientId || !$clientSecret) {
            $this->_debug(['error' => 'USPS REST API: Missing client_id or client_secret']);
            return null;
        }

        /** @var Mage_Usa_Model_Shipping_Carrier_UspsAuth $authModel */
        $authModel = Mage::getSingleton('usa/shipping_carrier_uspsAuth');
        return $authModel->getAccessToken($clientId, $clientSecret, $baseUrl);
    }

    /**
     * Get Payment Authorization Token for USPS label generation
     *
     * The payment authorization token is required for creating shipping labels.
     * It is obtained from the USPS /payments/v3/payment-authorization endpoint.
     *
     * @return string|null Payment authorization token or null on failure
     */
    protected function _getPaymentAuthToken()
    {
        // Check if label generation is enabled
        if (!$this->getConfigFlag('enable_labels')) {
            $this->_debug(['error' => 'USPS Label generation is disabled']);
            return null;
        }

        // Get required credentials
        $crid = $this->getConfigData('crid');
        $mid = $this->getConfigData('mid');
        $manifestMid = $this->getConfigData('manifest_mid');
        $accountType = $this->getConfigData('eps_account_type');
        $accountNumber = $this->getConfigData('eps_account_number');
        $permitZip = $this->getConfigData('permit_zip');

        if (!$crid || !$mid || !$accountNumber) {
            $this->_debug(['error' => 'USPS Payment Auth: Missing CRID, MID, or Account Number']);
            return null;
        }

        // PERMIT accounts require a ZIP code
        if ($accountType === 'PERMIT' && !$permitZip) {
            $this->_debug(['error' => 'USPS Payment Auth: PERMIT account type requires Permit ZIP Code']);
            return null;
        }

        // Check cache for existing payment auth token
        $cacheKey = 'usps_payment_auth_' . md5($crid . $mid . $accountNumber);
        $cache = Mage::app()->getCache();
        $cachedToken = $cache->load($cacheKey);

        if ($cachedToken !== false) {
            $this->_debug(['message' => 'Using cached payment authorization token']);
            return $cachedToken;
        }

        // Get OAuth token first
        $accessToken = $this->_getOAuthToken();
        if (!$accessToken) {
            return null;
        }

        // Build payment authorization request
        $baseUrl = $this->_getRestGatewayUrl();
        $url = $baseUrl . 'payments/v3/payment-authorization';

        // Build role data with conditional permitZIP for PERMIT accounts
        $roleData = [
            'CRID' => $crid,
            'MID' => $mid,
            'manifestMID' => $manifestMid ?: $mid,
            'accountType' => $accountType ?: 'EPS',
            'accountNumber' => $accountNumber
        ];

        // Add permitZIP for PERMIT account type (required by USPS API)
        if ($accountType === 'PERMIT' && $permitZip) {
            $roleData['permitZIP'] = $permitZip;
        }

        $payload = [
            'roles' => [
                array_merge(['roleName' => 'PAYER'], $roleData),
                array_merge(['roleName' => 'LABEL_OWNER'], $roleData)
            ]
        ];

        $this->_debug([
            'message' => 'Requesting USPS payment authorization token',
            'url' => $url,
            'payload' => $payload
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $this->_debug([
                'error' => 'Payment authorization failed',
                'http_code' => $httpCode,
                'response' => $errorData
            ]);
            return null;
        }

        $data = json_decode($response, true);
        $paymentAuthToken = $data['paymentAuthorizationToken'] ?? null;

        if (!$paymentAuthToken) {
            $this->_debug(['error' => 'No payment authorization token in response', 'response' => $data]);
            return null;
        }

        // Cache the token for 1 hour (tokens typically valid for longer, but refresh frequently)
        $cache->save($paymentAuthToken, $cacheKey, ['usps_payment_auth'], 3600);

        $this->_debug(['message' => 'Payment authorization token obtained successfully']);
        return $paymentAuthToken;
    }

    /**
     * Get quotes via USPS REST API
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getRestQuotes()
    {
        
        $r = $this->_rawRequest;
        $result = Mage::getModel('shipping/rate_result');

        // The origin address(shipper) must be only in USA
        if (!$this->_isUSCountry($r->getOrigCountryId())) {
            return $result;
        }

        $accessToken = $this->_getOAuthToken();
        if (!$accessToken) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage(Mage::helper('usa')->__('Unable to authenticate with USPS. Please check your API credentials.'));
            $result->append($error);
            return $result;
        }

        $baseUrl = $this->_getRestGatewayUrl();
        $isDomestic = $this->_isUSCountry($r->getDestCountryId());

        // Build single request payload without specifying mail class to get all rates
        $requestPayload = $this->_buildRestRateRequest($r, $isDomestic, null);

        // Build cart/quote fingerprint to prevent cached rates from different cart contents
        $cartFingerprint = $this->_buildCartFingerprint($r);

        // Generate cache key from ALL critical parameters to prevent false rate matches
        // TTL handles staleness - no need to include date (USPS rates don't change daily)
        $cacheKey = 'usps_rates_' . md5(serialize([
            'endpoint' => $isDomestic ? 'domestic' : 'international',
            'origin_zip' => substr($r->getOrigPostal(), 0, 5),
            'dest_zip' => substr($r->getDestPostal(), 0, 5),
            'dest_country' => $r->getDestCountryId(),
            'weight' => $requestPayload['weight'],
            'length' => $requestPayload['length'],
            'width' => $requestPayload['width'],
            'height' => $requestPayload['height'],
            'price_type' => $requestPayload['priceType'],
            'machinable' => $requestPayload['processingCategory'],
            'account_number' => $this->getConfigData('account_number'), // Prevent wrong rates after account change
            'account_type' => $this->getConfigData('account_type'),
            'allowed_methods_hash' => md5(serialize($this->getAllowedMethods())), // Detect config changes
            'cart_fingerprint' => $cartFingerprint, // Detect cart/quote changes (items, quantities, SKUs)
        ]));

        // Check cache first (only if caching is enabled)
        $cacheTtl = (int) $this->getConfigData('cache_ttl'); // Allow admin to configure TTL
        if ($cacheTtl === 0) {
            $cacheTtl = 1800; // Default: 30 minutes
        }
        
        $cache = Mage::app()->getCache();
        $cachedResponse = false;
        
        // Only use cache if TTL is positive (negative TTL = disabled)
        if ($cacheTtl > 0) {
            $cachedResponse = $cache->load($cacheKey);
        }
        
        if ($cachedResponse !== false) {
            $this->_debug([
                'message' => 'USPS: Using cached rates',
                'cache_key_prefix' => substr($cacheKey, 0, 40),
                'cache_hit' => true,
                '__pid' => getmypid(),
            ]);
            return $this->_parseRestRateResponse($cachedResponse, $isDomestic);
        }

        // Use total-rates/search for both domestic and international to get all rate options
        $endpoint = $isDomestic ? 'prices/v3/total-rates/search' : 'international-prices/v3/total-rates/search';
        $debugData = [
            'request' => [
                'endpoint' => $endpoint,
                'payload' => $requestPayload,
            ],
            'cache_key' => $cacheKey,
            '__pid' => getmypid(),
        ];
        
        // Log request payload for debugging
        $this->_debug($debugData);
        $this->_debug([
            'message' => 'USPS: Making API request (cache miss)',
            'endpoint' => $endpoint
        ]);

        try {
            $url = $baseUrl . $endpoint;

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($accessToken),
            ];
            
            // Log token info for debugging auth issues (redacted for security)
            $this->_debug([
                'message' => 'USPS: Using access token',
                'token_length' => strlen($accessToken),
                'token_preview' => '[REDACTED]'
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($responseBody === false) {
                $error = curl_error($ch);
                curl_close($ch);
                $this->_debug(['message' => 'USPS: Request failed', 'error' => $error]);
                $errorResult = Mage::getModel('shipping/rate_result_error');
                $errorResult->setCarrier('usps');
                $errorResult->setCarrierTitle($this->getConfigData('title'));
                $errorResult->setErrorMessage($this->getConfigData('specificerrmsg'));
                $result->append($errorResult);
                return $result;
            }

            curl_close($ch);
            
            // Log response
            $this->_debug([
                'result' => $responseBody,
                'http_code' => $httpCode,
                '__pid' => getmypid(),
            ]);

            // Check for HTTP error codes
            if ($httpCode >= 400) {
                $responseData = json_decode($responseBody, true);
                
                // Use error dictionary for user-friendly messages
                $errorDictionary = $this->_getErrorDictionary();
                $errorMessage = $errorDictionary->getErrorMessage(
                    $httpCode,
                    $responseData,
                    $this->getConfigData('specificerrmsg')
                );
                
                $this->_debug([
                    'message' => 'USPS API Error',
                    'http_code' => $httpCode,
                    'response' => $responseData,
                    'user_message' => $errorMessage
                ]);
                
                $errorResult = Mage::getModel('shipping/rate_result_error');
                $errorResult->setCarrier('usps');
                $errorResult->setCarrierTitle($this->getConfigData('title'));
                $errorResult->setErrorMessage($errorMessage);
                $result->append($errorResult);
                return $result;
            }

            // Validate response contains valid rate data before caching
            $responseData = json_decode($responseBody, true);
            $hasValidRates = false;
            
            if (isset($responseData['rateOptions']) && is_array($responseData['rateOptions']) && count($responseData['rateOptions']) > 0) {
                // Check if at least one rate option has rates
                foreach ($responseData['rateOptions'] as $rateOption) {
                    if (isset($rateOption['rates']) && is_array($rateOption['rates']) && count($rateOption['rates']) > 0) {
                        $hasValidRates = true;
                        break;
                    }
                }
            }

            // Only cache successful responses with valid rate data
            if ($cacheTtl > 0 && $hasValidRates) {
                $cache->save(
                    $responseBody,
                    $cacheKey,
                    ['usps_rates', 'shipping_rates'],
                    $cacheTtl
                );
                $this->_debug(['message' => 'USPS: Cached response with rates', 'ttl' => $cacheTtl, 'cache_key_prefix' => substr($cacheKey, 0, 40)]);
            } elseif ($cacheTtl > 0 && !$hasValidRates) {
                $this->_debug(['message' => 'USPS: Response has no valid rates - not caching']);
            } else {
                $this->_debug(['message' => 'USPS: Cache disabled', 'ttl' => $cacheTtl]);
            }

            // Parse the single response containing all rates
            return $this->_parseRestRateResponse($responseBody, $isDomestic);
            
        } catch (Exception $e) {
            $this->_debug(['message' => 'USPS: Exception', 'error' => $e->getMessage()]);
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $result;
        }
    }

    /**
     * Build REST API rate request payload
     *
     * Based on USPS REST API v3 specification from https://github.com/USPS/api-examples
     *
     * @param Varien_Object $r Raw request object
     * @param bool $isDomestic Is domestic shipment
     * @param string|null $mailClass Specific mail class to query (optional)
     * @return array
     */
    protected function _buildRestRateRequest(Varien_Object $r, bool $isDomestic, $mailClass = null)
    {
        $weightPounds = (float) $r->getWeightPounds();
        $weightOunces = (float) $r->getWeightOunces();
        $totalOunces = ($weightPounds * self::OUNCES_POUND) + $weightOunces;
        $weightInPounds = round($totalOunces / self::OUNCES_POUND, 2);

        // Get configuration values
        // Map EPS account type to COMMERCIAL price type (EPS is not a valid priceType enum)
        $configPriceType = $this->getConfigData('price_type') ?: 'COMMERCIAL';
        $priceType = ($configPriceType === 'EPS') ? 'COMMERCIAL' : $configPriceType;
        $accountType = $this->getConfigData('account_type') ?: 'EPS';
        $accountNumber = $this->getConfigData('account_number') ?: '';
        $machinable = $this->getConfigData('machinable') === 'true' ? 'MACHINABLE' : 'NON_MACHINABLE';

        if ($isDomestic) {
            $request = [
                'originZIPCode' => substr($r->getOrigPostal(), 0, 5),
                'destinationZIPCode' => substr($r->getDestPostal(), 0, 5),
                'weight' => $weightInPounds > 0 ? $weightInPounds : 0.1,
                'length' => (float) ($r->getLength() ?: 6),
                'width' => (float) ($r->getWidth() ?: 4),
                'height' => (float) ($r->getHeight() ?: 1),
                'processingCategory' => $machinable,
                'destinationEntryFacilityType' => 'NONE',
                'rateIndicator' => 'DR',
                'priceType' => $priceType,
                'mailingDate' => date('Y-m-d'),
            ];

            // Add account info for commercial pricing
            if ($priceType === 'COMMERCIAL' && $accountNumber) {
                $request['accountType'] = $accountType;
                $request['accountNumber'] = $accountNumber;
            }

            // Add optional item value for insurance calculations
            if ($r->getValue() && $r->getValue() > 0) {
                $request['itemValue'] = (float) $r->getValue();
            }

            return $request;
        } else {
            $request = [
                'originZIPCode' => substr($r->getOrigPostal(), 0, 5),
                'foreignPostalCode' => $r->getDestPostal() ?: '',
                'destinationCountryCode' => $this->_getIso2CountryCode($r->getDestCountryId()),
                'weight' => $weightInPounds > 0 ? $weightInPounds : 0.1,
                'length' => (float) ($r->getLength() ?: 6),
                'width' => (float) ($r->getWidth() ?: 4),
                'height' => (float) ($r->getHeight() ?: 1),
                'processingCategory' => $machinable,
                'destinationEntryFacilityType' => 'NONE',
                'rateIndicator' => 'SP',
                'priceType' => $priceType,
                'mailingDate' => date('Y-m-d'),
            ];

            // Add account info for commercial pricing
            if ($priceType === 'COMMERCIAL' && $accountNumber) {
                $request['accountType'] = $accountType;
                $request['accountNumber'] = $accountNumber;
            }

            // Add optional item value for customs/insurance
            if ($r->getValue() && $r->getValue() > 0) {
                $request['itemValue'] = (float) $r->getValue();
            }

            return $request;
        }
    }

    /**
     * Build cart/quote fingerprint to detect changes in cart contents
     * Prevents cached rates from being shown when cart items/quantities change
     *
     * @param Varien_Object $r Rate request object
     * @return string Cart fingerprint hash
     */
    protected function _buildCartFingerprint(Varien_Object $r)
    {
        $cartData = [];
        
        // Get all items from request (includes product SKU, qty, weight, price)
        $allItems = $r->getAllItems();
        if ($allItems) {
            foreach ($allItems as $item) {
                // Build item fingerprint: SKU + qty + weight + price + product_id
                $cartData[] = [
                    'sku' => $item->getSku(),
                    'qty' => $item->getQty(),
                    'weight' => $item->getWeight(),
                    'price' => $item->getPrice(),
                    'product_id' => $item->getProductId(),
                    'row_weight' => $item->getRowWeight(), // Total weight for this line item
                ];
            }
        }
        
        // Include package value (affects insurance/customs declarations)
        $cartData['package_value'] = $r->getPackageValue();
        $cartData['order_subtotal'] = $r->getOrderShipment() 
            ? $r->getOrderShipment()->getOrder()->getSubtotal() 
            : $r->getBaseSubtotalInclTax();
        
        // Sort by SKU to ensure consistent hash regardless of item order
        usort($cartData, function($a, $b) {
            if (!is_array($a) || !is_array($b)) return 0;
            return strcmp($a['sku'] ?? '', $b['sku'] ?? '');
        });
        
        return md5(serialize($cartData));
    }

    /**
     * Get ISO 2 character country code
     *
     * @param string $countryId Country ID
     * @return string
     */
    protected function _getIso2CountryCode($countryId)
    {
        $country = Mage::getModel('directory/country')->loadByCode($countryId);
        return $country->getIso2Code() ?: $countryId;
    }

    /**
     * Extract unique mail classes from configured methods
     *
     * @param array $allowedMethods Array of configured method codes
     * @param bool $isDomestic Whether this is a domestic shipment
     * @return array Array of unique mail class strings
     */
    protected function _extractUniqueMailClasses(array $allowedMethods, $isDomestic)
    {
        $uniqueMailClasses = [];
        
        // International-only mail classes (not valid for domestic API)
        $internationalOnlyClasses = [
            'GLOBAL_EXPRESS_GUARANTEED',
            'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE',
            'PRIORITY_MAIL_INTERNATIONAL',
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
        ];
        
        // getAllowedMethods() returns ['CODE' => 'Name', ...], so iterate over keys
        foreach (array_keys($allowedMethods) as $methodCode) {
            // Skip international methods for domestic shipments and vice versa
            $hasInternational = strpos($methodCode, 'INTERNATIONAL') !== false 
                             || strpos($methodCode, 'GLOBAL_EXPRESS') !== false;
            if ($isDomestic && $hasInternational) {
                continue;
            }
            if (!$isDomestic && !$hasInternational) {
                continue;
            }
            
            // Extract mail class by removing rate indicator (last part)
            // Format: {MAIL_CLASS}_{RATE_INDICATOR} (e.g., PRIORITY_MAIL_SP)
            $parts = explode('_', $methodCode);
            if (count($parts) >= 2) {
                $mailClass = implode('_', array_slice($parts, 0, -1));
                
                // Skip international-only classes for domestic shipments
                if ($isDomestic && in_array($mailClass, $internationalOnlyClasses)) {
                    continue;
                }
                
                // Normalize mail class names to match USPS API expectations
                $mailClass = $this->_normalizeMailClass($mailClass);
                
                $uniqueMailClasses[$mailClass] = true; // Use associative array for uniqueness
            }
        }
        
        // Return as indexed array
        return array_keys($uniqueMailClasses);
    }
    
    /**
     * Normalize mail class name to match USPS API expectations
     *
     * @param string $mailClass The mail class extracted from method code
     * @return string Normalized mail class name
     */
    protected function _normalizeMailClass($mailClass)
    {
        // USPS API uses hyphen for First-Class: FIRST-CLASS_PACKAGE_SERVICE
        // Our config uses underscore: FIRST_CLASS_PACKAGE_SERVICE
        $normalizations = [
            'FIRST_CLASS_PACKAGE_SERVICE' => 'FIRST-CLASS_PACKAGE_SERVICE',
            'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE' => 'FIRST-CLASS_PACKAGE_INTERNATIONAL_SERVICE',
        ];
        
        return $normalizations[$mailClass] ?? $mailClass;
    }

    /**
     * Parse REST API rate response
     *
     * @param string $responseBody JSON response
     * @param bool $isDomestic Is domestic shipment
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseRestRateResponse(string $responseBody, bool $isDomestic)
    {
        $result = Mage::getModel('shipping/rate_result');
        $priceArr = [];
        $costArr = [];
        $serviceCodeToNameMap = [];

        $response = json_decode($responseBody, true);

        if (!is_array($response)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $result;
        }

        // Check for error response
        if (isset($response['error'])) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($response['error']['message'] ?? $this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $result;
        }

        // Parse rates from response - handle both rate structures
        $rateOptions = $response['rateOptions'] ?? ($response['rates'] ? [['rates' => $response['rates']]] : []);

        $this->_debug(['message' => 'API returned rate options', 'rate_options_count' => count($rateOptions)]);

        /** @var Mage_Usa_Model_Shipping_Carrier_Usps_Source_Method $methodSource */
        $methodSource = Mage::getSingleton('usa/shipping_carrier_usps_source_method');
        $methodLabels = $methodSource->toOptionArray();
        $methodLabelMap = [];
        foreach ($methodLabels as $option) {
            $methodLabelMap[$option['value']] = $option['label'];
        }

        // Get allowed methods for filtering
        $allowedMethods = $this->getAllowedMethods();
        $this->_debug(['message' => 'Filtering rates', 'allowed_methods' => array_keys($allowedMethods), 'count' => count($allowedMethods)]);

        // Loop through rate options - filter by allowed methods
        foreach ($rateOptions as $rateOption) {
            $optionRates = $rateOption['rates'] ?? [];
            
            foreach ($optionRates as $rate) {
                $mailClass = $rate['mailClass'] ?? '';
                $rateIndicator = $rate['rateIndicator'] ?? '';
                $description = $rate['description'] ?? '';

                // Build method code: {MAIL_CLASS}_{RATE_INDICATOR}
                $methodCode = $mailClass;
                if ($rateIndicator) {
                    $methodCode .= '_' . $rateIndicator;
                }

                // Skip if method not in allowed methods
                if (!isset($allowedMethods[$methodCode])) {
                    $this->_debug(['message' => 'Skipping method (not allowed)', 'method' => $methodCode]);
                    continue;
                }

                // Use totalBasePrice from rateOption level, or price from rate level
                $price = (float) ($rateOption['totalBasePrice'] ?? $rate['price'] ?? 0);
                if ($price <= 0) {
                    continue;
                }

                // Keep lowest price for each method code (handles duplicate rate entries)
                if (!isset($priceArr[$methodCode]) || $price < $costArr[$methodCode]) {
                    $costArr[$methodCode] = $price;
                    $priceArr[$methodCode] = $this->getMethodPrice($price, $methodCode);
                    $serviceCodeToNameMap[$methodCode] = $methodLabelMap[$methodCode]
                        ?? $description
                        ?? str_replace('_', ' ', $mailClass);

                    $this->_debug([
                        'message' => 'Added rate',
                        'method' => $methodCode,
                        'cost' => $price,
                        'price' => $priceArr[$methodCode],
                        'title' => $serviceCodeToNameMap[$methodCode]
                    ]);
                }
            }
        }

        $this->_debug(['message' => 'Final price array', 'count' => count($priceArr)]);
        
        if (empty($priceArr)) {
            $this->_debug(['message' => 'No rates found - returning error']);
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            asort($priceArr);
            $this->_debug(['message' => 'Appending rates to result', 'count' => count($priceArr)]);

            // Fetch delivery estimates if feature is enabled (domestic only)
            $deliveryEstimates = [];
            if ($isDomestic && $this->getConfigData('show_delivery_estimates')) {
                $deliveryEstimates = $this->_getDeliveryEstimates(array_keys($priceArr));
            }

            foreach ($priceArr as $method => $price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('usps');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                
                // Append delivery estimate to method title if available
                $methodTitle = $serviceCodeToNameMap[$method];
                if (!empty($deliveryEstimates[$method]['display'])) {
                    $methodTitle .= ' (' . $deliveryEstimates[$method]['display'] . ')';
                }
                $rate->setMethodTitle($methodTitle);
                
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
                
                $this->_debug(['message' => 'Appended rate', 'method' => $method, 'price' => $price]);
            }
        }

        $this->_debug(['message' => 'Returning result object', 'total_rates' => count($result->getAllRates())]);
        return $result;
    }

    /**
     * Get delivery estimates for mail classes using USPS Service Standards API
     *
     * @param array $methodCodes Array of method codes (e.g., ['PRIORITY_MAIL_SP', 'USPS_GROUND_ADVANTAGE_SP'])
     * @return array Keyed by method code, values contain 'display', 'min_days', 'max_days'
     */
    protected function _getDeliveryEstimates(array $methodCodes)
    {
        /** @var Mage_Usa_Model_Shipping_Carrier_Usps_ServiceStandards $serviceStandards */
        $serviceStandards = Mage::getModel('usa/shipping_carrier_usps_service_standards');
        
        if (!$serviceStandards->isEnabled()) {
            return [];
        }
        
        $r = $this->_rawRequest;
        $originZip = substr($r->getOrigPostal(), 0, 5);
        $destZip = substr($r->getDestPostal(), 0, 5);
        
        if (!$originZip || !$destZip) {
            return [];
        }
        
        try {
            return $serviceStandards->getEstimates($originZip, $destZip, $methodCodes);
        } catch (Exception $e) {
            $this->_debug([
                'message' => 'Failed to get delivery estimates',
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Set free method request
     *
     * @param  $freeMethod
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;

        $freeMethod = $this->getConfigData('free_method');

        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $r->setWeightPounds(floor($weight));
        $r->setWeightOunces(round(($weight - floor($weight)) * self::OUNCES_POUND, 1));
        $r->setService($freeMethod);
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|bool
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                '0_FCLE' => Mage::helper('usa')->__('First-Class Mail Large Envelope'),
                '0_FCL'  => Mage::helper('usa')->__('First-Class Mail Letter'),
                '0_FCSL' => Mage::helper('usa')->__('First-Class Mail Stamped Letter'),
                '0_FCPC' => Mage::helper('usa')->__('First-Class Mail Postcards'),
                '1'      => Mage::helper('usa')->__('Priority Mail'),
                '2'      => Mage::helper('usa')->__('Priority Mail Express Hold For Pickup'),
                '3'      => Mage::helper('usa')->__('Priority Mail Express'),
                '4'      => Mage::helper('usa')->__('Retail Ground'),
                '6'      => Mage::helper('usa')->__('Media Mail Parcel'),
                '7'      => Mage::helper('usa')->__('Library Mail Parcel'),
                '13'     => Mage::helper('usa')->__('Priority Mail Express Flat Rate Envelope'),
                '15'     => Mage::helper('usa')->__('First-Class Mail Large Postcards'),
                '16'     => Mage::helper('usa')->__('Priority Mail Flat Rate Envelope'),
                '17'     => Mage::helper('usa')->__('Priority Mail Medium Flat Rate Box'),
                '22'     => Mage::helper('usa')->__('Priority Mail Large Flat Rate Box'),
                '23'     => Mage::helper('usa')->__('Priority Mail Express Sunday/Holiday Delivery'),
                '25'     => Mage::helper('usa')->__('Priority Mail Express Sunday/Holiday Delivery Flat Rate Envelope'),
                '27'     => Mage::helper('usa')->__('Priority Mail Express Flat Rate Envelope Hold For Pickup'),
                '28'     => Mage::helper('usa')->__('Priority Mail Small Flat Rate Box'),
                '29'     => Mage::helper('usa')->__('Priority Mail Padded Flat Rate Envelope'),
                '30'     => Mage::helper('usa')->__('Priority Mail Express Legal Flat Rate Envelope'),
                '31'     => Mage::helper('usa')->__('Priority Mail Express Legal Flat Rate Envelope Hold For Pickup'),
                '32'     => Mage::helper('usa')->__('Priority Mail Express Sunday/Holiday Delivery Legal Flat Rate Envelope'),
                '33'     => Mage::helper('usa')->__('Priority Mail Hold For Pickup'),
                '34'     => Mage::helper('usa')->__('Priority Mail Large Flat Rate Box Hold For Pickup'),
                '35'     => Mage::helper('usa')->__('Priority Mail Medium Flat Rate Box Hold For Pickup'),
                '36'     => Mage::helper('usa')->__('Priority Mail Small Flat Rate Box Hold For Pickup'),
                '37'     => Mage::helper('usa')->__('Priority Mail Flat Rate Envelope Hold For Pickup'),
                '38'     => Mage::helper('usa')->__('Priority Mail Gift Card Flat Rate Envelope'),
                '39'     => Mage::helper('usa')->__('Priority Mail Gift Card Flat Rate Envelope Hold For Pickup'),
                '40'     => Mage::helper('usa')->__('Priority Mail Window Flat Rate Envelope'),
                '41'     => Mage::helper('usa')->__('Priority Mail Window Flat Rate Envelope Hold For Pickup'),
                '42'     => Mage::helper('usa')->__('Priority Mail Small Flat Rate Envelope'),
                '43'     => Mage::helper('usa')->__('Priority Mail Small Flat Rate Envelope Hold For Pickup'),
                '44'     => Mage::helper('usa')->__('Priority Mail Legal Flat Rate Envelope'),
                '45'     => Mage::helper('usa')->__('Priority Mail Legal Flat Rate Envelope Hold For Pickup'),
                '46'     => Mage::helper('usa')->__('Priority Mail Padded Flat Rate Envelope Hold For Pickup'),
                '47'     => Mage::helper('usa')->__('Priority Mail Regional Rate Box A'),
                '48'     => Mage::helper('usa')->__('Priority Mail Regional Rate Box A Hold For Pickup'),
                '49'     => Mage::helper('usa')->__('Priority Mail Regional Rate Box B'),
                '50'     => Mage::helper('usa')->__('Priority Mail Regional Rate Box B Hold For Pickup'),
                '53'     => Mage::helper('usa')->__('First-Class Package Service Hold For Pickup'),
                '57'     => Mage::helper('usa')->__('Priority Mail Express Sunday/Holiday Delivery Flat Rate Boxes'),
                '58'     => Mage::helper('usa')->__('Priority Mail Regional Rate Box C'),
                '59'     => Mage::helper('usa')->__('Priority Mail Regional Rate Box C Hold For Pickup'),
                '62'     => Mage::helper('usa')->__('Priority Mail Express Padded Flat Rate Envelope'),
                '63'     => Mage::helper('usa')->__('Priority Mail Express Padded Flat Rate Envelope Hold For Pickup'),
                '64'     => Mage::helper('usa')->__('Priority Mail Express Sunday/Holiday Delivery Padded Flat Rate Envelope'),
                '72'     => Mage::helper('usa')->__('First-Class Mail Metered Letter'),
                'INT_1'  => Mage::helper('usa')->__('Priority Mail Express International'),
                'INT_2'  => Mage::helper('usa')->__('Priority Mail International'),
                'INT_4'  => Mage::helper('usa')->__('Global Express Guaranteed (GXG)'),
                'INT_5'  => Mage::helper('usa')->__('Global Express Guaranteed Document'),
                'INT_6'  => Mage::helper('usa')->__('Global Express Guaranteed Non-Document Rectangular'),
                'INT_7'  => Mage::helper('usa')->__('Global Express Guaranteed Non-Document Non-Rectangular'),
                'INT_8'  => Mage::helper('usa')->__('Priority Mail International Flat Rate Envelope'),
                'INT_9'  => Mage::helper('usa')->__('Priority Mail International Medium Flat Rate Box'),
                'INT_10' => Mage::helper('usa')->__('Priority Mail Express International Flat Rate Envelope'),
                'INT_11' => Mage::helper('usa')->__('Priority Mail International Large Flat Rate Box'),
                'INT_12' => Mage::helper('usa')->__('USPS GXG Envelopes'),
                'INT_13' => Mage::helper('usa')->__('First-Class Mail International Letter'),
                'INT_14' => Mage::helper('usa')->__('First-Class Mail International Large Envelope'),
                'INT_15' => Mage::helper('usa')->__('First-Class Package International Service'),
                'INT_16' => Mage::helper('usa')->__('Priority Mail International Small Flat Rate Box'),
                'INT_17' => Mage::helper('usa')->__('Priority Mail Express International Legal Flat Rate Envelope'),
                'INT_18' => Mage::helper('usa')->__('Priority Mail International Gift Card Flat Rate Envelope'),
                'INT_19' => Mage::helper('usa')->__('Priority Mail International Window Flat Rate Envelope'),
                'INT_20' => Mage::helper('usa')->__('Priority Mail International Small Flat Rate Envelope'),
                'INT_21' => Mage::helper('usa')->__('First-Class Mail International Postcard'),
                'INT_22' => Mage::helper('usa')->__('Priority Mail International Legal Flat Rate Envelope'),
                'INT_23' => Mage::helper('usa')->__('Priority Mail International Padded Flat Rate Envelope'),
                'INT_24' => Mage::helper('usa')->__('Priority Mail International DVD Flat Rate priced box'),
                'INT_25' => Mage::helper('usa')->__('Priority Mail International Large Video Flat Rate priced box'),
                'INT_27' => Mage::helper('usa')->__('Priority Mail Express International Padded Flat Rate Envelope'),
                '1058'   => Mage::helper('usa')->__('USPS Ground Advantage'),
            ],

            'service_to_code' => [
                '0_FCLE' => 'First Class',
                '0_FCL'  => 'First Class',
                '0_FCSL' => 'First Class',
                '0_FCPC' => 'First Class',
                '1'      => 'Priority',
                '2'      => 'Priority Express',
                '3'      => 'Priority Express',
                '4'      => 'Retail Ground',
                '6'      => 'Media',
                '7'      => 'Library',
                '13'     => 'Priority Express',
                '15'     => 'First Class',
                '16'     => 'Priority',
                '17'     => 'Priority',
                '22'     => 'Priority',
                '23'     => 'Priority Express',
                '25'     => 'Priority Express',
                '27'     => 'Priority Express',
                '28'     => 'Priority',
                '29'     => 'Priority',
                '30'     => 'Priority Express',
                '31'     => 'Priority Express',
                '32'     => 'Priority Express',
                '33'     => 'Priority',
                '34'     => 'Priority',
                '35'     => 'Priority',
                '36'     => 'Priority',
                '37'     => 'Priority',
                '38'     => 'Priority',
                '39'     => 'Priority',
                '40'     => 'Priority',
                '41'     => 'Priority',
                '42'     => 'Priority',
                '43'     => 'Priority',
                '44'     => 'Priority',
                '45'     => 'Priority',
                '46'     => 'Priority',
                '47'     => 'Priority',
                '48'     => 'Priority',
                '49'     => 'Priority',
                '50'     => 'Priority',
                '53'     => 'First Class',
                '57'     => 'Priority Express',
                '58'     => 'Priority',
                '59'     => 'Priority',
                '62'     => 'Priority Express',
                '63'     => 'Priority Express',
                '64'     => 'Priority Express',
                '72'     => 'First Class',
                'INT_1'  => 'Priority Express',
                'INT_2'  => 'Priority',
                'INT_4'  => 'Priority Express',
                'INT_5'  => 'Priority Express',
                'INT_6'  => 'Priority Express',
                'INT_7'  => 'Priority Express',
                'INT_8'  => 'Priority',
                'INT_9'  => 'Priority',
                'INT_10' => 'Priority Express',
                'INT_11' => 'Priority',
                'INT_12' => 'Priority Express',
                'INT_13' => 'First Class',
                'INT_14' => 'First Class',
                'INT_15' => 'First Class',
                'INT_16' => 'Priority',
                'INT_17' => 'Priority',
                'INT_18' => 'Priority',
                'INT_19' => 'Priority',
                'INT_20' => 'Priority',
                'INT_21' => 'First Class',
                'INT_22' => 'Priority',
                'INT_23' => 'Priority',
                'INT_24' => 'Priority',
                'INT_25' => 'Priority',
                'INT_27' => 'Priority Express',
                '1058'   => 'Ground Advantage',
            ],

            // Added because USPS has different services but with same CLASSID value, which is "0"
            'method_to_code' => [
                'First-Class Mail Large Envelope'      => '0_FCLE',
                'First-Class Mail Letter'              => '0_FCL',
                'First-Class Mail Stamped Letter'      => '0_FCSL',
                'First-Class Mail Metered Letter'      => '72',
            ],

            'first_class_mail_type' => [
                'LETTER'      => Mage::helper('usa')->__('Letter'),
                'FLAT'        => Mage::helper('usa')->__('Flat'),
                'PARCEL'      => Mage::helper('usa')->__('Parcel'),
            ],

            'container' => [
                'VARIABLE'           => Mage::helper('usa')->__('Variable'),
                'FLAT RATE ENVELOPE' => Mage::helper('usa')->__('Flat-Rate Envelope'),
                'FLAT RATE BOX'      => Mage::helper('usa')->__('Flat-Rate Box'),
                'RECTANGULAR'        => Mage::helper('usa')->__('Rectangular'),
                'NONRECTANGULAR'     => Mage::helper('usa')->__('Non-rectangular'),
            ],

            'containers_filter' => [
                [
                    'containers' => ['VARIABLE'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'Priority Mail Express Flat Rate Envelope',
                                'Priority Mail Express Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Flat Rate Envelope',
                                'Priority Mail Large Flat Rate Box',
                                'Priority Mail Medium Flat Rate Box',
                                'Priority Mail Small Flat Rate Box',
                                'Priority Mail Express Hold For Pickup',
                                'Priority Mail Express',
                                'Priority Mail',
                                'Priority Mail Hold For Pickup',
                                'Priority Mail Large Flat Rate Box Hold For Pickup',
                                'Priority Mail Medium Flat Rate Box Hold For Pickup',
                                'Priority Mail Small Flat Rate Box Hold For Pickup',
                                'Priority Mail Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Small Flat Rate Envelope',
                                'Priority Mail Small Flat Rate Envelope Hold For Pickup',
                                'First-Class Package Service Hold For Pickup',
                                'Priority Mail Express Flat Rate Boxes',
                                'Priority Mail Express Flat Rate Boxes Hold For Pickup',
                                'Retail Ground',
                                'Media Mail',
                                'First-Class Mail Large Envelope',
                                'Priority Mail Express Sunday/Holiday Delivery',
                                'Priority Mail Express Sunday/Holiday Delivery Flat Rate Envelope',
                                'Priority Mail Express Sunday/Holiday Delivery Flat Rate Boxes',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'Priority Mail Express International Flat Rate Envelope',
                                'Priority Mail International Flat Rate Envelope',
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                                'Priority Mail International Small Flat Rate Envelope',
                                'Priority Mail Express International Flat Rate Boxes',
                                'Global Express Guaranteed (GXG)',
                                'USPS GXG Envelopes',
                                'Priority Mail Express International',
                                'Priority Mail International',
                                'First-Class Mail International Letter',
                                'First-Class Mail International Large Envelope',
                                'First-Class Package International Service',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['FLAT RATE BOX'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'Priority Mail Large Flat Rate Box',
                                'Priority Mail Medium Flat Rate Box',
                                'Priority Mail Small Flat Rate Box',
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                                'Priority Mail International DVD Flat Rate priced box',
                                'Priority Mail International Large Video Flat Rate priced box',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['FLAT RATE ENVELOPE'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'Priority Mail Express Flat Rate Envelope',
                                'Priority Mail Express Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Flat Rate Envelope',
                                'First-Class Mail Large Envelope',
                                'Priority Mail Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Small Flat Rate Envelope',
                                'Priority Mail Small Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Express Sunday/Holiday Delivery Flat Rate Envelope',
                                'Priority Mail Express Padded Flat Rate Envelope',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'Priority Mail Express International Flat Rate Envelope',
                                'Priority Mail International Flat Rate Envelope',
                                'First-Class Mail International Large Envelope',
                                'Priority Mail International Small Flat Rate Envelope',
                                'Priority Mail Express International Legal Flat Rate Envelope',
                                'Priority Mail International Gift Card Flat Rate Envelope',
                                'Priority Mail International Window Flat Rate Envelope',
                                'Priority Mail International Legal Flat Rate Envelope',
                                'Priority Mail Express International Padded Flat Rate Envelope',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['RECTANGULAR'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'Priority Mail Express',
                                'Priority Mail',
                                'Retail Ground',
                                'Media Mail',
                                'Library Mail',
                                'First-Class Package Service',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'USPS GXG Envelopes',
                                'Priority Mail Express International',
                                'Priority Mail International',
                                'First-Class Package International Service',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['NONRECTANGULAR'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'Priority Mail Express',
                                'Priority Mail',
                                'Retail Ground',
                                'Media Mail',
                                'Library Mail',
                                'First-Class Package Service',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'Global Express Guaranteed (GXG)',
                                'Priority Mail Express International',
                                'Priority Mail International',
                                'First-Class Package International Service',
                            ],
                        ],
                    ],
                ],
            ],
            'size' => [
                'REGULAR'     => Mage::helper('usa')->__('Regular'),
                'LARGE'       => Mage::helper('usa')->__('Large'),
            ],

            'machinable' => [
                'true'        => Mage::helper('usa')->__('Yes'),
                'false'       => Mage::helper('usa')->__('No'),
            ],

            // REST API method codes - maps method_code to display label
            // Format: {MAIL_CLASS}_{RATE_INDICATOR}
            'rest_method' => [
                // USPS Ground Advantage
                'USPS_GROUND_ADVANTAGE_SP' => Mage::helper('usa')->__('USPS Ground Advantage'),
                'USPS_GROUND_ADVANTAGE_CP' => Mage::helper('usa')->__('USPS Ground Advantage - Cubic'),
                
                // Priority Mail
                'PRIORITY_MAIL_SP' => Mage::helper('usa')->__('Priority Mail'),
                'PRIORITY_MAIL_CP' => Mage::helper('usa')->__('Priority Mail - Cubic'),
                'PRIORITY_MAIL_FE' => Mage::helper('usa')->__('Priority Mail - Flat Rate Envelope'),
                'PRIORITY_MAIL_FA' => Mage::helper('usa')->__('Priority Mail - Legal Flat Rate Envelope'),
                'PRIORITY_MAIL_FP' => Mage::helper('usa')->__('Priority Mail - Padded Flat Rate Envelope'),
                'PRIORITY_MAIL_FS' => Mage::helper('usa')->__('Priority Mail - Small Flat Rate Box'),
                'PRIORITY_MAIL_FB' => Mage::helper('usa')->__('Priority Mail - Medium Flat Rate Box'),
                'PRIORITY_MAIL_PL' => Mage::helper('usa')->__('Priority Mail - Large Flat Rate Box'),
                'PRIORITY_MAIL_PM' => Mage::helper('usa')->__('Priority Mail - APO/FPO/DPO'),
                
                // Priority Mail Express
                'PRIORITY_MAIL_EXPRESS_SP' => Mage::helper('usa')->__('Priority Mail Express'),
                'PRIORITY_MAIL_EXPRESS_FE' => Mage::helper('usa')->__('Priority Mail Express - Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_FA' => Mage::helper('usa')->__('Priority Mail Express - Legal Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_FP' => Mage::helper('usa')->__('Priority Mail Express - Padded Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_FB' => Mage::helper('usa')->__('Priority Mail Express - Flat Rate Box'),
                
                // First-Class Package
                'FIRST_CLASS_PACKAGE_SERVICE_SP' => Mage::helper('usa')->__('First-Class Package Service'),
                
                // Library & Media Mail
                'LIBRARY_MAIL_SP' => Mage::helper('usa')->__('Library Mail'),
                'MEDIA_MAIL_SP' => Mage::helper('usa')->__('Media Mail'),
                
                // Parcel Select
                'PARCEL_SELECT_SP' => Mage::helper('usa')->__('Parcel Select'),
                'PARCEL_SELECT_DE' => Mage::helper('usa')->__('Parcel Select - USPS Delivery'),
                'PARCEL_SELECT_LW' => Mage::helper('usa')->__('Parcel Select Lightweight'),
                
                // International - First-Class
                'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE_SP' => Mage::helper('usa')->__('First-Class Package International'),
                
                // International - Priority Mail
                'PRIORITY_MAIL_INTERNATIONAL_SP' => Mage::helper('usa')->__('Priority Mail International'),
                'PRIORITY_MAIL_INTERNATIONAL_FE' => Mage::helper('usa')->__('Priority Mail International - Flat Rate Envelope'),
                'PRIORITY_MAIL_INTERNATIONAL_FA' => Mage::helper('usa')->__('Priority Mail International - Legal Flat Rate Envelope'),
                'PRIORITY_MAIL_INTERNATIONAL_FP' => Mage::helper('usa')->__('Priority Mail International - Padded Flat Rate Envelope'),
                'PRIORITY_MAIL_INTERNATIONAL_FS' => Mage::helper('usa')->__('Priority Mail International - Small Flat Rate Box'),
                'PRIORITY_MAIL_INTERNATIONAL_FB' => Mage::helper('usa')->__('Priority Mail International - Medium Flat Rate Box'),
                'PRIORITY_MAIL_INTERNATIONAL_PL' => Mage::helper('usa')->__('Priority Mail International - Large Flat Rate Box'),
                
                // International - Priority Mail Express
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_SP' => Mage::helper('usa')->__('Priority Mail Express International'),
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_PA' => Mage::helper('usa')->__('Priority Mail Express International'),
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FE' => Mage::helper('usa')->__('Priority Mail Express International - Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_E4' => Mage::helper('usa')->__('Priority Mail Express International - Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FA' => Mage::helper('usa')->__('Priority Mail Express International - Legal Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_E6' => Mage::helper('usa')->__('Priority Mail Express International - Legal Flat Rate Envelope'),
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FP' => Mage::helper('usa')->__('Priority Mail Express International - Padded Flat Rate Envelope'),
                
                // Global Express Guaranteed
                'GLOBAL_EXPRESS_GUARANTEED_SP' => Mage::helper('usa')->__('Global Express Guaranteed'),
            ],

            // REST API method code to service category mapping (for label generation)
            'rest_method_to_service' => [
                'USPS_GROUND_ADVANTAGE_SP' => 'GROUND_ADVANTAGE',
                'USPS_GROUND_ADVANTAGE_CP' => 'GROUND_ADVANTAGE',
                'PRIORITY_MAIL_SP' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_CP' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_FE' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_FA' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_FP' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_FS' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_FB' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_PL' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_PM' => 'PRIORITY_MAIL',
                'PRIORITY_MAIL_EXPRESS_SP' => 'PRIORITY_MAIL_EXPRESS',
                'PRIORITY_MAIL_EXPRESS_FE' => 'PRIORITY_MAIL_EXPRESS',
                'PRIORITY_MAIL_EXPRESS_FA' => 'PRIORITY_MAIL_EXPRESS',
                'PRIORITY_MAIL_EXPRESS_FP' => 'PRIORITY_MAIL_EXPRESS',
                'PRIORITY_MAIL_EXPRESS_FB' => 'PRIORITY_MAIL_EXPRESS',
                'FIRST_CLASS_PACKAGE_SERVICE_SP' => 'FIRST_CLASS_PACKAGE_SERVICE',
                'LIBRARY_MAIL_SP' => 'LIBRARY_MAIL',
                'MEDIA_MAIL_SP' => 'MEDIA_MAIL',
                'PARCEL_SELECT_SP' => 'PARCEL_SELECT',
                'PARCEL_SELECT_DE' => 'PARCEL_SELECT',
                'PARCEL_SELECT_LW' => 'PARCEL_SELECT_LIGHTWEIGHT',
                'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE_SP' => 'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE',
                'PRIORITY_MAIL_INTERNATIONAL_SP' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_INTERNATIONAL_FE' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_INTERNATIONAL_FA' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_INTERNATIONAL_FP' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_INTERNATIONAL_FS' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_INTERNATIONAL_FB' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_INTERNATIONAL_PL' => 'PRIORITY_MAIL_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_SP' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_PA' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FE' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_E4' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FA' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_E6' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FP' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
                'GLOBAL_EXPRESS_GUARANTEED_SP' => 'GLOBAL_EXPRESS_GUARANTEED',
            ],

            'delivery_confirmation_types' => [
                'True' => Mage::helper('usa')->__('Not Required'),
                'False'  => Mage::helper('usa')->__('Required'),
            ],
        ];

        if (!isset($codes[$type])) {
            return false;
        } elseif ($code === '') {
            return $codes[$type];
        }

        return $codes[$type][$code] ?? false;
    }

    /**
     * Get tracking
     *
     * @param mixed $trackingData
     * @return Mage_Shipping_Model_Rate_Result|null
     */
    public function getTracking($trackingData)
    {
        $this->setTrackingRequest();

        if (!is_array($trackingData)) {
            $trackingData = [$trackingData];
        }

        $this->_getRestTracking($trackingData);

        return $this->_result;
    }

    /**
     * Get tracking via REST API
     *
     * @param array $trackingData
     * @return void
     */
    protected function _getRestTracking(array $trackingData)
    {
        $accessToken = $this->_getOAuthToken();
        if (!$accessToken) {
            $this->_debug(['error' => 'Unable to get OAuth token for tracking']);
            if (!$this->_result) {
                $this->_result = Mage::getModel('shipping/tracking_result');
            }
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage(Mage::helper('usa')->__('Unable to authenticate with USPS. Please check your API credentials.'));
            $this->_result->append($error);
            return;
        }

        $baseUrl = $this->_getRestGatewayUrl();

        /** @var Mage_Usa_Model_Shipping_Carrier_Usps_TrackingService $trackingService */
        $trackingService = Mage::getSingleton('usa/shipping_carrier_usps_tracking_service');

        $debugData = ['tracking_numbers' => $trackingData];

        try {
            // TrackingService handles parsing internally and returns a Mage_Shipping_Model_Tracking_Result
            $result = $trackingService->getRestTracking($trackingData, $accessToken, $baseUrl);
            $debugData['result'] = 'TrackingService completed';
            $this->_debug($debugData);

            if ($result) {
                $this->_result = $result;
            } else {
                if (!$this->_result) {
                    $this->_result = Mage::getModel('shipping/tracking_result');
                }
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('usps');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage(Mage::helper('usa')->__('Unable to retrieve tracking'));
                $this->_result->append($error);
            }
        } catch (Exception $e) {
            $debugData['error'] = $e->getMessage();
            $this->_debug($debugData);

            if (!$this->_result) {
                $this->_result = Mage::getModel('shipping/tracking_result');
            }
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage(Mage::helper('usa')->__('Unable to retrieve tracking'));
            $this->_result->append($error);
        }
    }

    /**
     * Set tracking request
     *
     * @return void
     */
    protected function setTrackingRequest()
    {
        $r = new Varien_Object();

        $this->_rawTrackRequest = $r;
    }

    /**
     * Get allowed shipping methods
     *
     * Returns REST API methods based on admin configuration.
     * Returns empty array if no valid methods configured.
     *
     * @return array Method code => Method name pairs
     */
    public function getAllowedMethods()
    {
        $configMethods = $this->getConfigData('allowed_methods');
        
        /** @var Mage_Usa_Model_Shipping_Carrier_Usps_Source_Method $methodSource */
        $methodSource = Mage::getSingleton('usa/shipping_carrier_usps_source_method');
        $allMethods = $methodSource->getRestMethods();
        
        // If no methods configured, return empty (force admin to configure)
        if (empty($configMethods)) {
            return [];
        }
        
        // Filter to only configured methods
        $allowed = explode(',', $configMethods);
        $arr = [];
        
        foreach ($allowed as $code) {
            $code = trim($code);
            if (empty($code)) {
                continue;
            }
            
            // Direct lookup - no legacy normalization needed
            if (isset($allMethods[$code])) {
                $arr[$code] = $allMethods[$code];
            }
        }
        
        return $arr;
    }

    /**
     * Return USPS county name by country ISO 3166-1-alpha-2 code
     * Return false for unknown countries
     *
     * @param string $countryId
     * @return string|false
     */
    protected function _getCountryName($countryId)
    {
        $countries = [
            'AD' => 'Andorra',
            'AE' => 'United Arab Emirates',
            'AF' => 'Afghanistan',
            'AG' => 'Antigua and Barbuda',
            'AI' => 'Anguilla',
            'AL' => 'Albania',
            'AM' => 'Armenia',
            'AN' => 'Netherlands Antilles',
            'AO' => 'Angola',
            'AR' => 'Argentina',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'AW' => 'Aruba',
            'AX' => 'Aland Island (Finland)',
            'AZ' => 'Azerbaijan',
            'BA' => 'Bosnia-Herzegovina',
            'BB' => 'Barbados',
            'BD' => 'Bangladesh',
            'BE' => 'Belgium',
            'BF' => 'Burkina Faso',
            'BG' => 'Bulgaria',
            'BH' => 'Bahrain',
            'BI' => 'Burundi',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BN' => 'Brunei Darussalam',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'BS' => 'Bahamas',
            'BT' => 'Bhutan',
            'BW' => 'Botswana',
            'BY' => 'Belarus',
            'BZ' => 'Belize',
            'CA' => 'Canada',
            'CC' => 'Cocos Island (Australia)',
            'CD' => 'Congo, Democratic Republic of the',
            'CF' => 'Central African Republic',
            'CG' => 'Congo, Republic of the',
            'CH' => 'Switzerland',
            'CI' => 'Ivory Coast (Cote d Ivoire)',
            'CK' => 'Cook Islands (New Zealand)',
            'CL' => 'Chile',
            'CM' => 'Cameroon',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'CV' => 'Cape Verde',
            'CX' => 'Christmas Island (Australia)',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DJ' => 'Djibouti',
            'DK' => 'Denmark',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'DZ' => 'Algeria',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egypt',
            'ER' => 'Eritrea',
            'ES' => 'Spain',
            'ET' => 'Ethiopia',
            'FI' => 'Finland',
            'FJ' => 'Fiji',
            'FK' => 'Falkland Islands',
            'FM' => 'Micronesia, Federated States of',
            'FO' => 'Faroe Islands',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GB' => 'Great Britain and Northern Ireland',
            'GD' => 'Grenada',
            'GE' => 'Georgia, Republic of',
            'GF' => 'French Guiana',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GL' => 'Greenland',
            'GM' => 'Gambia',
            'GN' => 'Guinea',
            'GP' => 'Guadeloupe',
            'GQ' => 'Equatorial Guinea',
            'GR' => 'Greece',
            'GS' => 'South Georgia (Falkland Islands)',
            'GT' => 'Guatemala',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HK' => 'Hong Kong',
            'HN' => 'Honduras',
            'HR' => 'Croatia',
            'HT' => 'Haiti',
            'HU' => 'Hungary',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IN' => 'India',
            'IQ' => 'Iraq',
            'IR' => 'Iran',
            'IS' => 'Iceland',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JO' => 'Jordan',
            'JP' => 'Japan',
            'KE' => 'Kenya',
            'KG' => 'Kyrgyzstan',
            'KH' => 'Cambodia',
            'KI' => 'Kiribati',
            'KM' => 'Comoros',
            'KN' => 'Saint Kitts (Saint Christopher and Nevis)',
            'KP' => "North Korea (Korea, Democratic People's Republic of)",
            'KR' => 'South Korea (Korea, Republic of)',
            'KW' => 'Kuwait',
            'KY' => 'Cayman Islands',
            'KZ' => 'Kazakhstan',
            'LA' => 'Laos',
            'LB' => 'Lebanon',
            'LC' => 'Saint Lucia',
            'LI' => 'Liechtenstein',
            'LK' => 'Sri Lanka',
            'LR' => 'Liberia',
            'LS' => 'Lesotho',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'LY' => 'Libya',
            'MA' => 'Morocco',
            'MC' => 'Monaco (France)',
            'MD' => 'Moldova',
            'MG' => 'Madagascar',
            'MK' => 'Macedonia, Republic of',
            'ML' => 'Mali',
            'MM' => 'Burma',
            'MN' => 'Mongolia',
            'MO' => 'Macao',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MS' => 'Montserrat',
            'MT' => 'Malta',
            'MU' => 'Mauritius',
            'MV' => 'Maldives',
            'MW' => 'Malawi',
            'MX' => 'Mexico',
            'MY' => 'Malaysia',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NC' => 'New Caledonia',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NI' => 'Nicaragua',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NP' => 'Nepal',
            'NR' => 'Nauru',
            'NZ' => 'New Zealand',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PF' => 'French Polynesia',
            'PG' => 'Papua New Guinea',
            'PH' => 'Philippines',
            'PK' => 'Pakistan',
            'PL' => 'Poland',
            'PM' => 'Saint Pierre and Miquelon',
            'PN' => 'Pitcairn Island',
            'PT' => 'Portugal',
            'PY' => 'Paraguay',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RS' => 'Serbia',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SB' => 'Solomon Islands',
            'SC' => 'Seychelles',
            'SD' => 'Sudan',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'SH' => 'Saint Helena',
            'SI' => 'Slovenia',
            'SK' => 'Slovak Republic',
            'SL' => 'Sierra Leone',
            'SM' => 'San Marino',
            'SN' => 'Senegal',
            'SO' => 'Somalia',
            'SR' => 'Suriname',
            'ST' => 'Sao Tome and Principe',
            'SV' => 'El Salvador',
            'SY' => 'Syrian Arab Republic',
            'SZ' => 'Swaziland',
            'TC' => 'Turks and Caicos Islands',
            'TD' => 'Chad',
            'TG' => 'Togo',
            'TH' => 'Thailand',
            'TJ' => 'Tajikistan',
            'TK' => 'Tokelau (Union Group) (Western Samoa)',
            'TL' => 'East Timor (Timor-Leste, Democratic Republic of)',
            'TM' => 'Turkmenistan',
            'TN' => 'Tunisia',
            'TO' => 'Tonga',
            'TR' => 'Turkey',
            'TT' => 'Trinidad and Tobago',
            'TV' => 'Tuvalu',
            'TW' => 'Taiwan',
            'TZ' => 'Tanzania',
            'UA' => 'Ukraine',
            'UG' => 'Uganda',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VA' => 'Vatican City',
            'VC' => 'Saint Vincent and the Grenadines',
            'VE' => 'Venezuela',
            'VG' => 'British Virgin Islands',
            'VN' => 'Vietnam',
            'VU' => 'Vanuatu',
            'WF' => 'Wallis and Futuna Islands',
            'WS' => 'Western Samoa',
            'YE' => 'Yemen',
            'YT' => 'Mayotte (France)',
            'ZA' => 'South Africa',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'US' => 'United States',
        ];

        return $countries[$countryId] ?? false;
    }

    /**
     * Clean service name from unsupported strings and characters
     *
     * @param  string $name
     * @return string
     */
    protected function _filterServiceName($name)
    {
        $name = (string) preg_replace(
            ['~<[^/!][^>]+>.*</[^>]+>~sU', '~\<!--.*--\>~isU', '~<[^>]+>~is'],
            '',
            html_entity_decode($name),
        );

        return str_replace('*', '', $name);
    }

    /**
     * Extract mail class and rate indicator from shipping method code
     *
     * @param string $shippingMethod e.g., 'usps_PRIORITY_MAIL_SP'
     * @return array [mailClass, rateIndicator]
     */
    protected function _parseShippingMethodCode($shippingMethod)
    {
        // Remove 'usps_' prefix if present
        $methodCode = preg_replace('/^usps_/', '', $shippingMethod);
        
        // Common mail classes and their patterns (ordered from most specific to least)
        $mailClasses = [
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL',
            'PRIORITY_MAIL_EXPRESS',
            'PRIORITY_MAIL_INTERNATIONAL',
            'PRIORITY_MAIL',
            'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE',
            'FIRST_CLASS_PACKAGE_SERVICE',
            'USPS_GROUND_ADVANTAGE',
            'PARCEL_SELECT',
            'MEDIA_MAIL',
            'LIBRARY_MAIL',
        ];
        
        foreach ($mailClasses as $mailClass) {
            if (strpos($methodCode, $mailClass) === 0) {
                $rateIndicator = substr($methodCode, strlen($mailClass) + 1);
                return [$mailClass, $rateIndicator ?: 'SP'];
            }
        }
        
        // Fallback: assume last segment is rate indicator
        $parts = explode('_', $methodCode);
        if (count($parts) > 1) {
            $rateIndicator = array_pop($parts);
            return [implode('_', $parts), $rateIndicator];
        }
        
        return [$methodCode, 'SP'];
    }

    /**
     * Do shipment request to USPS REST API v3
     *
     * Creates shipping label via POST /labels/v3/label endpoint.
     *
     * @param Varien_Object $request
     * @return Varien_Object Label info with tracking number and image
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $result = new Varien_Object();
        
        // Check if label generation is enabled
        if (!$this->getConfigFlag('enable_labels')) {
            $result->setErrors(Mage::helper('usa')->__('USPS label generation is disabled. Enable it in System > Configuration > Shipping Methods > USPS.'));
            return $result;
        }
        
        // Get OAuth and payment authorization tokens
        $oauthToken = $this->_getOAuthToken();
        if (!$oauthToken) {
            $result->setErrors(Mage::helper('usa')->__('USPS API authentication failed. Check client_id and client_secret.'));
            return $result;
        }
        
        $paymentAuthToken = $this->_getPaymentAuthToken();
        if (!$paymentAuthToken) {
            $result->setErrors(Mage::helper('usa')->__('USPS payment authorization failed. Check CRID, MID, and Account Number configuration.'));
            return $result;
        }
        
        // Build label request payload
        $packageParams = $request->getPackageParams();
        $packageWeight = $request->getPackageWeight();
        
        // Convert weight to pounds if needed
        if ($packageParams && $packageParams->getWeightUnits() && $packageParams->getWeightUnits() != Zend_Measure_Weight::POUND) {
            $packageWeight = Mage::helper('usa')->convertMeasureWeight(
                $packageWeight,
                $packageParams->getWeightUnits(),
                Zend_Measure_Weight::POUND
            );
        }
        
        // Determine if domestic or international
        $recipientUSCountry = $this->_isUSCountry($request->getRecipientAddressCountryCode());
        
        // Parse shipping method to get mail class and rate indicator
        [$mailClass, $rateIndicator] = $this->_parseShippingMethodCode($request->getShippingMethod());
        
        // Build payload
        $payload = [
            'imageInfo' => [
                'imageType' => 'PDF',
                'labelType' => '4X6LABEL',
                'receiptOption' => 'NONE',
                'suppressPostage' => false,
                'suppressMailDate' => false,
                'returnLabel' => false
            ],
            'toAddress' => [
                'firstName' => $request->getRecipientContactPersonFirstName() ?: '',
                'lastName' => $request->getRecipientContactPersonLastName() ?: '',
                'streetAddress' => $request->getRecipientAddressStreet1() ?: '',
                'secondaryAddress' => $request->getRecipientAddressStreet2() ?: '',
                'city' => $request->getRecipientAddressCity() ?: '',
                'state' => $request->getRecipientAddressStateOrProvinceCode() ?: '',
                'ZIPCode' => substr($request->getRecipientAddressPostalCode() ?: '', 0, 5)
            ],
            'fromAddress' => [
                'firstName' => $request->getShipperContactPersonFirstName() ?: '',
                'lastName' => $request->getShipperContactPersonLastName() ?: '',
                'firm' => $request->getShipperContactCompanyName() ?: '',
                'streetAddress' => $request->getShipperAddressStreet1() ?: '',
                'secondaryAddress' => $request->getShipperAddressStreet2() ?: '',
                'city' => $request->getShipperAddressCity() ?: '',
                'state' => $request->getShipperAddressStateOrProvinceCode() ?: '',
                'ZIPCode' => substr($request->getShipperAddressPostalCode() ?: '', 0, 5)
            ],
            'packageDescription' => [
                'mailClass' => $mailClass,
                'rateIndicator' => $rateIndicator,
                'weightUOM' => 'lb',
                'weight' => round($packageWeight, 2),
                'processingCategory' => 'MACHINABLE',
                'mailingDate' => date('Y-m-d'),
                'destinationEntryFacilityType' => 'NONE'
            ]
        ];
        
        // Add dimensions if available
        if ($packageParams && $packageParams->getLength() && $packageParams->getWidth() && $packageParams->getHeight()) {
            $payload['packageDescription']['dimensionsUOM'] = 'in';
            $payload['packageDescription']['length'] = (float) $packageParams->getLength();
            $payload['packageDescription']['width'] = (float) $packageParams->getWidth();
            $payload['packageDescription']['height'] = (float) $packageParams->getHeight();
        }
        
        // For international, add destination country
        if (!$recipientUSCountry) {
            $payload['toAddress']['country'] = $this->_getCountryName($request->getRecipientAddressCountryCode());
        }
        
        $debugData = ['request' => $payload];
        
        // Make API request
        $baseUrl = $this->_getRestGatewayUrl();
        $endpoint = $recipientUSCountry ? 'labels/v3/label' : 'international-labels/v3/label';
        $url = $baseUrl . $endpoint;
        
        $this->_debug(['message' => 'USPS Label API request', 'url' => $url]);
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $oauthToken,
                'X-Payment-Authorization-Token: ' . $paymentAuthToken
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $debugData['response'] = [
            'http_code' => $httpCode,
            'body' => $response
        ];
        
        if ($httpCode !== 200 && $httpCode !== 201) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] 
                ?? $errorData['message'] 
                ?? Mage::helper('usa')->__('Label generation failed (HTTP %s)', $httpCode);
            $this->_debug($debugData);
            $result->setErrors($errorMsg);
            return $result;
        }
        
        $data = json_decode($response, true);
        
        // Handle multipart response (label image may be separate)
        $trackingNumber = $data['trackingNumber'] ?? '';
        $labelContent = '';
        
        // Label image may be base64 encoded in response or in a separate part
        if (isset($data['labelImage'])) {
            $labelContent = base64_decode($data['labelImage']);
        } elseif (isset($data['labelBrokerID'])) {
            // Label available via broker - may need additional fetch
            $this->_debug(['message' => 'Label available via broker', 'brokerID' => $data['labelBrokerID']]);
        }
        
        if (empty($trackingNumber)) {
            $result->setErrors(Mage::helper('usa')->__('No tracking number returned from USPS API'));
            $this->_debug($debugData);
            return $result;
        }
        
        $result->setShippingLabelContent($labelContent);
        $result->setTrackingNumber($trackingNumber);
        $result->setGatewayResponse($data);
        
        $debugData['result'] = [
            'tracking_number' => $trackingNumber,
            'postage' => $data['postage'] ?? 0,
            'zone' => $data['zone'] ?? ''
        ];
        $this->_debug($debugData);
        
        return $result;
    }

    /**
     * Return container types of carrier
     *
     * @return array|bool
     */
    public function getContainerTypes(?Varien_Object $params = null)
    {
        if (is_null($params)) {
            return $this->_getAllowedContainers();
        }

        return $this->_isUSCountry($params->getCountryRecipient()) ? [] : $this->_getAllowedContainers($params);
    }

    /**
     * Return all container types of carrier
     *
     * @return array|bool
     */
    public function getContainerTypesAll()
    {
        return $this->getCode('container');
    }

    /**
     * Return structured data of containers witch related with shipping methods
     *
     * @return array|bool
     */
    public function getContainerTypesFilter()
    {
        return $this->getCode('containers_filter');
    }

    /**
     * Return delivery confirmation types of carrier
     *
     * @return array
     */
    public function getDeliveryConfirmationTypes(?Varien_Object $params = null)
    {
        if ($params == null) {
            return [];
        }

        $countryRecipient = $params->getCountryRecipient();
        if ($this->_isUSCountry($countryRecipient)) {
            return $this->getCode('delivery_confirmation_types');
        } else {
            return [];
        }
    }

    /**
     * Check whether girth is allowed for the USPS
     *
     * @param null|string $countyDest
     * @return bool
     */
    public function isGirthAllowed($countyDest = null)
    {
        return $this->_isUSCountry($countyDest) ? false : true;
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes(Varien_Object $params)
    {
        $countryShipper     = $params->getCountryShipper();
        $countryRecipient   = $params->getCountryRecipient();

        if ($countryShipper == self::USA_COUNTRY_ID
            && $countryRecipient != self::USA_COUNTRY_ID
        ) {
            return [
                'MERCHANDISE' => Mage::helper('usa')->__('Merchandise'),
                'SAMPLE' => Mage::helper('usa')->__('Sample'),
                'GIFT' => Mage::helper('usa')->__('Gift'),
                'DOCUMENTS' => Mage::helper('usa')->__('Documents'),
                'RETURN' => Mage::helper('usa')->__('Return'),
                'OTHER' => Mage::helper('usa')->__('Other'),
            ];
        }

        return [];
    }

    /**
     * Parse zip from string to zip5-zip4
     *
     * @param string $zipString
     * @param bool $returnFull
     * @return array
     */
    protected function _parseZip($zipString, $returnFull = false)
    {
        $zip4 = '';
        $zip5 = '';
        $zip = [$zipString];
        if (preg_match('/[\\d\\w]{5}\\-[\\d\\w]{4}/', $zipString) != 0) {
            $zip = explode('-', $zipString);
        }

        $zipCount = count($zip);
        for ($i = 0; $i < $zipCount; ++$i) {
            if (strlen($zip[$i]) == 5) {
                $zip5 = $zip[$i];
            } elseif (strlen($zip[$i]) == 4) {
                $zip4 = $zip[$i];
            }
        }

        if (empty($zip5) && empty($zip4) && $returnFull) {
            $zip5 = $zipString;
        }

        return [$zip5, $zip4];
    }

    /**
     * @deprecated
     */
    protected function _methodsMapper($method, $valuesToLabels = true)
    {
        return $method;
    }

    /**
     * @deprecated
     */
    public function getMethodLabel($value)
    {
        return $this->_methodsMapper($value, true);
    }

    /**
     * Get value of method by its label
     * @deprecated
     */
    public function getMethodValue($label)
    {
        return $this->_methodsMapper($label, false);
    }

    /**
      * @deprecated
      */
    protected function setTrackingReqeust()
    {
        $this->setTrackingRequest();
    }

    /**
     * Calculate package dimensions from cart items
     *
     * @customization Vitasalus: Dynamic dimension calculation
     *
     * Iterates through cart items to determine package dimensions:
     * - Length/Width: Maximum of all items (largest item determines box size)
     * - Height: Sum of all items (assumes stacking)
     *
     * Falls back to configuration defaults if no product dimensions found.
     *
     * @param Mage_Shipping_Model_Rate_Request $request Shipping rate request
     * @return array Array with keys: height, length, width (in inches)
     */
    protected function _calculatePackageDimensions(Mage_Shipping_Model_Rate_Request $request)
    {
        $maxLength = 0;
        $maxWidth = 0;
        $totalHeight = 0;
        $hasProductDimensions = false;

        // Iterate through cart items
        foreach ($request->getAllItems() as $item) {
            // Skip if item is child of another (configurable, bundle)
            if ($item->getParentItem()) {
                continue;
            }

            $product = $item->getProduct();
            if (!$product) {
                continue;
            }

            // Read dimension attributes from product
            $length = (float) $product->getData('package_length');
            $width = (float) $product->getData('package_width');
            $height = (float) $product->getData('package_height');

            // If product has any dimension, use it
            if ($length > 0 || $width > 0 || $height > 0) {
                $hasProductDimensions = true;

                // Get quantity (accounting for parent-child relationships)
                $qty = $item->getQty();
                if (!$qty) {
                    $qty = $item->getQtyOrdered();
                }
                if (!$qty) {
                    $qty = 1;
                }

                // Max length and width (largest item determines box dimensions)
                if ($length > $maxLength) {
                    $maxLength = $length;
                }
                if ($width > $maxWidth) {
                    $maxWidth = $width;
                }

                // Sum heights (assumes stacking)
                $totalHeight += ($height * $qty);
            }
        }

        // Fall back to configuration if no product dimensions found
        if (!$hasProductDimensions) {
            return [
                'height' => $this->getConfigData('height') ?: 1,
                'length' => $this->getConfigData('length') ?: 6,
                'width'  => $this->getConfigData('width') ?: 4,
            ];
        }

        // Use calculated dimensions, fallback to config for missing values
        return [
            'height' => $totalHeight > 0 ? $totalHeight : ($this->getConfigData('height') ?: 1),
            'length' => $maxLength > 0 ? $maxLength : ($this->getConfigData('length') ?: 6),
            'width'  => $maxWidth > 0 ? $maxWidth : ($this->getConfigData('width') ?: 4),
        ];
    }
    // End @customization
}
