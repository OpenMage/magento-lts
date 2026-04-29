<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * FedEx shipping implementation using the FedEx REST API
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Fedex extends Mage_Usa_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    public const CODE = 'fedex';

    /**
     * Purpose of rate request
     *
     * @var string
     */
    public const RATE_REQUEST_GENERAL = 'general';

    /**
     * Purpose of rate request
     *
     * @var string
     */
    public const RATE_REQUEST_SMARTPOST = 'SMART_POST';

    /**
     * Smart post hub id valid in sandbox
     */
    protected const SANDBOX_SMARTPOST_HUB_ID = '5531';

    /** SOAP-era service code → REST code. */
    private const LEGACY_SERVICE_TYPE_MAP = [
        'INTERNATIONAL_PRIORITY' => 'FEDEX_INTERNATIONAL_PRIORITY',
    ];

    /** Fallback labels for codes absent from the active map (retired or renamed). */
    private const DEPRECATED_METHOD_LABELS = [
        'INTERNATIONAL_PRIORITY'              => 'International Priority',
        'INTERNATIONAL_GROUND'                => 'International Ground',
        'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'Europe First Priority',
        'FEDEX_FREIGHT'                       => 'Freight',
        'FEDEX_NATIONAL_FREIGHT'              => 'National Freight',
    ];

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Rate request data
     *
     * @var ?Mage_Shipping_Model_Rate_Request
     */
    protected $_request = null;

    /**
     * Raw rate request data
     *
     * @var ?Varien_Object
     */
    protected $_rawRequest = null;

    /**
     * Rate result data
     *
     * @var ?Mage_Shipping_Model_Rate_Result
     */
    protected $_result = null;

    /**
     * Raw tracking rate request data
     *
     * @var ?Varien_Object
     */
    protected $_rawTrackingRequest = null;

    /**
     * Tracking result data
     *
     * @var ?Mage_Shipping_Model_Tracking_Result
     */
    protected $_trackingResult;

    /**
     * Container types that could be customized for FedEx carrier
     *
     * @var array
     */
    protected $_customizableContainerTypes = ['YOUR_PACKAGING'];

    /**
     * Collect and get rates
     *
     * @return null|bool|Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag($this->_activeFlag)) {
            return false;
        }

        $this->setRequest($request);

        $this->_getQuotes();

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

        $result = new Varien_Object();

        if ($request->getLimitMethod()) {
            $result->setService($request->getLimitMethod());
        }

        $account = $request->getFedexAccount() ? $request->getFedexAccount() : $this->getConfigData('account');
        $result->setAccount($account);

        $dropoff = $request->getFedexDropoff() ? $request->getFedexDropoff() : $this->getConfigData('dropoff');
        $result->setDropoffType($dropoff);

        $packaging = $request->getFedexPackaging() ? $request->getFedexPackaging() : $this->getConfigData('packaging');
        $result->setPackaging($packaging);

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $request->getStoreId(),
            );
        }

        $result->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigPostcode()) {
            $result->setOrigPostal($request->getOrigPostcode());
        } else {
            $result->setOrigPostal(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $request->getStoreId(),
            ));
        }

        $destCountry = $request->getDestCountryId() ? $request->getDestCountryId() : self::USA_COUNTRY_ID;

        $result->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $result->setDestPostal($request->getDestPostcode());
        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $result->setWeight($weight);
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $result->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $result->setValue($request->getPackagePhysicalValue());
        $result->setValueWithDiscount($request->getPackageValueWithDiscount());

        $result->setUnitOfMeasure($this->getConfigData('unit_of_measure'));
        $result->setResidenceDelivery((bool) $this->getConfigData('residence_delivery'));
        $result->setSmartpostHubid($this->getEffectiveSmartpostHubId());

        $result->setIsReturn($request->getIsReturn());

        $result->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $this->_rawRequest = $result;

        return $this;
    }

    /**
     * Get result of request
     *
     * @return ?Mage_Shipping_Model_Rate_Result
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Get result of tracking request
     *
     * @return ?Mage_Shipping_Model_Tracking_Result
     */
    public function getTrackingResult()
    {
        return $this->_trackingResult;
    }

    /**
     * Do remote request for and handle errors
     *
     * @return bool|Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $this->_result = Mage::getModel('shipping/rate_result');
        $this->_rawRequest ??= new Varien_Object();
        $mapped = $this->_requestRates($this->_buildRatePayload($this->_rawRequest));

        $prepared = $this->_buildRateResult($mapped);
        $this->_result->append($prepared);

        if ($prepared->getError() === true) {
            return $prepared->getError();
        }

        $this->_removeErrorsIfRateExist();

        return $this->_result;
    }

    /**
     * @param  array<string, mixed[]> $ratesRequest
     * @return array<string, mixed>
     */
    protected function _requestRates(array $ratesRequest): array
    {
        $requestString = serialize($ratesRequest);
        $debugData = ['request' => $ratesRequest];

        $cached = $this->isCacheEnabled() ? $this->_getCachedQuotes($requestString) : null;
        if ($cached !== null) {
            $mapped = unserialize($cached, ['allowed_classes' => false]);

            if (is_array($mapped) && $mapped !== []) {
                $debugData['response'] = $mapped;
                $this->_debug($debugData);
                return $mapped;
            }
        }

        try {
            $response = $this->_getRestClient()->getRates($ratesRequest);
            $mapped = $this->_getResponseMapper()->mapRateReply($response);
            if ($this->isCacheEnabled()) {
                $this->_setCachedQuotes($requestString, serialize($mapped));
            }

            $debugData['result'] = $mapped;
        } catch (Throwable $throwable) {
            $mapped = [
                'rates' => [],
                'alerts' => [],
                'errors' => [[
                    'severity' => Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper::SEVERITY_ERROR,
                    'code' => (string) $throwable->getCode(),
                    'message' => $throwable->getMessage(),
                ]],
            ];
            $debugData['result'] = ['error' => $throwable->getMessage(), 'code' => $throwable->getCode()];
            Mage::logException($throwable);
        }

        $this->_debug($debugData);
        return $mapped;
    }

    /**
     * @return array<string, mixed[]>
     */
    protected function _buildRatePayload(Varien_Object $request): array
    {
        return $this->_getRequestBuilder()->buildRatePayload($request, $this->getCurrencyCode());
    }

    /**
     * @param array<string, mixed> $mapped
     */
    protected function _buildRateResult(array $mapped): Mage_Shipping_Model_Rate_Result
    {
        $allowedMethods = array_map(
            static fn(string $method) => self::translateLegacyServiceType(trim($method)),
            explode(',', (string) $this->getConfigData('allowed_methods')),
        );
        $priceArr = [];
        $costArr = [];

        foreach ($this->_selectRatesByPriority($mapped['rates']) as $serviceType => $amount) {
            if (!in_array($serviceType, $allowedMethods, true)) {
                continue;
            }

            $costArr[$serviceType] = $amount;
            $priceArr[$serviceType] = $this->getMethodPrice($amount, $serviceType);
        }

        asort($priceArr);

        $result = Mage::getModel('shipping/rate_result');
        if ($priceArr === []) {
            $errorMessage = $this->_firstErrorMessage($mapped) ?? (string) $this->getConfigData('specificerrmsg');
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorMessage);
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }

        return $result;
    }

    /**
     * @param  float        $cost
     * @param  string       $method
     * @return float|string
     */
    #[Override]
    public function getMethodPrice($cost, $method = '')
    {
        $freeMethod = self::translateLegacyServiceType(
            (string) $this->getConfigData($this->_freeMethod),
        );
        $this->_rawRequest ??= new Varien_Object();

        return $method === $freeMethod
            && $this->getConfigFlag('free_shipping_enable')
            && $this->getConfigData('free_shipping_subtotal') <= $this->_rawRequest->getBaseSubtotalInclTax()
            ? '0.00'
            : $this->getFinalPriceWithHandlingFee($cost);
    }

    /**
     * @param  list<array{service_type:string,rated_type:string,currency:string,amount:float}> $rates
     * @return array<string,float>                                                             service_type => chosen amount
     */
    protected function _selectRatesByPriority(array $rates): array
    {
        // maps to jsonv1 schema RateReplyDetail > RatedShipmentDetail > rateType
        $ratesOrder = [
            //            'PREFERRED_CURRENCY' // unsupported
            'PREFERRED_INCENTIVE',
            'INCENTIVE',
            'PREFERRED',
            'ACCOUNT',
            'ACTUAL',
            'CUSTOM',
            'CURRENT',
            'LIST',
        ];

        $byService = [];
        foreach ($rates as $rate) {
            $byService[$rate['service_type']][$rate['rated_type']] = (float) $rate['amount'];
        }

        $selected = [];
        foreach ($byService as $serviceType => $ratedTypes) {
            foreach ($ratesOrder as $candidate) {
                if (isset($ratedTypes[$candidate])) {
                    $selected[$serviceType] = $ratedTypes[$candidate];
                    break;
                }
            }

            if (!isset($selected[$serviceType])) {
                $selected[$serviceType] = array_first($ratedTypes);
            }
        }

        return $selected;
    }

    /**
     * @param array<array<string, mixed>, mixed> $mapped
     */
    protected function _firstErrorMessage(array $mapped): ?string
    {
        foreach ($mapped['errors'] ?? [] as $error) {
            if (!empty($error['message'])) {
                return (string) $error['message'];
            }
        }

        foreach ($mapped['alerts'] ?? [] as $alert) {
            if (($alert['severity'] ?? '') === Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper::SEVERITY_ERROR
                && !empty($alert['message'])
            ) {
                return (string) $alert['message'];
            }
        }

        return null;
    }

    /**
     * Set free method request
     *
     * @param $freeMethod
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $request = $this->_rawRequest;
        $request->setWeight($this->getTotalNumOfBoxes($request->getFreeMethodWeight()));
        $request->setService($freeMethod);
    }

    /**
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _removeErrorsIfRateExist()
    {
        $rateResultExist = false;
        $rates = [];
        foreach ($this->_result->getAllRates() as $rate) {
            if (!($rate instanceof Mage_Shipping_Model_Rate_Result_Error)) {
                $rateResultExist = true;
                $rates[] = $rate;
            }
        }

        if ($rateResultExist) {
            $this->_result->reset();
            $this->_result->setError(false);
            foreach ($rates as $rate) {
                $this->_result->append($rate);
            }
        }

        return $this->_result;
    }

    /**
     * @param  string             $type
     * @param  string             $code
     * @return array|false|string
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                'FEDEX_1_DAY_FREIGHT'                 => Mage::helper('usa')->__('1 Day Freight'),
                'FEDEX_2_DAY_FREIGHT'                 => Mage::helper('usa')->__('2 Day Freight'),
                'FEDEX_2_DAY'                         => Mage::helper('usa')->__('2 Day'),
                'FEDEX_2_DAY_AM'                      => Mage::helper('usa')->__('2 Day AM'),
                'FEDEX_3_DAY_FREIGHT'                 => Mage::helper('usa')->__('3 Day Freight'),
                'FEDEX_EXPRESS_SAVER'                 => Mage::helper('usa')->__('Express Saver'),
                'FEDEX_GROUND'                        => Mage::helper('usa')->__('Ground'),
                'FIRST_OVERNIGHT'                     => Mage::helper('usa')->__('First Overnight'),
                'GROUND_HOME_DELIVERY'                => Mage::helper('usa')->__('Home Delivery'),
                'INTERNATIONAL_ECONOMY'               => Mage::helper('usa')->__('International Economy'),
                'INTERNATIONAL_ECONOMY_FREIGHT'       => Mage::helper('usa')->__('Intl Economy Freight'),
                'INTERNATIONAL_FIRST'                 => Mage::helper('usa')->__('International First'),
                'FEDEX_INTERNATIONAL_PRIORITY'        => Mage::helper('usa')->__('International Priority'),
                'INTERNATIONAL_PRIORITY_FREIGHT'      => Mage::helper('usa')->__('Intl Priority Freight'),
                'PRIORITY_OVERNIGHT'                  => Mage::helper('usa')->__('Priority Overnight'),
                'SMART_POST'                          => Mage::helper('usa')->__('Smart Post'),
                'STANDARD_OVERNIGHT'                  => Mage::helper('usa')->__('Standard Overnight'),
            ],
            'dropoff' => [
                'REGULAR_PICKUP'          => Mage::helper('usa')->__('Regular Pickup'),
                'REQUEST_COURIER'         => Mage::helper('usa')->__('Request Courier'),
                'DROP_BOX'                => Mage::helper('usa')->__('Drop Box'),
                'BUSINESS_SERVICE_CENTER' => Mage::helper('usa')->__('Business Service Center'),
                'STATION'                 => Mage::helper('usa')->__('Station'),
            ],
            'packaging' => [
                'FEDEX_ENVELOPE' => Mage::helper('usa')->__('FedEx Envelope'),
                'FEDEX_PAK'      => Mage::helper('usa')->__('FedEx Pak'),
                'FEDEX_BOX'      => Mage::helper('usa')->__('FedEx Box'),
                'FEDEX_TUBE'     => Mage::helper('usa')->__('FedEx Tube'),
                'FEDEX_10KG_BOX' => Mage::helper('usa')->__('FedEx 10kg Box'),
                'FEDEX_25KG_BOX' => Mage::helper('usa')->__('FedEx 25kg Box'),
                'YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging'),
            ],
            'containers_filter' => [
                [
                    'containers' => ['FEDEX_ENVELOPE', 'FEDEX_PAK'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'FEDEX_INTERNATIONAL_PRIORITY',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_BOX', 'FEDEX_TUBE'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'FEDEX_INTERNATIONAL_PRIORITY',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'],
                    'filters'    => [
                        'within_us' => [],
                        'from_us' => ['method' => ['FEDEX_INTERNATIONAL_PRIORITY']],
                    ],
                ],
                [
                    'containers' => ['YOUR_PACKAGING'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_GROUND',
                                'GROUND_HOME_DELIVERY',
                                'SMART_POST',
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'FEDEX_INTERNATIONAL_PRIORITY',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'INTERNATIONAL_ECONOMY_FREIGHT',
                                'INTERNATIONAL_PRIORITY_FREIGHT',
                            ],
                        ],
                    ],
                ],
            ],

            'delivery_confirmation_types' => [
                'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
                'ADULT'                 => Mage::helper('usa')->__('Adult'),
                'DIRECT'                => Mage::helper('usa')->__('Direct'),
                'INDIRECT'              => Mage::helper('usa')->__('Indirect'),
            ],

            'unit_of_measure' => [
                Mage_Usa_Model_Shipping_Carrier_Fedex_Unitofmeasure::WEIGHT_POUND   =>  Mage::helper('usa')->__('Pounds'),
                Mage_Usa_Model_Shipping_Carrier_Fedex_Unitofmeasure::WEIGHT_KILOGRAM   =>  Mage::helper('usa')->__('Kilograms'),
            ],
        ];

        if (!isset($codes[$type])) {
            return false;
        }

        if ($code === '') {
            return $codes[$type];
        }

        if (isset($codes[$type][$code])) {
            return $codes[$type][$code];
        }

        if ($type === 'method' && isset(self::DEPRECATED_METHOD_LABELS[$code])) {
            return Mage::helper('usa')->__(self::DEPRECATED_METHOD_LABELS[$code]);
        }

        return false;
    }

    public static function translateLegacyServiceType(string $code): string
    {
        return self::LEGACY_SERVICE_TYPE_MAP[$code] ?? $code;
    }

    /**
     *  Return FedEx currency ISO code by Magento Base Currency Code
     *
     * @return string 3-digit currency code
     */
    public function getCurrencyCode()
    {
        $codes = [
            'DOP' => 'RDD', // Dominican Peso
            'XCD' => 'ECD', // Caribbean Dollars
            'ARS' => 'ARN', // Argentina Peso
            'SGD' => 'SID', // Singapore Dollars
            'KRW' => 'WON', // South Korea Won
            'JMD' => 'JAD', // Jamaican Dollars
            'CHF' => 'SFR', // Swiss Francs
            'JPY' => 'JYE', // Japanese Yen
            'KWD' => 'KUD', // Kuwaiti Dinars
            'GBP' => 'UKL', // British Pounds
            'AED' => 'DHS', // UAE Dirhams
            'MXN' => 'NMP', // Mexican Pesos
            'UYU' => 'UYP', // Uruguay New Pesos
            'CLP' => 'CHP', // Chilean Pesos
            'TWD' => 'NTD', // New Taiwan Dollars
        ];
        $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        return $codes[$currencyCode] ?? $currencyCode;
    }

    /**
     * Get tracking
     *
     * @param  mixed                                    $trackings
     * @return null|Mage_Shipping_Model_Tracking_Result
     */
    public function getTracking($trackings)
    {
        $this->setTrackingRequest();

        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        foreach ($trackings as $tracking) {
            $this->_requestTracking((string) $tracking);
        }

        return $this->_trackingResult;
    }

    /**
     * Set tracking request
     */
    protected function setTrackingRequest()
    {
        $request = new Varien_Object();

        $account = $this->getConfigData('account');
        $request->setAccount($account);

        $this->_rawTrackingRequest = $request;
    }

    protected function _requestTracking(string $trackingNumber): void
    {
        $payload = $this->_getRequestBuilder()->buildTrackingPayload($trackingNumber);
        $debugData = ['request' => $payload];

        try {
            $response = $this->_getTrackingRestClient()->track($payload);
            $mapped = $this->_getResponseMapper()->mapTrackReply($response, $trackingNumber);
            $debugData['result'] = $mapped;
        } catch (Throwable $throwable) {
            $debugData['result'] = ['error' => $throwable->getMessage(), 'code' => $throwable->getCode()];
            Mage::logException($throwable);
            $mapped = null;
        }

        $this->_debug($debugData);

        if (!$this->_trackingResult) {
            $this->_trackingResult = Mage::getModel('shipping/tracking_result');
        }

        if ($mapped !== null && !empty($mapped['status'])) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier($this->_code);
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingNumber);
            $tracking->addData($this->_trackingStatusData($mapped));
            $this->_trackingResult->append($tracking);
        } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingNumber);
            $errorMessage = $mapped !== null
                ? $this->_firstErrorMessage($mapped) ?? Mage::helper('usa')->__('Unable to retrieve tracking')
                : Mage::helper('usa')->__('Unable to retrieve tracking');
            $error->setErrorMessage($errorMessage);
            $this->_trackingResult->append($error);
        }
    }

    /**
     * @param  array<array<string, mixed>, mixed> $mapped
     * @return array<string, mixed>
     */
    protected function _trackingStatusData(array $mapped): array
    {
        $fields = [
            'status',
            'service',
            'deliverydate',
            'deliverytime',
            'deliverylocation',
            'shippeddate',
            'signedby',
            'weight',
            'progressdetail',
        ];

        $data = [];
        foreach ($fields as $field) {
            if (isset($mapped[$field])) {
                $data[$field] = $mapped[$field];
            }
        }

        return $data;
    }

    /**
     * Get tracking response
     *
     * @return string
     */
    public function getResponse()
    {
        $statuses = '';
        if (($this->_trackingResult instanceof Mage_Shipping_Model_Tracking_Result)
            && ($trackings = $this->_trackingResult->getAllTrackings())) {
            foreach ($trackings as $tracking) {
                if ($data = $tracking->getAllData()) {
                    if (isset($data['status']) && ((string) $data['status'] !== '')) {
                        $statuses .= Mage::helper('usa')->__((string) $data['status']) . "\n<br/>";
                    } else {
                        $statuses .= Mage::helper('usa')->__('Empty response') . "\n<br/>";
                    }
                }
            }
        }

        if ($statuses === '') {
            return Mage::helper('usa')->__('Empty response');
        }

        return $statuses;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowed = explode(',', (string) $this->getConfigData('allowed_methods'));
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }

        return $arr;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @return Varien_Object
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $result = new Varien_Object();

        $storeCountryCode = (string) Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $request->getStoreId(),
        );
        $payload = $this->_getRequestBuilder()->buildShipmentPayload(
            $request,
            (string) $this->getConfigData('dropoff'),
            (string) $this->getConfigData('account'),
            $storeCountryCode,
            $this->getEffectiveSmartpostHubId(),
        );

        $debugData = ['request' => $payload];

        try {
            $response = $this->_getRestClient()->processShipment($payload);
            $mapped = $this->_getResponseMapper()->mapShipReply($response);
            $debugData['result'] = $mapped;
        } catch (Throwable $throwable) {
            $mapped = null;
            $debugData['result'] = ['error' => $throwable->getMessage(), 'code' => $throwable->getCode()];
            Mage::logException($throwable);
        }

        $this->_debug($debugData);

        $hasErrors = isset($mapped['errors']) && is_array($mapped['errors']) && $mapped['errors'] !== [];
        $hasTracking = isset($mapped['tracking_number']) && $mapped['tracking_number'] !== '';
        if ($hasTracking && !$hasErrors) {
            $result->setTrackingNumber($mapped['tracking_number']);
            if (isset($mapped['label_content']) && $mapped['label_content'] !== '') {
                $result->setShippingLabelContent($mapped['label_content']);
            }
        } else {
            $errorMessage = $mapped !== null
                ? $this->_firstErrorMessage($mapped) ?? Mage::helper('usa')->__('FedEx shipment request failed')
                : Mage::helper('usa')->__('FedEx shipment request failed');
            $result->setErrors($errorMessage);
        }

        return $result;
    }

    /**
     * For multi package shipments. Delete requested shipments if the current shipment
     * request is failed
     *
     * This method historically has always returned true, so any errors cancelling a shipment get swallowed. Now at
     * least the error and stacktrace are emitted to the exception log
     *
     * @param  array $data
     * @return bool
     */
    #[Override]
    public function rollBack($data)
    {
        $accountNumber = (string) $this->getConfigData('account');
        foreach ((array) $data as $item) {
            if (!isset($item['tracking_number'])) {
                continue;
            }

            if ((string) $item['tracking_number'] === '') {
                continue;
            }

            $payload = $this->_getRequestBuilder()->buildCancelShipmentPayload(
                $accountNumber,
                (string) $item['tracking_number'],
            );

            try {
                $this->_getRestClient()->deleteShipment($payload);
            } catch (Throwable $e) {
                Mage::logException($e);
            }
        }

        return true;
    }

    /**
     * Return container types of carrier
     *
     * @return array|bool
     */
    #[Override]
    public function getContainerTypes(?Varien_Object $params = null)
    {
        if (!$params instanceof Varien_Object) {
            return $this->_getAllowedContainers($params);
        }

        $method             = $params->getMethod();
        $countryShipper     = $params->getCountryShipper();
        $countryRecipient   = $params->getCountryRecipient();
        if ((($countryShipper === self::USA_COUNTRY_ID && $countryRecipient === self::CANADA_COUNTRY_ID)
            || ($countryShipper === self::CANADA_COUNTRY_ID && $countryRecipient === self::USA_COUNTRY_ID))
            && $method === 'FEDEX_GROUND') {
            return ['YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging')];
        }

        if ($method === 'INTERNATIONAL_ECONOMY' || $method === 'INTERNATIONAL_FIRST') {
            $allTypes = $this->getContainerTypesAll();
            $exclude = ['FEDEX_10KG_BOX' => '', 'FEDEX_25KG_BOX' => ''];
            return array_diff_key($allTypes, $exclude);
        }

        if ($countryShipper === self::CANADA_COUNTRY_ID && $countryRecipient === self::CANADA_COUNTRY_ID) {
            // hack for Canada domestic. Apply the same filter rules as for US domestic
            $params->setCountryShipper(self::USA_COUNTRY_ID);
            $params->setCountryRecipient(self::USA_COUNTRY_ID);
        }

        return $this->_getAllowedContainers($params);
    }

    /**
     * Return all container types of carrier
     *
     * @return array|bool
     */
    public function getContainerTypesAll()
    {
        $packaging = $this->getCode('packaging');
        return is_array($packaging) ? $packaging : false;
    }

    /**
     * Return structured data of containers witch related with shipping methods
     *
     * @return array|bool
     */
    public function getContainerTypesFilter()
    {
        $filter = $this->getCode('containers_filter');
        return is_array($filter) ? $filter : false;
    }

    /**
     * Return delivery confirmation types of carrier
     *
     * @return array
     */
    #[Override]
    public function getDeliveryConfirmationTypes(?Varien_Object $params = null)
    {
        $types = $this->getCode('delivery_confirmation_types');
        return is_array($types) ? $types : [];
    }

    public function isCacheEnabled(): bool
    {
        $flag = $this->getData('cache_enabled');
        return $flag === null || (bool) $flag;
    }

    protected function _getRestClient(): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client
    {
        $client = $this->getData('rest_client');
        if ($client instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client) {
            return $client;
        }

        $client = $this->_createRestClient(
            (string) $this->getConfigData('client_id'),
            (string) $this->getConfigData('client_secret'),
            $this->getConfigFlag('sandbox_mode'),
        );
        $this->setData('rest_client', $client);

        return $client;
    }

    /**
     * @throws Mage_Core_Exception
     */
    protected function _getTrackingRestClient(): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client
    {
        $client = $this->getData('tracking_rest_client');
        if ($client instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client) {
            return $client;
        }

        $clientId = (string) $this->getConfigData('tracking_client_id');
        $clientSecret = (string) $this->getConfigData('tracking_client_secret');
        $missing = [];
        if ($clientId === '') {
            $missing[] = 'tracking_client_id';
        }

        if ($clientSecret === '') {
            $missing[] = 'tracking_client_secret';
        }

        if ($missing !== []) {
            Mage::throwException(sprintf(
                'FedEx Track API credentials are not configured (missing: %s). FedEx apps are per-API; register a separate developer.fedex.com app for Track and populate both fields.',
                implode(', ', $missing),
            ));
        }

        $client = $this->_createRestClient($clientId, $clientSecret, $this->getConfigFlag('sandbox_mode'));
        $this->setData('tracking_rest_client', $client);

        return $client;
    }

    protected function _createRestClient(
        string $clientId,
        string $clientSecret,
        bool $sandboxMode
    ): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client {
        $factory = $this->getData('rest_client_factory');
        if (!$factory instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ClientfactoryInterface) {
            $factory = Mage::getModel('usa/shipping_carrier_fedex_rest_clientfactory');
            $this->setData('rest_client_factory', $factory);
        }

        return $factory->create($clientId, $clientSecret, $sandboxMode);
    }

    protected function _getRequestBuilder(): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder
    {
        $builder = $this->getData('request_builder');
        if ($builder instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder) {
            return $builder;
        }

        $builder = Mage::getModel('usa/shipping_carrier_fedex_rest_requestbuilder');
        $this->setData('request_builder', $builder);

        return $builder;
    }

    protected function _getResponseMapper(): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper
    {
        $mapper = $this->getData('response_mapper');
        if ($mapper instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper) {
            return $mapper;
        }

        $mapper = Mage::getModel('usa/shipping_carrier_fedex_rest_responsemapper');
        $this->setData('response_mapper', $mapper);

        return $mapper;
    }

    protected function getEffectiveSmartpostHubId(): string
    {
        if ($this->getConfigFlag('sandbox_mode')) {
            return self::SANDBOX_SMARTPOST_HUB_ID;
        }

        return (string) $this->getConfigData('smartpost_hubid');
    }
}
