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
 * DHL shipping rates estimation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl
    extends Mage_Usa_Model_Shipping_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'dhl';

    protected $_request = null;

    protected $_result = null;

    protected $_errors = array();

    protected $_dhlRates = array();

    protected $_defaultGatewayUrl = 'https://eCommerce.airborne.com/ApiLandingTest.asp';

    const SUCCESS_CODE = 203;

    const ADDITIONAL_PROTECTION_ASSET = 'AP';
    const ADDITIONAL_PROTECTION_NOT_REQUIRED = 'NR';

    const ADDITIONAL_PROTECTION_VALUE_CONFIG = 0;
    const ADDITIONAL_PROTECTION_VALUE_SUBTOTAL = 1;
    const ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT = 2;

    const ADDITIONAL_PROTECTION_ROUNDING_FLOOR = 0;
    const ADDITIONAL_PROTECTION_ROUNDING_CEIL = 1;
    const ADDITIONAL_PROTECTION_ROUNDING_ROUND = 2;


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

        $r->setStoreId($request->getStoreId());

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }

        if ($request->getDhlId()) {
            $id = $request->getDhlId();
        } else {
            $id = $this->getConfigData('id');
        }
        $r->setId($id);

        if ($request->getDhlPassword()) {
            $password = $request->getDhlPassword();
        } else {
            $password = $this->getConfigData('password');
        }
        $r->setPassword($password);

        if ($request->getDhlAccount()) {
            $accountNbr = $request->getDhlAccount();
        } else {
            $accountNbr = $this->getConfigData('account');
        }
        $r->setAccountNbr($accountNbr);

        if ($request->getDhlShippingKey()) {
            $shippingKey = $request->getDhlShippingKey();
        } else {
            $shippingKey = $this->getConfigData('shipping_key');
        }
        $r->setShippingKey($shippingKey);

        if ($request->getDhlShippingIntlKey()) {
            $shippingKey = $request->getDhlShippingIntlKey();
        } else {
            $shippingKey = $this->getConfigData('shipping_intlkey');
        }
        $r->setShippingIntlKey($shippingKey);

        if ($request->getDhlShipmentType()) {
            $shipmentType = $request->getDhlShipmentType();
        } else {
            $shipmentType = $this->getConfigData('shipment_type');
        }
        $r->setShipmentType($shipmentType);

        if($request->getDhlDutiable()){
            $shipmentDutible = $request->getDhlDutiable();
        }else{
            $shipmentDutible = $this->getConfigData('dutiable');
        }
        $r->setDutiable($shipmentDutible);

        if($request->getDhlDutyPaymentType()){
            $dutypaytype = $request->getDhlDutyPaymentType();
        }else{
          $dutypaytype = $this->getConfigData('dutypaymenttype');
        }
        $r->setDutyPaymentType($dutypaytype);

        if($request->getDhlContentDesc()){
           $contentdesc = $request->getDhlContentDesc();
        }else{
          $contentdesc = $this->getConfigData('contentdesc');
        }
        $r->setContentDesc($contentdesc);

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID, $this->getStore());
        }
        $r->setOrigCountry($origCountry);

        /*
        * DHL only accepts weight as a whole number. Maximum length is 3 digits.
        */
        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $shippingWeight = round(max(1, $weight),0);

        $r->setValue(round($request->getPackageValue(),2));
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());
        $r->setDestStreet(Mage::helper('core/string')->substr($request->getDestStreet(), 0, 35));
        $r->setDestCity($request->getDestCity());

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        //for DHL, puero rico state for US will assume as puerto rico country
        //for puerto rico, dhl will ship as international
        if ($destCountry==self::USA_COUNTRY_ID && ($request->getDestPostcode()=='00912' || $request->getDestRegionCode()==self::PUERTORICO_COUNTRY_ID)) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        $r->setDestCountryId($destCountry);
        $r->setDestState( $request->getDestRegionCode());

       $r->setWeight($shippingWeight);
       $r->setFreeMethodWeight($request->getFreeMethodWeight());
       $this->_rawRequest = $r;
//        $methods = explode(',', $this->getConfigData('allowed_methods'));
//
//        $freeMethod = $this->getConfigData('free_method');
//
//        $internationcode = $this->getCode('international_searvice');



