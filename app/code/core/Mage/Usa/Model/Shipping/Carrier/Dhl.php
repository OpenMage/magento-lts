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
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHL shipping implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl extends Mage_Usa_Model_Shipping_Carrier_Dhl_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    public const CODE = 'dhl';

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
    protected $_errors = [];

    /**
     * Dhl rates result
     *
     * @var array
     */
    protected $_dhlRates = [];

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
    protected $_customizableContainerTypes = ['P'];

    public const SUCCESS_CODE = 203;
    public const SUCCESS_LABEL_CODE = 100;

    public const ADDITIONAL_PROTECTION_ASSET = 'AP';
    public const ADDITIONAL_PROTECTION_NOT_REQUIRED = 'NR';

    public const ADDITIONAL_PROTECTION_VALUE_CONFIG = 0;
    public const ADDITIONAL_PROTECTION_VALUE_SUBTOTAL = 1;
    public const ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT = 2;

    public const ADDITIONAL_PROTECTION_ROUNDING_FLOOR = 0;
    public const ADDITIONAL_PROTECTION_ROUNDING_CEIL = 1;
    public const ADDITIONAL_PROTECTION_ROUNDING_ROUND = 2;

    /**
     * Collect and get rates
     *
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
                $requestDhl->getStoreId(),
            );
        }

        $origCountryId = $requestDhl->getOrigCountryId();
        if (!$origCountryId) {
            $origCountryId = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $requestDhl->getStoreId(),
            );
        }
        $origState = $requestDhl->getOrigState();
        if (!$origState) {
            $origState = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID,
                $requestDhl->getStoreId(),
            );
        }
        $origCity = $requestDhl->getOrigCity();
        if (!$origCity) {
            $origCity = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY,
                $requestDhl->getStoreId(),
            );
        }

        $origPostcode = $requestDhl->getOrigPostcode();
        if (!$origPostcode) {
            $origPostcode = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $requestDhl->getStoreId(),
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
     * @return $this
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
                $r->getStoreId(),
            );
        }
        $r->setOrigCountry($origCountry);

        if ($request->getOrigCountryId()) {
            $origCountryId = $request->getOrigCountryId();
        } else {
            $origCountryId = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $r->getStoreId(),
            );
        }
        $r->setOrigCountryId($origCountryId);

        if ($request->getAction() == 'GenerateLabel') {
            $packageParams = $request->getPackageParams();
            $shippingWeight = $request->getPackageWeight();
            if ($packageParams->getWeightUnits() != Zend_Measure_Weight::POUND) {
                $shippingWeight = round((float) Mage::helper('usa')->convertMeasureWeight(
                    $request->getPackageWeight(),
                    $packageParams->getWeightUnits(),
                    Zend_Measure_Weight::POUND,
                ));
            }
            if ($packageParams->getDimensionUnits() != Zend_Measure_Length::INCH) {
                $packageParams->setLength(round((float) Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getLength(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH,
                )));
                $packageParams->setWidth(round((float) Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getWidth(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH,
                )));
                $packageParams->setHeight(round((float) Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getHeight(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH,
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
        $originStreet1 = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1, $r->getStoreId());
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
     * @return Mage_Shipping_Model_Rate_Result|null
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
     * @return Mage_Shipping_Model_Rate_Result|Varien_Object|null
     */
    protected function _getXmlQuotes()
    {
        return $this->_doRequest();
    }

    /**
     * Do rate request and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result|Varien_Object|void
     */
    protected function _doRequest()
    {
        $rawRequest = $this->_rawRequest;

        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><eCommerce/>');
        $xml->addAttribute('action', 'Request');
        $xml->addAttribute('version', '1.1');

        $requestor = $xml->addChild('Requestor');
        $requestor->addChild('ID', $rawRequest->getId());
        $requestor->addChild('Password', $rawRequest->getPassword());

        $methods = explode(',', $this->getConfigData('allowed_methods'));
        $internationcode = $this->getCode('international_searvice');
        $hasShipCode = false;

        $shipDate = $this->_getShipDate();

        if ($rawRequest->hasService() && $rawRequest->getFreeMethodRequest()) {
            if ($rawRequest->getDestCountryId() == self::USA_COUNTRY_ID) {
                $shipment = $xml->addChild('Shipment');
                $shipKey = $rawRequest->getShippingKey();
                $rawRequest->setShipDate($shipDate);
            } else {
                $shipment = $xml->addChild('IntlShipment');
                $shipKey = $rawRequest->getShippingIntlKey();
                $rawRequest->setShipDate($this->_getShipDate(false));
                /*
                * For international shipment customs value must be posted
                */
                $shippingDuty = $shipment->addChild('Dutiable');
                $shippingDuty->addChild('DutiableFlag', ($rawRequest->getDutiable() ? 'Y' : 'N'));
                $shippingDuty->addChild('CustomsValue', $rawRequest->getValue());
                $shippingDuty->addChild('IsSEDReqd', 'N');
            }
            $hasShipCode = true;
            $this->_createShipmentXml($shipment, $shipKey);
        } else {
            if ($rawRequest->getAction() == 'GenerateLabel') {
                $methods = [$rawRequest->getService()];
            }

            foreach ($methods as $method) {
                $shipment = false;
                if (array_key_exists($method, $this->getCode('special_express'))) {
                    $rawRequest->setService('E');
                    $rawRequest->setExtendedService($this->getCode('special_express', $method));
                } else {
                    $rawRequest->setService($method);
                    $rawRequest->setExtendedService(null);
                }
                if ($rawRequest->getDestCountryId() == self::USA_COUNTRY_ID && $method != $internationcode) {
                    $shipment = $xml->addChild('Shipment');
                    $shipKey = $rawRequest->getShippingKey();
                    $rawRequest->setShipDate($shipDate);
                } elseif ($rawRequest->getDestCountryId() != self::USA_COUNTRY_ID && $method == $internationcode) {
                    $shipment = $xml->addChild('IntlShipment');
                    $shipKey = $rawRequest->getShippingIntlKey();
                    if ($rawRequest->getCustomsValue() != null && $rawRequest->getCustomsValue() != '') {
                        $customsValue =  $rawRequest->getCustomsValue();
                    } else {
                        $customsValue =  $rawRequest->getValue();
                    }

                    $rawRequest->setShipDate($this->_getShipDate(false));

                    /*
                    * For international shipment customs value must be posted
                    */
                    $shippingDuty = $shipment->addChild('Dutiable');
                    $shippingDuty->addChild('DutiableFlag', ($rawRequest->getDutiable() ? 'Y' : 'N'));
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
        $request = mb_convert_encoding($request, 'UTF-8', 'ISO-8859-1');
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
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
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
     * Create shipment xml
     *
     * @param  $shipment
     * @param  $shipKey
     */
    protected function _createShipmentXml($shipment, $shipKey)
    {
        $rawRequest = $this->_rawRequest;

        $isHaz = $this->getConfigFlag('hazardous_materials');

        $subtotal = $rawRequest->getValue();
        $subtotalWithDiscount = $rawRequest->getValueWithDiscount();

        $width = max(0, (float) $this->getConfigData('default_width'));
        $height = max(0, (float) $this->getConfigData('default_height'));
        $length = max(0, (float) $this->getConfigData('default_length'));

        $packageParams = $rawRequest->getPackageParams();
        if ($packageParams) {
            $length = $packageParams->getLength();
            $width = $packageParams->getWidth();
            $height = $packageParams->getHeight();
        }

        $apEnabled = $this->getConfigFlag('additional_protection_enabled');
        $apUseSubtotal = $this->getConfigData('additional_protection_use_subtotal');
        $apConfigValue = max(0, (float) $this->getConfigData('additional_protection_value'));
        $apMinValue = max(0, (float) $this->getConfigData('additional_protection_min_value'));
        $apValueRounding = $this->getConfigData('additional_protection_rounding');

        $apValue = 0;
        $apCode = self::ADDITIONAL_PROTECTION_NOT_REQUIRED;
        if ($apEnabled) {
            if ($apMinValue <= $subtotal) {
                switch ($apUseSubtotal) {
                    case self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL:
                        $apValue = $subtotal;
                        break;
                    case self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT:
                        $apValue = $subtotalWithDiscount;
                        break;
                    default:
                    case self::ADDITIONAL_PROTECTION_VALUE_CONFIG:
                        $apValue = $apConfigValue;
                        break;
                }

                if ($apValue) {
                    $apCode = self::ADDITIONAL_PROTECTION_ASSET;

                    switch ($apValueRounding) {
                        case self::ADDITIONAL_PROTECTION_ROUNDING_CEIL:
                            $apValue = ceil($apValue);
                            break;
                        case self::ADDITIONAL_PROTECTION_ROUNDING_ROUND:
                            $apValue = round((float) $apValue);
                            break;
                        default:
                        case self::ADDITIONAL_PROTECTION_ROUNDING_FLOOR:
                            $apValue = floor($apValue);
                            break;
                    }
                }
            }
        }

        if ($rawRequest->getAction() == 'GenerateLabel') {
            $shipment->addAttribute('action', 'GenerateLabel');
        } else {
            $shipment->addAttribute('action', 'RateEstimate');
        }
        $shipment->addAttribute('version', '1.0');

        $shippingCredentials = $shipment->addChild('ShippingCredentials');
        $shippingCredentials->addChild('ShippingKey', $shipKey);
        $shippingCredentials->addChild('AccountNbr', $rawRequest->getAccountNbr());

        $shipmentDetail = $shipment->addChild('ShipmentDetail');
        if ($rawRequest->getAction() == 'GenerateLabel') {
            if ($this->_request->getReferenceData()) {
                $referenceData = $this->_request->getReferenceData() . $this->_request->getPackageId();
            } else {
                $referenceData = 'Order #'
                                 . $rawRequest->getOrderShipment()->getOrder()->getIncrementId()
                                 . ' P'
                                 . $rawRequest->getPackageId();
            }

            $shipmentDetail->addChild('ShipperReference', $referenceData);
        }
        $shipmentDetail->addChild('ShipDate', $rawRequest->getShipDate());
        $shipmentDetail->addChild('Service')->addChild('Code', $rawRequest->getService());
        $shipmentDetail->addChild('ShipmentType')->addChild('Code', $rawRequest->getShipmentType());
        $shipmentDetail->addChild('Weight', $rawRequest->getWeight());
        $shipmentDetail->addChild('ContentDesc', $rawRequest->getContentDesc());
        $additionalProtection = $shipmentDetail->addChild('AdditionalProtection');
        $additionalProtection->addChild('Code', $apCode);
        $additionalProtection->addChild('Value', floor($apValue));

        if ($width || $height || $length) {
            $dimensions = $shipmentDetail->addChild('Dimensions');
            $dimensions->addChild('Length', $length);
            $dimensions->addChild('Width', $width);
            $dimensions->addChild('Height', $height);
        }

        if ($isHaz || ($rawRequest->getExtendedService())) {
            $specialServices = $shipmentDetail->addChild('SpecialServices');
        }

        if ($isHaz) {
            $hazardousMaterials = $specialServices->addChild('SpecialService');
            $hazardousMaterials->addChild('Code', 'HAZ');
        }

        if ($rawRequest->getExtendedService()) {
            $extendedService = $specialServices->addChild('SpecialService');
            $extendedService->addChild('Code', $rawRequest->getExtendedService());
        }

        /*
        * R = Receiver (if receiver, need AccountNbr)
        * S = Sender
        * 3 = Third Party (if third party, need AccountNbr)
        */
        $billing = $shipment->addChild('Billing');
        $billing->addChild('Party')->addChild('Code', $rawRequest->getIsGenerateLabelReturn() ? 'R' : 'S');
        $billing->addChild('DutyPaymentType', $rawRequest->getDutyPaymentType());
        if ($rawRequest->getIsGenerateLabelReturn()) {
            $billing->addChild('AccountNbr', $rawRequest->getAccountNbr());
        }

        $sender = $shipment->addChild('Sender');
        $sender->addChild('SentBy', ($rawRequest->getOrigPersonName()));
        $sender->addChild('PhoneNbr', $rawRequest->getOrigPhoneNumber());
        $sender->addChild('Email', $rawRequest->getOrigEmail());

        $senderAddress = $sender->addChild('Address');
        $senderAddress->addChild('Street', htmlspecialchars($rawRequest->getOrigStreet() ? $rawRequest->getOrigStreet() : 'N/A'));
        $senderAddress->addChild('City', htmlspecialchars($rawRequest->getOrigCity()));
        $senderAddress->addChild('State', htmlspecialchars($rawRequest->getOrigState()));
        $senderAddress->addChild('CompanyName', htmlspecialchars($rawRequest->getOrigCompanyName()));
        /*
        * DHL xml service is using UK for united kingdom instead of GB which is a standard ISO country code
        */
        $senderAddress->addChild('Country', ($rawRequest->getOrigCountryId() == 'GB' ? 'UK' : $rawRequest->getOrigCountryId()));
        $senderAddress->addChild('PostalCode', $rawRequest->getOrigPostal());

        $receiver = $shipment->addChild('Receiver');
        $receiver->addChild('AttnTo', $rawRequest->getDestPersonName());
        $receiver->addChild('PhoneNbr', $rawRequest->getDestPhoneNumber());

        $receiverAddress = $receiver->addChild('Address');
        $receiverAddress->addChild('Street', htmlspecialchars($rawRequest->getDestStreet() ? $rawRequest->getDestStreet() : 'N/A'));
        $receiverAddress->addChild(
            'StreetLine2',
            htmlspecialchars($rawRequest->getDestStreetLine2() ? $rawRequest->getDestStreetLine2() : 'N/A'),
        );
        $receiverAddress->addChild('City', htmlspecialchars($rawRequest->getDestCity()));
        $receiverAddress->addChild('State', htmlspecialchars($rawRequest->getDestState()));
        $receiverAddress->addChild(
            'CompanyName',
            htmlspecialchars($rawRequest->getDestCompanyName() ? $rawRequest->getDestCompanyName() : 'N/A'),
        );

        /*
        * DHL xml service is using UK for united kingdom instead of GB which is a standard ISO country code
        */
        $receiverAddress->addChild('Country', ($rawRequest->getDestCountryId() == 'GB' ? 'UK' : $rawRequest->getDestCountryId()));
        $receiverAddress->addChild('PostalCode', $rawRequest->getDestPostal());

        if ($rawRequest->getAction() == 'GenerateLabel') {
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
        $costArr = [];
        $priceArr = [];
        $errorTitle = 'Unable to retrieve quotes';

        if (strlen(trim($response)) > 0) {
            if (str_starts_with(trim($response), '<?xml')) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if (is_object($xml->Faults)
                        && is_object($xml->Faults->Fault)
                        && is_object($xml->Faults->Fault->Code)
                        && is_object($xml->Faults->Fault->Description)
                        && is_object($xml->Faults->Fault->Context)
                    ) {
                        $code = (string) $xml->Faults->Fault->Code;
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
                        $shipXml = (
                            ($r->getDestCountryId() == self::USA_COUNTRY_ID)
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
                $result->setErrors(implode('; ', $this->_errors));
            } else {
                if ($xml !== false) {
                    if ($r->getDestCountryId() == self::USA_COUNTRY_ID) {
                        $shippingLabelContent = base64_decode((string) $xml->Shipment->Label->Image);
                        $trackingNumber = (string) $xml->Shipment->ShipmentDetail->AirbillNbr;
                    } else {
                        $shippingLabelContent = base64_decode((string) $xml->IntlShipment->Label->Image);
                        $trackingNumber = (string) $xml->IntlShipment->ShipmentDetail->AirbillNbr;
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
            } elseif (!empty($this->_errors)) {
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
     * @return $this
     */
    protected function _parseXmlObject($shipXml)
    {
        if (is_object($shipXml->Faults)
            && is_object($shipXml->Faults->Fault)
            && is_object($shipXml->Faults->Fault->Desc)
            && (int) $shipXml->Faults->Fault->Code != self::SUCCESS_CODE
            && (int) $shipXml->Faults->Fault->Code != self::SUCCESS_LABEL_CODE
        ) {
            $code = (string) $shipXml->Faults->Fault->Code;
            $description = $shipXml->Faults->Fault->Desc;
            $this->_errors[$code] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
        } elseif (is_object($shipXml->Faults)
            && is_object($shipXml->Result->Code)
            && is_object($shipXml->Result->Desc)
            && (int) $shipXml->Result->Code != self::SUCCESS_CODE
            && (int) $shipXml->Result->Code != self::SUCCESS_LABEL_CODE
        ) {
            $code = (string) $shipXml->Result->Code;
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
        $codes = [
            'service' => [
                'IE' => Mage::helper('usa')->__('International Express'),
                'E SAT' => Mage::helper('usa')->__('Express Saturday'),
                'E 10:30AM' => Mage::helper('usa')->__('Express 10:30 AM'),
                'E' => Mage::helper('usa')->__('Express'),
                'N' => Mage::helper('usa')->__('Next Afternoon'),
                'S' => Mage::helper('usa')->__('Second Day Service'),
                'G' => Mage::helper('usa')->__('Ground'),
            ],
            'shipment_type' => [
                'L' => Mage::helper('usa')->__('Letter'),
                'P' => Mage::helper('usa')->__('Package'),
            ],
            'international_searvice' => 'IE',
            'dutypayment_type' => [
                'S' => Mage::helper('usa')->__('Sender'),
                'R' => Mage::helper('usa')->__('Receiver'),
                '3' => Mage::helper('usa')->__('Third Party'),
            ],

            'special_express' => [
                'E SAT' => 'SAT',
                'E 10:30AM' => '1030',
            ],

            'descr_to_service' => [
                'E SAT' => 'Saturday',
                'E 10:30AM' => '10:30 A.M',
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
     * Parse xml and add rates to instance property
     *
     * @param mixed $shipXml
     */
    protected function _addRate($shipXml)
    {
        $r = $this->_rawRequest;
        $services = $this->getCode('service');
        $regexps = $this->getCode('descr_to_service');
        $desc = ($shipXml->EstimateDetail) ? (string) $shipXml->EstimateDetail->ServiceLevelCommitment->Desc : null;

        $totalEstimate = $shipXml->EstimateDetail
                ? (float) $shipXml->EstimateDetail->RateEstimate->TotalChargeEstimate
                : null;
        /*
        * DHL can return with empty result and success code
        * we need to make sure there is shipping estimate and code
        */
        if ($desc && $totalEstimate) {
            $service = (string) $shipXml->EstimateDetail->Service->Code;
            $description = (string) $shipXml->EstimateDetail->ServiceLevelCommitment->Desc;
            if ($service == 'E') {
                foreach ($regexps as $expService => $exp) {
                    if (preg_match('/' . preg_quote($exp, '/') . '/', $description)) {
                        $service = $expService;
                    }
                }
            }

            $data['term'] = $services[$service] ?? $desc;
            $data['price_total'] = $this->getMethodPrice($totalEstimate, $service);
            $this->_dhlRates[] = ['service' => $service, 'data' => $data];
        }
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
        $this->_getXMLTracking($trackings);

        return $this->_result;
    }

    /**
     * Set tracking request
     */
    protected function setTrackingReqeust()
    {
        $request = new Varien_Object();

        $requestId = $this->getConfigData('id');
        $request->setId($requestId);

        $password = $this->getConfigData('password');
        $request->setPassword($password);

        $this->_rawTrackRequest = $request;
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
        $debugData = ['request' => $request];
        /*
         * tracking api cannot process from 3pm to 5pm PST time on Sunday
         * DHL Airborne conducts a maintenance during that period.
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
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $responseBody = '';
        }
        $this->_debug($debugData);
        $this->_parseXmlTrackingResponse($trackings, $responseBody);
    }

    /**
     * Parse xml tracking response
     *
     * @param array $trackings
     * @param string $response
     */
    protected function _parseXmlTrackingResponse($trackings, $response)
    {
        $errorTitle = Mage::helper('usa')->__('Unable to retrieve tracking');
        $resultArr = [];
        $errorArr = [];
        if (strlen(trim($response)) > 0) {
            if (str_starts_with(trim($response), '<?xml')) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    $trackxml = $xml->Track;
                    if (is_object($xml->Faults)
                        && is_object($xml->Faults->Fault)
                        && is_object($xml->Faults->Fault->Code)
                        && is_object($xml->Faults->Fault->Description)
                        && is_object($xml->Faults->Fault->Context)
                    ) {
                        $code = (string) $xml->Faults->Fault->Code;
                        $description = $xml->Faults->Fault->Description;
                        $context = $xml->Faults->Fault->Context;
                        $errorTitle = Mage::helper('usa')->__('Error #%s : %s (%s)', $code, $description, $context);
                    } elseif (is_object($trackxml) && is_object($trackxml->Shipment)) {
                        foreach ($trackxml->Shipment as $txml) {
                            $rArr = [];

                            if (is_object($txml)) {
                                $tracknum = (string) $txml->TrackingNbr;
                                if ($txml->Fault) {
                                    $code = (string) $txml->Fault->Code;
                                    $description = $txml->Fault->Description;
                                    $errorArr[$tracknum] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
                                } elseif ($txml->Result) {
                                    $code = (int) $txml->Result->Code;
                                    if ($code === 0) {
                                        /*
                                        * Code 0== airbill  found
                                        */
                                        $rArr['service'] = (string) $txml->Service->Desc;
                                        if (isset($txml->Weight)) {
                                            $rArr['weight'] = (string) $txml->Weight . ' lbs';
                                        }
                                        if (isset($txml->Delivery)) {
                                            $rArr['deliverydate'] = (string) $txml->Delivery->Date;
                                            $rArr['deliverytime'] = (string) $txml->Delivery->Time . ':00';
                                            $rArr['status'] = Mage::helper('usa')->__('Delivered');
                                            if (isset($txml->Delivery->Location->Desc)) {
                                                $rArr['deliverylocation'] = (string) $txml->Delivery->Location->Desc;
                                            }
                                        } elseif (isset($txml->Pickup)) {
                                            $rArr['deliverydate'] = (string) $txml->Pickup->Date;
                                            $rArr['deliverytime'] = (string) $txml->Pickup->Time . ':00';
                                            $rArr['status'] = Mage::helper('usa')->__('Shipment picked up');
                                        } else {
                                            $rArr['status'] = (string) $txml->ShipmentType->Desc
                                                  . Mage::helper('usa')->__(' was not delivered nor scanned');
                                        }

                                        $packageProgress = [];
                                        if (isset($txml->TrackingHistory) && isset($txml->TrackingHistory->Status)) {
                                            foreach ($txml->TrackingHistory->Status as $thistory) {
                                                $tempArr = [];
                                                $tempArr['activity'] = (string) $thistory->StatusDesc;
                                                $tempArr['deliverydate'] = (string) $thistory->Date; //YYYY-MM-DD
                                                $tempArr['deliverytime'] = (string) $thistory->Time . ':00'; //HH:MM:ss
                                                $addArr = [];
                                                if (isset($thistory->Location->City)) {
                                                    $addArr[] = (string) $thistory->Location->City;
                                                }
                                                if (isset($thistory->Location->State)) {
                                                    $addArr[] = (string) $thistory->Location->State;
                                                }
                                                if (isset($thistory->Location->CountryCode)) {
                                                    $addArr[] = (string) $thistory->Location->Country;
                                                }
                                                if ($addArr) {
                                                    $tempArr['deliverylocation'] = implode(', ', $addArr);
                                                } elseif (isset($thistory['final_delivery'])
                                                          && (string) $thistory['final_delivery'] === 'true'
                                                ) {
                                                    /*
                                                    * if the history is final delivery, there is no informationabout
                                                    * city, state and country
                                                    */
                                                    $addArr = [];
                                                    if (isset($txml->Receiver->City)) {
                                                        $addArr[] = (string) $txml->Receiver->City;
                                                    }
                                                    if (isset($thistory->Receiver->State)) {
                                                        $addArr[] = (string) $txml->Receiver->State;
                                                    }
                                                    if (isset($thistory->Receiver->CountryCode)) {
                                                        $addArr[] = (string) $txml->Receiver->Country;
                                                    }
                                                    $tempArr['deliverylocation'] = implode(', ', $addArr);
                                                }
                                                $packageProgress[] = $tempArr;
                                            }
                                            $rArr['progressdetail'] = $packageProgress;
                                        }
                                        $resultArr[$tracknum] = $rArr;
                                    } else {
                                        $description = (string) $txml->Result->Desc;
                                        if ($description) {
                                            $errorArr[$tracknum] = Mage::helper('usa')->__('Error #%s: %s', $code, $description);
                                        } else {
                                            $errorArr[$tracknum] = Mage::helper('usa')->__('Unable to retrieve tracking');
                                        }
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
        $arr = [];
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
        return [
            self::ADDITIONAL_PROTECTION_VALUE_CONFIG => Mage::helper('usa')->__('Configuration'),
            self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL => Mage::helper('usa')->__('Subtotal'),
            self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT => Mage::helper('usa')->__('Subtotal With Discount'),
        ];
    }

    /**
     * Get additional protection rounding types
     *
     * @return array
     */
    public function getAdditionalProtectionRoundingTypes()
    {
        return [
            self::ADDITIONAL_PROTECTION_ROUNDING_FLOOR => Mage::helper('usa')->__('To Lower'),
            self::ADDITIONAL_PROTECTION_ROUNDING_CEIL => Mage::helper('usa')->__('To Upper'),
            self::ADDITIONAL_PROTECTION_ROUNDING_ROUND => Mage::helper('usa')->__('Round'),
        ];
    }

    /**
     * Map request to shipment
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
     * @return array|bool
     */
    public function getContainerTypes(?Varien_Object $params = null)
    {
        return $this->getCode('shipment_type');
    }
}
