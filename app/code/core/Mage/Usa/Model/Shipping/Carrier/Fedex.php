<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex shipping implementation
 *
 * @category   Mage
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
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

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
     * Rate result data
     *
     * @var Mage_Shipping_Model_Rate_Result|null
     */
    protected $_result = null;

    /**
     * Path to wsdl file of rate service
     *
     * @var string
     */
    protected $_rateServiceWsdl;

    /**
     * Path to wsdl file of ship service
     *
     * @var string
     */
    protected $_shipServiceWsdl = null;

    /**
     * Path to wsdl file of track service
     *
     * @var string
     */
    protected $_trackServiceWsdl = null;

    /**
     * Container types that could be customized for FedEx carrier
     *
     * @var array
     */
    protected $_customizableContainerTypes = ['YOUR_PACKAGING'];

    public function __construct()
    {
        parent::__construct();
        $wsdlBasePath = Mage::getModuleDir('etc', 'Mage_Usa') . DS . 'wsdl' . DS . 'FedEx' . DS;
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v10.wsdl';
        $this->_rateServiceWsdl = $wsdlBasePath . 'RateService_v10.wsdl';
        $this->_trackServiceWsdl = $wsdlBasePath . 'TrackService_v5.wsdl';
    }

    /**
     * Create soap client with selected wsdl
     *
     * @param string $wsdl
     * @param bool|int $trace
     * @return SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = new SoapClient($wsdl, ['trace' => $trace]);
        $client->__setLocation(
            $this->getConfigFlag('sandbox_mode')
            ? 'https://wsbeta.fedex.com:443/web-services'
            : 'https://ws.fedex.com:443/web-services'
        );

        return $client;
    }

    /**
     * Create rate soap client
     *
     * @return SoapClient
     */
    protected function _createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }

    /**
     * Create ship soap client
     *
     * @return SoapClient
     */
    protected function _createShipSoapClient()
    {
        return $this->_createSoapClient($this->_shipServiceWsdl, 1);
    }

    /**
     * Create track soap client
     *
     * @return SoapClient
     */
    protected function _createTrackSoapClient()
    {
        return $this->_createSoapClient($this->_trackServiceWsdl, 1);
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

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setAccount($account);

        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $request->getStoreId()
            );
        }
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $request->getStoreId()
            ));
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackagePhysicalValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $r->setMeterNumber($this->getConfigData('meter_number'));
        $r->setKey($this->getConfigData('key'));
        $r->setPassword($this->getConfigData('password'));

        $r->setIsReturn($request->getIsReturn());

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
            'Minor'        => '0'
        ];
    }

    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
        $r = $this->_rawRequest;
        $ratesRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => [
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                ]
            ],
            'ClientDetail' => [
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ],
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => [
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => [
                    'Amount'  => $r->getValue(),
                    'Currency' => $this->getCurrencyCode()
                ],
                'Shipper' => [
                    'Address' => [
                        'PostalCode'  => $r->getOrigPostal(),
                        'CountryCode' => $r->getOrigCountry()
                    ]
                ],
                'Recipient' => [
                    'Address' => [
                        'PostalCode'  => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery')
                    ]
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => [
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $r->getOrigCountry()
                    ]
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => [
                        'Amount' => $r->getValue(),
                        'Currency' => $this->getCurrencyCode()
                    ]
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount'     => '1',
                'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => [
                    '0' => [
                        'Weight' => [
                            'Value' => (float)$r->getWeight(),
                            'Units' => $this->getConfigData('unit_of_measure')
                        ],
                        'GroupPackageCount' => 1,
                    ]
                ]
            ]
        ];

        if ($purpose == self::RATE_REQUEST_GENERAL) {
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = [
                'Amount'  => $r->getValue(),
                'Currency' => $this->getCurrencyCode()
            ];
        } elseif ($purpose == self::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = [
                'Indicia' => ((float)$r->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid')
            ];
        }

        return $ratesRequest;
    }

    /**
     * Makes remote request to the carrier and returns a response
     *
     * @param string $purpose
     * @return mixed
     */
    protected function _doRatesRequest($purpose)
    {
        $ratesRequest = $this->_formRateRequest($purpose);
        $requestString = serialize($ratesRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = ['request' => $ratesRequest];
        if ($response === null) {
            try {
                $client = $this->_createRateSoapClient();
                $response = $client->getRates($ratesRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
        $this->_debug($debugData);
        return $response;
    }

    /**
     * Do remote request for and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $this->_result = Mage::getModel('shipping/rate_result');
        $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));
        if (in_array(self::RATE_REQUEST_SMARTPOST, $allowedMethods)) {
            $response = $this->_doRatesRequest(self::RATE_REQUEST_SMARTPOST);
            $preparedSmartpost = $this->_prepareRateResponse($response);
            $this->_result->append($preparedSmartpost);
        }
        $response = $this->_doRatesRequest(self::RATE_REQUEST_GENERAL);
        $preparedGeneral = $this->_prepareRateResponse($response);
        if ($this->_result->getError() && $preparedGeneral->getError()) {
            return $this->_result->getError();
        }
        $this->_result->append($preparedGeneral);
        $this->_removeErrorsIfRateExist();

        return $this->_result;
    }

    /**
     * Remove Errors in Case When Rate Exist
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _removeErrorsIfRateExist()
    {
        $rateResultExist = false;
        $rates           = [];
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
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _prepareRateResponse($response)
    {
        $costArr = [];
        $priceArr = [];
        $errorTitle = 'Unable to retrieve tracking';

        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                if (is_array($response->Notifications)) {
                    $notification = array_pop($response->Notifications);
                    $errorTitle = (string)$notification->Message;
                } else {
                    $errorTitle = (string)$response->Notifications->Message;
                }
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));

                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $serviceName = (string)$rate->ServiceType;
                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRateAmountOriginBased($rate);
                            $costArr[$serviceName]  = $amount;
                            $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                        }
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;
                    $serviceName = (string)$rate->ServiceType;
                    if (in_array($serviceName, $allowedMethods)) {
                        $amount = $this->_getRateAmountOriginBased($rate);
                        $costArr[$serviceName]  = $amount;
                        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                    }
                }
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
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
     * Get origin based amount form response of rate estimation
     *
     * @param stdClass $rate
     * @return null|float
     */
    protected function _getRateAmountOriginBased($rate)
    {
        $amount = null;
        $rateTypeAmounts = [];

        if (is_object($rate)) {
            // The "RATED..." rates are expressed in the currency of the origin country
            foreach ($rate->RatedShipmentDetails as $ratedShipmentDetail) {
                $netAmount = (string)$ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                $rateType = (string)$ratedShipmentDetail->ShipmentRateDetail->RateType;
                $rateTypeAmounts[$rateType] = $netAmount;
            }

            // Order is important
            $ratesOrder = [
                'RATED_ACCOUNT_PACKAGE',
                'PAYOR_ACCOUNT_PACKAGE',
                'RATED_ACCOUNT_SHIPMENT',
                'PAYOR_ACCOUNT_SHIPMENT',
                'RATED_LIST_PACKAGE',
                'PAYOR_LIST_PACKAGE',
                'RATED_LIST_SHIPMENT',
                'PAYOR_LIST_SHIPMENT'
            ];
            foreach ($ratesOrder as $rateType) {
                if (!empty($rateTypeAmounts[$rateType])) {
                    $amount = $rateTypeAmounts[$rateType];
                    break;
                }
            }

            if (is_null($amount)) {
                $amount = (string)$rate->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
            }
        }

        return $amount;
    }

    /**
     * Set free method request
     *
     * @param  $freeMethod
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;
        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $r->setWeight($weight);
        $r->setService($freeMethod);
    }

    /**
     * Get xml quotes
     *
     * @deprecated
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;
        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><FDXRateAvailableServicesRequest/>');

        $xml->addAttribute('xmlns:api', 'http://www.fedex.com/fsmapi');
        $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->addAttribute('xsi:noNamespaceSchemaLocation', 'FDXRateAvailableServicesRequest.xsd');

        $requestHeader = $xml->addChild('RequestHeader');
        $requestHeader->addChild('AccountNumber', $r->getAccount());
        $requestHeader->addChild('MeterNumber', '0');

        $xml->addChild('ShipDate', date('Y-m-d'));
        $xml->addChild('DropoffType', $r->getDropoffType());
        if ($r->hasService()) {
            $xml->addChild('Service', $r->getService());
        }
        $xml->addChild('Packaging', $r->getPackaging());
        $xml->addChild('WeightUnits', 'LBS');
        $xml->addChild('Weight', $r->getWeight());

        $originAddress = $xml->addChild('OriginAddress');
        $originAddress->addChild('PostalCode', $r->getOrigPostal());
        $originAddress->addChild('CountryCode', $r->getOrigCountry());

        $destinationAddress = $xml->addChild('DestinationAddress');
        $destinationAddress->addChild('PostalCode', $r->getDestPostal());
        $destinationAddress->addChild('CountryCode', $r->getDestCountry());

        $payment = $xml->addChild('Payment');
        $payment->addChild('PayorType', 'SENDER');

        $declaredValue = $xml->addChild('DeclaredValue');
        $declaredValue->addChild('Value', $r->getValue());
        $declaredValue->addChild('CurrencyCode', $this->getCurrencyCode());

        if ($this->getConfigData('residence_delivery')) {
            $specialServices = $xml->addChild('SpecialServices');
            $specialServices->addChild('ResidentialDelivery', 'true');
        }

        $xml->addChild('PackageCount', '1');

        $request = $xml->asXML();

        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = ['request' => $request];
            try {
                $url = $this->getConfigData('gateway_url');
                if (!$url) {
                    $url = $this->_defaultGatewayUrl;
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                $responseBody = curl_exec($ch);
                curl_close($ch);

                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            } catch (Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                $responseBody = '';
            }
            $this->_debug($debugData);
        }
        return $this->_parseXmlResponse($responseBody);
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseXmlResponse($response)
    {
        $costArr = [];
        $priceArr = [];

        if (strlen(trim($response)) > 0) {
            if ($xml = $this->_parseXml($response)) {
                if (is_object($xml->Error) && is_object($xml->Error->Message)) {
                    $errorTitle = (string)$xml->Error->Message;
                } elseif (is_object($xml->SoftError) && is_object($xml->SoftError->Message)) {
                    $errorTitle = (string)$xml->SoftError->Message;
                } else {
                    $errorTitle = 'Unknown error';
                }

                $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));

                foreach ($xml->Entry as $entry) {
                    if (in_array((string)$entry->Service, $allowedMethods)) {
                        $costArr[(string)$entry->Service] =
                           (string)$entry->EstimatedCharges->DiscountedCharges->NetCharge;
                        $priceArr[(string)$entry->Service] = $this->getMethodPrice(
                            (float)$entry->EstimatedCharges->DiscountedCharges->NetCharge,
                            (string)$entry->Service
                        );
                    }
                }

                asort($priceArr);
            } else {
                $errorTitle = 'Response is in the wrong format.';
            }
        } else {
            $errorTitle = 'Unable to retrieve tracking';
        }

        $result = Mage::getModel('shipping/rate_result');
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('fedex');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('fedex');
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
     * Parse XML string and return XML document object or false
     *
     * @param string $xmlContent
     * @return SimpleXMLElement|bool
     */
    protected function _parseXml($xmlContent)
    {
        try {
            try {
                return simplexml_load_string($xmlContent);
            } catch (Exception $e) {
                throw new Exception(Mage::helper('usa')->__('Failed to parse xml document: %s', $xmlContent));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
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
                'STATION'                 => Mage::helper('usa')->__('Station')
            ],
            'packaging' => [
                'FEDEX_ENVELOPE' => Mage::helper('usa')->__('FedEx Envelope'),
                'FEDEX_PAK'      => Mage::helper('usa')->__('FedEx Pak'),
                'FEDEX_BOX'      => Mage::helper('usa')->__('FedEx Box'),
                'FEDEX_TUBE'     => Mage::helper('usa')->__('FedEx Tube'),
                'FEDEX_10KG_BOX' => Mage::helper('usa')->__('FedEx 10kg Box'),
                'FEDEX_25KG_BOX' => Mage::helper('usa')->__('FedEx 25kg Box'),
                'YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging')
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
                            ]
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                            ]
                        ]
                    ]
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
                            ]
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                            ]
                        ]
                    ]
                ],
                [
                    'containers' => ['FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'],
                    'filters'    => [
                        'within_us' => [],
                        'from_us' => ['method' => ['INTERNATIONAL_PRIORITY']]
                    ]
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
                            ]
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
                            ]
                        ]
                    ]
                ]
            ],

            'delivery_confirmation_types' => [
                'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
                'ADULT'                 => Mage::helper('usa')->__('Adult'),
                'DIRECT'                => Mage::helper('usa')->__('Direct'),
                'INDIRECT'              => Mage::helper('usa')->__('Indirect'),
            ],

            'unit_of_measure' => [
                'LB'   =>  Mage::helper('usa')->__('Pounds'),
                'KG'   =>  Mage::helper('usa')->__('Kilograms'),
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
     *  @return string 3-digit currency code
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
     * @param mixed $trackings
     * @return Mage_Shipping_Model_Rate_Result|null
     */
    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        foreach ($trackings as $tracking) {
            $this->_getXMLTracking($tracking);
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

    /**
     * Send request for tracking
     *
     * @param array $tracking
     */
    protected function _getXMLTracking($tracking)
    {
        $trackRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => [
                    'Key'      => $this->getConfigData('key'),
                    'Password' => $this->getConfigData('password')
                ]
            ],
            'ClientDetail' => [
                'AccountNumber' => $this->getConfigData('account'),
                'MeterNumber'   => $this->getConfigData('meter_number')
            ],
            'Version' => [
                'ServiceId'    => 'trck',
                'Major'        => '5',
                'Intermediate' => '0',
                'Minor'        => '0'
            ],
            'PackageIdentifier' => [
                'Type'  => 'TRACKING_NUMBER_OR_DOORTAG',
                'Value' => $tracking,
            ],
            /*
             * 0 = summary data, one single scan structure with the most recent scan
             * 1 = multiple scan activity for each package
             */
            'IncludeDetailedScans' => 1,
        ];
        $requestString = serialize($trackRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = ['request' => $trackRequest];
        if ($response === null) {
            try {
                $client = $this->_createTrackSoapClient();
                $response = $client->track($trackRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
        $this->_debug($debugData);

        $this->_parseTrackingResponse($tracking, $response);
    }

    /**
     * Parse tracking response
     *
     * @param array $trackingValue
     * @param stdClass $response
     */
    protected function _parseTrackingResponse($trackingValue, $response)
    {
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                $errorTitle = (string)$response->Notifications->Message;
            } elseif (isset($response->TrackDetails)) {
                $trackInfo = $response->TrackDetails;
                $resultArray['status'] = (string)$trackInfo->StatusDescription;
                $resultArray['service'] = (string)$trackInfo->ServiceInfo;
                $timestamp = $trackInfo->EstimatedDeliveryTimestamp ?? $trackInfo->ActualDeliveryTimestamp;
                $timestamp = strtotime((string)$timestamp);
                if ($timestamp) {
                    $resultArray['deliverydate'] = date('Y-m-d', $timestamp);
                    $resultArray['deliverytime'] = date('H:i:s', $timestamp);
                }

                $deliveryLocation = $trackInfo->EstimatedDeliveryAddress ?? $trackInfo->ActualDeliveryAddress;
                $deliveryLocationArray = [];
                if (isset($deliveryLocation->City)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->City;
                }
                if (isset($deliveryLocation->StateOrProvinceCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->StateOrProvinceCode;
                }
                if (isset($deliveryLocation->CountryCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->CountryCode;
                }
                if ($deliveryLocationArray) {
                    $resultArray['deliverylocation'] = implode(', ', $deliveryLocationArray);
                }

                $resultArray['signedby'] = (string)$trackInfo->DeliverySignatureName;
                $resultArray['shippeddate'] = date('Y-m-d', (int)$trackInfo->ShipTimestamp);
                if (isset($trackInfo->PackageWeight) && isset($trackInfo->Units)) {
                    $weight = (string)$trackInfo->PackageWeight;
                    $unit = (string)$trackInfo->Units;
                    $resultArray['weight'] = "{$weight} {$unit}";
                }

                $packageProgress = [];
                if (isset($trackInfo->Events)) {
                    $events = $trackInfo->Events;
                    if (isset($events->Address)) {
                        $events = [$events];
                    }
                    foreach ($events as $event) {
                        $tempArray = [];
                        $tempArray['activity'] = (string)$event->EventDescription;
                        $timestamp = strtotime((string)$event->Timestamp);
                        if ($timestamp) {
                            $tempArray['deliverydate'] = date('Y-m-d', $timestamp);
                            $tempArray['deliverytime'] = date('H:i:s', $timestamp);
                        }
                        if (isset($event->Address)) {
                            $addressArray = [];
                            $address = $event->Address;
                            if (isset($address->City)) {
                                $addressArray[] = (string)$address->City;
                            }
                            if (isset($address->StateOrProvinceCode)) {
                                $addressArray[] = (string)$address->StateOrProvinceCode;
                            }
                            if (isset($address->CountryCode)) {
                                $addressArray[] = (string)$address->CountryCode;
                            }
                            if ($addressArray) {
                                $tempArray['deliverylocation'] = implode(', ', $addressArray);
                            }
                        }
                        $packageProgress[] = $tempArray;
                    }
                }

                $resultArray['progressdetail'] = $packageProgress;
            }
        }

        if (!$this->_result) {
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if (isset($resultArray)) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('fedex');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArray);
            $this->_result->append($tracking);
        } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('fedex');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingValue);
            $error->setErrorMessage($errorTitle ? $errorTitle : Mage::helper('usa')->__('Unable to retrieve tracking'));
            $this->_result->append($error);
        }
    }

    /**
     * Parse xml tracking response
     *
     * @deprecated after 1.6.0.0 see _parseTrackingResponse()
     * @param array $trackingvalue
     * @param string $response
     */
    protected function _parseXmlTrackingResponse($trackingvalue, $response)
    {
        $resultArr = [];
        if (strlen(trim($response)) > 0) {
            if ($xml = $this->_parseXml($response)) {
                if (is_object($xml->Error) && is_object($xml->Error->Message)) {
                    $errorTitle = (string)$xml->Error->Message;
                } elseif (is_object($xml->SoftError) && is_object($xml->SoftError->Message)) {
                    $errorTitle = (string)$xml->SoftError->Message;
                }

                if (!isset($errorTitle)) {
                    $resultArr['status'] = (string)$xml->Package->StatusDescription;
                    $resultArr['service'] = (string)$xml->Package->Service;
                    $resultArr['deliverydate'] = (string)$xml->Package->DeliveredDate;
                    $resultArr['deliverytime'] = (string)$xml->Package->DeliveredTime;
                    $resultArr['deliverylocation'] = (string)$xml->TrackProfile->DeliveredLocationDescription;
                    $resultArr['signedby'] = (string)$xml->Package->SignedForBy;
                    $resultArr['shippeddate'] = (string)$xml->Package->ShipDate;
                    $weight = (string)$xml->Package->Weight;
                    $unit = (string)$xml->Package->WeightUnits;
                    $resultArr['weight'] = "{$weight} {$unit}";

                    $packageProgress = [];
                    if (isset($xml->Package->Event)) {
                        foreach ($xml->Package->Event as $event) {
                            $tempArr = [];
                            $tempArr['activity'] = (string)$event->Description;
                            $tempArr['deliverydate'] = (string)$event->Date;//YYYY-MM-DD
                            $tempArr['deliverytime'] = (string)$event->Time;//HH:MM:ss
                            $addArr = [];
                            if (isset($event->Address->City)) {
                                $addArr[] = (string)$event->Address->City;
                            }
                            if (isset($event->Address->StateProvinceCode)) {
                                $addArr[] = (string)$event->Address->StateProvinceCode;
                            }
                            if (isset($event->Address->CountryCode)) {
                                $addArr[] = (string)$event->Address->CountryCode;
                            }
                            if ($addArr) {
                                $tempArr['deliverylocation'] = implode(', ', $addArr);
                            }
                            $packageProgress[] = $tempArr;
                        }
                    }

                    $resultArr['progressdetail'] = $packageProgress;
                }
            } else {
                $errorTitle = 'Response is in the wrong format';
            }
        } else {
            $errorTitle = false;
        }

        if (!$this->_result) {
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if ($resultArr) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('fedex');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingvalue);
            $tracking->addData($resultArr);
            $this->_result->append($tracking);
        } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('fedex');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingvalue);
            $error->setErrorMessage($errorTitle ? $errorTitle : Mage::helper('usa')->__('Unable to retrieve tracking'));
            $this->_result->append($error);
        }
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
                            $statuses .= Mage::helper('usa')->__($data['status']) . "\n<br/>";
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
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }
        return $arr;
    }

    /**
     * Return array of authenticated information
     *
     * @return array
     */
    protected function _getAuthDetails()
    {
        return [
            'WebAuthenticationDetail' => [
                'UserCredential' => [
                    'Key'      => $this->getConfigData('key'),
                    'Password' => $this->getConfigData('password')
                ]
            ],
            'ClientDetail' => [
                'AccountNumber' => $this->getConfigData('account'),
                'MeterNumber'   => $this->getConfigData('meter_number')
            ],
            'TransactionDetail' => [
                'CustomerTransactionId' => '*** Express Domestic Shipping Request v9 using PHP ***'
            ],
            'Version' => [
                'ServiceId'     => 'ship',
                'Major'         => '10',
                'Intermediate'  => '0',
                'Minor'         => '0'
            ]
        ];
    }

    /**
     * Form array with appropriate structure for shipment request
     *
     * @return array
     */
    protected function _formShipmentRequest(Varien_Object $request)
    {
        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData() . $request->getPackageId();
        } else {
            $referenceData = 'Order #'
                             . $request->getOrderShipment()->getOrder()->getIncrementId()
                             . ' P'
                             . $request->getPackageId();
        }
        $packageParams = $request->getPackageParams();
        $customsValue = $packageParams->getCustomsValue();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = $packageParams->getWeightUnits() == Zend_Measure_Weight::POUND ? 'LB' : 'KG';
        $dimensionsUnits = $packageParams->getDimensionUnits() == Zend_Measure_Length::INCH ? 'IN' : 'CM';
        $unitPrice = 0;
        $itemsQty = 0;
        $itemsDesc = [];
        $countriesOfManufacture = [];
        $productIds = [];
        $packageItems = $request->getPackageItems();
        foreach ($packageItems as $itemShipment) {
            $item = new Varien_Object();
            $item->setData($itemShipment);

            $unitPrice  += $item->getPrice();
            $itemsQty   += $item->getQty();

            $itemsDesc[]    = $item->getName();
            $productIds[]   = $item->getProductId();
        }

        // get countries of manufacture
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addStoreFilter($request->getStoreId())
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToSelect('country_of_manufacture');
        foreach ($productCollection as $product) {
            $countriesOfManufacture[] = $product->getCountryOfManufacture();
        }

        $paymentType = $request->getIsReturn() ? 'RECIPIENT' : 'SENDER';
        $requestClient = [
            'RequestedShipment' => [
                'ShipTimestamp' => time(),
                'DropoffType'   => $this->getConfigData('dropoff'),
                'PackagingType' => $request->getPackagingType(),
                'ServiceType' => $request->getShippingMethod(),
                'Shipper' => [
                    'Contact' => [
                        'PersonName' => $request->getShipperContactPersonName(),
                        'CompanyName' => $request->getShipperContactCompanyName(),
                        'PhoneNumber' => $request->getShipperContactPhoneNumber()
                    ],
                    'Address' => [
                        'StreetLines' => [
                            $request->getShipperAddressStreet1(),
                            $request->getShipperAddressStreet2()
                        ],
                        'City' => $request->getShipperAddressCity(),
                        'StateOrProvinceCode' => $request->getShipperAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getShipperAddressPostalCode(),
                        'CountryCode' => $request->getShipperAddressCountryCode()
                    ]
                ],
                'Recipient' => [
                    'Contact' => [
                        'PersonName' => $request->getRecipientContactPersonName(),
                        'CompanyName' => $request->getRecipientContactCompanyName(),
                        'PhoneNumber' => $request->getRecipientContactPhoneNumber()
                    ],
                    'Address' => [
                        'StreetLines' => [
                            $request->getRecipientAddressStreet1(),
                            $request->getRecipientAddressStreet2()
                        ],
                        'City' => $request->getRecipientAddressCity(),
                        'StateOrProvinceCode' => $request->getRecipientAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getRecipientAddressPostalCode(),
                        'CountryCode' => $request->getRecipientAddressCountryCode(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery')
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => $paymentType,
                    'Payor' => [
                        'AccountNumber' => $this->getConfigData('account'),
                        'CountryCode'   => Mage::getStoreConfig(
                            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                            $request->getStoreId()
                        )
                    ]
                ],
                'LabelSpecification' => [
                    'LabelFormatType' => 'COMMON2D',
                    'ImageType' => 'PNG',
                    'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL',
                ],
                'RateRequestTypes'  => ['ACCOUNT'],
                'PackageCount'      => 1,
                'RequestedPackageLineItems' => [
                    'SequenceNumber' => '1',
                    'Weight' => [
                        'Units' => $weightUnits,
                        'Value' =>  $request->getPackageWeight()
                    ],
                    'CustomerReferences' => [
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => $referenceData
                    ],
                    'SpecialServicesRequested' => [
                        'SpecialServiceTypes' => 'SIGNATURE_OPTION',
                        'SignatureOptionDetail' => ['OptionType' => $packageParams->getDeliveryConfirmation()]
                    ],
                ]
            ]
        ];

        // for international shipping
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
            $requestClient['RequestedShipment']['CustomsClearanceDetail'] =
                [
                    'CustomsValue' =>
                    [
                        'Currency' => $request->getBaseCurrencyCode(),
                        'Amount' => $customsValue,
                    ],
                    'DutiesPayment' => [
                        'PaymentType' => $paymentType,
                        'Payor' => [
                            'AccountNumber' => $this->getConfigData('account'),
                            'CountryCode'   => Mage::getStoreConfig(
                                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                                $request->getStoreId()
                            )
                        ]
                    ],
                    'Commodities' => [
                        'Weight' => [
                            'Units' => $weightUnits,
                            'Value' =>  $request->getPackageWeight()
                        ],
                        'NumberOfPieces' => 1,
                        'CountryOfManufacture' => implode(',', array_unique($countriesOfManufacture)),
                        'Description' => implode(', ', $itemsDesc),
                        'Quantity' => ceil($itemsQty),
                        'QuantityUnits' => 'pcs',
                        'UnitPrice' => [
                            'Currency' => $request->getBaseCurrencyCode(),
                            'Amount' =>  $unitPrice
                        ],
                        'CustomsValue' => [
                            'Currency' => $request->getBaseCurrencyCode(),
                            'Amount' =>  $customsValue
                        ],
                    ]
                ];
        }

        if ($request->getMasterTrackingId()) {
            $requestClient['RequestedShipment']['MasterTrackingId'] = $request->getMasterTrackingId();
        }

        // set dimensions
        if ($length || $width || $height) {
            $requestClient['RequestedShipment']['RequestedPackageLineItems']['Dimensions'] = [];
            $dimenssions = &$requestClient['RequestedShipment']['RequestedPackageLineItems']['Dimensions'];
            $dimenssions['Length'] = $length;
            $dimenssions['Width']  = $width;
            $dimenssions['Height'] = $height;
            $dimenssions['Units'] = $dimensionsUnits;
        }

        return $this->_getAuthDetails() + $requestClient;
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
        $client = $this->_createShipSoapClient();
        $requestClient = $this->_formShipmentRequest($request);
        $response = $client->processShipment($requestClient);

        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
            $shippingLabelContent = $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
            $trackingNumber = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
            $result->setShippingLabelContent($shippingLabelContent);
            $result->setTrackingNumber($trackingNumber);
            $debugData = ['request' => $client->__getLastRequest(), 'result' => $client->__getLastResponse()];
            $this->_debug($debugData);
        } else {
            $debugData = [
                'request' => $client->__getLastRequest(),
                'result' => [
                    'error' => '',
                    'code' => '',
                    'xml' => $client->__getLastResponse()
                ]
            ];
            if (is_array($response->Notifications)) {
                foreach ($response->Notifications as $notification) {
                    $debugData['result']['code'] .= $notification->Code . '; ';
                    $debugData['result']['error'] .= $notification->Message . '; ';
                }
            } else {
                $debugData['result']['code'] = $response->Notifications->Code . ' ';
                $debugData['result']['error'] = $response->Notifications->Message . ' ';
            }
            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }

    /**
     * For multi package shipments. Delete requested shipments if the current shipment
     * request is failed
     *
     * @param array $data
     * @return bool
     */
    public function rollBack($data)
    {
        $requestData = $this->_getAuthDetails();
        $requestData['DeletionControl'] = 'DELETE_ONE_PACKAGE';
        foreach ($data as &$item) {
            $requestData['TrackingId'] = $item['tracking_number'];
            $client = $this->_createShipSoapClient();
            $client->deleteShipment($requestData);
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
        if ($params == null) {
            return $this->_getAllowedContainers($params);
        }
        $method             = $params->getMethod();
        $countryShipper     = $params->getCountryShipper();
        $countryRecipient   = $params->getCountryRecipient();

        if (($countryShipper == self::USA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID
            || $countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::USA_COUNTRY_ID)
            && $method == 'FEDEX_GROUND'
        ) {
            return ['YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging')];
        } elseif ($method == 'INTERNATIONAL_ECONOMY' || $method == 'INTERNATIONAL_FIRST') {
            $allTypes = $this->getContainerTypesAll();
            $exclude = ['FEDEX_10KG_BOX' => '', 'FEDEX_25KG_BOX' => ''];
            return array_diff_key($allTypes, $exclude);
        } elseif ($method == 'EUROPE_FIRST_INTERNATIONAL_PRIORITY') {
            $allTypes = $this->getContainerTypesAll();
            $exclude = ['FEDEX_BOX' => '', 'FEDEX_TUBE' => ''];
            return array_diff_key($allTypes, $exclude);
        } elseif ($countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID) {
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
}