//        $minOrderAmount = $this->getConfigData('cutoff_cost') ? $this->getConfigData('cutoff_cost') : 0;
//        if ($shippingWeight>0) {
//             $this->_rawRequest->setWeight($shippingWeight);
//             $this->_getQuotes();
//            foreach ($methods as $method) {
//                if(($method==$internationcode && ($r->getDestCountryId() != self::USA_COUNTRY_ID)) ||
//                ($method!=$internationcode && ($r->getDestCountryId() == self::USA_COUNTRY_ID)))
//                {
//                    $weight = $freeMethod==$method && $this->getConfigData('cutoff_cost') <= $r->getValue() ? 0 : $shippingWeight;
//                    if ($weight>0) {
//                        $this->_rawRequest->setWeight($weight);
//                	    $this->_rawRequest->setService($method);
//                        $this->_getQuotes();
//                    } else {
//                        $this->_dhlRates[$method] = array(
//                            'term' => $this->getCode('service', $method),
//                            'price_total' => 0,
//                        );
//                    }
//                }
//            }
//        } else {
//           $this->_errors[] = Mage::helper('usa')->__('Please enter the package weight');
//        }

        return $this;
    }

    public function getResult()
    {
        return $this->_result;
//        $result = Mage::getModel('shipping/rate_result');
//
//        foreach ($this->_errors as $errorText) {
//        	$error = Mage::getModel('shipping/rate_result_error');
//            $error->setCarrier('dhl');
//            $error->setCarrierTitle($this->getConfigData('title'));
//            $error->setErrorMessage($errorText);
//            $result->append($error);
//        }
//
//        foreach($this->_dhlRates as $method => $data) {
//            $rate = Mage::getModel('shipping/rate_result_method');
//            $rate->setCarrier('dhl');
//            $rate->setCarrierTitle($this->getConfigData('title'));
//            $rate->setMethod($method);
//            $rate->setMethodTitle($data['term']);
//            $rate->setCost($data['price_total']);
//            $rate->setPrice($data['price_total']);
//            $result->append($rate);
//        }
//
//       return $result;
    }

    protected function _getQuotes()
    {
        return $this->_getXmlQuotes();
    }

    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;

        $r->setFreeMethodRequest(true);
        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $freeWeight = round(max(1, $weight),0);
        $r->setWeight($freeWeight);
        $r->setService($freeMethod);
    }

