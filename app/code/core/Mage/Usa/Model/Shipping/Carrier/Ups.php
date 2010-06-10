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
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * UPS shipping rates estimation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Ups
    extends Mage_Usa_Model_Shipping_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'ups';

    protected $_request = null;

    protected $_result = null;

    protected $_xmlAccessRequest = null;

    protected $_defaultCgiGatewayUrl = 'http://www.ups.com:80/using/services/rave/qcostcgi.cgi';

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
            $r->setAction($this->getCode('action', 'single'));
            $r->setProduct($request->getLimitMethod());
        } else {
            $r->setAction($this->getCode('action', 'all'));
            $r->setProduct('GND'.$this->getConfigData('dest_type'));
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
            $origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
        }

        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigRegionCode()) {
            $origRegionCode = $request->getOrigRegionCode();
        } else {
            $origRegionCode = Mage::getStoreConfig('shipping/origin/region_id', $this->getStore());
            if (is_numeric($origRegionCode)) {
                $origRegionCode = Mage::getModel('directory/region')->load($origRegionCode)->getCode();
            }
        }
        $r->setOrigRegionCode($origRegionCode);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));
        }

        if ($request->getOrigCity()) {
            $r->setOrigCity($request->getOrigCity());
        } else {
            $r->setOrigCity(Mage::getStoreConfig('shipping/origin/city', $this->getStore()));
        }


        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        //for UPS, puero rico state for US will assume as puerto rico country
        if ($destCountry==self::USA_COUNTRY_ID && ($request->getDestPostcode()=='00912' || $request->getDestRegionCode()==self::PUERTORICO_COUNTRY_ID)) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        $r->setDestRegionCode($request->getDestRegionCode());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {

        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());

        $weight = $this->_getCorrectWeight($weight);

        $r->setWeight($weight);
        if ($request->getFreeMethodWeight()!=$request->getPackageWeight()) {
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

        $this->_rawRequest = $r;

        return $this;
    }

    /**
     * Get correct weigt.
     *
     * Namely:
     * Checks the current weight to comply with the minimum weight standards set by the carrier.
     * Then strictly rounds the weight up until the first significant digit after the decimal point.
     *
     * @param float|integer|double $weight
     * @return float
     */
    protected function _getCorrectWeight($weight)
    {
        $minWeight = $this->getConfigData('min_package_weight');

        if($weight < $minWeight){
            $weight = $minWeight;
        }

        //rounds a number to one significant figure
        $weight = ceil($weight*10) / 10;

        return $weight;
    }

    public function getResult()
    {
       return $this->_result;
    }

    protected function _getQuotes()
    {
        switch ($this->getConfigData('type')) {
            case 'UPS':
               return $this->_getCgiQuotes();

            case 'UPS_XML':
               return $this->_getXmlQuotes();
        }
        return null;
    }

    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;

        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $weight = $this->_getCorrectWeight($weight);
        $r->setWeight($weight);
        $r->setAction($this->getCode('action', 'single'));
        $r->setProduct($freeMethod);
    }

    protected function _getCgiQuotes()
    {
        $r = $this->_rawRequest;

        $params = array(
            'accept_UPS_license_agreement' => 'yes',
            '10_action'      => $r->getAction(),
            '13_product'     => $r->getProduct(),
            '14_origCountry' => $r->getOrigCountry(),
            '15_origPostal'  => $r->getOrigPostal(),
            'origCity'       => $r->getOrigCity(),
            '19_destPostal'  => 'US' == $r->getDestCountry() ? substr($r->getDestPostal(), 0, 5) : $r->getDestPostal(), // UPS returns error for zip+4 US codes
            '22_destCountry' => $r->getDestCountry(),
            '23_weight'      => $r->getWeight(),
            '47_rate_chart'  => $r->getPickup(),
            '48_container'   => $r->getContainer(),
            '49_residential' => $r->getDestType(),
            'weight_std'     => strtolower($r->getUnitMeasure()),
        );
        $params['47_rate_chart'] = $params['47_rate_chart']['label'];
        $debugData = array('request' => $params);
        try {
            $url = $this->getConfigData('gateway_url');
            if (!$url) {
                $url = $this->_defaultCgiGatewayUrl;
            }
            $client = new Zend_Http_Client();
            $client->setUri($url);
            $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
            $client->setParameterGet($params);
            $response = $client->request();
            $responseBody = $response->getBody();
            $debugData['result'] = $responseBody;
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $responseBody = '';
        }
        $this->_debug($debugData);
        return $this->_parseCgiResponse($responseBody);
    }

    public function getShipmentByCode($code,$origin = null){
        if($origin===null){
            $origin = $this->getConfigData('origin_shipment');
        }
        $arr = $this->getCode('originShipment',$origin);
        if(isset($arr[$code]))
            return $arr[$code];
        else
            return false;
    }


    protected function _parseCgiResponse($response)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = Mage::helper('usa')->__('Unknown error');
        if (strlen(trim($response))>0) {
            $rRows = explode("\n", $response);
            $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
            foreach ($rRows as $rRow) {
                $r = explode('%', $rRow);
                switch (substr($r[0],-1)) {
                    case 3: case 4:
                        if (in_array($r[1], $allowedMethods)) {
                            $responsePrice = Mage::app()->getLocale()->getNumber($r[8]);
                            $costArr[$r[1]] = $responsePrice;
                            $priceArr[$r[1]] = $this->getMethodPrice($responsePrice, $r[1]);
                        }
                        break;
                    case 5:
                        $errorTitle = $r[1];
                        break;
                    case 6:
                        if (in_array($r[3], $allowedMethods)) {
                            $responsePrice = Mage::app()->getLocale()->getNumber($r[10]);
                            $costArr[$r[3]] = $responsePrice;
                            $priceArr[$r[3]] = $this->getMethodPrice($responsePrice, $r[3]);
                        }
                        break;
                }
            }
            asort($priceArr);
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $method_arr = $this->getCode('method', $method);
                $rate->setMethodTitle(Mage::helper('usa')->__($method_arr));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
#echo "<pre>".print_r($result,1)."</pre>";
        return $result;
    }

