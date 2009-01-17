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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * USPS shipping rates estimation
 *
 * @link       http://www.usps.com/webtools/htm/Development-Guide.htm
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Usps
    extends Mage_Usa_Model_Shipping_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'usps';

    protected $_request = null;

    protected $_result = null;

    protected $_defaultGatewayUrl = 'http://production.shippingapis.com/ShippingAPI.dll';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $this->setRequest($request);

        $this->_result = $this->_getQuotes();

        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

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

        if ($request->getUspsMachinable()) {
            $machinable = $request->getUspsMachinable();
        } else {
            $machinable = $this->getConfigData('machinable');
        }
        $r->setMachinable($machinable);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode'));
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        $r->setDestCountryId($destCountry);

        /*
        for GB, we cannot use United Kingdom
        */
        if ($destCountry=='GB') {
           $countryName = 'Great Britain and Northern Ireland';
        } else {
             $countries = Mage::getResourceModel('directory/country_collection')
                            ->addCountryIdFilter($destCountry)
                            ->load()
                            ->getItems();
            $country = array_shift($countries);
            $countryName = $country->getName();
        }
        $r->setDestCountryName($countryName);

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeightPounds(floor($weight));
        $r->setWeightOunces(($weight-floor($weight))*16);
        if ($request->getFreeMethodWeight()!=$request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $this->_rawRequest = $r;

        return $this;
    }

    public function getResult()
    {
       return $this->_result;
    }

    protected function _getQuotes()
    {
        return $this->_getXmlQuotes();
    }

    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;

        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $r->setWeightPounds(floor($weight));
        $r->setWeightOunces(floor(($weight-floor($weight))*16));
        $r->setService($freeMethod);
    }

    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;
        if ($r->getDestCountryId() == self::USA_COUNTRY_ID || $r->getDestCountryId() == self::PUERTORICO_COUNTRY_ID) {
            $xml = new SimpleXMLElement('<RateV3Request/>');

            $xml->addAttribute('USERID', $r->getUserId());

            $package = $xml->addChild('Package');
                $package->addAttribute('ID', 0);
                $package->addChild('Service', $r->getService());

                // no matter Letter, Flat or Parcel, use Parcel
                if ($r->getService() == 'FIRST CLASS') {
                    $package->addChild('FirstClassMailType', 'PARCEL');
                }
                $package->addChild('ZipOrigination', $r->getOrigPostal());
                //only 5 chars avaialble
                $package->addChild('ZipDestination', substr($r->getDestPostal(),0,5));
                $package->addChild('Pounds', $r->getWeightPounds());
                $package->addChild('Ounces', $r->getWeightOunces());
//                $package->addChild('Pounds', '0');
//                $package->addChild('Ounces', '3');
                $package->addChild('Container', $r->getContainer());
                $package->addChild('Size', $r->getSize());
                $package->addChild('Machinable', $r->getMachinable());

            $api = 'RateV3';
            $request = $xml->asXML();

        } else {
            $xml = new SimpleXMLElement('<IntlRateRequest/>');

            $xml->addAttribute('USERID', $r->getUserId());

            $package = $xml->addChild('Package');
                $package->addAttribute('ID', 0);
                $package->addChild('Pounds', $r->getWeightPounds());
                $package->addChild('Ounces', $r->getWeightOunces());
                $package->addChild('MailType', 'Package');
                $package->addChild('ValueOfContents', $r->getValue());
                $package->addChild('Country', $r->getDestCountryName());

            $api = 'IntlRate';
            $request = $xml->asXML();
        }

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
        } catch (Exception $e) {
            $responseBody = '';
        }

        return $this->_parseXmlResponse($responseBody);;
    }

    protected function _parseXmlResponse($response)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve quotes';
        if (strlen(trim($response))>0) {
            if (strpos(trim($response), '<?xml')===0) {
                if (preg_match('#<\?xml version="1.0"\?>#', $response)) {
                    $response = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="ISO-8859-1"?>', $response);
                }

                $xml = simplexml_load_string($response);
                    if (is_object($xml)) {
                        if (is_object($xml->Number) && is_object($xml->Description) && (string)$xml->Description!='') {
                            $errorTitle = (string)$xml->Description;
                        } elseif (is_object($xml->Package) && is_object($xml->Package->Error) && is_object($xml->Package->Error->Description) && (string)$xml->Package->Error->Description!='') {
                            $errorTitle = (string)$xml->Package->Error->Description;
                        } else {
                            $errorTitle = 'Unknown error';
                        }
                        $r = $this->_rawRequest;
                        $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
                        $allMethods = $this->getCode('method');
                        $newMethod = false;
                        if ($r->getDestCountryId() == self::USA_COUNTRY_ID || $r->getDestCountryId() == self::PUERTORICO_COUNTRY_ID) {
                            if (is_object($xml->Package) && is_object($xml->Package->Postage)) {
                                foreach ($xml->Package->Postage as $postage) {
//                                    if (in_array($this->getCode('service_to_code', (string)$postage->MailService), $allowedMethods) && $this->getCode('service', $this->getCode('service_to_code', (string)$postage->MailService))) {
                                    if (in_array((string)$postage->MailService, $allowedMethods)) {
                                        $costArr[(string)$postage->MailService] = (string)$postage->Rate;
//                                        $priceArr[(string)$postage->MailService] = $this->getMethodPrice((string)$postage->Rate, $this->getCode('service_to_code', (string)$postage->MailService));
                                        $priceArr[(string)$postage->MailService] = $this->getMethodPrice((string)$postage->Rate, (string)$postage->MailService);
                                    } elseif (!in_array((string)$postage->MailService, $allMethods)) {
                                        $allMethods[] = (string)$postage->MailService;
                                        $newMethod = true;
                                    }
                                }
                                asort($priceArr);
                            }
                        } else {
                            if (is_object($xml->Package) && is_object($xml->Package->Service)) {
                                foreach ($xml->Package->Service as $service) {
//                                    if (in_array($this->getCode('service_to_code', (string)$service->SvcDescription), $allowedMethods) && $this->getCode('service', $this->getCode('service_to_code', (string)$service->SvcDescription))) {
                                    if (in_array((string)$service->SvcDescription, $allowedMethods)) {
                                        $costArr[(string)$service->SvcDescription] = (string)$service->Postage;
//                                        $priceArr[(string)$service->SvcDescription] = $this->getMethodPrice((string)$service->Postage, $this->getCode('service_to_code', (string)$service->SvcDescription));
                                        $priceArr[(string)$service->SvcDescription] = $this->getMethodPrice((string)$service->Postage, (string)$service->SvcDescription);
                                    } elseif (!in_array((string)$service->SvcDescription, $allMethods)) {
                                        $allMethods[] = (string)$service->SvcDescription;
                                        $newMethod = true;
                                    }
                                }
                                asort($priceArr);
                            }
                        }
                        /*
                        * following if statement is obsolete
                        * we don't have adminhtml/config resoure model
                        */
                        if (false && $newMethod) {
                            sort($allMethods);
                            $insert['usps']['fields']['methods']['value'] = $allMethods;
                            Mage::getResourceModel('adminhtml/config')->saveSectionPost('carriers','','',$insert);
                        }
                    }
            } else {
                $errorTitle = 'Response is in the wrong format';
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('usps');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($method);
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }

    public function getCode($type, $code='')
    {
        $codes = array(

            'service'=>array(
                'FIRST CLASS' => Mage::helper('usa')->__('First-Class'),
                'PRIORITY'    => Mage::helper('usa')->__('Priority Mail'),
                'EXPRESS'     => Mage::helper('usa')->__('Express Mail'),
                'BPM'         => Mage::helper('usa')->__('Bound Printed Matter'),
                'PARCEL'      => Mage::helper('usa')->__('Parcel Post'),
                'MEDIA'       => Mage::helper('usa')->__('Media Mail'),
                'LIBRARY'     => Mage::helper('usa')->__('Library'),
//                'ALL'         => Mage::helper('usa')->__('All Services'),
            ),

/*
            'method'=>array(
                'First-Class',
                'Express Mail',
                'Express Mail PO to PO',
                'Priority Mail',
                'Parcel Post',
                'Express Mail Flat-Rate Envelope',
                'Priority Mail Flat-Rate Box',
                'Bound Printed Matter',
                'Media Mail',
                'Library Mail',
                'Priority Mail Flat-Rate Envelope',
                'Global Express Guaranteed',
                'Global Express Guaranteed Non-Document Rectangular',
                'Global Express Guaranteed Non-Document Non-Rectangular',
                'Express Mail International (EMS)',
                'Express Mail International (EMS) Flat Rate Envelope',
                'Priority Mail International',
                'Priority Mail International Flat Rate Box',
            ),
*/

            'service_to_code'=>array(
                'First-Class'                      => 'FIRST CLASS',
                'Express Mail'                     => 'EXPRESS',
                'Express Mail PO to PO'            => 'EXPRESS',
                'Priority Mail'                    => 'PRIORITY',
                'Parcel Post'                      => 'PARCEL',
                'Express Mail Flat-Rate Envelope'  => 'EXPRESS',
                'Priority Mail Flat-Rate Box'      => 'PRIORITY',
                'Bound Printed Matter'             => 'BPM',
                'Media Mail'                       => 'MEDIA',
                'Library Mail'                     => 'LIBRARY',
                'Priority Mail Flat-Rate Envelope' => 'PRIORITY',
                'Global Express Guaranteed'        => 'EXPRESS',
                'Global Express Guaranteed Non-Document Rectangular'     => 'EXPRESS',
                'Global Express Guaranteed Non-Document Non-Rectangular' => 'EXPRESS',
                'Express Mail International (EMS)'                       => 'EXPRESS',
                'Express Mail International (EMS) Flat-Rate Envelope'    => 'EXPRESS',
                'Priority Mail International'                            => 'PRIORITY',
                'Priority Mail International Flat-Rate Box'              => 'PRIORITY',
                'Priority Mail International Large Flat-Rate Box'        => 'PRIORITY'
            ),

            'first_class_mail_type'=>array(
                'LETTER'      => Mage::helper('usa')->__('Letter'),
                'FLAT'        => Mage::helper('usa')->__('Flat'),
                'PARCEL'      => Mage::helper('usa')->__('Parcel'),
            ),

            'container'=>array(
                'VARIABLE'           => Mage::helper('usa')->__('Variable'),
                'FLAT RATE BOX'      => Mage::helper('usa')->__('Flat-Rate Box'),
                'FLAT RATE ENVELOPE' => Mage::helper('usa')->__('Flat-Rate Envelope'),
                'RECTANGULAR'        => Mage::helper('usa')->__('Rectangular'),
                'NONRECTANGULAR'     => Mage::helper('usa')->__('Non-rectangular'),
            ),

            'size'=>array(
                'REGULAR'     => Mage::helper('usa')->__('Regular'),
                'LARGE'       => Mage::helper('usa')->__('Large'),
                'OVERSIZE'    => Mage::helper('usa')->__('Oversize'),
            ),

            'machinable'=>array(
                'true'        => Mage::helper('usa')->__('Yes'),
                'false'       => Mage::helper('usa')->__('No'),
            ),

        );

        $methods = $this->getConfigData('methods');
        if (!empty($methods)) {
            $codes['method'] = explode(",", $methods);
        } else {
            $codes['method'] = array();
        }

        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid USPS XML code type: %s', $type));
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid USPS XML code for type %s: %s', $type, $code));
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }

        $this->_getXmlTracking($trackings);

        return $this->_result;
    }

    protected function setTrackingReqeust()
    {
        $r = new Varien_Object();

        $userId = $this->getConfigData('userid');
        $r->setUserId($userId);

        $this->_rawTrackRequest = $r;

    }

    protected function _getXmlTracking($trackings)
    {
         $r = $this->_rawTrackRequest;

         foreach ($trackings as $tracking){
             $xml = new SimpleXMLElement('<TrackRequest/>');
             $xml->addAttribute('USERID', $r->getUserId());


             $trackid = $xml->addChild('TrackID');
             $trackid->addAttribute('ID',$tracking);

             $api = 'TrackV2';
             $request = $xml->asXML();

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
            } catch (Exception $e) {
                $responseBody = '';
            }

            $this->_parseXmlTrackingResponse($tracking, $responseBody);
         }
    }

    protected function _parseXmlTrackingResponse($trackingvalue, $response)
    {
        $errorTitle = 'Unable to retrieve tracking';
        $resultArr=array();
        if (strlen(trim($response))>0) {
            if (strpos(trim($response), '<?xml')===0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if (isset($xml->Number) && isset($xml->Description) && (string)$xml->Description!='') {
                        $errorTitle = (string)$xml->Description;
                    } elseif (isset($xml->TrackInfo) && isset($xml->TrackInfo->Error) && isset($xml->TrackInfo->Error->Description) && (string)$xml->TrackInfo->Error->Description!='') {
                        $errorTitle = (string)$xml->TrackInfo->Error->Description;
                    } else {
                        $errorTitle = 'Unknown error';
                    }

                    if(isset($xml->TrackInfo) && isset($xml->TrackInfo->TrackSummary)){
                       $resultArr['tracksummary'] = (string)$xml->TrackInfo->TrackSummary;

                    }
                }
            }
        }

        if(!$this->_result){
            $this->_result = Mage::getModel('shipping/tracking_result');
        }
        $defaults = $this->getDefaults();

        if ($resultArr) {
             $tracking = Mage::getModel('shipping/tracking_result_status');
             $tracking->setCarrier('usps');
             $tracking->setCarrierTitle($this->getConfigData('title'));
             $tracking->setTracking($trackingvalue);
             $tracking->setTrackSummary($resultArr['tracksummary']);
             $this->_result->append($tracking);
         } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingvalue);
            $error->setErrorMessage($errorTitle);
            $this->_result->append($error);
         }
    }

    public function getResponse()
    {
        $statuses = '';
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking){
                    if($data = $tracking->getAllData()){
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
            $arr[$k] = $k;
        }
        return $arr;
    }

}