//    protected function _getShipDate($includeSaturday=true)
//    {
//        $i = 0;
//        $weekday = date('w');
//        /*
//        * need to omit saturday and sunday
//        * dhl will not work on sunday
//        * 0 (for Sunday) through 6 (for Saturday)
//        */
//        if (!$weekday || $weekday===0) $i += 1;
//        elseif (!$includeSaturday && $weekday==6) $i += 2;
//        return date('Y-m-d', strtotime("+$i day"));
//    }

    protected function _getShipDate($domestic=true)
    {
        if ($domestic) {
            $days = explode(',', $this->getConfigData('shipment_days'));
        } else {
            $days = explode(',', $this->getConfigData('intl_shipment_days'));
        }

        if (!$days) {
            return date('Y-m-d');
        }

        $i=0;
        $weekday = date('w');
        while(!in_array($weekday, $days) && $i < 10) {
            $i++;
            $weekday = date('w', strtotime("+$i day"));
        }

        return date('Y-m-d', strtotime("+$i day"));
    }

    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;

        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><eCommerce/>');
        $xml->addAttribute('action', 'Request');
        $xml->addAttribute('version', '1.1');

        $requestor = $xml->addChild('Requestor');
            $requestor->addChild('ID', $r->getId());
            $requestor->addChild('Password', $r->getPassword());

        $methods = explode(',', $this->getConfigData('allowed_methods'));
        $internationcode = $this->getCode('international_searvice');
        $hasShipCode = false;

        $shipDate = $this->_getShipDate();

        if ($r->hasService() && $r->getFreeMethodRequest()) {
            if ($r->getDestCountryId() == self::USA_COUNTRY_ID) {
                 $shipment = $xml->addChild('Shipment');
                 $shipKey=$r->getShippingKey();
                 $r->setShipDate($shipDate);
            } else {
                 $shipment = $xml->addChild('IntlShipment');
                 $shipKey=$r->getShippingIntlKey();
                 $r->setShipDate($this->_getShipDate(false));
                 /*
                 * For internation shippingment customsvalue must be posted
                 */
                 $shippingDuty = $shipment->addChild('Dutiable');
                    $shippingDuty->addChild('DutiableFlag',($r->getDutiable()?'Y':'N'));
                    $shippingDuty->addChild('CustomsValue',$r->getValue());
            }
            $hasShipCode = true;
            $this->_createShipmentXml($shipment,$shipKey);
        } else {
            foreach ($methods as $method) {
                $shipment = false;
                if (in_array($method, array_keys($this->getCode('special_express')))) {
                    $r->setService('E');
                    $r->setExtendedService($this->getCode('special_express', $method));
                } else {
                    $r->setService($method);
                    $r->setExtendedService(null);
                }
                if ($r->getDestCountryId() == self::USA_COUNTRY_ID && $method!=$internationcode) {
                    $shipment = $xml->addChild('Shipment');
                    $shipKey=$r->getShippingKey();
                    $r->setShipDate($shipDate);
                }elseif($r->getDestCountryId() != self::USA_COUNTRY_ID && $method==$internationcode){
                    $shipment = $xml->addChild('IntlShipment');
                    $shipKey=$r->getShippingIntlKey();
                    $r->setShipDate($this->_getShipDate(false));

                    /*
                    * For internation shippingment customsvalue must be posted
                    */
                    $shippingDuty = $shipment->addChild('Dutiable');
                        $shippingDuty->addChild('DutiableFlag',($r->getDutiable()?'Y':'N'));
                        $shippingDuty->addChild('CustomsValue',$r->getValue());
                }
                if ($shipment!==false) {
                    $hasShipCode = true;
                    $this->_createShipmentXml($shipment,$shipKey);
                }
            }
        }

        if (!$hasShipCode) {
            $this->_errors[] = Mage::helper('usa')->__('There is no available method for selected shipping address.');
            return;
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
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                $responseBody = curl_exec($ch);
                curl_close ($ch);

                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            }
            catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $responseBody = '';
            }
            $this->_debug($debugData);
        }

        return $this->_parseXmlResponse($responseBody);
    }

    protected function _createShipmentXml($shipment,$shipKey)
    {
        $r = $this->_rawRequest;

        $store = Mage::app()->getStore($r->getStoreId());

        $_haz = $this->getConfigFlag('hazardous_materials');

        $_subtotal = $r->getValue();
        $_subtotalWithDiscount = $r->getValueWithDiscount();

        $_width = max(0, (double) $this->getConfigData('default_width'));
        $_height = max(0, (double) $this->getConfigData('default_height'));
        $_length = max(0, (double) $this->getConfigData('default_length'));

        $_apEnabled = $this->getConfigFlag('additional_protection_enabled');
        $_apUseSubtotal = $this->getConfigData('additional_protection_use_subtotal');
        $_apConfigValue = max(0, (double) $this->getConfigData('additional_protection_value'));
        $_apMinValue = max(0, (double) $this->getConfigData('additional_protection_min_value'));
        $_apValueRounding = $this->getConfigData('additional_protection_rounding');

        $apValue = 0;
        $apCode = self::ADDITIONAL_PROTECTION_NOT_REQUIRED;
        if ($_apEnabled) {
            if ($_apMinValue <= $_subtotal) {
                switch ($_apUseSubtotal) {
                    case self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL:
                        $apValue = $_subtotal;
                        break;
                    case self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT:
                        $apValue = $_subtotalWithDiscount;
                        break;
                    default:
                    case self::ADDITIONAL_PROTECTION_VALUE_CONFIG:
                        $apValue = $_apConfigValue;
                        break;
                }

                if ($apValue) {
                    $apCode = self::ADDITIONAL_PROTECTION_ASSET;


                    switch ($_apValueRounding) {
                        case self::ADDITIONAL_PROTECTION_ROUNDING_CEIL:
                            $apValue = ceil($apValue);
                            break;
                        case self::ADDITIONAL_PROTECTION_ROUNDING_ROUND:
                            $apValue = round($apValue);
                            break;
                        default:
                        case self::ADDITIONAL_PROTECTION_ROUNDING_FLOOR:
                            $apValue = floor($apValue);
                            break;
                    }
                }
            }
        }

        $shipment->addAttribute('action', 'RateEstimate');
            $shipment->addAttribute('version', '1.0');

        $shippingCredentials = $shipment->addChild('ShippingCredentials');
            $shippingCredentials->addChild('ShippingKey',$shipKey);
            $shippingCredentials->addChild('AccountNbr', $r->getAccountNbr());

        $shipmentDetail = $shipment->addChild('ShipmentDetail');
            $shipmentDetail->addChild('ShipDate', $r->getShipDate());
            $shipmentDetail->addChild('Service')->addChild('Code', $r->getService());
            $shipmentDetail->addChild('ShipmentType')->addChild('Code', $r->getShipmentType());
            $shipmentDetail->addChild('Weight', $r->getWeight());
            $shipmentDetail->addChild('ContentDesc', $r->getContentDesc());
            $additionalProtection = $shipmentDetail->addChild('AdditionalProtection');
                $additionalProtection->addChild('Code', $apCode);
                $additionalProtection->addChild('Value', floor($apValue));

            if ($_width && $_height && $_length) {
                $dimensions = $shipmentDetail->addChild('Dimensions');
                    $dimensions->addChild('Length', $_length);
                    $dimensions->addChild('Width', $_width);
                    $dimensions->addChild('Height', $_height);
            }

            if ($_haz || ($r->getExtendedService())) {
                $specialServices = $shipmentDetail->addChild('SpecialServices');
            }

            if ($_haz) {
                $hazardousMaterials = $specialServices->addChild('SpecialService');
                $hazardousMaterials->addChild('Code', 'HAZ');
            }

            if ($r->getExtendedService()) {
                $extendedService = $specialServices->addChild('SpecialService');
                $extendedService->addChild('Code', $r->getExtendedService());
            }


         /*
         * R = Receiver (if receiver, need AccountNbr)
         * S = Sender
         * 3 = Third Party (if third party, need AccountNbr)
         */
         $billing = $shipment->addChild('Billing');
            $billing->addChild('Party')->addChild('Code', 'S');
            $billing->addChild('DutyPaymentType',$r->getDutyPaymentType());

            /*
            $cod = $billing->addChild('CODPayment');
                $cod->addChild('Code', 'P');
                $cod->addChild('Value', 100);
                */


        $receiverAddress = $shipment->addChild('Receiver')->addChild('Address');
            $receiverAddress->addChild('Street', htmlspecialchars($r->getDestStreet()?$r->getDestStreet():'NA'));
            $receiverAddress->addChild('City', htmlspecialchars($r->getDestCity()));
            $receiverAddress->addChild('State', htmlspecialchars($r->getDestState()));
            /*
            * DHL xml service is using UK for united kingdom instead of GB which is a standard ISO country code
            */
            $receiverAddress->addChild('Country', ($r->getDestCountryId()=='GB'?'UK':$r->getDestCountryId()));
            $receiverAddress->addChild('PostalCode', $r->getDestPostal());
        /*
        $special_service=$this->getCode('special_service');
        if(array_key_exists($r->getService(),$special_service)){
             $specialService = $shipment->addChild('SpecialServices')->addChild('SpecialService');
             $specialService->addChild('Code',$special_service[$r->getService()]);
        }
        */
    }

    protected function _parseXmlResponse($response)
    {
        $r = $this->_rawRequest;
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve quotes';

        $tr = get_html_translation_table(HTML_ENTITIES);
        unset($tr['<'], $tr['>'], $tr['"']);
        $response = str_replace(array_keys($tr), array_values($tr), $response);

        if (strlen(trim($response))>0) {
            if (strpos(trim($response), '<?xml')===0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if (
                        is_object($xml->Faults)
                        && is_object($xml->Faults->Fault)
                        && is_object($xml->Faults->Fault->Code)
                        && is_object($xml->Faults->Fault->Description)
                        && is_object($xml->Faults->Fault->Context)
                       ) {
                        $code = (string)$xml->Faults->Fault->Code;
                        $description = $xml->Faults->Fault->Description;
                        $context = $xml->Faults->Fault->Context;
                        $this->_errors[$code] = Mage::helper('usa')->__('Error #%s : %s (%s)', $code, $description, $context);
                    } else {
                        if ($r->getDestCountryId() == self::USA_COUNTRY_ID) {
                            if ($xml->Shipment) {
                                foreach($xml->Shipment  as $shipXml){
                                   $this->_parseXmlObject($shipXml);
                                }
                            } else {
                              $this->_errors[] = Mage::helper('usa')->__('Shipment is not available.');
                            }
                        } else {
                            $shipXml = $xml->IntlShipment;
                            $this->_parseXmlObject($shipXml);
                        }
                        $shipXml=(($r->getDestCountryId() == self::USA_COUNTRY_ID)?$xml->Shipment:$xml->IntlShipment);
                    }
                }
            } else {
                $this->_errors[] = Mage::helper('usa')->__('The response is in wrong format.');
            }
        }

        $result = Mage::getModel('shipping/rate_result');

        foreach ($this->_errors as $errorText) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('dhl');
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorText);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }

        foreach($this->_dhlRates as $rate) {
            $method = $rate['service'];
            $data = $rate['data'];
            $rate = Mage::getModel('shipping/rate_result_method');
            $rate->setCarrier('dhl');
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod($method);
            $rate->setMethodTitle($data['term']);
            $rate->setCost($data['price_total']);
            $rate->setPrice($data['price_total']);
            $result->append($rate);
        }
       return $result;

    }

    protected function _parseXmlObject($shipXml)
    {
        if(
            is_object($shipXml->Faults)
            && is_object($shipXml->Faults->Fault)
            && is_object($shipXml->Faults->Fault->Desc)
            && intval($shipXml->Faults->Fault->Code) != self::SUCCESS_CODE
           ) {
               $code = (string)$shipXml->Faults->Fault->Code;
               $description = $shipXml->Faults->Fault->Desc;
               $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
        } elseif(
            is_object($shipXml->Faults)
            && is_object($shipXml->Result->Code)
            && is_object($shipXml->Result->Desc)
            && intval($shipXml->Result->Code) != self::SUCCESS_CODE
           ) {
               $code = (string)$shipXml->Result->Code;
               $description = $shipXml->Result->Desc;
               $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
        }else {
            $this->_addRate($shipXml);
        }
        return $this;
    }

