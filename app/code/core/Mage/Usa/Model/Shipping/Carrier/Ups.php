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
 * @copyright  Copyright (c) 2017-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * UPS shipping implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Ups extends Mage_Usa_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    public const CODE = 'ups';

    /**
     * Delivery Confirmation level based on origin/destination
     *
     * @var int
     */
    public const DELIVERY_CONFIRMATION_SHIPMENT = 1;
    public const DELIVERY_CONFIRMATION_PACKAGE = 2;

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
     * Tracking result data
     *
     * @var Mage_Shipping_Model_Tracking_Result|null
     */
    protected $_trackingResult = null;

    /**
     * Base currency rate
     *
     * @var double
     */
    protected $_baseCurrencyRate;

    /**
     * Xml access request
     *
     * @var string
     */
    protected $_xmlAccessRequest = null;

    /**
     * Default urls
     *
     * @var array
     */
    protected $_defaultUrls = [
        'Rate'            => 'https://onlinetools.ups.com/ups.app/xml/Rate',
        'Track'           => 'https://onlinetools.ups.com/ups.app/xml/Track',
        'ShipConfirm'     => 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm',
        'ShipAccept'      => 'https://onlinetools.ups.com/ups.app/xml/ShipAccept',
        'AuthUrl'         => 'https://wwwcie.ups.com/security/v1/oauth/token',
        'RateRest'        => 'https://wwwcie.ups.com/api/rating',
        'TrackRest'       => 'https://wwwcie.ups.com/api/track',
        'ShipRestConfirm' => 'https://wwwcie.ups.com/api/shipments/v2403/ship',
    ];

    /**
     * Live urls
     *
     * @var array
     */
    protected $_liveUrls = [
        'Rate'            => 'https://onlinetools.ups.com/ups.app/xml/Rate',
        'Track'           => 'https://onlinetools.ups.com/ups.app/xml/Track',
        'ShipConfirm'     => 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm',
        'ShipAccept'      => 'https://onlinetools.ups.com/ups.app/xml/ShipAccept',
        'AuthUrl'         => 'https://onlinetools.ups.com/security/v1/oauth/token',
        'RateRest'        => 'https://onlinetools.ups.com/api/rating',
        'TrackRest'       => 'https://onlinetools.ups.com/api/track',
        'ShipRestConfirm' => 'https://onlinetools.ups.com/api/shipments/v2403/ship',
    ];

    /**
     * Container types that could be customized for UPS carrier
     *
     * @var array
     */
    protected $_customizableContainerTypes = ['CP', 'CSP'];

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
            $r->setAction($this->getCode('action', 'single'));
            $r->setProduct($request->getLimitMethod());
        } else {
            $r->setAction($this->getCode('action', 'all'));
            $r->setProduct('GND' . $this->getConfigData('dest_type'));
        }

        if ($request->getUpsPickup()) {
            $pickup = $request->getUpsPickup();
        } else {
            $pickup = $this->getConfigData('pickup');
        }
        $r->setPickup($this->getCode('pickup', $pickup));

        if ($request->getUpsContainer()) {
            $container = $request->getUpsContainer();
        } else {
            $container = $this->getConfigData('container');
        }
        $r->setContainer($this->getCode('container', $container));

        if ($request->getUpsDestType()) {
            $destType = $request->getUpsDestType();
        } else {
            $destType = $this->getConfigData('dest_type');
        }
        $r->setDestType($this->getCode('dest_type', $destType));

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $request->getStoreId(),
            );
        }
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigRegionCode()) {
            $origRegionCode = $request->getOrigRegionCode();
        } else {
            $origRegionCode = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID,
                $request->getStoreId(),
            );
        }
        if (is_numeric($origRegionCode)) {
            $origRegionCode = Mage::getModel('directory/region')->load($origRegionCode)->getCode();
        }
        $r->setOrigRegionCode($origRegionCode);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $request->getStoreId(),
            ));
        }

        if ($request->getOrigCity()) {
            $r->setOrigCity($request->getOrigCity());
        } else {
            $r->setOrigCity(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY,
                $request->getStoreId(),
            ));
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        //for UPS, puero rico state for US will assume as puerto rico country
        if ($destCountry == self::USA_COUNTRY_ID
            && ($request->getDestPostcode() == '00912' || $request->getDestRegionCode() == self::PUERTORICO_COUNTRY_ID)
        ) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        // For UPS, Guam state of the USA will be represented by Guam country
        if ($destCountry == self::USA_COUNTRY_ID && $request->getDestRegionCode() == self::GUAM_REGION_CODE) {
            $destCountry = self::GUAM_COUNTRY_ID;
        }

        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());
        $r->setDestRegionCode($request->getDestRegionCode());
        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $weight = $this->_getCorrectWeight($weight);
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        if ($request->getUpsUnitMeasure()) {
            $unit = $request->getUpsUnitMeasure();
        } else {
            $unit = $this->getConfigData('unit_of_measure');
        }
        $r->setUnitMeasure($unit);
        if ($r->getUnitMeasure() == 'LBS') {
            $r->setUnitDimensions('IN');
            $r->setUnitDimensionsDescription('Inches');
        } else {
            $r->setUnitDimensions('CM');
            $r->setUnitDimensionsDescription('Centimeters');
        }
        $r->setIsReturn($request->getIsReturn());
        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());
        $this->_rawRequest = $r;

        return $this;
    }

    /**
     * Get correct weight.
     *
     * Namely:
     * Checks the current weight to comply with the minimum weight standards set by the carrier.
     * Then strictly rounds the weight up until the first significant digit after the decimal point.
     *
     * @param float|int $weight
     * @return float
     */
    protected function _getCorrectWeight($weight)
    {
        $minWeight = $this->getConfigData('min_package_weight');

        if ($weight < $minWeight) {
            $weight = $minWeight;
        }

        //rounds a number to one significant figure
        $weight = ceil($weight * 10) / 10;

        return $weight;
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
     * Do remote request for  and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        // this "if" will be removed after XML APIs will be shut down
        if ($this->getConfigData('type') == 'UPS_XML') {
            return $this->_getXmlQuotes();
        }

        // REST is default
        return $this->_getRestQuotes();
    }

    /**
     * Set free method request
     *
     * @param string $freeMethod
     * @return void
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;

        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $weight = $this->_getCorrectWeight($weight);
        $r->setWeight($weight);
        $r->setAction($this->getCode('action', 'single'));
        $r->setProduct($freeMethod);
    }

    /**
     * Get shipment by code
     *
     * @param string $code
     * @param string $origin
     * @return array|false
     */
    public function getShipmentByCode($code, $origin = null)
    {
        if ($origin === null) {
            $origin = $this->getConfigData('origin_shipment');
        }
        $arr = $this->getCode('originShipment', $origin);
        if (isset($arr[$code])) {
            return $arr[$code];
        } else {
            return false;
        }
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|false
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'action' => [
                'single' => '3',
                'all' => '4',
            ],

            'originShipment' => [
                // United States Domestic Shipments
                'United States Domestic Shipments' => [
                    '01' => Mage::helper('usa')->__('UPS Next Day Air'),
                    '02' => Mage::helper('usa')->__('UPS Second Day Air'),
                    '03' => Mage::helper('usa')->__('UPS Ground'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '13' => Mage::helper('usa')->__('UPS Next Day Air Saver'),
                    '14' => Mage::helper('usa')->__('UPS Next Day Air Early A.M.'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '59' => Mage::helper('usa')->__('UPS Second Day Air A.M.'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ],
                // Shipments Originating in United States
                'Shipments Originating in United States' => [
                    '01' => Mage::helper('usa')->__('UPS Next Day Air'),
                    '02' => Mage::helper('usa')->__('UPS Second Day Air'),
                    '03' => Mage::helper('usa')->__('UPS Ground'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '13' => Mage::helper('usa')->__('UPS Next Day Air Saver'),
                    '14' => Mage::helper('usa')->__('UPS Next Day Air Early A.M.'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '59' => Mage::helper('usa')->__('UPS Second Day Air A.M.'),
                    '65' => Mage::helper('usa')->__('UPS Worldwide Saver'),
                ],
                // Shipments Originating in Canada
                'Shipments Originating in Canada' => [
                    '01' => Mage::helper('usa')->__('UPS Express'),
                    '02' => Mage::helper('usa')->__('UPS Expedited'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '14' => Mage::helper('usa')->__('UPS Express Early A.M.'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ],
                // Shipments Originating in the European Union
                'Shipments Originating in the European Union' => [
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express PlusSM'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ],
                // Polish Domestic Shipments
                'Polish Domestic Shipments' => [
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                    '82' => Mage::helper('usa')->__('UPS Today Standard'),
                    '83' => Mage::helper('usa')->__('UPS Today Dedicated Courrier'),
                    '84' => Mage::helper('usa')->__('UPS Today Intercity'),
                    '85' => Mage::helper('usa')->__('UPS Today Express'),
                    '86' => Mage::helper('usa')->__('UPS Today Express Saver'),
                ],
                // Puerto Rico Origin
                'Puerto Rico Origin' => [
                    '01' => Mage::helper('usa')->__('UPS Next Day Air'),
                    '02' => Mage::helper('usa')->__('UPS Second Day Air'),
                    '03' => Mage::helper('usa')->__('UPS Ground'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '14' => Mage::helper('usa')->__('UPS Next Day Air Early A.M.'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ],
                // Shipments Originating in Mexico
                'Shipments Originating in Mexico' => [
                    '01' => Mage::helper('usa')->__('UPS Next Day Air'),
                    '02' => Mage::helper('usa')->__('UPS Second Day Air'),
                    '03' => Mage::helper('usa')->__('UPS Ground'),
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '13' => Mage::helper('usa')->__('UPS Next Day Air Saver'),
                    '14' => Mage::helper('usa')->__('UPS Next Day Air Early A.M.'),
                    '54' => Mage::helper('usa')->__('UPS Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ],
                // Shipments Originating in Other Countries
                'Shipments Originating in Other Countries' => [
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ],
            ],

            'method' => [
                '1DM'    => Mage::helper('usa')->__('Next Day Air Early AM'),
                '1DML'   => Mage::helper('usa')->__('Next Day Air Early AM Letter'),
                '1DA'    => Mage::helper('usa')->__('Next Day Air'),
                '1DAL'   => Mage::helper('usa')->__('Next Day Air Letter'),
                '1DAPI'  => Mage::helper('usa')->__('Next Day Air Intra (Puerto Rico)'),
                '1DP'    => Mage::helper('usa')->__('Next Day Air Saver'),
                '1DPL'   => Mage::helper('usa')->__('Next Day Air Saver Letter'),
                '2DM'    => Mage::helper('usa')->__('2nd Day Air AM'),
                '2DML'   => Mage::helper('usa')->__('2nd Day Air AM Letter'),
                '2DA'    => Mage::helper('usa')->__('2nd Day Air'),
                '2DAL'   => Mage::helper('usa')->__('2nd Day Air Letter'),
                '3DS'    => Mage::helper('usa')->__('3 Day Select'),
                'GND'    => Mage::helper('usa')->__('Ground'),
                'GNDCOM' => Mage::helper('usa')->__('Ground Commercial'),
                'GNDRES' => Mage::helper('usa')->__('Ground Residential'),
                'STD'    => Mage::helper('usa')->__('Canada Standard'),
                'XPR'    => Mage::helper('usa')->__('Worldwide Express'),
                'WXS'    => Mage::helper('usa')->__('Worldwide Express Saver'),
                'XPRL'   => Mage::helper('usa')->__('Worldwide Express Letter'),
                'XDM'    => Mage::helper('usa')->__('Worldwide Express Plus'),
                'XDML'   => Mage::helper('usa')->__('Worldwide Express Plus Letter'),
                'XPD'    => Mage::helper('usa')->__('Worldwide Expedited'),
            ],

            'pickup' => [
                'RDP'    => ['label' => 'Regular Daily Pickup','code' => '01'],
                'OCA'    => ['label' => 'On Call Air','code' => '07'],
                'OTP'    => ['label' => 'One Time Pickup','code' => '06'],
                'LC'     => ['label' => 'Letter Center','code' => '19'],
                'CC'     => ['label' => 'Customer Counter','code' => '03'],
            ],

            'container' => [
                'CP'     => '00', // Customer Packaging
                'ULE'    => '01', // UPS Letter Envelope
                'CSP'    => '02', // Customer Supplied Package
                'UT'     => '03', // UPS Tube
                'PAK'    => '04', // PAK
                'UEB'    => '21', // UPS Express Box
                'UW25'   => '24', // UPS Worldwide 25 kilo
                'UW10'   => '25', // UPS Worldwide 10 kilo
                'PLT'    => '30', // Pallet
                'SEB'    => '2a', // Small Express Box
                'MEB'    => '2b', // Medium Express Box
                'LEB'    => '2c', // Large Express Box
            ],

            'container_description' => [
                'CP'     => Mage::helper('usa')->__('Customer Packaging'),
                'ULE'    => Mage::helper('usa')->__('UPS Letter Envelope'),
                'CSP'    => Mage::helper('usa')->__('Customer Supplied Package'),
                'UT'     => Mage::helper('usa')->__('UPS Tube'),
                'PAK'    => Mage::helper('usa')->__('PAK'),
                'UEB'    => Mage::helper('usa')->__('UPS Express Box'),
                'UW25'   => Mage::helper('usa')->__('UPS Worldwide 25 kilo'),
                'UW10'   => Mage::helper('usa')->__('UPS Worldwide 10 kilo'),
                'PLT'    => Mage::helper('usa')->__('Pallet'),
                'SEB'    => Mage::helper('usa')->__('Small Express Box'),
                'MEB'    => Mage::helper('usa')->__('Medium Express Box'),
                'LEB'    => Mage::helper('usa')->__('Large Express Box'),
            ],

            'dest_type' => [
                'RES'    => '01', // Residential
                'COM'    => '02', // Commercial
            ],

            'dest_type_description' => [
                'RES'    => Mage::helper('usa')->__('Residential'),
                'COM'    => Mage::helper('usa')->__('Commercial'),
            ],

            'unit_of_measure' => [
                'LBS'   =>  Mage::helper('usa')->__('Pounds'),
                'KGS'   =>  Mage::helper('usa')->__('Kilograms'),
            ],
            'containers_filter' => [
                [
                    'containers' => ['00'], // Customer Packaging
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                '01', // Next Day Air
                                '13', // Next Day Air Saver
                                '12', // 3 Day Select
                                '59', // 2nd Day Air AM
                                '03', // Ground
                                '14', // Next Day Air Early AM
                                '02', // 2nd Day Air
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                '07', // Worldwide Express
                                '54', // Worldwide Express Plus
                                '08', // Worldwide Expedited
                                '65', // Worldwide Saver
                                '11', // Standard
                            ],
                        ],
                    ],
                ],
                [
                    // Small Express Box, Medium Express Box, Large Express Box, UPS Tube
                    'containers' => ['2a', '2b', '2c', '03'],
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                '01', // Next Day Air
                                '13', // Next Day Air Saver
                                '14', // Next Day Air Early AM
                                '02', // 2nd Day Air
                                '59', // 2nd Day Air AM
                                '13', // Next Day Air Saver
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                '07', // Worldwide Express
                                '54', // Worldwide Express Plus
                                '08', // Worldwide Expedited
                                '65', // Worldwide Saver
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['24', '25'], // UPS Worldwide 25 kilo, UPS Worldwide 10 kilo
                    'filters'    => [
                        'within_us' => [
                            'method' => [],
                        ],
                        'from_us' => [
                            'method' => [
                                '07', // Worldwide Express
                                '54', // Worldwide Express Plus
                                '65', // Worldwide Saver
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['01', '04'], // UPS Letter, UPS PAK
                    'filters'    => [
                        'within_us' => [
                            'method' => [
                                '01', // Next Day Air
                                '14', // Next Day Air Early AM
                                '02', // 2nd Day Air
                                '59', // 2nd Day Air AM
                                '13', // Next Day Air Saver
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                '07', // Worldwide Express
                                '54', // Worldwide Express Plus
                                '65', // Worldwide Saver
                            ],
                        ],
                    ],
                ],
                [
                    'containers' => ['04'], // UPS PAK
                    'filters'    => [
                        'within_us' => [
                            'method' => [],
                        ],
                        'from_us' => [
                            'method' => [
                                '08', // Worldwide Expedited
                            ],
                        ],
                    ],
                ],
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
     * Get xml rates
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getXmlQuotes()
    {
        $url = $this->getConfigData('gateway_xml_url');
        if (!$url) {
            if ($this->getConfigFlag('mode_xml')) {
                $url = $this->_liveUrls['Rate'];
            } else {
                $url = $this->_defaultUrls['Rate'];
            }
        }

        $this->setXMLAccessRequest();
        $xmlRequest = $this->_xmlAccessRequest;

        $r = $this->_rawRequest;
        $params = $this->setQuoteRequestData($r);

        $xmlRequest .= <<< XMLRequest
<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang="en-US">
  <Request>
    <TransactionReference>
      <CustomerContext>Rating and Service</CustomerContext>
      <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
    <RequestAction>Rate</RequestAction>
    <RequestOption>{$params['10_action']}</RequestOption>
  </Request>
  <PickupType>
          <Code>{$params['47_rate_chart']['code']}</Code>
          <Description>{$params['47_rate_chart']['label']}</Description>
  </PickupType>

  <Shipment>
XMLRequest;

        if ($params['serviceCode'] !== null) {
            $xmlRequest .= '<Service>' .
                "<Code>{$params['serviceCode']}</Code>" .
                "<Description>{$params['serviceDescription']}</Description>" .
                '</Service>';
        }

        $xmlRequest .= <<< XMLRequest
      <Shipper>
XMLRequest;

        if ($this->getConfigFlag('negotiated_active') && ($shipper = $this->getConfigData('shipper_number'))) {
            $xmlRequest .= "<ShipperNumber>{$shipper}</ShipperNumber>";
        }

        if ($r->getIsReturn()) {
            $shipperCity = '';
            $shipperPostalCode = $params['19_destPostal'];
            $shipperCountryCode = $params['22_destCountry'];
            $shipperStateProvince = $params['destRegionCode'];
        } else {
            $shipperCity = $params['origCity'];
            $shipperPostalCode = $params['15_origPostal'];
            $shipperCountryCode = $params['14_origCountry'];
            $shipperStateProvince = $params['origRegionCode'];
        }

        $xmlRequest .= <<< XMLRequest
      <Address>
          <City>{$shipperCity}</City>
          <PostalCode>{$shipperPostalCode}</PostalCode>
          <CountryCode>{$shipperCountryCode}</CountryCode>
          <StateProvinceCode>{$shipperStateProvince}</StateProvinceCode>
      </Address>
    </Shipper>
    <ShipTo>
      <Address>
          <PostalCode>{$params['19_destPostal']}</PostalCode>
          <CountryCode>{$params['22_destCountry']}</CountryCode>
          <ResidentialAddress>{$params['49_residential']}</ResidentialAddress>
          <StateProvinceCode>{$params['destRegionCode']}</StateProvinceCode>
XMLRequest;

        $xmlRequest .= (
            $params['49_residential'] === '01'
                ? "<ResidentialAddressIndicator>{$params['49_residential']}</ResidentialAddressIndicator>"
                : ''
        );

        $xmlRequest .= <<< XMLRequest
      </Address>
    </ShipTo>


    <ShipFrom>
      <Address>
          <PostalCode>{$params['15_origPostal']}</PostalCode>
          <CountryCode>{$params['14_origCountry']}</CountryCode>
          <StateProvinceCode>{$params['origRegionCode']}</StateProvinceCode>
      </Address>
    </ShipFrom>

    <Package>
      <PackagingType><Code>{$params['48_container']}</Code></PackagingType>
      <PackageWeight>
         <UnitOfMeasurement><Code>{$r->getUnitMeasure()}</Code></UnitOfMeasurement>
        <Weight>{$params['23_weight']}</Weight>
      </PackageWeight>
    </Package>
XMLRequest;
        if ($this->getConfigFlag('negotiated_active')) {
            $xmlRequest .= '<RateInformation><NegotiatedRatesIndicator/></RateInformation>';
        }

        $xmlRequest .= <<< XMLRequest
  </Shipment>
</RatingServiceSelectionRequest>
XMLRequest;

        $xmlResponse = $this->_getCachedQuotes($xmlRequest);
        if ($xmlResponse === null) {
            $debugData = ['request' => $xmlRequest];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
            $xmlResponse = curl_exec($ch);
            if ($xmlResponse === false) {
                $debugData['result'] = ['error' => curl_error($ch), 'code' => curl_errno($ch)];
                $xmlResponse = '';
            } else {
                $debugData['result'] = $xmlResponse;
                $this->_setCachedQuotes($xmlRequest, $xmlResponse);
            }
            curl_close($ch);
            $this->_debug($debugData);
        }

        return $this->_parseXmlResponse($xmlResponse);
    }

    /**
     * Get base currency rate
     *
     * @param string $code
     * @return double
     */
    protected function _getBaseCurrencyRate($code)
    {
        if (!$this->_baseCurrencyRate) {
            $this->_baseCurrencyRate = Mage::getModel('directory/currency')
                ->load($code)
                ->getAnyRate($this->_request->getBaseCurrency()->getCode());
        }

        return $this->_baseCurrencyRate;
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $xmlResponse
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseXmlResponse($xmlResponse)
    {
        $costArr = [];
        $priceArr = [];
        if (strlen(trim($xmlResponse)) > 0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $arr = $xml->getXpath('//RatingServiceSelectionResponse/Response/ResponseStatusCode/text()');
            $success = (int) $arr[0];
            if ($success === 1) {
                $arr = $xml->getXpath('//RatingServiceSelectionResponse/RatedShipment');
                $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));

                // Negotiated rates
                $negotiatedArr = $xml->getXpath('//RatingServiceSelectionResponse/RatedShipment/NegotiatedRates');
                $negotiatedActive = $this->getConfigFlag('negotiated_active')
                    && $this->getConfigData('shipper_number')
                    && !empty($negotiatedArr);

                $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();

                foreach ($arr as $shipElement) {
                    $code = (string) $shipElement->Service->Code;
                    if (in_array($code, $allowedMethods)) {
                        if ($negotiatedActive) {
                            $cost = $shipElement->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
                        } else {
                            $cost = $shipElement->TotalCharges->MonetaryValue;
                        }

                        //convert price with Origin country currency code to base currency code
                        $successConversion = true;
                        $responseCurrencyCode = (string) $shipElement->TotalCharges->CurrencyCode;
                        if ($responseCurrencyCode) {
                            if (in_array($responseCurrencyCode, $allowedCurrencies)) {
                                $cost = (float) $cost * $this->_getBaseCurrencyRate($responseCurrencyCode);
                            } else {
                                $errorTitle = Mage::helper('directory')->__('Can\'t convert rate from "%s-%s".', $responseCurrencyCode, $this->_request->getPackageCurrency()->getCode());
                                $error = Mage::getModel('shipping/rate_result_error');
                                $error->setCarrier('ups');
                                $error->setCarrierTitle($this->getConfigData('title'));
                                $error->setErrorMessage($errorTitle);
                                $successConversion = false;
                            }
                        }

                        if ($successConversion) {
                            $costArr[$code] = $cost;
                            $priceArr[$code] = $this->getMethodPrice((float) $cost, $code);
                        }
                    }
                }
            } else {
                $arr = $xml->getXpath('//RatingServiceSelectionResponse/Response/Error/ErrorDescription/text()');
                $errorTitle = (string) $arr[0][0];
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier('ups');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if (!isset($errorTitle)) {
                $errorTitle = Mage::helper('usa')->__('Cannot retrieve shipping rates');
            }
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $methods = $this->getShipmentByCode($method);
                $rate->setMethodTitle($methods);
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }

    /**
     * Get tracking
     *
     * @param mixed $trackings
     * @return Mage_Shipping_Model_Tracking_Result|null
     */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        if ($this->getConfigData('type') === 'UPS_XML') {
            $this->setXMLAccessRequest();
            $this->_getXmlTracking($trackings);
        } else {
            $this->_getRestTracking($trackings);
        }

        return $this->_trackingResult;
    }

    /**
     * Set xml access request
     *
     * @return void
     */
    protected function setXMLAccessRequest()
    {
        $userId     = $this->getConfigData('username');
        $userIdPass = $this->getConfigData('password');
        $accessKey  = $this->getConfigData('access_license_number');

        $this->_xmlAccessRequest =  <<<XMLAuth
<?xml version="1.0"?>
<AccessRequest xml:lang="en-US">
  <AccessLicenseNumber>$accessKey</AccessLicenseNumber>
  <UserId>$userId</UserId>
  <Password>$userIdPass</Password>
</AccessRequest>
XMLAuth;
    }

    /**
     * Get xml tracking
     *
     * @param array $trackings
     * @return Mage_Shipping_Model_Tracking_Result|null
     */
    protected function _getXmlTracking($trackings)
    {
        $url = $this->getConfigData('tracking_xml_url');
        if (!$url) {
            if ($this->getConfigFlag('mode_xml')) {
                $url = $this->_liveUrls['Track'];
            } else {
                $url = $this->_defaultUrls['Track'];
            }
        }

        foreach ($trackings as $tracking) {
            $xmlRequest = $this->_xmlAccessRequest;

            /*
            * RequestOption==>'activity' or '1' to request all activities
            */
            $xmlRequest .=  <<<XMLAuth
<?xml version="1.0" ?>
<TrackRequest xml:lang="en-US">
    <Request>
        <RequestAction>Track</RequestAction>
        <RequestOption>activity</RequestOption>
    </Request>
    <TrackingNumber>$tracking</TrackingNumber>
    <IncludeFreight>01</IncludeFreight>
</TrackRequest>
XMLAuth;
            $debugData = ['request' => $xmlRequest];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $xmlResponse = curl_exec($ch);
            if ($xmlResponse === false) {
                $debugData['result'] = ['error' => curl_error($ch), 'code' => curl_errno($ch)];
                $xmlResponse = '';
            } else {
                $debugData['result'] = $xmlResponse;
            }
            curl_close($ch);

            $this->_debug($debugData);
            $this->_parseXmlTrackingResponse($tracking, $xmlResponse);
        }

        return $this->_trackingResult;
    }

    /**
     * Parse xml tracking response
     *
     * @param string $trackingvalue
     * @param string $xmlResponse
     * @return void
     */
    protected function _parseXmlTrackingResponse($trackingvalue, $xmlResponse)
    {
        $errorTitle = 'Unable to retrieve tracking';
        $resultArr = [];
        $packageProgress = [];

        if ($xmlResponse) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $arr = $xml->getXpath('//TrackResponse/Response/ResponseStatusCode/text()');
            $success = (int) $arr[0][0];

            if ($success === 1) {
                $arr = $xml->getXpath('//TrackResponse/Shipment/Service/Description/text()');
                $resultArr['service'] = (string) $arr[0];

                $arr = $xml->getXpath('//TrackResponse/Shipment/PickupDate/text()');
                $resultArr['shippeddate'] = (string) $arr[0];

                $arr = $xml->getXpath('//TrackResponse/Shipment/Package/PackageWeight/Weight/text()');
                $weight = (string) $arr[0];

                $arr = $xml->getXpath('//TrackResponse/Shipment/Package/PackageWeight/UnitOfMeasurement/Code/text()');
                $unit = (string) $arr[0];

                $resultArr['weight'] = "{$weight} {$unit}";

                $activityTags = $xml->getXpath('//TrackResponse/Shipment/Package/Activity');
                if ($activityTags) {
                    $i = 1;
                    foreach ($activityTags as $activityTag) {
                        $addArr = [];
                        if (isset($activityTag->ActivityLocation->Address->City)) {
                            $addArr[] = (string) $activityTag->ActivityLocation->Address->City;
                        }
                        if (isset($activityTag->ActivityLocation->Address->StateProvinceCode)) {
                            $addArr[] = (string) $activityTag->ActivityLocation->Address->StateProvinceCode;
                        }
                        if (isset($activityTag->ActivityLocation->Address->CountryCode)) {
                            $addArr[] = (string) $activityTag->ActivityLocation->Address->CountryCode;
                        }
                        $dateArr = [];
                        $date = (string) $activityTag->Date;//YYYYMMDD
                        $dateArr[] = substr($date, 0, 4);
                        $dateArr[] = substr($date, 4, 2);
                        $dateArr[] = substr($date, -2, 2);

                        $timeArr = [];
                        $time = (string) $activityTag->Time;//HHMMSS
                        $timeArr[] = substr($time, 0, 2);
                        $timeArr[] = substr($time, 2, 2);
                        $timeArr[] = substr($time, -2, 2);

                        if ($i == 1) {
                            $resultArr['status'] = (string) $activityTag->Status->StatusType->Description;
                            $resultArr['deliverydate'] = implode('-', $dateArr);//YYYY-MM-DD
                            $resultArr['deliverytime'] = implode(':', $timeArr);//HH:MM:SS
                            $resultArr['deliverylocation'] = (string) $activityTag->ActivityLocation->Description;
                            $resultArr['signedby'] = (string) $activityTag->ActivityLocation->SignedForByName;
                            if ($addArr) {
                                $resultArr['deliveryto'] = implode(', ', $addArr);
                            }
                        } else {
                            $tempArr = [];
                            $tempArr['activity'] = (string) $activityTag->Status->StatusType->Description;
                            $tempArr['deliverydate'] = implode('-', $dateArr);//YYYY-MM-DD
                            $tempArr['deliverytime'] = implode(':', $timeArr);//HH:MM:SS
                            if ($addArr) {
                                $tempArr['deliverylocation'] = implode(', ', $addArr);
                            }
                            $packageProgress[] = $tempArr;
                        }
                        $i++;
                    }
                    $resultArr['progressdetail'] = $packageProgress;
                }
            } else {
                $arr = $xml->getXpath('//TrackResponse/Response/Error/ErrorDescription/text()');
                $errorTitle = (string) $arr[0][0];
            }
        }

        $this->setTrackingResultData($resultArr, $trackingvalue, $errorTitle);
    }

    /**
     * Get REST tracking
     *
     * @param string[] $trackings
     * @return Mage_Shipping_Model_Tracking_Result|null
     */
    protected function _getRestTracking($trackings)
    {
        $url = $this->getConfigData('tracking_rest_url');
        if (!$url) {
            if ($this->getConfigFlag('mode_xml')) {
                $url = $this->_liveUrls['TrackRest'] . '/';
            } else {
                $url = $this->_defaultUrls['TrackRest'] . '/';
            }
        }

        try {
            $accessToken = $this->setAPIAccessRequest();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_trackingResult = Mage::getModel('shipping/tracking_result');
            $this->_trackingResult->setError('Authentication error');
            return $this->_trackingResult;
        }

        $version = 'v1';
        $query = http_build_query([
            'locale' => 'en_US',
            'returnSignature' => 'false',
            'returnMilestones' => 'false',
            'returnPOD' => 'false',
        ]);
        $headers = [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json',
            'transId: track' . uniqid(),
            'transactionSrc: OpenMage',
        ];

        $ch = curl_init();
        foreach ($trackings as $tracking) {
            $debugData = ['request' => $tracking];
            curl_setopt_array($ch, [
                CURLOPT_URL => $url . $version . '/details/' . $tracking . '?' . $query,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => $this->getConfigFlag('verify_peer'),
            ]);
            $responseData = curl_exec($ch);
            if ($responseData === false) {
                $debugData['result'] = ['error' => curl_error($ch), 'code' => curl_errno($ch)];
                $responseData = '';
            } else {
                $debugData['result'] = $responseData;
            }
            curl_reset($ch);

            $this->_debug($debugData);
            $this->_parseRestTrackingResponse($tracking, $responseData);
        }
        curl_close($ch);

        return $this->_trackingResult;
    }

    /**
     * Parse REST tracking response
     *
     * @param string $trackingValue
     * @param string $jsonResponse
     * @return void
     */
    protected function _parseRestTrackingResponse($trackingValue, $jsonResponse)
    {
        $errorTitle = 'For some reason we can\'t retrieve tracking info right now.';
        $resultArr = [];
        $packageProgress = [];

        if ($jsonResponse) {
            $responseData = json_decode($jsonResponse, true);

            if (isset($responseData['trackResponse']['shipment'])) {
                $activityTags = $responseData['trackResponse']['shipment'][0]['package'][0]['activity'] ?? [];
                if ($activityTags) {
                    $index = 1;
                    foreach ($activityTags as $activityTag) {
                        $addressArr = [];
                        if (isset($activityTag['location']['address']['city'])) {
                            $addressArr[] = (string) $activityTag['location']['address']['city'];
                        }
                        if (isset($activityTag['location']['address']['stateProvince'])) {
                            $addressArr[] = (string) $activityTag['location']['address']['stateProvince'];
                        }
                        if (isset($activityTag['location']['address']['countryCode'])) {
                            $addressArr[] = (string) $activityTag['location']['address']['countryCode'];
                        }
                        $dateArr = [];
                        $date = (string) $activityTag['date'];
                        $dateArr[] = substr($date, 0, 4);
                        $dateArr[] = substr($date, 4, 2);
                        $dateArr[] = substr($date, -2, 2);

                        $timeArr = [];
                        $time = (string) $activityTag['time'];
                        $timeArr[] = substr($time, 0, 2);
                        $timeArr[] = substr($time, 2, 2);
                        $timeArr[] = substr($time, -2, 2);

                        if ($index === 1) {
                            $resultArr['status'] = (string) $activityTag['status']['description'];
                            $resultArr['deliverydate'] = implode('-', $dateArr); //YYYY-MM-DD
                            $resultArr['deliverytime'] = implode(':', $timeArr); //HH:MM:SS
                            if ($addressArr) {
                                $resultArr['deliveryto'] = implode(', ', $addressArr);
                            }
                        } else {
                            $tempArr = [];
                            $tempArr['activity'] = (string) $activityTag['status']['description'];
                            $tempArr['deliverydate'] = implode('-', $dateArr); //YYYY-MM-DD
                            $tempArr['deliverytime'] = implode(':', $timeArr); //HH:MM:SS
                            if ($addressArr) {
                                $tempArr['deliverylocation'] = implode(', ', $addressArr);
                            }
                            $packageProgress[] = $tempArr;
                        }
                        $index++;
                    }
                    $resultArr['progressdetail'] = $packageProgress;
                }
            } elseif (isset($responseData['response']['errors'][0]['message'])) {
                $errorTitle = $responseData['response']['errors'][0]['message'];
            }
        }

        $this->setTrackingResultData($resultArr, $trackingValue, $errorTitle);
    }

    /**
     * Set Tracking Response Data
     *
     * @param array $resultArr
     * @param string $trackingValue
     * @param string $errorTitle
     */
    private function setTrackingResultData($resultArr, $trackingValue, $errorTitle)
    {
        if (!$this->_trackingResult) {
            $this->_trackingResult = Mage::getModel('shipping/tracking_result');
        }

        if ($resultArr) {
            /** @var Mage_Shipping_Model_Tracking_Result_Status $tracking */
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('ups');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArr);
            $this->_trackingResult->append($tracking);
        } else {
            /** @var Mage_Shipping_Model_Tracking_Result_Error $error */
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingValue);
            $error->setErrorMessage($errorTitle);
            $this->_trackingResult->append($error);
        }
    }

    /**
     * Get tracking response
     *
     * @return string
     */
    public function getResponse()
    {
        if ($this->_trackingResult === null) {
            $trackings = [];
        } else {
            $trackings = $this->_trackingResult->getAllTrackings();
        }

        $statuses = '';
        foreach ($trackings as $tracking) {
            if ($data = $tracking->getAllData()) {
                if (isset($data['status'])) {
                    $statuses .= Mage::helper('usa')->__($data['status']);
                } else {
                    $statuses .= Mage::helper('usa')->__($data['error_message']);
                }
            }
        }
        if (empty($statuses)) {
            $statuses = Mage::helper('usa')->__('Empty response');
        }
        return $statuses;
    }

    /**
     * Get allowed shipping methods.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowedMethods = explode(',', (string) $this->getConfigData('allowed_methods'));
        $availableByTypeMethods = $this->getCode('originShipment', $this->getConfigData('origin_shipment'));

        $methods = [];
        foreach ($availableByTypeMethods as $methodCode => $methodData) {
            if (in_array($methodCode, $allowedMethods)) {
                $methods[$methodCode] = $methodData;
            }
        }

        return $methods;
    }

    /**
     * Form XML for shipment request
     *
     * @return string
     */
    protected function _formShipmentRequest(Varien_Object $request)
    {
        $shipmentDescription = $this->generateShipmentDescription($request->getPackageItems());
        $packageParams = $request->getPackageParams();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = $packageParams->getWeightUnits() == Zend_Measure_Weight::POUND ? 'LBS' : 'KGS';
        $dimensionsUnits = $packageParams->getDimensionUnits() == Zend_Measure_Length::INCH ? 'IN' : 'CM';

        $xmlRequest = new SimpleXMLElement('<?xml version = "1.0" ?><ShipmentConfirmRequest xml:lang="en-US"/>');
        $requestPart = $xmlRequest->addChild('Request');
        $requestPart->addChild('RequestAction', 'ShipConfirm');
        $requestPart->addChild('RequestOption', 'nonvalidate');

        $shipmentPart = $xmlRequest->addChild('Shipment');
        if ($request->getIsReturn()) {
            $returnPart = $shipmentPart->addChild('ReturnService');
            // UPS Print Return Label
            $returnPart->addChild('Code', '9');
        }
        $shipmentPart->addChild('Description', $shipmentDescription);

        $shipperPart = $shipmentPart->addChild('Shipper');
        if ($request->getIsReturn()) {
            $shipperPart->addChild('Name', $request->getRecipientContactCompanyName());
            $shipperPart->addChild('AttentionName', $request->getRecipientContactPersonName());
            $shipperPart->addChild('ShipperNumber', $this->getConfigData('shipper_number'));
            $shipperPart->addChild('PhoneNumber', $request->getRecipientContactPhoneNumber());

            $addressPart = $shipperPart->addChild('Address');
            $addressPart->addChild('AddressLine1', $request->getRecipientAddressStreet());
            $addressPart->addChild('AddressLine2', $request->getRecipientAddressStreet2());
            $addressPart->addChild('City', $request->getRecipientAddressCity());
            $addressPart->addChild('CountryCode', $request->getRecipientAddressCountryCode());
            $addressPart->addChild('PostalCode', $request->getRecipientAddressPostalCode());
            if ($request->getRecipientAddressStateOrProvinceCode()) {
                $addressPart->addChild('StateProvinceCode', $request->getRecipientAddressStateOrProvinceCode());
            }
        } else {
            $shipperPart->addChild('Name', $request->getShipperContactCompanyName());
            $shipperPart->addChild('AttentionName', $request->getShipperContactPersonName());
            $shipperPart->addChild('ShipperNumber', $this->getConfigData('shipper_number'));
            $shipperPart->addChild('PhoneNumber', $request->getShipperContactPhoneNumber());

            $addressPart = $shipperPart->addChild('Address');
            $addressPart->addChild('AddressLine1', $request->getShipperAddressStreet());
            $addressPart->addChild('AddressLine2', $request->getShipperAddressStreet2());
            $addressPart->addChild('City', $request->getShipperAddressCity());
            $addressPart->addChild('CountryCode', $request->getShipperAddressCountryCode());
            $addressPart->addChild('PostalCode', $request->getShipperAddressPostalCode());
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $addressPart->addChild('StateProvinceCode', $request->getShipperAddressStateOrProvinceCode());
            }
        }

        $shipToPart = $shipmentPart->addChild('ShipTo');
        $shipToPart->addChild('AttentionName', $request->getRecipientContactPersonName());
        $shipToPart->addChild('CompanyName', $request->getRecipientContactCompanyName()
            ? $request->getRecipientContactCompanyName()
            : 'N/A');
        $shipToPart->addChild('PhoneNumber', $request->getRecipientContactPhoneNumber());

        $addressPart = $shipToPart->addChild('Address');
        $addressPart->addChild('AddressLine1', $request->getRecipientAddressStreet1());
        $addressPart->addChild('AddressLine2', $request->getRecipientAddressStreet2());
        $addressPart->addChild('City', $request->getRecipientAddressCity());
        $addressPart->addChild('CountryCode', $request->getRecipientAddressCountryCode());
        $addressPart->addChild('PostalCode', $request->getRecipientAddressPostalCode());
        if ($request->getRecipientAddressStateOrProvinceCode()) {
            $addressPart->addChild('StateProvinceCode', $request->getRecipientAddressRegionCode());
        }
        if ($this->getConfigData('dest_type') == 'RES') {
            $addressPart->addChild('ResidentialAddress');
        }

        if ($request->getIsReturn()) {
            $shipFromPart = $shipmentPart->addChild('ShipFrom');
            $shipFromPart->addChild('AttentionName', $request->getShipperContactPersonName());
            $shipFromPart->addChild('CompanyName', $request->getShipperContactCompanyName()
                ? $request->getShipperContactCompanyName()
                : $request->getShipperContactPersonName());
            $shipFromAddress = $shipFromPart->addChild('Address');
            $shipFromAddress->addChild('AddressLine1', $request->getShipperAddressStreet1());
            $shipFromAddress->addChild('AddressLine2', $request->getShipperAddressStreet2());
            $shipFromAddress->addChild('City', $request->getShipperAddressCity());
            $shipFromAddress->addChild('CountryCode', $request->getShipperAddressCountryCode());
            $shipFromAddress->addChild('PostalCode', $request->getShipperAddressPostalCode());
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $shipFromAddress->addChild('StateProvinceCode', $request->getShipperAddressStateOrProvinceCode());
            }

            $addressPart = $shipToPart->addChild('Address');
            $addressPart->addChild('AddressLine1', $request->getShipperAddressStreet1());
            $addressPart->addChild('AddressLine2', $request->getShipperAddressStreet2());
            $addressPart->addChild('City', $request->getShipperAddressCity());
            $addressPart->addChild('CountryCode', $request->getShipperAddressCountryCode());
            $addressPart->addChild('PostalCode', $request->getShipperAddressPostalCode());
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $addressPart->addChild('StateProvinceCode', $request->getShipperAddressStateOrProvinceCode());
            }
            if ($this->getConfigData('dest_type') == 'RES') {
                $addressPart->addChild('ResidentialAddress');
            }
        }

        $servicePart = $shipmentPart->addChild('Service');
        $servicePart->addChild('Code', $request->getShippingMethod());
        $packagePart = $shipmentPart->addChild('Package');
        // Package description is same as shipment description because it's one package
        $packagePart->addChild('Description', $shipmentDescription);
        $packagePart->addChild('PackagingType')
            ->addChild('Code', $request->getPackagingType());
        $packageWeight = $packagePart->addChild('PackageWeight');
        $packageWeight->addChild('Weight', $request->getPackageWeight());
        $packageWeight->addChild('UnitOfMeasurement')->addChild('Code', $weightUnits);

        // set dimensions
        if ($length || $width || $height) {
            $packageDimensions = $packagePart->addChild('Dimensions');
            $packageDimensions->addChild('UnitOfMeasurement')->addChild('Code', $dimensionsUnits);
            $packageDimensions->addChild('Length', $length);
            $packageDimensions->addChild('Width', $width);
            $packageDimensions->addChild('Height', $height);
        }

        // ups support reference number only for domestic service
        if ($this->_isUSCountry($request->getRecipientAddressCountryCode())
            && $this->_isUSCountry($request->getShipperAddressCountryCode())
        ) {
            if ($request->getReferenceData()) {
                $referenceData = $request->getReferenceData() . $request->getPackageId();
            } else {
                $referenceData = 'Order #'
                    . $request->getOrderShipment()->getOrder()->getIncrementId()
                    . ' P'
                    . $request->getPackageId();
            }
            $referencePart = $packagePart->addChild('ReferenceNumber');
            $referencePart->addChild('Code', '02');
            $referencePart->addChild('Value', $referenceData);
        }

        $deliveryConfirmation = $packageParams->getDeliveryConfirmation();
        if ($deliveryConfirmation) {
            /** @var SimpleXMLElement|null $serviceOptionsNode */
            $serviceOptionsNode = null;
            switch ($this->_getDeliveryConfirmationLevel($request->getRecipientAddressCountryCode())) {
                case self::DELIVERY_CONFIRMATION_PACKAGE:
                    $serviceOptionsNode = $packagePart->addChild('PackageServiceOptions');
                    break;
                case self::DELIVERY_CONFIRMATION_SHIPMENT:
                    $serviceOptionsNode = $shipmentPart->addChild('ShipmentServiceOptions');
                    break;
            }
            if (!is_null($serviceOptionsNode)) {
                $serviceOptionsNode
                    ->addChild('DeliveryConfirmation')
                    ->addChild('DCISType', $packageParams->getDeliveryConfirmation());
            }
        }

        $shipmentPart->addChild('PaymentInformation')
            ->addChild('Prepaid')
            ->addChild('BillShipper')
            ->addChild('AccountNumber', $this->getConfigData('shipper_number'));

        if ($request->getPackagingType() != $this->getCode('container', 'ULE')
            && $request->getShipperAddressCountryCode() == Mage_Usa_Model_Shipping_Carrier_Abstract::USA_COUNTRY_ID
            && (
                $request->getRecipientAddressCountryCode() == 'CA' //Canada
                || $request->getRecipientAddressCountryCode() == 'PR' //Puerto Rico
            )
        ) {
            $invoiceLineTotalPart = $shipmentPart->addChild('InvoiceLineTotal');
            $invoiceLineTotalPart->addChild('CurrencyCode', $request->getBaseCurrencyCode());
            $invoiceLineTotalPart->addChild('MonetaryValue', ceil($packageParams->getCustomsValue()));
        }

        $labelPart = $xmlRequest->addChild('LabelSpecification');
        $labelPart->addChild('LabelPrintMethod')
            ->addChild('Code', 'GIF');
        $labelPart->addChild('LabelImageFormat')
            ->addChild('Code', 'GIF');

        $this->setXMLAccessRequest();
        $xmlRequest = $this->_xmlAccessRequest . $xmlRequest->asXML();
        return $xmlRequest;
    }

    /**
     * Send and process shipment accept request
     *
     * @return Varien_Object
     */
    protected function _sendShipmentAcceptRequest(SimpleXMLElement $shipmentConfirmResponse)
    {
        $xmlRequest = new SimpleXMLElement('<?xml version = "1.0" ?><ShipmentAcceptRequest/>');
        $request = $xmlRequest->addChild('Request');
        $request->addChild('RequestAction', 'ShipAccept');
        $xmlRequest->addChild('ShipmentDigest', $shipmentConfirmResponse->ShipmentDigest);

        $debugData = ['request' => $xmlRequest->asXML()];
        $url = $this->getConfigData('shipaccept_xml_url');
        if (!$url) {
            if ($this->getConfigFlag('mode_xml')) {
                $url = $this->_liveUrls['ShipAccept'];
            } else {
                $url = $this->_defaultUrls['ShipAccept'];
            }
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_xmlAccessRequest . $xmlRequest->asXML());
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
        $xmlResponse = curl_exec($ch);
        if ($xmlResponse === false) {
            $debugData['result'] = ['error' => curl_error($ch), 'code' => curl_errno($ch)];
            $xmlResponse = '';
        } else {
            $debugData['result'] = $xmlResponse;
            $this->_setCachedQuotes($xmlRequest, $xmlResponse);
        }
        curl_close($ch);

        try {
            $response = new SimpleXMLElement($xmlResponse);
        } catch (Exception $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
        }

        $result = new Varien_Object();
        if (isset($response->Error)) {
            $result->setErrors((string) $response->Error->ErrorDescription);
        } else {
            $shippingLabelContent = (string) $response->ShipmentResults->PackageResults->LabelImage->GraphicImage;
            $trackingNumber       = (string) $response->ShipmentResults->PackageResults->TrackingNumber;

            $result->setShippingLabelContent(base64_decode($shippingLabelContent));
            $result->setTrackingNumber($trackingNumber);
        }

        $this->_debug($debugData);
        return $result;
    }

    protected function _doShipmentRequest(Varien_Object $request): Varien_Object
    {
        // this "if" will be removed after XML APIs will be shut down
        if ($this->getConfigData('type') === 'UPS_XML') {
            return $this->_doShipmentRequestXML($request);
        }

        // REST is default
        return $this->_doShipmentRequestRest($request);
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     */
    protected function _doShipmentRequestRest(Varien_Object $request): Varien_Object
    {
        $request->setShipperAddressCountryCode(
            $this->getNormalizedCountryCode(
                $request->getShipperAddressCountryCode(),
                $request->getShipperAddressStateOrProvinceCode(),
                $request->getShipperAddressPostalCode(),
            ),
        );

        $request->setRecipientAddressCountryCode(
            $this->getNormalizedCountryCode(
                $request->getRecipientAddressCountryCode(),
                $request->getRecipientAddressStateOrProvinceCode(),
                $request->getRecipientAddressPostalCode(),
            ),
        );

        $result = new Varien_Object();
        $this->_prepareShipmentRequest($request);
        $rawJsonRequest = $this->_formShipmentRestRequest($request);
        try {
            $accessToken = $this->setAPIAccessRequest();
        } catch (Exception $e) {
            $result->setErrors(Mage::helper('usa')->__('Authentication error'));
            return $result;
        }
        $this->_debug(['request_quote' => $rawJsonRequest]);

        $shipConfirmUrl = $this->getConfigData('shipconfirm_rest_url');
        if (!$shipConfirmUrl) {
            if ($this->getConfigFlag('mode_xml')) {
                $shipConfirmUrl = $this->_liveUrls['ShipRestConfirm'];
            } else {
                $shipConfirmUrl = $this->_defaultUrls['ShipRestConfirm'];
            }
        }

        /** Rest API Payload */
        $headers = [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json',
        ];
        $debugData = [
            'request' => $rawJsonRequest,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $shipConfirmUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawJsonRequest);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
        $responseData = curl_exec($ch);
        if ($responseData === false) {
            $debugData['result'] = ['error' => curl_error($ch), 'code' => curl_errno($ch)];
            $responseData = '';
        } else {
            $debugData['result'] = $responseData;
        }
        curl_close($ch);

        $responseData = json_decode($responseData);
        if (!$responseData) {
            $result->setErrors(Mage::helper('usa')->__('Empty response'));
        } elseif (isset($responseData->response->errors)) {
            $result->setErrors((string) $responseData->response->errors[0]->message);
        }

        if ($result->hasErrors() || empty($responseData)) {
            $this->_debug($debugData);
            return $result;
        }

        // PackageResults is always an array for API version v2403, but could be an object for other versions.
        // The UPS API docs don't mark it required and don't say if it is always set, so let's be cautious.
        if (!isset($responseData->ShipmentResponse->ShipmentResults->PackageResults)) {
            $package = null;
        } elseif (is_array($responseData->ShipmentResponse->ShipmentResults->PackageResults)) {
            /** @var null|object{TrackingNumber: string, ShippingLabel: object{GraphicImage: string}} $package */
            $package = $responseData->ShipmentResponse->ShipmentResults->PackageResults[0] ?? null;
        } elseif (is_object($responseData->ShipmentResponse->ShipmentResults->PackageResults)) {
            /** @var object{TrackingNumber: string, ShippingLabel: object{GraphicImage: string}} $package */
            $package = $responseData->ShipmentResponse->ShipmentResults->PackageResults;
        } else {
            Mage::log(
                'Unexpected response shape from UPS REST API /shipments endpoint for .ShipmentResults.PackageResults',
                Zend_Log::WARN,
            );
            $result->setErrors(Mage::helper('usa')->__('Error reading response from UPS'));
            $this->_debug($debugData);
            return $result;
        }

        if ($package !== null) {
            $result->setTrackingNumber($package->TrackingNumber);
            // ShippingLabel is not guaranteed to be set, but if it is, GraphicImage will be set.
            if (isset($package->ShippingLabel->GraphicImage)) {
                $result->setShippingLabelContent(base64_decode($package->ShippingLabel->GraphicImage));
            }
        }

        $this->_debug($debugData);
        return $result;
    }

    /**
     * Return country code according to UPS
     *
     * @param string $countryCode
     * @param string $regionCode
     * @param string $postCode
     * @return string
     */
    private function getNormalizedCountryCode($countryCode, $regionCode, $postCode)
    {
        //for UPS, puerto rico state for US will assume as puerto rico country
        if ($countryCode == self::USA_COUNTRY_ID && ($postCode == '00912' || $regionCode == self::PUERTORICO_COUNTRY_ID)) {
            $countryCode = self::PUERTORICO_COUNTRY_ID;
        }

        // For UPS, Guam state of the USA will be represented by Guam country
        if ($countryCode == self::USA_COUNTRY_ID && $regionCode == self::GUAM_REGION_CODE) {
            $countryCode = self::GUAM_COUNTRY_ID;
        }

        // For UPS, Las Palmas and Santa Cruz de Tenerife will be represented by Canary Islands country
        if ($countryCode === 'ES' && ($regionCode === 'Las Palmas' || $regionCode === 'Santa Cruz de Tenerife')) {
            $countryCode = 'IC';
        }

        return $countryCode;
    }

    protected function _formShipmentRestRequest(Varien_Object $request): string
    {
        $shipmentDescription = $this->generateShipmentDescription($request->getPackageItems());
        $packageParams = $request->getPackageParams();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weight = $packageParams->getWeight();
        $weightUnits = $packageParams->getWeightUnits() == Zend_Measure_Weight::POUND ? 'LBS' : 'KGS';
        $dimensionsUnits = $packageParams->getDimensionUnits() == Zend_Measure_Length::INCH ? 'IN' : 'CM';

        /**  Shipment API Payload */
        $shipParams = [
            'ShipmentRequest' => [
                'Request' => [
                    'SubVersion' => '1801',
                    'RequestOption' => 'nonvalidate',
                    'TransactionReference' => [
                        'CustomerContext' => 'Shipment Request',
                    ],
                ],
                'Shipment' => [
                    'Description' => $shipmentDescription,
                    'Shipper' => [],
                    'ShipTo' => [],
                    'ShipFrom' => [],
                    'PaymentInformation' => [],
                    'Service' => [],
                    'Package' => [],
                    'ShipmentServiceOptions' => [],
                ],
                'LabelSpecification' => [],
            ],
        ];
        if ($request->getIsReturn()) {
            $returnPart = &$shipParams['ShipmentRequest']['Shipment'];
            $returnPart['ReturnService']['Code'] = '9';
        }

        /** Shipment Details */
        if ($request->getIsReturn()) {
            $shipperData = &$shipParams['ShipmentRequest']['Shipment']['Shipper'];

            $shipperData['Name'] = $request->getRecipientContactCompanyName();
            $shipperData['AttentionName'] = $request->getRecipientContactPersonName();
            $shipperData['ShipperNumber'] = $this->getConfigData('shipper_number');
            $shipperData['Phone']['Number'] = $request->getRecipientContactPhoneNumber();

            $addressData = &$shipperData['Address'];
            $addressData['AddressLine'] =
                $request->getRecipientAddressStreet1() . ' ' . $request->getRecipientAddressStreet2();
            $addressData['City'] = $request->getRecipientAddressCity();
            $addressData['CountryCode'] = $request->getRecipientAddressCountryCode();
            $addressData['PostalCode'] = $request->getRecipientAddressPostalCode();

            if ($request->getRecipientAddressStateOrProvinceCode()) {
                $addressData['StateProvinceCode'] = $request->getRecipientAddressStateOrProvinceCode();
            }
        } else {
            $shipperData = &$shipParams['ShipmentRequest']['Shipment']['Shipper'];

            $shipperData['Name'] = $request->getShipperContactCompanyName();
            $shipperData['AttentionName'] = $request->getShipperContactPersonName();
            $shipperData['ShipperNumber'] = $this->getConfigData('shipper_number');
            $shipperData['Phone']['Number'] = $request->getShipperContactPhoneNumber();

            $addressData = &$shipperData['Address'];
            $addressData['AddressLine'] = $request->getShipperAddressStreet1() . ' ' . $request->getShipperAddressStreet2();
            $addressData['City'] = $request->getShipperAddressCity();
            $addressData['CountryCode'] = $request->getShipperAddressCountryCode();
            $addressData['PostalCode'] = $request->getShipperAddressPostalCode();

            if ($request->getShipperAddressStateOrProvinceCode()) {
                $addressData['StateProvinceCode'] = $request->getShipperAddressStateOrProvinceCode();
            }
        }

        $shipToData = &$shipParams['ShipmentRequest']['Shipment']['ShipTo'];
        $shipToData = [
            'Name'   => $request->getRecipientContactPersonName(),
            'AttentionName' => $request->getRecipientContactPersonName(),
            'Phone' => ['Number' => $request->getRecipientContactPhoneNumber()],
            'Address' => [
                'AddressLine' => $request->getRecipientAddressStreet1() . ' ' . $request->getRecipientAddressStreet2(),
                'City' => $request->getRecipientAddressCity(),
                'CountryCode' => $request->getRecipientAddressCountryCode(),
                'PostalCode' => $request->getRecipientAddressPostalCode(),
            ],
        ];
        if ($request->getRecipientAddressStateOrProvinceCode()) {
            $shipToData['Address']['StateProvinceCode'] = $request->getRecipientAddressRegionCode();
        }
        if ($this->getConfigData('dest_type') == 'RES') {
            $shipToData['Address']['ResidentialAddress'] = '';
        }

        if ($request->getIsReturn()) {
            $shipFrom = &$shipParams['ShipmentRequest']['Shipment']['ShipFrom'];
            $shipFrom['Name'] = $request->getShipperContactPersonName();
            $shipFrom['AttentionName'] = $request->getShipperContactPersonName();
            $address = &$shipFrom['Address'];
            $address['AddressLine'] = $request->getShipperAddressStreet1() . ' ' . $request->getShipperAddressStreet2();
            $address['City'] = $request->getShipperAddressCity();
            $address['CountryCode'] = $request->getShipperAddressCountryCode();
            $address['PostalCode'] = $request->getShipperAddressPostalCode();
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $address['StateProvinceCode'] = $request->getShipperAddressStateOrProvinceCode();
            }
        }

        $shipParams['ShipmentRequest']['Shipment']['Service']['Code'] = $request->getShippingMethod();

        $deliveryConfirmationLevel = $this->_getDeliveryConfirmationLevel(
            $request->getRecipientAddressCountryCode(),
        );

        $packagePart = &$shipParams['ShipmentRequest']['Shipment']['Package'];
        // Package description is same as shipment description because it's one package
        $packagePart['Description'] = $shipmentDescription;
        $packagePart['Packaging']['Code'] = $request->getPackagingType();
        $packagePart['PackageWeight'] = [];
        $packageWeight = &$packagePart['PackageWeight'];
        $packageWeight['Weight'] = $weight;
        $packageWeight['UnitOfMeasurement']['Code'] = $weightUnits;

        // set dimensions
        if ($length || $width || $height) {
            $packagePart['Dimensions'] = [];
            $packageDimensions = &$packagePart['Dimensions'];
            $packageDimensions['UnitOfMeasurement']['Code'] = $dimensionsUnits;
            $packageDimensions['Length'] = $length;
            $packageDimensions['Width'] = $width;
            $packageDimensions['Height'] = $height;
        }

        // ups support reference number only for domestic service
        if ($this->_isUSCountry($request->getRecipientAddressCountryCode())
            && $this->_isUSCountry($request->getShipperAddressCountryCode())
        ) {
            if ($request->getReferenceData()) {
                $referenceData = $request->getReferenceData() . $request->getPackageId();
            } else {
                $referenceData = 'Order #' .
                    $request->getOrderShipment()->getOrder()->getIncrementId() .
                    ' P' .
                    $request->getPackageId();
            }
            $packagePart['ReferenceNumber'] = [];
            $referencePart = &$packagePart['ReferenceNumber'];
            $referencePart['Code'] = '02';
            $referencePart['Value'] = $referenceData;
        }

        $deliveryConfirmation = $packageParams->getDeliveryConfirmation();
        if ($deliveryConfirmation && $deliveryConfirmationLevel === self::DELIVERY_CONFIRMATION_PACKAGE) {
            $packagePart['PackageServiceOptions']['DeliveryConfirmation']['DCISType'] = $deliveryConfirmation;
        }

        if (!empty($deliveryConfirmation) && $deliveryConfirmationLevel === self::DELIVERY_CONFIRMATION_SHIPMENT) {
            $shipParams['ShipmentRequest']['Shipment']['ShipmentServiceOptions']['DeliveryConfirmation']['DCISType']
                = $deliveryConfirmation;
        }

        $shipParams['ShipmentRequest']['Shipment']['PaymentInformation']['ShipmentCharge']['Type'] = '01';
        $shipParams['ShipmentRequest']['Shipment']['PaymentInformation']['ShipmentCharge']['BillShipper']
        ['AccountNumber'] = $this->getConfigData('shipper_number');

        if ($this->getCode('container', 'ULE') != $request->getPackagingType()
            && $request->getShipperAddressCountryCode() == self::USA_COUNTRY_ID
            && ($request->getRecipientAddressCountryCode() == 'CA'
                || $request->getRecipientAddressCountryCode() == 'PR')
        ) {
            $invoiceLineTotalPart = &$shipParams['ShipmentRequest']['Shipment']['InvoiceLineTotal'];
            $invoiceLineTotalPart['CurrencyCode'] = $request->getBaseCurrencyCode();
            $invoiceLineTotalPart['MonetaryValue'] = ceil($packageParams->getCustomsValue());
        }

        /**  Label Details */

        $labelPart = &$shipParams['ShipmentRequest']['LabelSpecification'];
        $labelPart['LabelImageFormat']['Code'] = 'GIF';

        return json_encode($shipParams);
    }

    private function generateShipmentDescription(array $items): string
    {
        $itemsDesc = [];
        $itemsShipment = $items;
        foreach ($itemsShipment as $itemShipment) {
            $item = new Varien_Object();
            $item->setData($itemShipment);
            $itemsDesc[] = $item->getName();
        }

        return substr(implode(' ', $itemsDesc), 0, 35);
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     */
    protected function _doShipmentRequestXML(Varien_Object $request): Varien_Object
    {
        $this->_prepareShipmentRequest($request);
        $result = new Varien_Object();
        $xmlRequest = $this->_formShipmentRequest($request);
        $xmlResponse = $this->_getCachedQuotes($xmlRequest);

        if ($xmlResponse === null) {
            $url = $this->getConfigData('shipconfirm_xml_url');
            if (!$url) {
                if ($this->getConfigFlag('mode_xml')) {
                    $url = $this->_liveUrls['ShipConfirm'];
                } else {
                    $url = $this->_defaultUrls['ShipConfirm'];
                }
            }

            $debugData = ['request' => $xmlRequest];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
            $xmlResponse = curl_exec($ch);
            if ($xmlResponse === false) {
                throw new Exception(curl_error($ch));
            } else {
                $debugData['result'] = $xmlResponse;
                $this->_setCachedQuotes($xmlRequest, $xmlResponse);
            }
        }

        try {
            $response = new SimpleXMLElement($xmlResponse);
        } catch (Exception $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $result->setErrors($e->getMessage());
        }

        if (isset($response->Response->Error)
            && in_array($response->Response->Error->ErrorSeverity, ['Hard', 'Transient'])
        ) {
            $result->setErrors((string) $response->Response->Error->ErrorDescription);
        }

        $this->_debug($debugData);

        if ($result->hasErrors() || empty($response)) {
            return $result;
        } else {
            return $this->_sendShipmentAcceptRequest($response);
        }
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

        if (($countryShipper == self::USA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID)
            || ($countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::USA_COUNTRY_ID)
            || ($countryShipper == self::MEXICO_COUNTRY_ID && $countryRecipient == self::USA_COUNTRY_ID)
            && $method == '11' // UPS Standard
        ) {
            $containerTypes = [];
            if ($method == '07' // Worldwide Express
                || $method == '08' // Worldwide Expedited
                || $method == '65' // Worldwide Saver
            ) {
                // Worldwide Expedited
                if ($method != '08') {
                    $containerTypes = [
                        '01'   => Mage::helper('usa')->__('UPS Letter Envelope'),
                        '24'   => Mage::helper('usa')->__('UPS Worldwide 25 kilo'),
                        '25'   => Mage::helper('usa')->__('UPS Worldwide 10 kilo'),
                    ];
                }
                $containerTypes = $containerTypes + [
                    '03'     => Mage::helper('usa')->__('UPS Tube'),
                    '04'    => Mage::helper('usa')->__('PAK'),
                    '2a'    => Mage::helper('usa')->__('Small Express Box'),
                    '2b'    => Mage::helper('usa')->__('Medium Express Box'),
                    '2c'    => Mage::helper('usa')->__('Large Express Box'),
                ];
            }
            return ['00' => Mage::helper('usa')->__('Customer Packaging')] + $containerTypes;
        } elseif ($countryShipper == self::USA_COUNTRY_ID && $countryRecipient == self::PUERTORICO_COUNTRY_ID
            && (
                $method == '03' // UPS Ground
                || $method == '02' // UPS Second Day Air
                || $method == '01' // UPS Next Day Air
            )
        ) {
            // Container types should be the same as for domestic
            $params->setCountryRecipient(self::USA_COUNTRY_ID);
            $containerTypes = $this->_getAllowedContainers($params);
            $params->setCountryRecipient($countryRecipient);
            return $containerTypes;
        }
        return $this->_getAllowedContainers($params);
    }

    /**
     * Return all container types of carrier
     *
     * @return array
     */
    public function getContainerTypesAll()
    {
        $codes        = $this->getCode('container');
        $descriptions = $this->getCode('container_description');
        $result       = [];
        foreach ($codes as $key => &$code) {
            $result[$code] = $descriptions[$key];
        }
        return $result;
    }

    /**
     * Return structured data of containers witch related with shipping methods
     *
     * @return array|false
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
        $countryRecipient           = $params != null ? $params->getCountryRecipient() : null;
        $deliveryConfirmationTypes  = [];
        switch ($this->_getDeliveryConfirmationLevel($countryRecipient)) {
            case self::DELIVERY_CONFIRMATION_PACKAGE:
                $deliveryConfirmationTypes = [
                    1 => Mage::helper('usa')->__('Delivery Confirmation'),
                    2 => Mage::helper('usa')->__('Signature Required'),
                    3 => Mage::helper('usa')->__('Adult Signature Required'),
                ];
                break;
            case self::DELIVERY_CONFIRMATION_SHIPMENT:
                $deliveryConfirmationTypes = [
                    1 => Mage::helper('usa')->__('Signature Required'),
                    2 => Mage::helper('usa')->__('Adult Signature Required'),
                ];
        }
        array_unshift($deliveryConfirmationTypes, Mage::helper('usa')->__('Not Required'));

        return $deliveryConfirmationTypes;
    }

    /**
     * Get Container Types, that could be customized for UPS carrier
     *
     * @return array
     */
    public function getCustomizableContainerTypes()
    {
        $result = [];
        $containerTypes = $this->getCode('container');
        foreach (parent::getCustomizableContainerTypes() as $containerType) {
            $result[$containerType] = $containerTypes[$containerType];
        }
        return $result;
    }

    /**
     * Get delivery confirmation level based on origin/destination
     * Return null if delivery confirmation is not acceptable
     *
     * @param string $countyDest
     * @return int|null
     */
    protected function _getDeliveryConfirmationLevel($countyDest = null)
    {
        if (is_null($countyDest)) {
            return null;
        }

        if ($countyDest == Mage_Usa_Model_Shipping_Carrier_Abstract::USA_COUNTRY_ID) {
            return self::DELIVERY_CONFIRMATION_PACKAGE;
        }

        return self::DELIVERY_CONFIRMATION_SHIPMENT;
    }

    /**
     * Get REST rates
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getRestQuotes()
    {
        $url = $this->getConfigData('gateway_rest_url');
        if (!$url) {
            if ($this->getConfigFlag('mode_xml')) {
                $url = $this->_liveUrls['RateRest'] . '/';
            } else {
                $url = $this->_defaultUrls['RateRest'] . '/';
            }
        }
        try {
            $accessToken = $this->setAPIAccessRequest();
        } catch (Exception $e) {
            Mage::logException($e);
            $result = Mage::getModel('shipping/rate_result');
            $result->setError('Authentication error');
            return $result;
        }
        $rowRequest = $this->_rawRequest;

        $params = $this->setQuoteRequestData($rowRequest);
        $serviceCode = $params['serviceCode'];
        $serviceDescription = $params['serviceDescription'];

        $shipperNumber = '';
        if ($this->getConfigFlag('negotiated_active') && ($shipperNumber = $this->getConfigData('shipper_number'))) {
            $shipperNumber = $this->getConfigData('shipper_number');
        }

        if ($rowRequest->getIsReturn()) {
            $shipperCity = '';
            $shipperPostalCode = $params['19_destPostal'];
            $shipperCountryCode = $params['22_destCountry'];
            $shipperStateProvince = $params['destRegionCode'];
        } else {
            $shipperCity = $params['origCity'];
            $shipperPostalCode = $params['15_origPostal'];
            $shipperCountryCode = $params['14_origCountry'];
            $shipperStateProvince = $params['origRegionCode'];
        }

        $rateParams = [
            'RateRequest' => [
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => 'Rating and Service',
                    ],
                ],
                'Shipment' => [
                    'Shipper' => [
                        'Name' => 'UPS',
                        'ShipperNumber' => "{$shipperNumber}",
                        'Address' => [
                            'AddressLine' => [],
                            'City' => "{$shipperCity}",
                            'StateProvinceCode' => "{$shipperStateProvince}",
                            'PostalCode' => "{$shipperPostalCode}",
                            'CountryCode' => "{$shipperCountryCode}",
                        ],
                    ],
                    'ShipTo' => [
                        'Address' => [
                            'AddressLine' => ["{$params['49_residential']}"],
                            'StateProvinceCode' => "{$params['destRegionCode']}",
                            'PostalCode' => "{$params['19_destPostal']}",
                            'CountryCode' => "{$params['22_destCountry']}",
                        ],
                    ],
                    'ShipFrom' => [
                        'Address' => [
                            'AddressLine' => [],
                            'StateProvinceCode' => "{$params['origRegionCode']}",
                            'PostalCode' => "{$params['15_origPostal']}",
                            'CountryCode' => "{$params['14_origCountry']}",
                        ],
                    ],
                ],
            ],
        ];

        if ($params['49_residential'] === '01') {
            $rateParams['RateRequest']['Shipment']['ShipTo']['Address']['ResidentialAddressIndicator'] = '1';
        }

        if ($this->getConfigFlag('negotiated_active')) {
            $rateParams['RateRequest']['Shipment']['ShipmentRatingOptions']['TPFCNegotiatedRatesIndicator'] = 'Y';
            $rateParams['RateRequest']['Shipment']['ShipmentRatingOptions']['NegotiatedRatesIndicator'] = 'Y';
        }
        if ($this->getConfigFlag('include_taxes')) {
            $rateParams['RateRequest']['Shipment']['TaxInformationIndicator'] = 'Y';
        }

        if ($serviceCode !== null) {
            $rateParams['RateRequest']['Shipment']['Service']['Code'] = $serviceCode;
            $rateParams['RateRequest']['Shipment']['Service']['Description'] = $serviceDescription;
        }

        $rateParams['RateRequest']['Shipment']['Package'][] = [
            'PackagingType' => [
                'Code' => "{$params['48_container']}",
                'Description' => 'Packaging',
            ],
            'Dimensions' => [
                'UnitOfMeasurement' => [
                    'Code' => $rowRequest->getUnitDimensions(),
                    'Description' => $rowRequest->getUnitDimensionsDescription(),
                ],
                'Length' => '5',
                'Width' => '5',
                'Height' => '5',
            ],
            'PackageWeight' => [
                'UnitOfMeasurement' => [
                    'Code' => "{$rowRequest->getUnitMeasure()}",
                ],
                'Weight' => "{$params['23_weight']}",
            ],
        ];

        $ratePayload = json_encode($rateParams, JSON_PRETTY_PRINT);
        /** Rest API Payload */
        $version = 'v1';
        $requestOption = $params['10_action'];
        $headers = [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json',
        ];
        $debugData = [
            'request' => $ratePayload,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $version . '/' . $requestOption);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ratePayload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
        $responseData = curl_exec($ch);
        if ($responseData === false) {
            $debugData['result'] = ['error' => curl_error($ch), 'code' => curl_errno($ch)];
            $responseData = '';
        } else {
            $debugData['result'] = $responseData;
        }
        curl_close($ch);

        $this->_debug($debugData);
        return $this->_parseRestResponse($responseData);
    }

    /**
     * Prepare shipping rate result based on response
     * @return Mage_Shipping_Model_Rate_Result
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _parseRestResponse(string $rateResponse)
    {
        $costArr = [];
        $priceArr = [];
        $errorTitle = '';
        if (strlen($rateResponse) > 0) {
            $rateResponseData = json_decode($rateResponse, true);
            if (@$rateResponseData['RateResponse']['Response']['ResponseStatus']['Description'] === 'Success') {
                $arr = $rateResponseData['RateResponse']['RatedShipment'] ?? [];
                if (isset($arr['Service'])) {
                    // Handling cases where a single service is returned by UPS
                    $arr = [$arr];
                }
                $allowedMethods = explode(',', $this->getConfigData('allowed_methods') ?? '');
                $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
                foreach ($arr as $shipElement) {
                    $negotiatedArr = $shipElement['NegotiatedRateCharges'] ?? [] ;
                    $negotiatedActive = $this->getConfigFlag('negotiated_active')
                        && $this->getConfigData('shipper_number')
                        && !empty($negotiatedArr);

                    $this->processShippingRestRateForItem(
                        $shipElement,
                        $allowedMethods,
                        $allowedCurrencies,
                        $costArr,
                        $priceArr,
                        $negotiatedActive,
                    );
                }
            } else {
                $errorTitle = $rateResponseData['RateResponse']['Response']['ResponseStatus']['Description'] ?? '';
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier('ups');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            }
        }

        return $this->setRatePriceData($priceArr, $costArr, $errorTitle);
    }

    private function setRatePriceData(array $priceArr, array $costArr, string $errorTitle): Mage_Shipping_Model_Rate_Result
    {
        $result = Mage::getModel('shipping/rate_result');

        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if ($this->getConfigData('specificerrmsg') !== '') {
                $errorTitle = $this->getConfigData('specificerrmsg');
            }
            $error->setErrorMessage($errorTitle);
            $result->append($error);
            return $result;
        }

        foreach ($priceArr as $method => $price) {
            $shipmentDescription = $this->getShipmentByCode($method);
            if (!strlen($shipmentDescription)) {
                continue;
            }

            $rate = Mage::getModel('shipping/rate_result_method');
            $rate->setCarrier('ups');
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod($method);
            $rate->setMethodTitle($shipmentDescription);
            $rate->setCost($costArr[$method]);
            $rate->setPrice($price);
            $result->append($rate);
        }

        return $result;
    }

    /**
     * Processing rate for ship element
     */
    private function processShippingRestRateForItem(
        array $shipElement,
        array $allowedMethods,
        array $allowedCurrencies,
        array &$costArr,
        array &$priceArr,
        bool $negotiatedActive
    ): void {
        $code = $shipElement['Service']['Code'] ?? '';
        if (in_array($code, $allowedMethods)) {
            //The location of tax information is in a different place
            // depending on whether we are using negotiated rates or not
            if ($negotiatedActive) {
                $includeTaxesArr = $shipElement['NegotiatedRateCharges']['TotalChargesWithTaxes'] ?? [];
                $includeTaxesActive = $this->getConfigFlag('include_taxes') && !empty($includeTaxesArr);
                if ($includeTaxesActive) {
                    $cost = $shipElement['NegotiatedRateCharges']['TotalChargesWithTaxes']['MonetaryValue'];

                    $responseCurrencyCode = $this->mapCurrencyCode(
                        (string) $shipElement['NegotiatedRateCharges']['TotalChargesWithTaxes']['CurrencyCode'],
                    );
                } else {
                    $cost = $shipElement['NegotiatedRateCharges']['TotalCharge']['MonetaryValue'];
                    $responseCurrencyCode = $this->mapCurrencyCode(
                        (string) $shipElement['NegotiatedRateCharges']['TotalCharge']['CurrencyCode'],
                    );
                }
            } else {
                $includeTaxesArr = $shipElement['TotalChargesWithTaxes'] ?? [];
                $includeTaxesActive = $this->getConfigFlag('include_taxes') && !empty($includeTaxesArr);
                if ($includeTaxesActive) {
                    $cost = $shipElement['TotalChargesWithTaxes']['MonetaryValue'];
                    $responseCurrencyCode = $this->mapCurrencyCode(
                        (string) $shipElement['TotalChargesWithTaxes']['CurrencyCode'],
                    );
                } else {
                    $cost = $shipElement['TotalCharges']['MonetaryValue'];
                    $responseCurrencyCode = $this->mapCurrencyCode(
                        (string) $shipElement['TotalCharges']['CurrencyCode'],
                    );
                }
            }

            //convert price with Origin country currency code to base currency code
            $successConversion = true;
            if ($responseCurrencyCode) {
                if (in_array($responseCurrencyCode, $allowedCurrencies)) {
                    $cost = (float) $cost * $this->_getBaseCurrencyRate($responseCurrencyCode);
                } else {
                    $errorTitle = Mage::helper('usa')->__(
                        'We can\'t convert a rate from "%1-%2".',
                        $responseCurrencyCode,
                        $this->_request->getPackageCurrency()->getCode(),
                    );
                    $error = Mage::getModel('shipping/rate_result_error');
                    $error->setCarrier('ups');
                    $error->setCarrierTitle($this->getConfigData('title'));
                    $error->setErrorMessage($errorTitle);
                    $successConversion = false;
                }
            }

            if ($successConversion) {
                $costArr[$code] = $cost;
                $priceArr[$code] = $this->getMethodPrice((float) $cost, $code);
            }
        }
    }

    /**
     * To receive access token
     *
     * @return string
     * @throws Exception
     */
    protected function setAPIAccessRequest()
    {
        $userId = $this->getConfigData('client_id');
        $userIdPass = $this->getConfigData('client_secret');
        if ($this->getConfigFlag('mode_xml')) {
            $authUrl = $this->_liveUrls['AuthUrl'];
        } else {
            $authUrl = $this->_defaultUrls['AuthUrl'];
        }
        return Mage::getModel('usa/shipping_carrier_upsAuth')->getAccessToken($userId, $userIdPass, $authUrl);
    }

    /**
     * Setting common request params for Rate Request
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private function setQuoteRequestData(Varien_Object $rowRequest): array
    {
        if (self::USA_COUNTRY_ID == $rowRequest->getDestCountry()) {
            $destPostal = substr((string) $rowRequest->getDestPostal(), 0, 5);
        } else {
            $destPostal = $rowRequest->getDestPostal();
        }
        $params = [
            '10_action' => $rowRequest->getAction(),
            '13_product' => $rowRequest->getProduct(),
            '14_origCountry' => $rowRequest->getOrigCountry(),
            '15_origPostal' => $rowRequest->getOrigPostal(),
            'origCity' => $rowRequest->getOrigCity(),
            'origRegionCode' => $rowRequest->getOrigRegionCode(),
            '19_destPostal' => $destPostal,
            '22_destCountry' => $rowRequest->getDestCountry(),
            'destRegionCode' => $rowRequest->getDestRegionCode(),
            '23_weight' => $rowRequest->getWeight(),
            '47_rate_chart' => $rowRequest->getPickup(),
            '48_container' => $rowRequest->getContainer(),
            '49_residential' => $rowRequest->getDestType(),
        ];

        if ($params['10_action'] == '4') {
            $params['10_action'] = 'Shop';
            $params['serviceCode'] = null;
        } else {
            $params['10_action'] = 'Rate';
            $params['serviceCode'] = $rowRequest->getProduct() ? $rowRequest->getProduct() : null;
        }
        $params['serviceDescription'] = $params['serviceCode'] ? $this->getShipmentByCode($params['serviceCode']) : '';
        return $params;
    }

    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private function mapCurrencyCode(string $code): string
    {
        $currencyMapping = [
            'RMB' => 'CNY',
            'CNH' => 'CNY',
        ];

        return $currencyMapping[$code] ?? $code;
    }
}
