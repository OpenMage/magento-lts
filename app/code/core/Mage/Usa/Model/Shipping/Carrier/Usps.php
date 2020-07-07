<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * USPS shipping rates estimation
 *
 * @link       http://www.usps.com/webtools/htm/Development-Guide-v3-0b.htm
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Usps
    extends Mage_Usa_Model_Shipping_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * USPS containers
     */
    const CONTAINER_VARIABLE           = 'VARIABLE';
    const CONTAINER_FLAT_RATE_BOX      = 'FLAT RATE BOX';
    const CONTAINER_FLAT_RATE_ENVELOPE = 'FLAT RATE ENVELOPE';
    const CONTAINER_RECTANGULAR        = 'RECTANGULAR';
    const CONTAINER_NONRECTANGULAR     = 'NONRECTANGULAR';

    /**
     * USPS size
     */
    const SIZE_REGULAR = 'REGULAR';
    const SIZE_LARGE   = 'LARGE';

    /**
     * Default api revision
     *
     * @var int
     */
    const DEFAULT_REVISION = 2;

    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'usps';

    /**
     * Ounces in one pound for conversion
     */
    const OUNCES_POUND = 16;

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Destination Zip Code required flag
     *
     * @var boolean
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
     * Rate result data
     *
     * @var Mage_Shipping_Model_Rate_Result|null
     */
    protected $_result = null;

    /**
     * Default cgi gateway url
     *
     * @var string
     */
    protected $_defaultGatewayUrl = 'http://production.shippingapis.com/ShippingAPI.dll';

    /**
     * Container types that could be customized for USPS carrier
     *
     * @var array
     */
    protected $_customizableContainerTypes = array('VARIABLE', 'RECTANGULAR', 'NONRECTANGULAR');

    /**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
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
     * @param Mage_Shipping_Model_Rate_Request $request
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

        if ($request->getUspsUserid()) {
            $userId = $request->getUspsUserid();
        } else {
            $userId = $this->getConfigData('userid');
        }
        $r->setUserId($userId);

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

        if ($request->getHeight()) {
            $height = $request->getHeight();
        } else {
            $height = $this->getConfigData('height');
        }
        $r->setHeight($height);

        if ($request->getLength()) {
            $length = $request->getLength();
        } else {
            $length = $this->getConfigData('length');
        }
        $r->setLength($length);

        if ($request->getWidth()) {
            $width = $request->getWidth();
        } else {
            $width = $this->getConfigData('width');
        }
        $r->setWidth($width);

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
                $request->getStoreId()
            ));
        }

        if ($request->getOrigCountryId()) {
            $r->setOrigCountryId($request->getOrigCountryId());
        } else {
            $r->setOrigCountryId(Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $request->getStoreId()
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
        $r->setWeightOunces(round(($weight-floor($weight)) * self::OUNCES_POUND, 1));
        if ($request->getFreeMethodWeight()!=$request->getPackageWeight()) {
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
     * @return mixed
     */
    public function getResult()
    {
       return $this->_result;
    }

    /**
     * @inheritdoc
     * Starting from 23.02.2018 USPS doesn't allow to create free shipping labels via their API.
     */
    public function isShippingLabelsAvailable()
    {
        return false;
    }

    /**
     * Get quotes
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        return $this->_getXmlQuotes();
    }

    /**
     * Set free method request
     *
     * @param  $freeMethod
     * @return void
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;

        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $r->setWeightPounds(floor($weight));
        $r->setWeightOunces(round(($weight - floor($weight)) * self::OUNCES_POUND, 1));
        $r->setService($freeMethod);
    }

    /**
     * Build RateV3 request, send it to USPS gateway and retrieve quotes in XML format
     *
     * @link http://www.usps.com/webtools/htm/Rate-Calculators-v2-3.htm
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;

        // The origin address(shipper) must be only in USA
        if (!$this->_isUSCountry($r->getOrigCountryId())){
            $responseBody = '';
            return $this->_parseXmlResponse($responseBody);
        }

        if ($this->_isUSCountry($r->getDestCountryId())) {
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><RateV4Request/>');
            $xml->addAttribute('USERID', $r->getUserId());
            // according to usps v4 documentation
            $xml->addChild('Revision', '2');

            $package = $xml->addChild('Package');
            $package->addAttribute('ID', 0);
            $service = $this->getCode('service_to_code', $r->getService());
            if (!$service) {
                $service = $r->getService();
            }
            if ($r->getContainer() == 'FLAT RATE BOX' || $r->getContainer() == 'FLAT RATE ENVELOPE') {
                $service = 'Priority';
            }
            $package->addChild('Service', $service);

            // no matter Letter, Flat or Parcel, use Parcel
            if ($r->getService() == 'FIRST CLASS' || $r->getService() == 'FIRST CLASS HFP COMMERCIAL') {
                $package->addChild('FirstClassMailType', 'PARCEL');
            }
            if ($r->getService() == 'FIRST CLASS COMMERCIAL') {
                $package->addChild('FirstClassMailType', 'PACKAGE SERVICE');
            }

            $package->addChild('ZipOrigination', $r->getOrigPostal());
            //only 5 chars available
            $package->addChild('ZipDestination', substr($r->getDestPostal(), 0, 5));
            $package->addChild('Pounds', $r->getWeightPounds());
            $package->addChild('Ounces', $r->getWeightOunces());
            // Because some methods don't accept VARIABLE and (NON)RECTANGULAR containers
            $package->addChild('Container', $r->getContainer());
            $package->addChild('Size', $r->getSize());
            if ($r->getSize() == 'LARGE') {
                $package->addChild('Width', $r->getWidth());
                $package->addChild('Length', $r->getLength());
                $package->addChild('Height', $r->getHeight());
                if ($r->getContainer() == 'NONRECTANGULAR' || $r->getContainer() == 'VARIABLE') {
                    $package->addChild('Girth', $r->getGirth());
                }
            }
            $package->addChild('Machinable', $r->getMachinable());

            $api = 'RateV4';
        } else {
            $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><IntlRateV2Request/>');
            $xml->addAttribute('USERID', $r->getUserId());
            // according to usps v4 documentation
            $xml->addChild('Revision', '2');

            $package = $xml->addChild('Package');
            $package->addAttribute('ID', 0);
            $package->addChild('Pounds', $r->getWeightPounds());
            $package->addChild('Ounces', $r->getWeightOunces());
            $package->addChild('MailType', 'All');
            $package->addChild('ValueOfContents', $r->getValue());
            $package->addChild('Country', $r->getDestCountryName());
            $package->addChild('Container', $r->getContainer());
            $package->addChild('Size', $r->getSize());
            $width = $length = $height = $girth = '';
            if ($r->getSize() == 'LARGE') {
                $width = $r->getWidth();
                $length = $r->getLength();
                $height = $r->getHeight();
                if ($r->getContainer() == 'NONRECTANGULAR') {
                    $girth = $r->getGirth();
                }
            }
            $package->addChild('Width', $width);
            $package->addChild('Length', $length);
            $package->addChild('Height', $height);
            $package->addChild('Girth', $girth);

            if ($this->_isCanada($r->getDestCountryId())) {
                //only 5 chars available
                $package->addChild('OriginZip', substr($r->getOrigPostal(), 0, 5));
            }
            $api = 'IntlRateV2';
        }
        $request = $xml->asXML();

        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = array('request' => $request);
            try {
                $url = $this->getConfigData('gateway_url');
                if (!$url) {
                    $url = $this->_defaultGatewayUrl;
                }
                $client = new Zend_Http_Client();
                $client->setUri($url);
                $client->setConfig(array('maxredirects' => 0, 'timeout' => 30));
                $client->setParameterGet('API', $api);
                $client->setParameterGet('XML', $request);
                $response = $client->request();
                $responseBody = $response->getBody();

                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $responseBody = '';
            }
            $this->_debug($debugData);
        }
        return $this->_parseXmlResponse($responseBody);
    }

    /**
     * Parse calculated rates
     *
     * @link http://www.usps.com/webtools/htm/Rate-Calculators-v2-3.htm
     * @param string $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseXmlResponse($response)
    {
        $r = $this->_rawRequest;
        $costArr = array();
        $priceArr = array();
        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml') === 0) {
                if (strpos($response, '<?xml version="1.0"?>') !== false) {
                    $response = str_replace(
                        '<?xml version="1.0"?>',
                        '<?xml version="1.0" encoding="ISO-8859-1"?>',
                        $response
                    );
                }
                $xml = simplexml_load_string($response);

                if (is_object($xml)) {
                     $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));
                     $serviceCodeToActualNameMap = array();
                     /**
                      * US Rates
                      */
                      if ($this->_isUSCountry($r->getDestCountryId())) {
                          if (is_object($xml->Package) && is_object($xml->Package->Postage)) {
                             foreach ($xml->Package->Postage as $postage) {
                                $serviceName = $this->_filterServiceName((string)$postage->MailService);
                                $_serviceCode = $this->getCode('method_to_code', $serviceName);
                                $serviceCode = $_serviceCode ? $_serviceCode : (string)$postage->attributes()->CLASSID;
                                $serviceCodeToActualNameMap[$serviceCode] = $serviceName;
                                if (in_array($serviceCode, $allowedMethods)) {
                                     $costArr[$serviceCode] = (string)$postage->Rate;
                                     $priceArr[$serviceCode] = $this->getMethodPrice(
                                         (string)$postage->Rate,
                                         $serviceCode
                                     );
                                 }
                            }
                            asort($priceArr);
                        }
                     }
                     /**
                      * International Rates
                      */
                     else {
                        if (is_object($xml->Package) && is_object($xml->Package->Service)) {
                            foreach ($xml->Package->Service as $service) {
                                if ($service->ServiceErrors->count()) {
                                    continue;
                                }
                                $serviceName = $this->_filterServiceName((string)$service->SvcDescription);
                                $serviceCode = 'INT_' . (string)$service->attributes()->ID;
                                $serviceCodeToActualNameMap[$serviceCode] = $serviceName;
                                    if (in_array($serviceCode, $allowedMethods)) {
                                        $costArr[$serviceCode] = (string)$service->Postage;
                                        $priceArr[$serviceCode] = $this->getMethodPrice(
                                         (string)$service->Postage,
                                            $serviceCode);
                                }
                            }
                            asort($priceArr);
                        }
                    }
                }

        $result = Mage::getModel('shipping/rate_result');
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('usps');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                 $rate->setMethodTitle(
                     isset($serviceCodeToActualNameMap[$method])
                         ? $serviceCodeToActualNameMap[$method]
                         : $this->getCode('method', $method)
                );
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }

        return $result;
            }
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
        $codes = array(
             'method' => array(
                 '0_FCLE' => Mage::helper('usa')->__('First-Class Mail Large Envelope'),
                 '0_FCL'  => Mage::helper('usa')->__('First-Class Mail Letter'),
                 '0_FCSL' => Mage::helper('usa')->__('First-Class Mail Stamped Letter'),
                 '0_FCP'  => Mage::helper('usa')->__('First-Class Package Service - Retail'),
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
                 '61'     => Mage::helper('usa')->__('First-Class Package Service'),
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
             ),

           'service_to_code' => array(
                 '0_FCLE' => 'First Class',
                 '0_FCL'  => 'First Class',
                 '0_FCSL' => 'First Class',
                 '0_FCP'  => 'First Class',
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
                 '61'     => 'First Class',
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
             ),

       // Added because USPS has different services but with same CLASSID value, which is "0"
            'method_to_code' => array(
                'First-Class Mail Large Envelope'      => '0_FCLE',
                'First-Class Mail Letter'              => '0_FCL',
                'First-Class Mail Stamped Letter'      => '0_FCSL',
                'First-Class Mail Metered Letter'      => '72',
                'First-Class Package Service - Retail' => '0_FCP',
            ),

            'first_class_mail_type'=>array(
                'LETTER'      => Mage::helper('usa')->__('Letter'),
                'FLAT'        => Mage::helper('usa')->__('Flat'),
                'PARCEL'      => Mage::helper('usa')->__('Parcel'),
            ),

            'container'=>array(
                'VARIABLE'           => Mage::helper('usa')->__('Variable'),
                'FLAT RATE ENVELOPE' => Mage::helper('usa')->__('Flat-Rate Envelope'),
                'FLAT RATE BOX'      => Mage::helper('usa')->__('Flat-Rate Box'),
                'RECTANGULAR'        => Mage::helper('usa')->__('Rectangular'),
                'NONRECTANGULAR'     => Mage::helper('usa')->__('Non-rectangular'),
            ),

            'containers_filter' => array(
                array(
                    'containers' => array('VARIABLE'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
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
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
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
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('FLAT RATE BOX'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Priority Mail Large Flat Rate Box',
                                'Priority Mail Medium Flat Rate Box',
                                'Priority Mail Small Flat Rate Box',
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                                'Priority Mail International DVD Flat Rate priced box',
                                'Priority Mail International Large Video Flat Rate priced box'
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('FLAT RATE ENVELOPE'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Priority Mail Express Flat Rate Envelope',
                                'Priority Mail Express Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Flat Rate Envelope',
                                'First-Class Mail Large Envelope',
                                'Priority Mail Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Small Flat Rate Envelope',
                                'Priority Mail Small Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Express Sunday/Holiday Delivery Flat Rate Envelope',
                                'Priority Mail Express Padded Flat Rate Envelope'
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                 'Priority Mail Express International Flat Rate Envelope',
                                 'Priority Mail International Flat Rate Envelope',
                                 'First-Class Mail International Large Envelope',
                                 'Priority Mail International Small Flat Rate Envelope',
                                 'Priority Mail Express International Legal Flat Rate Envelope',
                                 'Priority Mail International Gift Card Flat Rate Envelope',
                                 'Priority Mail International Window Flat Rate Envelope',
                                 'Priority Mail International Legal Flat Rate Envelope',
                                 'Priority Mail Express International Padded Flat Rate Envelope',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('RECTANGULAR'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Priority Mail Express',
                                'Priority Mail',
                                'Retail Ground',
                                'Media Mail',
                                'Library Mail',
                                'First-Class Package Service'
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'USPS GXG Envelopes',
                                'Priority Mail Express International',
                                'Priority Mail International',
                                'First-Class Package International Service',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('NONRECTANGULAR'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Priority Mail Express',
                                'Priority Mail',
                                'Retail Ground',
                                'Media Mail',
                                'Library Mail',
                                'First-Class Package Service'
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'Global Express Guaranteed (GXG)',
                                'Priority Mail Express International',
                                'Priority Mail International',
                                'First-Class Package International Service',
                            )
                        )
                    )
                ),
          ),
            'size'=>array(
                'REGULAR'     => Mage::helper('usa')->__('Regular'),
                'LARGE'       => Mage::helper('usa')->__('Large'),
            ),

            'machinable'=>array(
                'true'        => Mage::helper('usa')->__('Yes'),
                'false'       => Mage::helper('usa')->__('No'),
            ),

            'delivery_confirmation_types' => array(
                'True' => Mage::helper('usa')->__('Not Required'),
                'False'  => Mage::helper('usa')->__('Required'),
            ),
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }
    /**
     * Get tracking
     *
     * @param mixed $trackingData
     * @return mixed
     */
    public function getTracking($trackingData)
    {
        $this->setTrackingRequest();

        if (!is_array($trackingData)) {
            $trackingData = array($trackingData);
        }

        $this->_getXmlTracking($trackingData);

        return $this->_result;
    }

    /**
     * Set tracking request
     *
     * @return null
     */
    protected function setTrackingRequest()
    {
        $r = new Varien_Object();

        $userId = $this->getConfigData('userid');
        $r->setUserId($userId);

        $this->_rawTrackRequest = $r;
    }

    /**
     * Send request for tracking
     *
     * @param array $trackingData
     */
    protected function _getXmlTracking($trackingData)
    {
         $r = $this->_rawTrackRequest;

         foreach ($trackingData as $tracking) {
             $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><TrackRequest/>');
             $xml->addAttribute('USERID', $r->getUserId());

             $trackid = $xml->addChild('TrackID');
             $trackid->addAttribute('ID',$tracking);

             $api = 'TrackV2';
             $request = $xml->asXML();
             $debugData = array('request' => $request);

             try {
                $url = $this->getConfigData('gateway_url');
                if (!$url) {
                    $url = $this->_defaultGatewayUrl;
                }
                $client = new Zend_Http_Client();
                $client->setUri($url);
                $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
                $client->setParameterGet('API', $api);
                $client->setParameterGet('XML', $request);
                $response = $client->request();
                $responseBody = $response->getBody();
                $debugData['result'] = $responseBody;
            }
            catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $responseBody = '';
            }

            $this->_debug($debugData);
            $this->_parseXmlTrackingResponse($tracking, $responseBody);
         }
    }
    /**
     * Parse xml tracking response
     *
     * @param array $trackingValue
     * @param string $response
     * @return null
     */
    protected function _parseXmlTrackingResponse($trackingValue, $response)
    {
        $errorTitle = Mage::helper('usa')->__('Unable to retrieve tracking');
        $resultArr=array();
        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml')===0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if (isset($xml->Number) && isset($xml->Description) && (string)$xml->Description!='') {
                        $errorTitle = (string)$xml->Description;
                    } elseif (isset($xml->TrackInfo)
                          && isset($xml->TrackInfo->Error)
                          && isset($xml->TrackInfo->Error->Description)
                          && (string)$xml->TrackInfo->Error->Description!=''
                    ) {
                        $errorTitle = (string)$xml->TrackInfo->Error->Description;
                    } else {
                        $errorTitle = Mage::helper('usa')->__('Unknown error');
                    }

                    if(isset($xml->TrackInfo) && isset($xml->TrackInfo->TrackSummary)){
                       $resultArr['tracksummary'] = (string)$xml->TrackInfo->TrackSummary;

                    }
                }
            }
        }

        if (!$this->_result) {
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if ($resultArr) {
             $tracking = Mage::getModel('shipping/tracking_result_status');
             $tracking->setCarrier('usps');
             $tracking->setCarrierTitle($this->getConfigData('title'));
             $tracking->setTracking($trackingValue);
             $tracking->setTrackSummary($resultArr['tracksummary']);
             $this->_result->append($tracking);
         } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingValue);
            $error->setErrorMessage($errorTitle);
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
            if ($trackingData = $this->_result->getAllTrackings()) {
                foreach ($trackingData as $tracking) {
                    if($data = $tracking->getAllData()) {
                        if (!empty($data['track_summary'])) {
                            $statuses .= Mage::helper('usa')->__($data['track_summary']);
                        } else {
                            $statuses .= Mage::helper('usa')->__('Empty response');
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
        $arr = array();
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
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
        $countries = array (
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
          'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
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
        );

        if (isset($countries[$countryId])) {
            return $countries[$countryId];
        }

        return false;
    }

    /**
     * Clean service name from unsupported strings and characters
     *
     * @param  string $name
     * @return string
     */
    protected function _filterServiceName($name)
    {
        $name = (string)preg_replace(array('~<[^/!][^>]+>.*</[^>]+>~sU', '~\<!--.*--\>~isU', '~<[^>]+>~is'), '',
            html_entity_decode($name)
        );
        $name = str_replace('*', '', $name);

        return $name;
    }

    /**
     * Form XML for US shipment request
     * As integration guide it is important to follow appropriate sequence for tags e.g.: <FromLastName /> must be
     * after <FromFirstName />
     *
     * @param Varien_Object $request
     * @return string
     * @deprecated This method should not be used anymore.
     * @see Mage_Usa_Model_Shipping_Carrier_Usps::_doShipmentRequest method doc block.
     */
    protected function _formUsExpressShipmentRequest(Varien_Object $request)
    {
        $packageParams = $request->getPackageParams();

        $packageWeight = $request->getPackageWeight();
        if ($packageParams->getWeightUnits() != Zend_Measure_Weight::OUNCE) {
            $packageWeight = round(Mage::helper('usa')->convertMeasureWeight(
                $request->getPackageWeight(),
                $packageParams->getWeightUnits(),
                Zend_Measure_Weight::OUNCE
            ));
        }

        list($fromZip5, $fromZip4) = $this->_parseZip($request->getShipperAddressPostalCode());
        list($toZip5, $toZip4) = $this->_parseZip($request->getRecipientAddressPostalCode(), true);

        $rootNode = 'ExpressMailLabelRequest';
        // the wrap node needs for remove xml declaration above
        $xmlWrap = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><wrap/>');
        $xml = $xmlWrap->addChild($rootNode);
        $xml->addAttribute('USERID', $this->getConfigData('userid'));
        $xml->addAttribute('PASSWORD', $this->getConfigData('password'));
        $xml->addChild('Option');
        $xml->addChild('Revision');
        $xml->addChild('EMCAAccount');
        $xml->addChild('EMCAPassword');
        $xml->addChild('ImageParameters');
        $xml->addChild('FromFirstName', $request->getShipperContactPersonFirstName());
        $xml->addChild('FromLastName', $request->getShipperContactPersonLastName());
        $xml->addChild('FromFirm', $request->getShipperContactCompanyName());
        $xml->addChild('FromAddress1', $request->getShipperAddressStreet2());
        $xml->addChild('FromAddress2', $request->getShipperAddressStreet1());
        $xml->addChild('FromCity', $request->getShipperAddressCity());
        $xml->addChild('FromState', $request->getShipperAddressStateOrProvinceCode());
        $xml->addChild('FromZip5', $fromZip5);
        $xml->addChild('FromZip4', $fromZip4);
        $xml->addChild('FromPhone', $request->getShipperContactPhoneNumber());
        $xml->addChild('ToFirstName', $request->getRecipientContactPersonFirstName());
        $xml->addChild('ToLastName', $request->getRecipientContactPersonLastName());
        $xml->addChild('ToFirm', $request->getRecipientContactCompanyName());
        $xml->addChild('ToAddress1', $request->getRecipientAddressStreet2());
        $xml->addChild('ToAddress2', $request->getRecipientAddressStreet1());
        $xml->addChild('ToCity', $request->getRecipientAddressCity());
        $xml->addChild('ToState', $request->getRecipientAddressStateOrProvinceCode());
        $xml->addChild('ToZip5', $toZip5);
        $xml->addChild('ToZip4', $toZip4);
        $xml->addChild('ToPhone', $request->getRecipientContactPhoneNumber());
        $xml->addChild('WeightInOunces', $packageWeight);
        $xml->addChild('WaiverOfSignature', $packageParams->getDeliveryConfirmation());
        $xml->addChild('POZipCode');
        $xml->addChild('ImageType', 'PDF');

        $xml = $xmlWrap->{$rootNode}->asXML();
        return $xml;
    }

    /**
     * Form XML for US Signature Confirmation request
     * As integration guide it is important to follow appropriate sequence for tags e.g.: <FromLastName /> must be
     * after <FromFirstName />
     *
     * @param Varien_Object $request
     * @param string $serviceType
     *
     * @throws Exception
     *
     * @return string
     */
    protected function _formUsSignatureConfirmationShipmentRequest(Varien_Object $request, $serviceType)
    {
        switch ($serviceType) {
            case 'PRIORITY':
            case 'Priority':
                $serviceType = 'Priority';
                break;
            case 'FIRST CLASS':
            case 'First Class':
                $serviceType = 'First Class';
                break;
            case 'STANDARD':
            case 'Standard Post':
            case 'Retail Ground':
                $serviceType = 'Retail Ground';
                break;
            case 'MEDIA':
            case 'Media':
                $serviceType = 'Media Mail';
                break;
            case 'LIBRARY':
            case 'Library':
                $serviceType = 'Library Mail';
                break;
            default:
                throw new Exception(Mage::helper('usa')->__('Service type does not match'));
        }
        $packageParams = $request->getPackageParams();
        $packageWeight = $request->getPackageWeight();
        if ($packageParams->getWeightUnits() != Zend_Measure_Weight::OUNCE) {
            $packageWeight = round(Mage::helper('usa')->convertMeasureWeight(
                $request->getPackageWeight(),
                $packageParams->getWeightUnits(),
                Zend_Measure_Weight::OUNCE
            ));
        }

        list($fromZip5, $fromZip4) = $this->_parseZip($request->getShipperAddressPostalCode());
        list($toZip5, $toZip4) = $this->_parseZip($request->getRecipientAddressPostalCode(), true);

        if ($this->getConfigData('mode')) {
            $rootNode = 'SignatureConfirmationV3.0Request';
        } else {
            $rootNode = 'SigConfirmCertifyV3.0Request';
        }
        // the wrap node needs for remove xml declaration above
        $xmlWrap = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><wrap/>');
        $xml = $xmlWrap->addChild($rootNode);
        $xml->addAttribute('USERID', $this->getConfigData('userid'));
        $xml->addChild('Option', 1);
        $xml->addChild('ImageParameters');
        $xml->addChild('FromName', $request->getShipperContactPersonName());
        $xml->addChild('FromFirm', $request->getShipperContactCompanyName());
        $xml->addChild('FromAddress1', $request->getShipperAddressStreet2());
        $xml->addChild('FromAddress2', $request->getShipperAddressStreet1());
        $xml->addChild('FromCity', $request->getShipperAddressCity());
        $xml->addChild('FromState', $request->getShipperAddressStateOrProvinceCode());
        $xml->addChild('FromZip5', $fromZip5);
        $xml->addChild('FromZip4', $fromZip4);
        $xml->addChild('ToName', $request->getRecipientContactPersonName());
        $xml->addChild('ToFirm', $request->getRecipientContactCompanyName());
        $xml->addChild('ToAddress1', $request->getRecipientAddressStreet2());
        $xml->addChild('ToAddress2', $request->getRecipientAddressStreet1());
        $xml->addChild('ToCity', $request->getRecipientAddressCity());
        $xml->addChild('ToState', $request->getRecipientAddressStateOrProvinceCode());
        $xml->addChild('ToZip5', $toZip5);
        $xml->addChild('ToZip4', $toZip4);
        $xml->addChild('WeightInOunces', $packageWeight);
        $xml->addChild('ServiceType', $serviceType);
        $xml->addChild('WaiverOfSignature', $packageParams->getDeliveryConfirmation());
        $xml->addChild('ImageType', 'PDF');

        $xml = $xmlWrap->{$rootNode}->asXML();
        return $xml;
    }

    /**
     * Convert decimal weight into pound-ounces format
     *
     * @param float $weightInPounds
     * @return array
     */
    protected function _convertPoundOunces($weightInPounds)
    {
        $weightInOunces = ceil($weightInPounds * self::OUNCES_POUND);
        $pounds = floor($weightInOunces / self::OUNCES_POUND);
        $ounces = $weightInOunces % self::OUNCES_POUND;
        return array($pounds, $ounces);
    }

    /**
     * Form XML for international shipment request
     * As integration guide it is important to follow appropriate sequence for tags e.g.: <FromLastName /> must be
     * after <FromFirstName />
     *
     * @param Varien_Object $request
     * @return string
     * @deprecated Should not be used anymore.
     * @see Mage_Usa_Model_Shipping_Carrier_Usps::_doShipmentRequest doc block.
     */
    protected function _formIntlShipmentRequest(Varien_Object $request)
    {
        $packageParams = $request->getPackageParams();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $girth = $packageParams->getGirth();
        $packageWeight = $request->getPackageWeight();
        if ($packageParams->getWeightUnits() != Zend_Measure_Weight::POUND) {
            $packageWeight = Mage::helper('usa')->convertMeasureWeight(
                $request->getPackageWeight(),
                $packageParams->getWeightUnits(),
                Zend_Measure_Weight::POUND
            );
        }
        if ($packageParams->getDimensionUnits() != Zend_Measure_Length::INCH) {
            $length = round(Mage::helper('usa')->convertMeasureDimension(
                $packageParams->getLength(),
                $packageParams->getDimensionUnits(),
                Zend_Measure_Length::INCH
            ));
            $width = round(Mage::helper('usa')->convertMeasureDimension(
                $packageParams->getWidth(),
                $packageParams->getDimensionUnits(),
                Zend_Measure_Length::INCH
            ));
            $height = round(Mage::helper('usa')->convertMeasureDimension(
                $packageParams->getHeight(),
                $packageParams->getDimensionUnits(),
                Zend_Measure_Length::INCH
            ));
        }
        if ($packageParams->getGirthDimensionUnits() != Zend_Measure_Length::INCH) {
            $girth = round(Mage::helper('usa')->convertMeasureDimension(
                $packageParams->getGirth(),
                $packageParams->getGirthDimensionUnits(),
                Zend_Measure_Length::INCH
            ));
        }

        $container = $request->getPackagingType();
        switch ($container) {
            case 'VARIABLE':
                $container = 'VARIABLE';
                break;
            case 'FLAT RATE ENVELOPE':
                $container = 'FLATRATEENV';
                break;
            case 'FLAT RATE BOX':
                $container = 'FLATRATEBOX';
                break;
            case 'RECTANGULAR':
                $container = 'RECTANGULAR';
                break;
            case 'NONRECTANGULAR':
                $container = 'NONRECTANGULAR';
                break;
            default:
                $container = 'VARIABLE';
        }
        $shippingMethod = $request->getShippingMethod();
        list($fromZip5, $fromZip4) = $this->_parseZip($request->getShipperAddressPostalCode());

        // the wrap node needs for remove xml declaration above
        $xmlWrap = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><wrap/>');
        $method = '';
        $service = $this->getCode('service_to_code', $shippingMethod);
        if ($service == 'Priority') {
            $method = 'Priority';
            $rootNode = 'PriorityMailIntlRequest';
            $xml = $xmlWrap->addChild($rootNode);
        } else if ($service == 'First Class') {
            $method = 'FirstClass';
            $rootNode = 'FirstClassMailIntlRequest';
            $xml = $xmlWrap->addChild($rootNode);
        } else {
            $method = 'Express';
            $rootNode = 'ExpressMailIntlRequest';
            $xml = $xmlWrap->addChild($rootNode);
        }

        $xml->addAttribute('USERID', $this->getConfigData('userid'));
        $xml->addAttribute('PASSWORD', $this->getConfigData('password'));
        $xml->addChild('Option');
        $xml->addChild('Revision', self::DEFAULT_REVISION);
        $xml->addChild('ImageParameters');
        $xml->addChild('FromFirstName', $request->getShipperContactPersonFirstName());
        $xml->addChild('FromLastName', $request->getShipperContactPersonLastName());
        $xml->addChild('FromFirm', $request->getShipperContactCompanyName());
        $xml->addChild('FromAddress1', $request->getShipperAddressStreet2());
        $xml->addChild('FromAddress2', $request->getShipperAddressStreet1());
        $xml->addChild('FromCity', $request->getShipperAddressCity());
        $xml->addChild('FromState', $request->getShipperAddressStateOrProvinceCode());
        $xml->addChild('FromZip5', $fromZip5);
        $xml->addChild('FromZip4', $fromZip4);
        $xml->addChild('FromPhone', $request->getShipperContactPhoneNumber());
        if ($method != 'FirstClass') {
            if ($request->getReferenceData()) {
                $referenceData = $request->getReferenceData() . ' P' . $request->getPackageId();
            } else {
                $referenceData = $request->getOrderShipment()->getOrder()->getIncrementId()
                                 . ' P'
                                 . $request->getPackageId();
            }
            $xml->addChild('FromCustomsReference', 'Order #' . $referenceData);
        }
        $xml->addChild('ToFirstName', $request->getRecipientContactPersonFirstName());
        $xml->addChild('ToLastName', $request->getRecipientContactPersonLastName());
        $xml->addChild('ToFirm', $request->getRecipientContactCompanyName());
        $xml->addChild('ToAddress1', $request->getRecipientAddressStreet1());
        $xml->addChild('ToAddress2', $request->getRecipientAddressStreet2());
        $xml->addChild('ToCity', $request->getRecipientAddressCity());
        $xml->addChild('ToProvince', $request->getRecipientAddressStateOrProvinceCode());
        $xml->addChild('ToCountry', $this->_getCountryName($request->getRecipientAddressCountryCode()));
        $xml->addChild('ToPostalCode', $request->getRecipientAddressPostalCode());
        $xml->addChild('ToPOBoxFlag', 'N');
        $xml->addChild('ToPhone', $request->getRecipientContactPhoneNumber());
        $xml->addChild('ToFax');
        $xml->addChild('ToEmail');
        if ($method != 'FirstClass') {
            $xml->addChild('NonDeliveryOption', 'Return');
        }
        if ($method == 'FirstClass') {
            if (stripos($shippingMethod, 'Letter') !== false) {
                $xml->addChild('FirstClassMailType', 'LETTER');
            } else if (stripos($shippingMethod, 'Flat') !== false) {
                $xml->addChild('FirstClassMailType', 'FLAT');
            } else{
                $xml->addChild('FirstClassMailType', 'PARCEL');
            }
        }
        if ($method != 'FirstClass') {
            $xml->addChild('Container', $container);
        }
        $shippingContents = $xml->addChild('ShippingContents');
        $packageItems = $request->getPackageItems();
        // get countries of manufacture
        $countriesOfManufacture = array();
        $productIds = array();
        foreach ($packageItems as $itemShipment) {
                $item = new Varien_Object();
                $item->setData($itemShipment);

                $productIds[]= $item->getProductId();
        }
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addStoreFilter($request->getStoreId())
            ->addFieldToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect('country_of_manufacture');
        foreach ($productCollection as $product) {
            $countriesOfManufacture[$product->getId()] = $product->getCountryOfManufacture();
        }

        $packagePoundsWeight = $packageOuncesWeight = 0;
        // for ItemDetail
        foreach ($packageItems as $itemShipment) {
            $item = new Varien_Object();
            $item->setData($itemShipment);

            $itemWeight = $item->getWeight() * $item->getQty();
            if ($packageParams->getWeightUnits() != Zend_Measure_Weight::POUND) {
                $itemWeight = Mage::helper('usa')->convertMeasureWeight(
                    $itemWeight,
                    $packageParams->getWeightUnits(),
                    Zend_Measure_Weight::POUND
                );
            }
            if (!empty($countriesOfManufacture[$item->getProductId()])) {
                $countryOfManufacture = $this->_getCountryName(
                    $countriesOfManufacture[$item->getProductId()]
                );
            } else {
                $countryOfManufacture = '';
            }
            $itemDetail = $shippingContents->addChild('ItemDetail');
            $itemDetail->addChild('Description', $item->getName());
            $ceiledQty = ceil($item->getQty());
            if ($ceiledQty < 1) {
                $ceiledQty = 1;
            }
            $individualItemWeight = $itemWeight / $ceiledQty;
            $itemDetail->addChild('Quantity', $ceiledQty);
            $itemDetail->addChild('Value', $item->getCustomsValue() * $item->getQty());
            list($individualPoundsWeight, $individualOuncesWeight) = $this->_convertPoundOunces($individualItemWeight);
            $itemDetail->addChild('NetPounds', $individualPoundsWeight);
            $itemDetail->addChild('NetOunces', $individualOuncesWeight);
            $itemDetail->addChild('HSTariffNumber', 0);
            $itemDetail->addChild('CountryOfOrigin', $countryOfManufacture);

            list($itemPoundsWeight, $itemOuncesWeight) = $this->_convertPoundOunces($itemWeight);
            $packagePoundsWeight += $itemPoundsWeight;
            $packageOuncesWeight += $itemOuncesWeight;
        }
        $additionalPackagePoundsWeight = floor($packageOuncesWeight / self::OUNCES_POUND);
        $packagePoundsWeight += $additionalPackagePoundsWeight;
        $packageOuncesWeight -= $additionalPackagePoundsWeight * self::OUNCES_POUND;
        if ($packagePoundsWeight + $packageOuncesWeight / self::OUNCES_POUND < $packageWeight) {
            list($packagePoundsWeight, $packageOuncesWeight) = $this->_convertPoundOunces($packageWeight);
        }

        $xml->addChild('GrossPounds', $packagePoundsWeight);
        $xml->addChild('GrossOunces', $packageOuncesWeight);
        if ($packageParams->getContentType() == 'OTHER' && $packageParams->getContentTypeOther() != null) {
            $xml->addChild('ContentType', $packageParams->getContentType());
            $xml->addChild('ContentTypeOther ', $packageParams->getContentTypeOther());
        } else {
            $xml->addChild('ContentType', $packageParams->getContentType());
        }

        $xml->addChild('Agreement', 'y');
        $xml->addChild('ImageType', 'PDF');
        $xml->addChild('ImageLayout', 'ALLINONEFILE');
        if ($method == 'FirstClass') {
            $xml->addChild('Container', $container);
        }
        // set size
        if ($packageParams->getSize()) {
            $xml->addChild('Size', $packageParams->getSize());
        }
        // set dimensions
        $xml->addChild('Length', $length);
        $xml->addChild('Width', $width);
        $xml->addChild('Height', $height);
        if ($girth) {
            $xml->addChild('Girth', $girth);
        }

        $xml = $xmlWrap->{$rootNode}->asXML();
        return $xml;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     * @deprecated This method must not be used anymore. Starting from 23.02.2018 USPS eliminates API usage for
     * free shipping labels generating.
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $result = new Varien_Object();
        $service = $this->getCode('service_to_code', $request->getShippingMethod());
        $recipientUSCountry = $this->_isUSCountry($request->getRecipientAddressCountryCode());

        if ($recipientUSCountry && $service == 'Priority Express') {
            $requestXml = $this->_formUsExpressShipmentRequest($request);
            $api = 'ExpressMailLabel';
        } else if ($recipientUSCountry) {
            $requestXml = $this->_formUsSignatureConfirmationShipmentRequest($request, $service);
            if ($this->getConfigData('mode')) {
                $api = 'SignatureConfirmationV3';
            } else {
                $api = 'SignatureConfirmationCertifyV3';
            }
        } else if ($service == 'First Class') {
            $requestXml = $this->_formIntlShipmentRequest($request);
            $api = 'FirstClassMailIntl';
        } else if ($service == 'Priority') {
            $requestXml = $this->_formIntlShipmentRequest($request);
            $api = 'PriorityMailIntl';
        } else {
            $requestXml = $this->_formIntlShipmentRequest($request);
            $api = 'ExpressMailIntl';
        }

        $debugData = array('request' => $requestXml);
        $url = $this->getConfigData('gateway_secure_url');
        if (!$url) {
            $url = $this->_defaultGatewayUrl;
        }
        $client = new Zend_Http_Client();
        $client->setUri($url);
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterGet('API', $api);
        $client->setParameterGet('XML', $requestXml);
        $response = $client->request()->getBody();

        $response = simplexml_load_string($response);
        if ($response === false || $response->getName() == 'Error') {
            $debugData['result'] = array(
                'error' => $response->Description,
                'code' => $response->Number,
                'xml' => $response->asXML()
            );
            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        } else {
            if ($recipientUSCountry && $service == 'Priority Express') {
                $labelContent = base64_decode((string) $response->EMLabel);
                $trackingNumber = (string) $response->EMConfirmationNumber;
            } else if ($recipientUSCountry) {
                $labelContent = base64_decode((string) $response->SignatureConfirmationLabel);
                $trackingNumber = (string) $response->SignatureConfirmationNumber;
            } else  {
                $labelContent = base64_decode((string) $response->LabelImage);
                $trackingNumber = (string) $response->BarcodeNumber;
            }
            $result->setShippingLabelContent($labelContent);
            $result->setTrackingNumber($trackingNumber);
        }

        $result->setGatewayResponse($response);
        $debugData['result'] = $response;
        $this->_debug($debugData);
        return $result;
    }

    /**
     * Return container types of carrier
     *
     * @param Varien_Object|null $params
     * @return array|bool
     */
    public function getContainerTypes(Varien_Object $params = null)
    {
        if (is_null($params)) {
            return $this->_getAllowedContainers();
        }
        return $this->_isUSCountry($params->getCountryRecipient()) ? array() : $this->_getAllowedContainers($params);
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
     * @param Varien_Object|null $params
     * @return array
     */
    public function getDeliveryConfirmationTypes(Varien_Object $params = null)
    {
        if ($params == null) {
            return array();
        }
        $countryRecipient = $params->getCountryRecipient();
        if ($this->_isUSCountry($countryRecipient)) {
            return $this->getCode('delivery_confirmation_types');
        } else {
            return array();
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
     * @param Varien_Object $params
     * @return array
     */
    public function getContentTypes(Varien_Object $params)
    {
        $countryShipper     = $params->getCountryShipper();
        $countryRecipient   = $params->getCountryRecipient();

        if ($countryShipper == self::USA_COUNTRY_ID
            && $countryRecipient != self::USA_COUNTRY_ID
        ) {
            return array(
                'MERCHANDISE' => Mage::helper('usa')->__('Merchandise'),
                'SAMPLE' => Mage::helper('usa')->__('Sample'),
                'GIFT' => Mage::helper('usa')->__('Gift'),
                'DOCUMENTS' => Mage::helper('usa')->__('Documents'),
                'RETURN' => Mage::helper('usa')->__('Return'),
                'OTHER' => Mage::helper('usa')->__('Other'),
            );
        }
        return array();
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
        $zip = array($zipString);
        if (preg_match('/[\\d\\w]{5}\\-[\\d\\w]{4}/', $zipString) != 0) {
            $zip = explode('-', $zipString);
        }
        for ($i = 0; $i < count($zip); ++$i) {
            if (strlen($zip[$i]) == 5) {
                $zip5 = $zip[$i];
            } elseif (strlen($zip[$i]) == 4) {
                $zip4 = $zip[$i];
            }
        }
        if (empty($zip5) && empty($zip4) && $returnFull) {
            $zip5 = $zipString;
        }

        return array($zip5, $zip4);
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
}