//    protected function _getXmlQuotes()
//    {
//        $r = $this->_rawRequest;
//
//        $xml = new SimpleXMLElement('<eCommerce/>');
//        $xml->addAttribute('action', 'Request');
//        $xml->addAttribute('version', '1.1');
//
//        $requestor = $xml->addChild('Requestor');
//            $requestor->addChild('ID', $r->getId());
//            $requestor->addChild('Password', $r->getPassword());
//
//
//        if ($r->getDestCountryId() == self::USA_COUNTRY_ID) {
//            $shipment = $xml->addChild('Shipment');
//            $shipKey=$r->getShippingKey();
//        }else{
//             $shipment = $xml->addChild('IntlShipment');
//             $shipKey=$r->getShippingIntlKey();
//
//             /*
//             * For internation shippingment customsvalue must be posted
//             */
//             $shippingDuty = $shipment->addChild('Dutiable');
//                $shippingDuty->addChild('DutiableFlag',($r->getDutiable()?'Y':'N'));
//                $shippingDuty->addChild('CustomsValue',$r->getValue());
//        }
//
//            $shipment->addAttribute('action', 'RateEstimate');
//            $shipment->addAttribute('version', '1.0');
//
//            $shippingCredentials = $shipment->addChild('ShippingCredentials');
//                $shippingCredentials->addChild('ShippingKey',$shipKey);
//                $shippingCredentials->addChild('AccountNbr', $r->getAccountNbr());
//
//            $shipmentDetail = $shipment->addChild('ShipmentDetail');
//                $shipmentDetail->addChild('ShipDate', $this->_getShipDate());
//                $shipmentDetail->addChild('Service')->addChild('Code', $r->getService());
//                $shipmentDetail->addChild('ShipmentType')->addChild('Code', $r->getShipmentType());
//                $shipmentDetail->addChild('Weight', $r->getWeight());
//                $shipmentDetail->addChild('ContentDesc', $r->getContentDesc());
//
//             $billing = $shipment->addChild('Billing');
//                $billing->addChild('Party')->addChild('Code', 'S');
//                $billing->addChild('DutyPaymentType',$r->getDutyPaymentType());
//
//            $receiverAddress = $shipment->addChild('Receiver')->addChild('Address');
//                $receiverAddress->addChild('Street', htmlspecialchars($r->getDestStreet()?$r->getDestStreet():'NA'));
//                $receiverAddress->addChild('City', htmlspecialchars($r->getDestCity()));
//                $receiverAddress->addChild('State', htmlspecialchars($r->getDestState()));
//                /*
//                * DHL xml service is using UK for united kingdom instead of GB which is a standard ISO country code
//                */
//                $receiverAddress->addChild('Country', ($r->getDestCountryId()=='GB'?'UK':$r->getDestCountryId()));
//                $receiverAddress->addChild('PostalCode', $r->getDestPostal());
//            /*
//            $special_service=$this->getCode('special_service');
//            if(array_key_exists($r->getService(),$special_service)){
//                 $specialService = $shipment->addChild('SpecialServices')->addChild('SpecialService');
//                 $specialService->addChild('Code',$special_service[$r->getService()]);
//            }
//            */
//
//
//        $request = $xml->asXML();
//
//        try {
//            $url = $this->getConfigData('gateway_url');
//            if (!$url) {
//                $url = $this->_defaultGatewayUrl;
//            }
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
//            $responseBody = curl_exec($ch);
//            curl_close ($ch);
//        } catch (Exception $e) {
//            $responseBody = '';
//        }
//
//        $this->_parseXmlResponse($responseBody);
//    }