/*
    public function isEligibleForFree($method)
    {
        return $method=='GND' || $method=='GNDCOM' || $method=='GNDRES';
    }
*/

    public function getCode($type, $code='')
    {
        $codes = array(
            'action'=>array(
                'single'=>'3',
                'all'=>'4',
            ),

            'originShipment'=>array(
                // United States Domestic Shipments
                'United States Domestic Shipments' => array(
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
                ),
                // Shipments Originating in United States
                'Shipments Originating in United States' => array(
                    '01' => Mage::helper('usa')->__('UPS Next Day Air'),
                    '02' => Mage::helper('usa')->__('UPS Second Day Air'),
                    '03' => Mage::helper('usa')->__('UPS Ground'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '14' => Mage::helper('usa')->__('UPS Next Day Air Early A.M.'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '59' => Mage::helper('usa')->__('UPS Second Day Air A.M.'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ),
                // Shipments Originating in Canada
                'Shipments Originating in Canada' => array(
                    '01' => Mage::helper('usa')->__('UPS Express'),
                    '02' => Mage::helper('usa')->__('UPS Expedited'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '12' => Mage::helper('usa')->__('UPS Three-Day Select'),
                    '14' => Mage::helper('usa')->__('UPS Express Early A.M.'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ),
                // Shipments Originating in the European Union
                'Shipments Originating in the European Union' => array(
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express PlusSM'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ),
                // Polish Domestic Shipments
                'Polish Domestic Shipments' => array(
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
                ),
                // Puerto Rico Origin
                'Puerto Rico Origin' => array(
                    '01' => Mage::helper('usa')->__('UPS Next Day Air'),
                    '02' => Mage::helper('usa')->__('UPS Second Day Air'),
                    '03' => Mage::helper('usa')->__('UPS Ground'),
                    '07' => Mage::helper('usa')->__('UPS Worldwide Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '14' => Mage::helper('usa')->__('UPS Next Day Air Early A.M.'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ),
                // Shipments Originating in Mexico
                'Shipments Originating in Mexico' => array(
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Expedited'),
                    '54' => Mage::helper('usa')->__('UPS Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver'),
                ),
                // Shipments Originating in Other Countries
                'Shipments Originating in Other Countries' => array(
                    '07' => Mage::helper('usa')->__('UPS Express'),
                    '08' => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                    '11' => Mage::helper('usa')->__('UPS Standard'),
                    '54' => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                    '65' => Mage::helper('usa')->__('UPS Saver')
                )
            ),

            'method'=>array(
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
            ),

            'pickup'=>array(
                'RDP'    => array("label"=>'Regular Daily Pickup',"code"=>"01"),
                'OCA'    => array("label"=>'On Call Air',"code"=>"07"),
                'OTP'    => array("label"=>'One Time Pickup',"code"=>"06"),
                'LC'     => array("label"=>'Letter Center',"code"=>"19"),
                'CC'     => array("label"=>'Customer Counter',"code"=>"03"),
            ),

            'container'=>array(
                'CP'     => '00', // Customer Packaging
                'ULE'    => '01', // UPS Letter Envelope
                'UT'     => '03', // UPS Tube
                'UEB'    => '21', // UPS Express Box
                'UW25'   => '24', // UPS Worldwide 25 kilo
                'UW10'   => '25', // UPS Worldwide 10 kilo
            ),

            'container_description'=>array(
                'CP'     => Mage::helper('usa')->__('Customer Packaging'),
                'ULE'    => Mage::helper('usa')->__('UPS Letter Envelope'),
                'UT'     => Mage::helper('usa')->__('UPS Tube'),
                'UEB'    => Mage::helper('usa')->__('UPS Express Box'),
                'UW25'   => Mage::helper('usa')->__('UPS Worldwide 25 kilo'),
                'UW10'   => Mage::helper('usa')->__('UPS Worldwide 10 kilo'),
            ),

            'dest_type'=>array(
                'RES'    => '01', // Residential
                'COM'    => '02', // Commercial
            ),

            'dest_type_description'=>array(
                'RES'    => Mage::helper('usa')->__('Residential'),
                'COM'    => Mage::helper('usa')->__('Commercial'),
            ),

            'unit_of_measure'=>array(
                'LBS'   =>  Mage::helper('usa')->__('Pounds'),
                'KGS'   =>  Mage::helper('usa')->__('Kilograms'),
            ),

        );

        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid UPS CGI code type: %s', $type));
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid UPS CGI code for type %s: %s', $type, $code));
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    protected function _getXmlQuotes()
    {
        $url = $this->getConfigData('gateway_xml_url');

        $this->setXMLAccessRequest();
        $xmlRequest=$this->_xmlAccessRequest;

        $r = $this->_rawRequest;
        $params = array(
            'accept_UPS_license_agreement' => 'yes',
            '10_action'      => $r->getAction(),
            '13_product'     => $r->getProduct(),
            '14_origCountry' => $r->getOrigCountry(),
            '15_origPostal'  => $r->getOrigPostal(),
            'origCity'       => $r->getOrigCity(),
            'origRegionCode' => $r->getOrigRegionCode(),
            '19_destPostal'  => 'US' == $r->getDestCountry() ? substr($r->getDestPostal(), 0, 5) : $r->getDestPostal(), // UPS returns error for zip+4 US codes
            '22_destCountry' => $r->getDestCountry(),
            'destRegionCode' => $r->getDestRegionCode(),
            '23_weight'      => $r->getWeight(),
            '47_rate_chart'  => $r->getPickup(),
            '48_container'   => $r->getContainer(),
            '49_residential' => $r->getDestType(),
        );
        $params['10_action'] = $params['10_action']=='4'? 'Shop' : 'Rate';
        $serviceCode = $r->getProduct() ? $r->getProduct() : '';
        $serviceDescription = $serviceCode ? $this->getShipmentByCode($serviceCode) : '';

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

      <Service>
          <Code>{$serviceCode}</Code>
          <Description>{$serviceDescription}</Description>
      </Service>
      <Shipper>
XMLRequest;

        if ($this->getConfigFlag('negotiated_active') && ($shipper = $this->getConfigData('shipper_number')) ) {
            $xmlRequest .= "<ShipperNumber>{$shipper}</ShipperNumber>";
        }

$xmlRequest .= <<< XMLRequest
      <Address>
          <City>{$params['origCity']}</City>
          <PostalCode>{$params['15_origPostal']}</PostalCode>
          <CountryCode>{$params['14_origCountry']}</CountryCode>
          <StateProvinceCode>{$params['origRegionCode']}</StateProvinceCode>
      </Address>
    </Shipper>
    <ShipTo>
      <Address>
          <PostalCode>{$params['19_destPostal']}</PostalCode>
          <CountryCode>{$params['22_destCountry']}</CountryCode>
          <ResidentialAddress>{$params['49_residential']}</ResidentialAddress>
          <StateProvinceCode>{$params['destRegionCode']}</StateProvinceCode>
XMLRequest;

          $xmlRequest .= ($params['49_residential']==='01' ? "<ResidentialAddressIndicator>{$params['49_residential']}</ResidentialAddressIndicator>" : '');

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
            $xmlRequest .= "<RateInformation><NegotiatedRatesIndicator/></RateInformation>";
        }

$xmlRequest .= <<< XMLRequest
  </Shipment>
</RatingServiceSelectionRequest>
XMLRequest;


        $debugData = array('request' => $xmlRequest);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (boolean)$this->getConfigFlag('mode_xml'));
            $xmlResponse = curl_exec ($ch);
            $debugData['result'] = $xmlResponse;
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $xmlResponse = '';
        }

        $this->_debug($debugData);
        return $this->_parseXmlResponse($xmlResponse);
    }

    protected function _parseXmlResponse($xmlResponse)
    {
        $costArr = array();
        $priceArr = array();
        if (strlen(trim($xmlResponse))>0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $arr = $xml->getXpath("//RatingServiceSelectionResponse/Response/ResponseStatusCode/text()");
            $success = (int)$arr[0];
            if($success===1){
                $arr = $xml->getXpath("//RatingServiceSelectionResponse/RatedShipment");
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                // Negotiated rates
                $negotiatedArr = $xml->getXpath("//RatingServiceSelectionResponse/RatedShipment/NegotiatedRates");
                $negotiatedActive = $this->getConfigFlag('negotiated_active')
                    && $this->getConfigData('shipper_number')
                    && !empty($negotiatedArr);

                foreach ($arr as $shipElement){
                    $code = (string)$shipElement->Service->Code;
                    #$shipment = $this->getShipmentByCode($code);
                    if (in_array($code, $allowedMethods)) {

                        if ($negotiatedActive) {
                            $cost = $shipElement->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
                        } else {
                            $cost = $shipElement->TotalCharges->MonetaryValue;
                        }

                        $costArr[$code] = $cost;
                        $priceArr[$code] = $this->getMethodPrice(floatval($cost),$code);
                    }
                }
            } else {
                $arr = $xml->getXpath("//RatingServiceSelectionResponse/Response/Error/ErrorDescription/text()");
                $errorTitle = (string)$arr[0][0];
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier('ups');
                $error->setCarrierTitle($this->getConfigData('title'));
                //$error->setErrorMessage($errorTitle);
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if(!isset($errorTitle)){
                $errorTitle = Mage::helper('usa')->__('Cannot retrieve shipping rates');
            }
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $method_arr = $this->getShipmentByCode($method);
                $rate->setMethodTitle($method_arr);
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }

    public function getTracking($trackings)
    {
        $return = array();

        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }

        if ($this->getConfigData('type')=='UPS') {
            $this->_getCgiTracking($trackings);
        } elseif ($this->getConfigData('type')=='UPS_XML'){
            $this->setXMLAccessRequest();
            $this->_getXmlTracking($trackings);
        }

        return $this->_result;
    }

    protected function setXMLAccessRequest()
    {
        $userid = $this->getConfigData('username');
        $userid_pass = $this->getConfigData('password');
        $access_key = $this->getConfigData('access_license_number');

        $this->_xmlAccessRequest =  <<<XMLAuth
<?xml version="1.0"?>
<AccessRequest xml:lang="en-US">
  <AccessLicenseNumber>$access_key</AccessLicenseNumber>
  <UserId>$userid</UserId>
  <Password>$userid_pass</Password>
</AccessRequest>
XMLAuth;
    }

    protected function _getCgiTracking($trackings)
    {
        //ups no longer support tracking for data streaming version
        //so we can only reply the popup window to ups.
        $result = Mage::getModel('shipping/tracking_result');
        $defaults = $this->getDefaults();
        foreach($trackings as $tracking){
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier('ups');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);
            $status->setUrl("http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&error_carried=true&tracknums_displayed=5&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=$tracking&AgreeToTermsAndConditions=yes");
            $result->append($status);
        }

        $this->_result = $result;
        return $result;
    }

    protected function _getXmlTracking($trackings)
    {
        $url = $this->getConfigData('tracking_xml_url');

        foreach($trackings as $tracking){
            $xmlRequest=$this->_xmlAccessRequest;

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
            $debugData = array('request' => $xmlRequest);

            try {
                $ch = curl_init();
                   curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $xmlResponse = curl_exec ($ch);
                $debugData['result'] = $xmlResponse;
                curl_close ($ch);
            }
            catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $xmlResponse = '';
            }

            $this->_debug($debugData);
            $this->_parseXmlTrackingResponse($tracking, $xmlResponse);
        }

        return $this->_result;
    }

    protected function _parseXmlTrackingResponse($trackingvalue, $xmlResponse)
    {
        $errorTitle = 'Unable to retrieve tracking';
        $resultArr = array();
        $packageProgress = array();

        if ($xmlResponse) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $arr = $xml->getXpath("//TrackResponse/Response/ResponseStatusCode/text()");
            $success = (int)$arr[0][0];

            if($success===1){
                $arr = $xml->getXpath("//TrackResponse/Shipment/Service/Description/text()");
                $resultArr['service'] = (string)$arr[0];

                $arr = $xml->getXpath("//TrackResponse/Shipment/PickupDate/text()");
                $resultArr['shippeddate'] = (string)$arr[0];

                $arr = $xml->getXpath("//TrackResponse/Shipment/Package/PackageWeight/Weight/text()");
                $weight = (string)$arr[0];

                $arr = $xml->getXpath("//TrackResponse/Shipment/Package/PackageWeight/UnitOfMeasurement/Code/text()");
                $unit = (string)$arr[0];

                $resultArr['weight'] = "{$weight} {$unit}";

                $activityTags = $xml->getXpath("//TrackResponse/Shipment/Package/Activity");
                if ($activityTags) {
                    $i=1;
                    foreach ($activityTags as $activityTag) {
                        $addArr=array();
                        if (isset($activityTag->ActivityLocation->Address->City)) {
                            $addArr[] = (string)$activityTag->ActivityLocation->Address->City;
                        }
                        if (isset($activityTag->ActivityLocation->Address->StateProvinceCode)) {
                            $addArr[] = (string)$activityTag->ActivityLocation->Address->StateProvinceCode;
                        }
                        if (isset($activityTag->ActivityLocation->Address->CountryCode)) {
                            $addArr[] = (string)$activityTag->ActivityLocation->Address->CountryCode;
                        }
                        $dateArr = array();
                        $date = (string)$activityTag->Date;//YYYYMMDD
                        $dateArr[] = substr($date,0,4);
                        $dateArr[] = substr($date,4,2);
                        $dateArr[] = substr($date,-2,2);

                        $timeArr = array();
                        $time = (string)$activityTag->Time;//HHMMSS
                        $timeArr[] = substr($time,0,2);
                        $timeArr[] = substr($time,2,2);
                        $timeArr[] = substr($time,-2,2);

                        if($i==1){
                           $resultArr['status'] = (string)$activityTag->Status->StatusType->Description;
                           $resultArr['deliverydate'] = implode('-',$dateArr);//YYYY-MM-DD
                           $resultArr['deliverytime'] = implode(':',$timeArr);//HH:MM:SS
                           $resultArr['deliverylocation'] = (string)$activityTag->ActivityLocation->Description;
                           $resultArr['signedby'] = (string)$activityTag->ActivityLocation->SignedForByName;
                           if ($addArr) {
                            $resultArr['deliveryto']=implode(', ',$addArr);
                           }
                        }else{
                           $tempArr=array();
                           $tempArr['activity'] = (string)$activityTag->Status->StatusType->Description;
                           $tempArr['deliverydate'] = implode('-',$dateArr);//YYYY-MM-DD
                           $tempArr['deliverytime'] = implode(':',$timeArr);//HH:MM:SS
                           if ($addArr) {
                            $tempArr['deliverylocation']=implode(', ',$addArr);
                           }
                           $packageProgress[] = $tempArr;
                        }
                        $i++;
                    }
                    $resultArr['progressdetail'] = $packageProgress;
                }
            } else {
                $arr = $xml->getXpath("//TrackResponse/Response/Error/ErrorDescription/text()");
                $errorTitle = (string)$arr[0][0];
            }
        }

        if (!$this->_result) {
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        $defaults = $this->getDefaults();

        if ($resultArr) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('ups');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingvalue);
            $tracking->addData($resultArr);
            $this->_result->append($tracking);
        } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingvalue);
            $error->setErrorMessage($errorTitle);
            $this->_result->append($error);
        }
        return $this->_result;
    }

    public function getResponse()
    {
        $statuses = '';
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking){
                    if($data = $tracking->getAllData()){
                        if (isset($data['status'])) {
                            $statuses .= Mage::helper('usa')->__($data['status']);
                        } else {
                            $statuses .= Mage::helper('usa')->__($data['error_message']);
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

}
