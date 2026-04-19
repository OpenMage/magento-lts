<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * FedEx shipping implementation using the FedEx REST API.
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
    private const SANDBOX_SMARTPOST_HUB_ID = '5531';

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Rate request data
     *
     * @var null|Mage_Shipping_Model_Rate_Request
     */
    protected $_request = null;

    /**
     * Raw rate request data
     *
     * @var null|Varien_Object
     */
    protected $_rawRequest = null;

    /**
     * Rate / tracking result data
     * @var null|Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Tracking_Result
     */
    protected $_result = null;

    /**
     * Raw tracking rate request data
     *
     * @var null|Varien_Object
     */
    protected $_rawTrackingRequest = null;

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

        $r = new Varien_Object();

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }

        $r->setAccount($request->getFedexAccount() ?: $this->getConfigData('account'));
        $r->setDropoffType($request->getFedexDropoff() ?: $this->getConfigData('dropoff'));
        $r->setPackaging($request->getFedexPackaging() ?: $this->getConfigData('packaging'));

        $origCountry = $request->getOrigCountry() ?: Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $request->getStoreId(),
        );
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $request->getStoreId(),
            ));
        }

        $destCountry = $request->getDestCountryId() ?: self::USA_COUNTRY_ID;
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        $r->setWeight($this->getTotalNumOfBoxes($request->getPackageWeight()));
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackagePhysicalValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $r->setUnitOfMeasure($this->getConfigData('unit_of_measure'));
        $r->setResidenceDelivery((bool) $this->getConfigData('residence_delivery'));
        $r->setSmartpostHubid($this->getEffectiveSmartpostHubId());

        $r->setIsReturn($request->getIsReturn());

        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $this->_rawRequest = $r;

        return $this;
    }

    /**
     * Get result of request
     *
     * @return Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Tracking_Result
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Get version of rates request
     *
     * @return array
     */
    public function getVersionInfo()
    {
        return [
            'ServiceId'    => 'crs',
            'Major'        => '10',
            'Intermediate' => '0',
            'Minor'        => '0',
        ];
    }

    /**
     * Do remote request for and handle errors
     *
     * @return bool|Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $this->_result = Mage::getModel('shipping/rate_result');

        $allowedMethods = explode(',', (string) $this->getConfigData('allowed_methods'));
        $destCountry = (string) $this->_request->getDestCountryId();
        if (!in_array(self::RATE_REQUEST_SMARTPOST, $allowedMethods, true)
            || !$this->_isUSCountry($destCountry)
        ) {
            $this->_rawRequest->setSmartpostHubid('');
        }

        $mapped = $this->_requestRates();
        $prepared = $this->_buildRateResult($mapped);
        if ($prepared->getError()) {
            return $prepared->getError();
        }

        $this->_result->append($prepared);
        $this->_removeErrorsIfRateExist();

        return $this->_result;
    }

    protected function _requestRates(): array
    {
        $payload = $this->_buildRatePayload();
        $requestString = serialize($payload);
        $cached = $this->isCacheEnabled() ? $this->_getCachedQuotes($requestString) : null;
        if ($cached !== null) {
            return unserialize($cached);
        }

        $debugData = ['request' => $payload];
        try {
            $response = $this->_getRestClient()->getRates($payload);
            $mapped = $this->_getResponseMapper()->mapRateReply($response);
            if ($this->isCacheEnabled()) {
                $this->_setCachedQuotes($requestString, serialize($mapped));
            }

            $debugData['result'] = $mapped;
        } catch (Throwable $e) {
            $mapped = [
                'rates' => [],
                'alerts' => [],
                'errors' => [[
                    'severity' => Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper::SEVERITY_ERROR,
                    'code' => (string) $e->getCode(),
                    'message' => $e->getMessage(),
                ]],
            ];
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            Mage::logException($e);
        }

        $this->_debug($debugData);
        return $mapped;
    }

    protected function _buildRatePayload(): array
    {
        return $this->_getRequestBuilder()->buildRatePayload(
            $this->_rawRequest,
            $this->getCurrencyCode(),
        );
    }

    protected function _buildRateResult(array $mapped): Mage_Shipping_Model_Rate_Result
    {
        $allowedMethods = explode(',', (string) $this->getConfigData('allowed_methods'));
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
        if (empty($priceArr)) {
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
     * @param  list<array{service_type:string,rated_type:string,currency:string,amount:float}> $rates
     * @return array<string,float>                                                             service_type => chosen amount
     */
    protected function _selectRatesByPriority(array $rates): array
    {
        $ratesOrder = [
            'RATED_ACCOUNT_PACKAGE',
            'PAYOR_ACCOUNT_PACKAGE',
            'RATED_ACCOUNT_SHIPMENT',
            'PAYOR_ACCOUNT_SHIPMENT',
            'ACCOUNT',
            'RATED_LIST_PACKAGE',
            'PAYOR_LIST_PACKAGE',
            'RATED_LIST_SHIPMENT',
            'PAYOR_LIST_SHIPMENT',
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

            if (!isset($selected[$serviceType]) && $ratedTypes) {
                $selected[$serviceType] = (float) reset($ratedTypes);
            }
        }

        return $selected;
    }

    protected function _firstErrorMessage(array $mapped): ?string
    {
        foreach ($mapped['errors'] ?? [] as $error) {
            if (!empty($error['message'])) {
                return (string) $error['message'];
            }
        }

        foreach ($mapped['alerts'] ?? [] as $alert) {
            if (($alert['severity'] ?? '') === Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper::SEVERITY_ERROR
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
        $r = $this->_rawRequest;
        $r->setWeight($this->getTotalNumOfBoxes($r->getFreeMethodWeight()));
        $r->setService($freeMethod);
    }

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

    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => Mage::helper('usa')->__('Europe First Priority'),
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
                'INTERNATIONAL_GROUND'                => Mage::helper('usa')->__('International Ground'),
                'INTERNATIONAL_PRIORITY'              => Mage::helper('usa')->__('International Priority'),
                'INTERNATIONAL_PRIORITY_FREIGHT'      => Mage::helper('usa')->__('Intl Priority Freight'),
                'PRIORITY_OVERNIGHT'                  => Mage::helper('usa')->__('Priority Overnight'),
                'SMART_POST'                          => Mage::helper('usa')->__('Smart Post'),
                'STANDARD_OVERNIGHT'                  => Mage::helper('usa')->__('Standard Overnight'),
                'FEDEX_FREIGHT'                       => Mage::helper('usa')->__('Freight'),
                'FEDEX_NATIONAL_FREIGHT'              => Mage::helper('usa')->__('National Freight'),
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
                                'INTERNATIONAL_PRIORITY',
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
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'],
                    'filters'    => [
                        'within_us' => [],
                        'from_us' => ['method' => ['INTERNATIONAL_PRIORITY']],
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
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                                'INTERNATIONAL_GROUND',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
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
                Mage_Usa_Model_Shipping_Carrier_Fedex_UnitOfMeasure::WEIGHT_POUND   =>  Mage::helper('usa')->__('Pounds'),
                Mage_Usa_Model_Shipping_Carrier_Fedex_UnitOfMeasure::WEIGHT_KILOGRAM   =>  Mage::helper('usa')->__('Kilograms'),
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
     *  Return FeDex currency ISO code by Magento Base Currency Code
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
     * @param  mixed                                                                    $trackings
     * @return null|Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Tracking_Result
     */
    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        foreach ($trackings as $tracking) {
            $this->_requestTracking((string) $tracking);
        }

        return $this->_result;
    }

    /**
     * Set tracking request
     */
    protected function setTrackingReqeust()
    {
        $r = new Varien_Object();

        $account = $this->getConfigData('account');
        $r->setAccount($account);

        $this->_rawTrackingRequest = $r;
    }

    protected function _requestTracking(string $trackingNumber): void
    {
        $payload = $this->_getRequestBuilder()->buildTrackingPayload($trackingNumber);
        $debugData = ['request' => $payload];

        try {
            $response = $this->_getTrackingRestClient()->track($payload);
            $mapped = $this->_getResponseMapper()->mapTrackReply($response, $trackingNumber);
            $debugData['result'] = $mapped;
        } catch (Throwable $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            Mage::logException($e);
            $mapped = null;
        }

        $this->_debug($debugData);

        if (!$this->_result) {
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if ($mapped !== null && !empty($mapped['status'])) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier($this->_code);
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingNumber);
            $tracking->addData($this->_trackingStatusData($mapped));
            $this->_result->append($tracking);
        } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingNumber);
            $errorMessage = $mapped !== null
                ? $this->_firstErrorMessage($mapped) ?? Mage::helper('usa')->__('Unable to retrieve tracking')
                : Mage::helper('usa')->__('Unable to retrieve tracking');
            $error->setErrorMessage($errorMessage);
            $this->_result->append($error);
        }
    }

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
            if (!empty($mapped[$field])) {
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
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result) {
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking) {
                    if ($data = $tracking->getAllData()) {
                        if (!empty($data['status'])) {
                            $statuses .= Mage::helper('usa')->__((string) $data['status']) . "\n<br/>";
                        } else {
                            $statuses .= Mage::helper('usa')->__('Empty response') . "\n<br/>";
                        }
                    }
                }
            }
        }

        if (empty($statuses)) {
            $statuses = Mage::helper('usa')->__('Empty response');
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
        );

        $debugData = ['request' => $payload];

        try {
            $response = $this->_getRestClient()->processShipment($payload);
            $mapped = $this->_getResponseMapper()->mapShipReply($response);
            $debugData['result'] = $mapped;
        } catch (Throwable $e) {
            $mapped = null;
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            Mage::logException($e);
        }

        $this->_debug($debugData);

        if ($mapped !== null && !empty($mapped['tracking_number']) && empty($mapped['errors'])) {
            $result->setTrackingNumber($mapped['tracking_number']);
            $result->setShippingLabelContent($mapped['label_content']);
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
     * @param  array $data
     * @return bool
     */
    public function rollBack($data)
    {
        $accountNumber = (string) $this->getConfigData('account');
        foreach ((array) $data as $item) {
            if (empty($item['tracking_number'])) {
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
    public function getContainerTypes(?Varien_Object $params = null)
    {
        if ($params === null) {
            return $this->_getAllowedContainers($params);
        }

        $method = $params->getMethod();
        $countryShipper = $params->getCountryShipper();
        $countryRecipient = $params->getCountryRecipient();

        if (($countryShipper == self::USA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID
             || $countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::USA_COUNTRY_ID)
            && $method == 'FEDEX_GROUND'
        ) {
            return ['YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging')];
        } elseif ($method == 'INTERNATIONAL_ECONOMY' || $method == 'INTERNATIONAL_FIRST') {
            return array_diff_key($this->getContainerTypesAll(), ['FEDEX_10KG_BOX' => '', 'FEDEX_25KG_BOX' => '']);
        } elseif ($method == 'EUROPE_FIRST_INTERNATIONAL_PRIORITY') {
            return array_diff_key($this->getContainerTypesAll(), ['FEDEX_BOX' => '', 'FEDEX_TUBE' => '']);
        } elseif ($countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID) {
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
        return $this->getCode('packaging');
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
        return $this->getCode('delivery_confirmation_types');
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

    protected function _createRestClient(string $clientId, string $clientSecret, bool $sandboxMode): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client
    {
        $factory = $this->getData('rest_client_factory');
        if ($factory instanceof Closure) {
            return $factory($clientId, $clientSecret, $sandboxMode);
        }

        return new Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client($clientId, $clientSecret, $sandboxMode);
    }

    protected function _getRequestBuilder(): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_RequestBuilder
    {
        $builder = $this->getData('request_builder');
        if ($builder instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_RequestBuilder) {
            return $builder;
        }

        $builder = new Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_RequestBuilder();
        $this->setData('request_builder', $builder);

        return $builder;
    }

    protected function _getResponseMapper(): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper
    {
        $mapper = $this->getData('response_mapper');
        if ($mapper instanceof Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper) {
            return $mapper;
        }

        $mapper = new Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper();
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