//    protected function _parseXmlResponse($response)
//    {
//        $r = $this->_rawRequest;
//        $costArr = array();
//        $priceArr = array();
//        $errorTitle = 'Unable to retrieve quotes';
//
//        $tr = get_html_translation_table(HTML_ENTITIES);
//        unset($tr['<'], $tr['>'], $tr['"']);
//        $response = str_replace(array_keys($tr), array_values($tr), $response);
//
//
//        if (strlen(trim($response))>0) {
//            if (strpos(trim($response), '<?xml')===0) {
//                $xml = simplexml_load_string($response);
//
//
//                /*echo "<pre>DEBUG:\n";
//                print_r($xml);
//                echo "</pre>";*/
//
//
//                if (is_object($xml)) {
//                    $shipXml=(($r->getDestCountryId() == self::USA_COUNTRY_ID)?$xml->Shipment:$xml->IntlShipment);
//                    if (
//                        is_object($xml->Faults)
//                        && is_object($xml->Faults->Fault)
//                        && is_object($xml->Faults->Fault->Code)
//                        && is_object($xml->Faults->Fault->Description)
//                        && is_object($xml->Faults->Fault->Context)
//                       ) {
//                        $code = (string)$xml->Faults->Fault->Code;
//                        $description = $xml->Faults->Fault->Description;
//                        $context = $xml->Faults->Fault->Context;
//                        $this->_errors[$code] = Mage::helper('usa')->__('Error #%s : %s (%s)', $code, $description, $context);
//                    } elseif(
//                        is_object($shipXml->Faults)
//                        && is_object($shipXml->Faults->Fault)
//                        && is_object($shipXml->Faults->Fault->Desc)
//                        && intval($shipXml->Faults->Fault->Code) != self::SUCCESS_CODE
//                       ) {
//                           $code = (string)$shipXml->Faults->Fault->Code;
//                           $description = $shipXml->Faults->Fault->Desc;
//                           $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
//                    } elseif(
//                        is_object($shipXml->Faults)
//                        && is_object($shipXml->Result->Code)
//                        && is_object($shipXml->Result->Desc)
//                        && intval($shipXml->Result->Code) != self::SUCCESS_CODE
//                       ) {
//                           $code = (string)$shipXml->Result->Code;
//                           $description = $shipXml->Result->Desc;
//                           $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
//                    }else {
//                        $this->_addRate($xml);
//                        return $this;
//                    }
//                }
//            } else {
//                $this->_errors[] = Mage::helper('usa')->__('Response is in the wrong format');
//            }
//        }
//    }

    public function getCode($type, $code='')
    {
        static $codes;
        $codes = array(
            'service'=>array(
                'IE' => Mage::helper('usa')->__('International Express'),
                'E SAT' => Mage::helper('usa')->__('Express Saturday'),
                'E 10:30AM' => Mage::helper('usa')->__('Express 10:30 AM'),
                'E' => Mage::helper('usa')->__('Express'),
                'N' => Mage::helper('usa')->__('Next Afternoon'),
                'S' => Mage::helper('usa')->__('Second Day Service'),
                'G' => Mage::helper('usa')->__('Ground'),
            ),
            'shipment_type'=>array(
                'L' => Mage::helper('usa')->__('Letter'),
                'P' => Mage::helper('usa')->__('Package'),
            ),
            'international_searvice'=>'IE',
            /*
            'special_service'=>array(
                'E SAT'=>'SAT',
                'E 10:30AM'=>'1030',
            ),
            */
           'dutypayment_type'=>array(
                'S' => Mage::helper('usa')->__('Sender'),
                'R' => Mage::helper('usa')->__('Receiver'),
                '3' => Mage::helper('usa')->__('Third Party'),
           ),

           'special_express'=>array(
                'E SAT'=>'SAT',
                'E 10:30AM'=>'1030',
           ),

           'descr_to_service'=>array(
                'E SAT'=>'Saturday',
                'E 10:30AM'=>'10:30 A.M',
           ),

        );



        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid DHL XML code type: %s.', $type));
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid DHL XML code for type %s: %s.', $type, $code));
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

//    protected function _addRate($xml)
//    {
//        $r = $this->_rawRequest;
//        $services=$this->getCode('service');
//        $shipXml=(($r->getDestCountryId() == self::USA_COUNTRY_ID)?$xml->Shipment:$xml->IntlShipment);
//        $desc=(string)$shipXml->EstimateDetail->ServiceLevelCommitment->Desc;
//        $totalEstimate=(string)$shipXml->EstimateDetail->RateEstimate->TotalChargeEstimate;
//        /*
//        * DHL can return with empty result and success code
//        * we need to make sure there is shipping estimate and code
//        */
//        if($desc && $totalEstimate){
//            $service = (string)$shipXml->EstimateDetail->Service->Code;
//            $data['term'] = (isset($services[$service])?$services[$service]:$desc);
//            $data['price_total'] = $totalEstimate;
//            $this->_dhlRates[$service] = $data;
//        }
//    }

    protected function _addRate($shipXml)
    {
        $r = $this->_rawRequest;
        $services = $this->getCode('service');
        $regexps = $this->getCode('descr_to_service');
        $desc=(string)$shipXml->EstimateDetail->ServiceLevelCommitment->Desc;
        $totalEstimate=(string)$shipXml->EstimateDetail->RateEstimate->TotalChargeEstimate;
        /*
        * DHL can return with empty result and success code
        * we need to make sure there is shipping estimate and code
        */
        if($desc && $totalEstimate){
            $service = (string)$shipXml->EstimateDetail->Service->Code;
            $description = (string)$shipXml->EstimateDetail->ServiceLevelCommitment->Desc;
            if ($service == 'E') {
                foreach ($regexps as $expService=>$exp) {
                    if (preg_match('/'.preg_quote($exp, '/').'/', $description)) {
                        $service = $expService;
                    }
                }
            }

            $data['term'] = (isset($services[$service])?$services[$service]:$desc);
            $data['price_total'] = $this->getMethodPrice($totalEstimate, $service);
            $this->_dhlRates[] = array('service'=>$service, 'data'=>$data);
        }
    }

    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings=array($trackings);
        }
       $this->_getXMLTracking($trackings);

       return $this->_result;
    }

    protected function setTrackingReqeust()
    {
        $r = new Varien_Object();

        $id = $this->getConfigData('id');
        $r->setId($id);

        $password = $this->getConfigData('password');
        $r->setPassword($password);

        $this->_rawTrackRequest = $r;
    }

    protected function _getXMLTracking($trackings)
    {
        $r = $this->_rawTrackRequest;

        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><eCommerce/>');
        $xml->addAttribute('action', 'Request');
        $xml->addAttribute('version', '1.1');

        $requestor = $xml->addChild('Requestor');
            $requestor->addChild('ID', $r->getId());
            $requestor->addChild('Password', $r->getPassword());

        $track=$xml->addChild('Track');
            $track->addAttribute('action', 'Get');
            $track->addAttribute('version', '1.0');

            //shippment has not been delivered or no scans
            //$track->addChild('Shipment')->addChild('TrackingNbr','1231230011');
            //home shipment
            //$track->addChild('Shipment')->addChild('TrackingNbr','2342340011');
            //international shipment
            //$track->addChild('Shipment')->addChild('TrackingNbr','5675670011');
            //tracking not in airborme tracking tsystem
            //$track->addChild('Shipment')->addChild('TrackingNbr','7897890011');
            //tracking need to contanct customer service for more information
            //$track->addChild('Shipment')->addChild('TrackingNbr','8198190011');

            foreach($trackings as $tracking){
               $track->addChild('Shipment')->addChild('TrackingNbr',$tracking);
            }
         $request = $xml->asXML();
         $debugData = array('request' => $request);
         /*
         * tracking api cannot process from 3pm to 5pm PST time on Sunday
         * DHL Airborne conduts a maintainance during that period.
         */
         try {
            $url = $this->getConfigData('gateway_url');
            if (!$url) {
                $url = $this->_defaultGatewayUrl;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            $responseBody = curl_exec($ch);
            $debugData['result'] = $responseBody;
            curl_close ($ch);
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $responseBody = '';
        }
        $this->_debug($debugData);
#echo "<xmp>".$responseBody."</xmp>";
        $this->_parseXmlTrackingResponse($trackings, $responseBody);
    }

    protected function _parseXmlTrackingResponse($trackings, $response)
    {
         $errorTitle = 'Unable to retrieve tracking';
         $resultArr=array();
         $errorArr=array();
         $trackingserror=array();
         $tracknum='';
         if (strlen(trim($response))>0) {
            if (strpos(trim($response), '<?xml')===0) {
                 $xml = simplexml_load_string($response);
                 if (is_object($xml)) {
                     $trackxml=$xml->Track;
                      if (
                        is_object($xml->Faults)
                        && is_object($xml->Faults->Fault)
                        && is_object($xml->Faults->Fault->Code)
                        && is_object($xml->Faults->Fault->Description)
                        && is_object($xml->Faults->Fault->Context)
                       ) {
                        $code = (string)$xml->Faults->Fault->Code;
                        $description = $xml->Faults->Fault->Description;
                        $context = $xml->Faults->Fault->Context;
                        $errorTitle = Mage::helper('usa')->__('Error #%s : %s (%s)', $code, $description, $context);
                    }elseif(is_object($trackxml) && is_object($trackxml->Shipment)){
                        foreach($trackxml->Shipment as $txml){
                         $rArr=array();

                            if(is_object($txml)){
                                $tracknum=(string)$txml->TrackingNbr;
                                if($txml->Fault){
                                     $code = (string)$txml->Fault->Code;
                                     $description   = $txml->Fault->Description;
                                     $errorArr[$tracknum] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
                                }elseif($txml->Result){
                                    $code = (int)$txml->Result->Code;
                                    if($code===0){
                                         /*
                                        * Code 0== airbill  found
                                        */
                                        $rArr['service']=(string)$txml->Service->Desc;
                                        if(isset($txml->Weight))
                                            $rArr['weight']=(string)$txml->Weight." lbs";
                                        if (isset($txml->Delivery)) {
                                            $rArr['deliverydate'] = (string)$txml->Delivery->Date;
                                            $rArr['deliverytime'] = (string)$txml->Delivery->Time.':00';
                                            $rArr['status'] = Mage::helper('usa')->__('Delivered');
                                            if (isset($txml->Delivery->Location->Desc)) {
                                                $rArr['deliverylocation'] = (string)$txml->Delivery->Location->Desc;
                                            }
                                        } elseif (isset($txml->Pickup)) {
                                            $rArr['deliverydate'] = (string)$txml->Pickup->Date;
                                            $rArr['deliverytime'] = (string)$txml->Pickup->Time.':00';
                                            $rArr['status'] = Mage::helper('usa')->__('Shipment picked up');
                                        } else {
                                             $rArr['status']=(string)$txml->ShipmentType->Desc.Mage::helper('usa')->__(' was not delivered nor scanned');
                                        }

                                        $packageProgress = array();
                                        if (isset($txml->TrackingHistory) && isset($txml->TrackingHistory->Status)) {

                                            foreach ($txml->TrackingHistory->Status as $thistory) {
                                                  $tempArr=array();
                                                  $tempArr['activity'] = (string)$thistory->StatusDesc;
                                                  $tempArr['deliverydate'] = (string)$thistory->Date;//YYYY-MM-DD
                                                  $tempArr['deliverytime'] = (string)$thistory->Time.':00';//HH:MM:ss
                                                  $addArr=array();
                                                  if (isset($thistory->Location->City)) {
                                                    $addArr[] = (string)$thistory->Location->City;
                                                  }
                                                  if (isset($thistory->Location->State)) {
                                                    $addArr[] = (string)$thistory->Location->State;
                                                  }
                                                  if (isset($thistory->Location->CountryCode)) {
                                                    $addArr[] = (string)$thistory->Location->Country;
                                                  }
                                                  if ($addArr) {
                                                    $tempArr['deliverylocation']=implode(', ',$addArr);
                                                  }elseif(isset($thistory['final_delivery']) && (string)$thistory['final_delivery']==='true'){
                                                      /*
                                                      if the history is final delivery, there is no informationabout city, state and country
                                                      */
                                                      $addArr=array();
                                                      if (isset($txml->Receiver->City)) {
                                                        $addArr[] = (string)$txml->Receiver->City;
                                                      }
                                                      if (isset($thistory->Receiver->State)) {
                                                        $addArr[] = (string)$txml->Receiver->State;
                                                      }
                                                      if (isset($thistory->Receiver->CountryCode)) {
                                                        $addArr[] = (string)$txml->Receiver->Country;
                                                      }
                                                      $tempArr['deliverylocation']=implode(', ',$addArr);
                                                  }
                                                  $packageProgress[] = $tempArr;
                                            }
                                            $rArr['progressdetail'] = $packageProgress;

                                        }
                                        $resultArr[$tracknum]=$rArr;
                                    }else{
                                        $description =(string)$txml->Result->Desc;
                                        if($description)
                                            $errorArr[$tracknum]=Mage::helper('usa')->__('Error #%s: %s', $code, $description);
                                        else
                                            $errorArr[$tracknum]=Mage::helper('usa')->__('Unable to retrieve tracking');
                                    }
                                }else{
                                    $errorArr[$tracknum]=Mage::helper('usa')->__('Unable to retrieve tracking');
                                }

                            }
                        }

                    }
                 }
            } else {
                $errorTitle = Mage::helper('usa')->__('Response is in the wrong format');
            }
          }

          $result = Mage::getModel('shipping/tracking_result');
          if($errorArr || $resultArr){
            foreach ($errorArr as $t=>$r) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('dhl');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($t);
                $error->setErrorMessage($r);
                $result->append($error);
            }

            foreach($resultArr as $t => $data) {
                $tracking = Mage::getModel('shipping/tracking_result_status');
                $tracking->setCarrier('dhl');
                $tracking->setCarrierTitle($this->getConfigData('title'));
                $tracking->setTracking($t);
                $tracking->addData($data);
                /*
                $tracking->setStatus($data['status']);
                $tracking->setService($data['service']);
                if(isset($data['deliverydate'])) $tracking->setDeliveryDate($data['deliverydate']);
                if(isset($data['deliverytime'])) $tracking->setDeliveryTime($data['deliverytime']);
                */
                $result->append($tracking);
            }
          }else{
              foreach($trackings as $t){
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('dhl');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($t);
                $error->setErrorMessage($errorTitle);
                $result->append($error);

              }
          }
        $this->_result = $result;
//echo "<pre>";print_r($result);

    }

    public function getResponse()
    {
        $statuses = '';
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking){
                    if($data = $tracking->getAllData()){
                        if (isset($data['status'])) {
                            $statuses .= Mage::helper('usa')->__($data['status'])."\n<br/>";
                        } else {
                            $statuses .= Mage::helper('usa')->__($data['error_message'])."\n<br/>";
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
            $arr[$k] = $this->getCode('service', $k);
        }
        return $arr;
    }

    public function isStateProvinceRequired()
    {
        return true;
    }

    public function getAdditionalProtectionValueTypes()
    {
        return array(
            self::ADDITIONAL_PROTECTION_VALUE_CONFIG=>Mage::helper('usa')->__('Configuration'),
            self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL=>Mage::helper('usa')->__('Subtotal'),
            self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT=>Mage::helper('usa')->__('Subtotal With Discount'),
            );
    }

    public function getAdditionalProtectionRoundingTypes()
    {
        return array(
            self::ADDITIONAL_PROTECTION_ROUNDING_FLOOR => Mage::helper('usa')->__('To Lower'),
            self::ADDITIONAL_PROTECTION_ROUNDING_CEIL  => Mage::helper('usa')->__('To Upper'),
            self::ADDITIONAL_PROTECTION_ROUNDING_ROUND => Mage::helper('usa')->__('Round'),
            );
    }
}
