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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * DHL shipping implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl
    extends Mage_Usa_Model_Shipping_Carrier_Dhl_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'dhl';

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
     * Errors placeholder
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Dhl rates result
     *
     * @var array
     */
    protected $_dhlRates = array();

    /**
     * Default gateway url
     *
     * @var string
     */
    protected $_defaultGatewayUrl = 'https://eCommerce.airborne.com/ApiLandingTest.asp';

    /**
     * Container types that could be customized
     *
     * @var array
     */
    protected $_customizableContainerTypes = array('P');

    const SUCCESS_CODE = 203;
    const SUCCESS_LABEL_CODE = 100;

    const ADDITIONAL_PROTECTION_ASSET = 'AP';
    const ADDITIONAL_PROTECTION_NOT_REQUIRED = 'NR';

    const ADDITIONAL_PROTECTION_VALUE_CONFIG = 0;
    const ADDITIONAL_PROTECTION_VALUE_SUBTOTAL = 1;
    const ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT = 2;

    const ADDITIONAL_PROTECTION_ROUNDING_FLOOR = 0;
    const ADDITIONAL_PROTECTION_ROUNDING_CEIL = 1;
    const ADDITIONAL_PROTECTION_ROUNDING_ROUND = 2;

    /**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag($this->_activeFlag)) {
            return false;
        }

        $requestDhl = clone $request;
        $origCompanyName = $requestDhl->getOrigCompanyName();
        if (!$origCompanyName) {
            $origCompanyName = Mage::getStoreConfig(
                Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME,
                $requestDhl->getStoreId()
            );
        }

        $origCountryId = $requestDhl->getOrigCountryId();
        if (!$origCountryId) {
            $origCountryId = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $requestDhl->getStoreId()
            );
        }
        $origState = $requestDhl->getOrigState();
        if (!$origState) {
            $origState = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID,
                $requestDhl->getStoreId()
            );
        }
        $origCity = $requestDhl->getOrigCity();
        if (!$origCity) {
            $origCity = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY,
                $requestDhl->getStoreId()
            );
        }

        $origPostcode = $requestDhl->getOrigPostcode();
        if (!$origPostcode) {
            $origPostcode = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $requestDhl->getStoreId()
            );
        }
        $requestDhl->setOrigCompanyName($origCompanyName)
            ->setCountryId($origCountryId)
            ->setOrigState($origState)
            ->setOrigCity($origCity)
            ->setOrigPostal($origPostcode);
        $this->setRequest($requestDhl);
        $this->_result = $this->_getQuotes();
        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

    /**
     * Prepare and set request in property of current instance
     *
     * @param Varien_Object $request
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl
     */
    public function setRequest(Varien_Object $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

        if ($request->getAction() == 'GenerateLabel') {
            $r->setAction('GenerateLabel');
        } else {
            $r->setAction('RateEstimate');
        }
        $r->setIsGenerateLabelReturn($request->getIsGenerateLabelReturn());

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

        if ($request->getDhlDutiable()) {
            $shipmentDutible = $request->getDhlDutiable();
        } else {
            $shipmentDutible = $this->getConfigData('dutiable');
        }
        $r->setDutiable($shipmentDutible);

        if ($request->getDhlDutyPaymentType()) {
            $dutypaytype = $request->getDhlDutyPaymentType();
        } else {
            $dutypaytype = $this->getConfigData('dutypaymenttype');
        }
        $r->setDutyPaymentType($dutypaytype);

        if ($request->getDhlContentDesc()) {
            $contentdesc = $request->getDhlContentDesc();
        } else {
            $contentdesc = $this->getConfigData('contentdesc');
        }
        $r->setContentDesc($contentdesc);

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $r->getStoreId()
            );
        }
        $r->setOrigCountry($origCountry);

        if ($request->getOrigCountryId()) {
            $origCountryId = $request->getOrigCountryId();
        } else {
            $origCountryId = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $r->getStoreId()
            );
        }
        $r->setOrigCountryId($origCountryId);

        if ($request->getAction() == 'GenerateLabel') {
            $packageParams = $request->getPackageParams();
            $shippingWeight = $request->getPackageWeight();
            if ($packageParams->getWeightUnits() != Zend_Measure_Weight::POUND) {
                $shippingWeight = round(Mage::helper('usa')->convertMeasureWeight(
                    $request->getPackageWeight(),
                    $packageParams->getWeightUnits(),
                    Zend_Measure_Weight::POUND
                ));
            }
            if ($packageParams->getDimensionUnits() != Zend_Measure_Length::INCH) {
                $packageParams->setLength(round(Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getLength(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH
                )));
                $packageParams->setWidth(round(Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getWidth(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH
                )));
                $packageParams->setHeight(round(Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getHeight(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH
                )));
            }
            $r->setPackageParams($packageParams);
        } else {
            /*
            * DHL only accepts weight as a whole number. Maximum length is 3 digits.
            */
            $shippingWeight = $request->getPackageWeight();
            if ($shipmentType != 'L') {
                $weight = $this->getTotalNumOfBoxes($shippingWeight);
                $shippingWeight = round(max(1, $weight), 0);
            }
        }

        $r->setValue(round($request->getPackageValue(), 2));
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());
        $r->setCustomsValue($request->getPackageCustomsValue());
        $r->setDestStreet(Mage::helper('core/string')->substr(str_replace("\n", '', $request->getDestStreet()), 0, 35));
        $r->setDestStreetLine2($request->getDestStreetLine2());
        $r->setDestCity($request->getDestCity());
        $r->setOrigCompanyName($request->getOrigCompanyName());
        $r->setOrigCity($request->getOrigCity());
        $r->setOrigPhoneNumber($request->getOrigPhoneNumber());
        $r->setOrigPersonName($request->getOrigPersonName());
        $r->setOrigEmail(Mage::getStoreConfig('trans_email/ident_general/email', $r->getStoreId()));
        $r->setOrigCity($request->getOrigCity());
        $r->setOrigPostal($request->getOrigPostal());
        $originStreet1 = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1,$r->getStoreId());
        $originStreet2 = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, $r->getStoreId());
        $r->setOrigStreet($request->getOrigStreet() ? $request->getOrigStreet() : $originStreet2);
        $r->setOrigStreetLine2($request->getOrigStreetLine2());
        $r->setDestPhoneNumber($request->getDestPhoneNumber());
        $r->setDestPersonName($request->getDestPersonName());
        $r->setDestCompanyName($request->getDestCompanyName());


        if (is_numeric($request->getOrigState())) {
            $r->setOrigState(Mage::getModel('directory/region')->load($request->getOrigState())->getCode());
        } else {
            $r->setOrigState($request->getOrigState());
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        //for DHL, puero rico state for US will assume as puerto rico country
        //for puerto rico, dhl will ship as international
        if ($destCountry == self::USA_COUNTRY_ID && ($request->getDestPostcode() == '00912'
                                                     || $request->getDestRegionCode() == self::PUERTORICO_COUNTRY_ID)
        ) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        $r->setDestCountryId($destCountry);
        $r->setDestState($request->getDestRegionCode());

        $r->setWeight($shippingWeight);
        $r->setFreeMethodWeight($request->getFreeMethodWeight());

        $r->setOrderShipment($request->getOrderShipment());

        if ($request->getPackageId()) {
            $r->setPackageId($request->getPackageId());
        }

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

        $r->setFreeMethodRequest(true);
        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $freeWeight = round(max(1, $weight), 0);
        $r->setWeight($freeWeight);
        $r->setService($freeMethod);
    }

    /**
     * Get xml quotes
     *
     * @return Mage_Core_Model_Abstract|Varien_Object
     */
    protected function _getXmlQuotes()
    {
        return $this->_doRequest();
    }

    /**
     * Do rate request and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result|Varien_Object
     */
    protected function _doRequest()
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
                $shipKey = $r->getShippingKey();
                $r->setShipDate($shipDate);
            } else {
                $shipment = $xml->addChild('IntlShipment');
                $shipKey = $r->getShippingIntlKey();
                $r->setShipDate($this->_getShipDate(false));
                /*
                * For internation shippingment customsvalue must be posted
                */
                $shippingDuty = $shipment->addChild('Dutiable');
                $shippingDuty->addChild('DutiableFlag', ($r->getDutiable() ? 'Y' : 'N'));
                $shippingDuty->addChild('CustomsValue', $r->getValue());
                $shippingDuty->addChild('IsSEDReqd', 'N');
            }
            $hasShipCode = true;
            $this->_createShipmentXml($shipment, $shipKey);
        } else {
            if ($r->getAction() == 'GenerateLabel') {
                $methods = array($r->getService());
            }

            foreach ($methods as $method) {
                $shipment = false;
                if (in_array($method, array_keys($this->getCode('special_express')))) {
                    $r->setService('E');
                    $r->setExtendedService($this->getCode('special_express', $method));
                } else {
                    $r->setService($method);
                    $r->setExtendedService(null);
                }
                if ($r->getDestCountryId() == self::USA_COUNTRY_ID && $method != $internationcode) {
                    $shipment = $xml->addChild('Shipment');
                    $shipKey = $r->getShippingKey();
                    $r->setShipDate($shipDate);
                } elseif ($r->getDestCountryId() != self::USA_COUNTRY_ID && $method == $internationcode) {
                    $shipment = $xml->addChild('IntlShipment');
                    $shipKey = $r->getShippingIntlKey();
                    if ($r->getCustomsValue() != null && $r->getCustomsValue() != '') {
                        $customsValue =  $r->getCustomsValue();
                    } else {
                        $customsValue =  $r->getValue();
                    }

                    $r->setShipDate($this->_getShipDate(false));

                    /*
                    * For internation shippingment customsvalue must be posted
                    */
                    $shippingDuty = $shipment->addChild('Dutiable');
                    $shippingDuty->addChild('DutiableFlag', ($r->getDutiable() ? 'Y' : 'N'));
                    $shippingDuty->addChild('CustomsValue', $customsValue);
                    $shippingDuty->addChild('IsSEDReqd', 'N');
                }
                if ($shipment !== false) {
                    $hasShipCode = true;
                    $this->_createShipmentXml($shipment, $shipKey);
                }
            }
        }

        if (!$hasShipCode) {
            $this->_errors[] = Mage::helper('usa')->__('There is no available method for selected shipping address.');
            return;
        }

        $request = $xml->asXML();
        $request = utf8_encode($request);
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
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                $responseBody = curl_exec($ch);
                curl_close($ch);

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

    /**
     * Create shipment xml
     *
     * @param  $shipment
     * @param  $shipKey
     * @return void
     */
    protected function _createShipmentXml($shipment, $shipKey)
    {
        $r = $this->_rawRequest;

        $store = Mage::app()->getStore($r->getStoreId());

        $_haz = $this->getConfigFlag('hazardous_materials');

        $_subtotal = $r->getValue();
        $_subtotalWithDiscount = $r->getValueWithDiscount();

        $_width = max(0, (double)$this->getConfigData('default_width'));
        $_height = max(0, (double)$this->getConfigData('default_height'));
        $_length = max(0, (double)$this->getConfigData('default_length'));

        $packageParams = $r->getPackageParams();
        if ($packageParams) {
            $_length = $packageParams->getLength();
            $_width = $packageParams->getWidth();
            $_height = $packageParams->getHeight();
        }

        $_apEnabled = $this->getConfigFlag('additional_protection_enabled');
        $_apUseSubtotal = $this->getConfigData('additional_protection_use_subtotal');
        $_apConfigValue = max(0, (double)$this->getConfigData('additional_protection_value'));
        $_apMinValue = max(0, (double)$this->getConfigData('additional_protection_min_value'));
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

        if ($r->getAction() == 'GenerateLabel') {
            $shipment->addAttribute('action', 'GenerateLabel');
        } else {
            $shipment->addAttribute('action', 'RateEstimate');
        }
        $shipment->addAttribute('version', '1.0');

        $shippingCredentials = $shipment->addChild('ShippingCredentials');
        $shippingCredentials->addChild('ShippingKey', $shipKey);
        $shippingCredentials->addChild('AccountNbr', $r->getAccountNbr());

        $shipmentDetail = $shipment->addChild('ShipmentDetail');
        if ($r->getAction() == 'GenerateLabel') {
            if ($this->_request->getReferenceData()) {
                $referenceData = $this->_request->getReferenceData() . $this->_request->getPackageId();
            } else {
                $referenceData = 'Order #'
                                 . $r->getOrderShipment()->getOrder()->getIncrementId()
                                 . ' P'
                                 . $r->getPackageId();
            }

            $shipmentDetail->addChild('ShipperReference', $referenceData);
        }
        $shipmentDetail->addChild('ShipDate', $r->getShipDate());
        $shipmentDetail->addChild('Service')->addChild('Code', $r->getService());
        $shipmentDetail->addChild('ShipmentType')->addChild('Code', $r->getShipmentType());
        $shipmentDetail->addChild('Weight', $r->getWeight());
        $shipmentDetail->addChild('ContentDesc', $r->getContentDesc());
        $additionalProtection = $shipmentDetail->addChild('AdditionalProtection');
        $additionalProtection->addChild('Code', $apCode);
        $additionalProtection->addChild('Value', floor($apValue));

        if ($_width || $_height || $_length) {
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
        $billing->addChild('Party')->addChild('Code', $r->getIsGenerateLabelReturn() ? 'R' : 'S');
        $billing->addChild('DutyPaymentType', $r->getDutyPaymentType());
        if ($r->getIsGenerateLabelReturn()) {
            $billing->addChild('AccountNbr', $r->getAccountNbr());
        }

        $sender = $shipment->addChild('Sender');
        $sender->addChild('SentBy', ($r->getOrigPersonName()));
        $sender->addChild('PhoneNbr', $r->getOrigPhoneNumber());
        $sender->addChild('Email', $r->getOrigEmail());

        $senderAddress = $sender->addChild('Address');
        $senderAddress->addChild('Street', htmlspecialchars($r->getOrigStreet() ? $r->getOrigStreet() : 'N/A'));
        $senderAddress->addChild('City', htmlspecialchars($r->getOrigCity()));
        $senderAddress->addChild('State', htmlspecialchars($r->getOrigState()));
        $senderAddress->addChild('CompanyName', htmlspecialchars($r->getOrigCompanyName()));
        /*
        * DHL xml service is using UK for united kingdom instead of GB which is a standard ISO country code
        */
        $senderAddress->addChild('Country', ($r->getOrigCountryId() == 'GB' ? 'UK' : $r->getOrigCountryId()));
        $senderAddress->addChild('PostalCode', $r->getOrigPostal());

        $receiver = $shipment->addChild('Receiver');
        $receiver->addChild('AttnTo', $r->getDestPersonName());
        $receiver->addChild('PhoneNbr', $r->getDestPhoneNumber());

        $receiverAddress = $receiver->addChild('Address');
        $receiverAddress->addChild('Street', htmlspecialchars($r->getDestStreet() ? $r->getDestStreet() : 'N/A'));
        $receiverAddress->addChild('StreetLine2',
                                   htmlspecialchars($r->getDestStreetLine2() ? $r->getDestStreetLine2() : 'N/A')
        );
        $receiverAddress->addChild('City', htmlspecialchars($r->getDestCity()));
        $receiverAddress->addChild('State', htmlspecialchars($r->getDestState()));
        $receiverAddress->addChild('CompanyName',
                                   htmlspecialchars($r->getDestCompanyName() ? $r->getDestCompanyName() : 'N/A')
        );

        /*
        * DHL xml service is using UK for united kingdom instead of GB which is a standard ISO country code
        */
        $receiverAddress->addChild('Country', ($r->getDestCountryId() == 'GB' ? 'UK' : $r->getDestCountryId()));
        $receiverAddress->addChild('PostalCode', $r->getDestPostal());

        if ($r->getAction() == 'GenerateLabel') {
            $label = $shipment->addChild('ShipmentProcessingInstructions')->addChild('Label');
            $label->addChild('ImageType', 'PNG');
        }
    }

    /**
     * Parse xml response and return result
     *
     * @param string $response
     * @return Mage_Shipping_Model_Rate_Result|Varien_Object
     */
    protected function _parseXmlResponse($response)
    {
        $r = $this->_rawRequest;
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve quotes';

        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml') === 0) {
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
                                foreach ($xml->Shipment as $shipXml) {
                                    $this->_parseXmlObject($shipXml);
                                }
                            } else {
                                $this->_errors[] = Mage::helper('usa')->__('Shipment is not available.');
                            }
                        } else {
                            $shipXml = $xml->IntlShipment;
                            $this->_parseXmlObject($shipXml);
                        }
                        $shipXml = (($r->getDestCountryId() == self::USA_COUNTRY_ID)
                            ? $xml->Shipment
                            : $xml->IntlShipment
                        );
                    }
                }
            } else {
                $this->_errors[] = Mage::helper('usa')->__('The response is in wrong format.');
            }
        }

        if ($this->_rawRequest->getAction() == 'GenerateLabel') {
            $result = new Varien_Object();
            if (!empty($this->_errors)) {
                $result->setErrors(implode($this->_errors, '; '));
            } else {
                if ($xml !== false) {
                    if ($r->getDestCountryId() == self::USA_COUNTRY_ID) {
                        $shippingLabelContent = base64_decode((string)$xml->Shipment->Label->Image);
                        $trackingNumber = (string)$xml->Shipment->ShipmentDetail->AirbillNbr;
                    } else {
                        $shippingLabelContent = base64_decode((string)$xml->IntlShipment->Label->Image);
                        $trackingNumber = (string)$xml->IntlShipment->ShipmentDetail->AirbillNbr;
                    }
                }
                $result->setShippingLabelContent($shippingLabelContent);
                $result->setTrackingNumber($trackingNumber);
            }
            return $result;
        } else {
            $result = Mage::getModel('shipping/rate_result');
            if ($this->_dhlRates) {
                foreach ($this->_dhlRates as $rate) {
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
            } else if (!empty($this->_errors)) {
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier('dhl');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
                $result->append($error);
            }
            return $result;
        }
    }

    /**
     * Parse xml object
     *
     * @param mixed $shipXml
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl
     */
    protected function _parseXmlObject($shipXml)
    {
        if (
            is_object($shipXml->Faults)
            && is_object($shipXml->Faults->Fault)
            && is_object($shipXml->Faults->Fault->Desc)
            && intval($shipXml->Faults->Fault->Code) != self::SUCCESS_CODE
            && intval($shipXml->Faults->Fault->Code) != self::SUCCESS_LABEL_CODE
        ) {
            $code = (string)$shipXml->Faults->Fault->Code;
            $description = $shipXml->Faults->Fault->Desc;
            $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
        } elseif (
            is_object($shipXml->Faults)
            && is_object($shipXml->Result->Code)
            && is_object($shipXml->Result->Desc)
            && intval($shipXml->Result->Code) != self::SUCCESS_CODE
            && intval($shipXml->Result->Code) != self::SUCCESS_LABEL_CODE
        ) {
            $code = (string)$shipXml->Result->Code;
            $description = $shipXml->Result->Desc;
            $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
        } else {
            $this->_addRate($shipXml);
        }
        return $this;
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
        static $codes;
        $codes = array(
            'service' => array(
                'IE' => Mage::helper('usa')->__('International Express'),
                'E SAT' => Mage::helper('usa')->__('Express Saturday'),
                'E 10:30AM' => Mage::helper('usa')->__('Express 10:30 AM'),
                'E' => Mage::helper('usa')->__('Express'),
                'N' => Mage::helper('usa')->__('Next Afternoon'),
                'S' => Mage::helper('usa')->__('Second Day Service'),
                'G' => Mage::helper('usa')->__('Ground'),
            ),
            'shipment_type' => array(
                'L' => Mage::helper('usa')->__('Letter'),
                'P' => Mage::helper('usa')->__('Package'),
            ),
            'international_searvice' => 'IE',
            'dutypayment_type' => array(
                'S' => Mage::helper('usa')->__('Sender'),
                'R' => Mage::helper('usa')->__('Receiver'),
                '3' => Mage::helper('usa')->__('Third Party'),
            ),

            'special_express' => array(
                'E SAT' => 'SAT',
                'E 10:30AM' => '1030',
            ),

            'descr_to_service' => array(
                'E SAT' => 'Saturday',
                'E 10:30AM' => '10:30 A.M',
            ),

        );


        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * Parse xml and add rates to instance property
     *
     * @param mixed $shipXml
     * @return void
     */
    protected function _addRate($shipXml)
    {
        $r = $this->_rawRequest;
        $services = $this->getCode('service');
        $regexps = $this->getCode('descr_to_service');
        $desc = ($shipXml->EstimateDetail) ? (string)$shipXml->EstimateDetail->ServiceLevelCommitment->Desc : null;

        $totalEstimate = $shipXml->EstimateDetail
                ? (string)$shipXml->EstimateDetail->RateEstimate->TotalChargeEstimate
                : null;
        /*
        * DHL can return with empty result and success code
        * we need to make sure there is shipping estimate and code
        */
        if ($desc && $totalEstimate) {
            $service = (string)$shipXml->EstimateDetail->Service->Code;
            $description = (string)$shipXml->EstimateDetail->ServiceLevelCommitment->Desc;
            if ($service == 'E') {
                foreach ($regexps as $expService => $exp) {
                    if (preg_match('/' . preg_quote($exp, '/') . '/', $description)) {
                        $service = $expService;
                    }
                }
            }

            $data['term'] = (isset($services[$service]) ? $services[$service] : $desc);
            $data['price_total'] = $this->getMethodPrice($totalEstimate, $service);
            $this->_dhlRates[] = array('service' => $service, 'data' => $data);
        }
    }

    /**
     * Get tracking
     *
     * @param mixed $trackings
     * @return mixed
     */
    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }
        $this->_getXMLTracking($trackings);

        return $this->_result;
    }

    /**
     * Set tracking request
     *
     * @return null
     */
    protected function setTrackingReqeust()
    {
        $r = new Varien_Object();

        $id = $this->getConfigData('id');
        $r->setId($id);

        $password = $this->getConfigData('password');
        $r->setPassword($password);

        $this->_rawTrackRequest = $r;
    }

    /**
     * Send request for trackings
     *
     * @param array $trackings
     */
    protected function _getXMLTracking($trackings)
    {
        $r = $this->_rawTrackRequest;

        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><eCommerce/>');
        $xml->addAttribute('action', 'Request');
        $xml->addAttribute('version', '1.1');

        $requestor = $xml->addChild('Requestor');
        $requestor->addChild('ID', $r->getId());
        $requestor->addChild('Password', $r->getPassword());

        $track = $xml->addChild('Track');
        $track->addAttribute('action', 'Get');
        $track->addAttribute('version', '1.0');

        foreach ($trackings as $tracking) {
            $track->addChild('Shipment')->addChild('TrackingNbr', $tracking);
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            $responseBody = curl_exec($ch);
            $debugData['result'] = $responseBody;
            curl_close($ch);
        } catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $responseBody = '';
        }
        $this->_debug($debugData);
        $this->_parseXmlTrackingResponse($trackings, $responseBody);
    }

    /**
     * Parse xml tracking response
     *
     * @param array $trackingvalue
     * @param string $response
     * @return null
     */
    protected function _parseXmlTrackingResponse($trackings, $response)
    {
        $errorTitle = Mage::helper('usa')->__('Unable to retrieve tracking');
        $resultArr = array();
        $errorArr = array();
        $trackingserror = array();
        $tracknum = '';
        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml') === 0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    $trackxml = $xml->Track;
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
                    } elseif (is_object($trackxml) && is_object($trackxml->Shipment)) {
                        foreach ($trackxml->Shipment as $txml) {
                            $rArr = array();

                            if (is_object($txml)) {
                                $tracknum = (string)$txml->TrackingNbr;
                                if ($txml->Fault) {
                                    $code = (string)$txml->Fault->Code;
                                    $description = $txml->Fault->Description;
                                    $errorArr[$tracknum] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
                                } elseif ($txml->Result) {
                                    $code = (int)$txml->Result->Code;
                                    if ($code === 0) {
                                        /*
                                        * Code 0== airbill  found
                                        */
                                        $rArr['service'] = (string)$txml->Service->Desc;
                                        if (isset($txml->Weight))
                                            $rArr['weight'] = (string)$txml->Weight . " lbs";
                                        if (isset($txml->Delivery)) {
                                            $rArr['deliverydate'] = (string)$txml->Delivery->Date;
                                            $rArr['deliverytime'] = (string)$txml->Delivery->Time . ':00';
                                            $rArr['status'] = Mage::helper('usa')->__('Delivered');
                                            if (isset($txml->Delivery->Location->Desc)) {
                                                $rArr['deliverylocation'] = (string)$txml->Delivery->Location->Desc;
                                            }
                                        } elseif (isset($txml->Pickup)) {
                                            $rArr['deliverydate'] = (string)$txml->Pickup->Date;
                                            $rArr['deliverytime'] = (string)$txml->Pickup->Time . ':00';
                                            $rArr['status'] = Mage::helper('usa')->__('Shipment picked up');
                                        } else {
                                            $rArr['status'] = (string)$txml->ShipmentType->Desc
                                                  . Mage::helper('usa')->__(' was not delivered nor scanned');
                                        }

                                        $packageProgress = array();
                                        if (isset($txml->TrackingHistory) && isset($txml->TrackingHistory->Status)) {

                                            foreach ($txml->TrackingHistory->Status as $thistory) {
                                                $tempArr = array();
                                                $tempArr['activity'] = (string)$thistory->StatusDesc;
                                                $tempArr['deliverydate'] = (string)$thistory->Date; //YYYY-MM-DD
                                                $tempArr['deliverytime'] = (string)$thistory->Time . ':00'; //HH:MM:ss
                                                $addArr = array();
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
                                                    $tempArr['deliverylocation'] = implode(', ', $addArr);
                                                } elseif (isset($thistory['final_delivery'])
                                                          && (string)$thistory['final_delivery'] === 'true'
                                                ) {
                                                    /*
                                                    * if the history is final delivery, there is no informationabout
                                                    * city, state and country
                                                    */
                                                    $addArr = array();
                                                    if (isset($txml->Receiver->City)) {
                                                        $addArr[] = (string)$txml->Receiver->City;
                                                    }
                                                    if (isset($thistory->Receiver->State)) {
                                                        $addArr[] = (string)$txml->Receiver->State;
                                                    }
                                                    if (isset($thistory->Receiver->CountryCode)) {
                                                        $addArr[] = (string)$txml->Receiver->Country;
                                                    }
                                                    $tempArr['deliverylocation'] = implode(', ', $addArr);
                                                }
                                                $packageProgress[] = $tempArr;
                                            }
                                            $rArr['progressdetail'] = $packageProgress;

                                        }
                                        $resultArr[$tracknum] = $rArr;
                                    } else {
                                        $description = (string)$txml->Result->Desc;
                                        if ($description)
                                            $errorArr[$tracknum] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
                                        else
                                            $errorArr[$tracknum] = Mage::helper('usa')->__('Unable to retrieve tracking');
                                    }
                                } else {
                                    $errorArr[$tracknum] = Mage::helper('usa')->__('Unable to retrieve tracking');
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
        if ($errorArr || $resultArr) {
            foreach ($errorArr as $t => $r) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('dhl');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($t);
                $error->setErrorMessage($r);
                $result->append($error);
            }

            foreach ($resultArr as $t => $data) {
                $tracking = Mage::getModel('shipping/tracking_result_status');
                $tracking->setCarrier('dhl');
                $tracking->setCarrierTitle($this->getConfigData('title'));
                $tracking->setTracking($t);
                $tracking->addData($data);

                $result->append($tracking);
            }
        } else {
            foreach ($trackings as $t) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('dhl');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($t);
                $error->setErrorMessage($errorTitle);
                $result->append($error);

            }
        }
        $this->_result = $result;
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
                        if (isset($data['status'])) {
                            $statuses .= Mage::helper('usa')->__($data['status']) . "\n<br/>";
                        } else {
                            $statuses .= Mage::helper('usa')->__($data['error_message']) . "\n<br/>";
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

    /**
     * Is state province required
     *
     * @return bool
     */
    public function isStateProvinceRequired()
    {
        return true;
    }

    /**
     * Get additional protection value types
     *
     * @return array
     */
    public function getAdditionalProtectionValueTypes()
    {
        return array(
            self::ADDITIONAL_PROTECTION_VALUE_CONFIG => Mage::helper('usa')->__('Configuration'),
            self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL => Mage::helper('usa')->__('Subtotal'),
            self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT => Mage::helper('usa')->__('Subtotal With Discount'),
        );
    }

    /**
     * Get additional protection rounding types
     *
     * @return array
     */
    public function getAdditionalProtectionRoundingTypes()
    {
        return array(
            self::ADDITIONAL_PROTECTION_ROUNDING_FLOOR => Mage::helper('usa')->__('To Lower'),
            self::ADDITIONAL_PROTECTION_ROUNDING_CEIL => Mage::helper('usa')->__('To Upper'),
            self::ADDITIONAL_PROTECTION_ROUNDING_ROUND => Mage::helper('usa')->__('Round'),
        );
    }

    /**
     * Map request to shipment
     *
     * @param Varien_Object $request
     * @return null
     */
    protected function _mapRequestToShipment(Varien_Object $request)
    {
        $customsValue = $request->getPackageParams()->getCustomsValue();
        $request->setOrigPersonName($request->getShipperContactPersonName());
        $request->setOrigPostal($request->getShipperAddressPostalCode());
        $request->setOrigPhoneNumber($request->getShipperContactPhoneNumber());
        $request->setOrigCompanyName($request->getShipperContactCompanyName());
        $request->setOrigCountryId($request->getShipperAddressCountryCode());
        $request->setOrigState($request->getShipperAddressStateOrProvinceCode());
        $request->setOrigCity($request->getShipperAddressCity());
        $request->setOrigStreet($request->getShipperAddressStreet1() . ' ' . $request->getShipperAddressStreet2());
        $request->setOrigStreetLine2($request->getShipperAddressStreet2());

        $request->setDestPersonName($request->getRecipientContactPersonName());
        $request->setDestPostcode($request->getRecipientAddressPostalCode());
        $request->setDestPhoneNumber($request->getRecipientContactPhoneNumber());
        $request->setDestCompanyName($request->getRecipientContactCompanyName());
        $request->setDestCountryId($request->getRecipientAddressCountryCode());
        $request->setDestRegionCode($request->getRecipientAddressStateOrProvinceCode());
        $request->setDestCity($request->getRecipientAddressCity());
        $request->setDestStreet($request->getRecipientAddressStreet1());
        $request->setDestStreetLine2($request->getRecipientAddressStreet2());

        $request->setLimitMethod($request->getShippingMethod());
        $request->setPackageValue($customsValue);
        $request->setValueWithDiscount($customsValue);
        $request->setPackageCustomsValue($customsValue);
        $request->setFreeMethodWeight(0);
        $request->setDhlShipmentType($request->getPackagingType());

        $request->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $request->setAction('GenerateLabel');
        $this->_mapRequestToShipment($request);
        $this->setRequest($request);

        return $this->_doRequest();
    }

    /**
     * Return container types of carrier
     *
     * @param Varien_Object|null $params
     * @return array|bool
     */
    public function getContainerTypes(Varien_Object $params = null)
    {
        return $this->getCode('shipment_type');
    }
}
