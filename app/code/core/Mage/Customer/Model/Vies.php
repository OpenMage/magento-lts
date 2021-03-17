<?php
/**
 * OpenMage
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009-2021 OpenMage (https://www.openmage.org/)
 */
class Mage_Customer_Model_Vies extends Varien_Object
{
    /**
     * WSDL of VAT validation service
     */
    const VAT_VALIDATION_WSDL_URL = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Config paths to VAT related customer groups
     */
    const XML_PATH_CUSTOMER_VIV_VAT_NUMBER_FORMAT = 'customer/create_account/viv_vat_number_format';
    const XML_PATH_CUSTOMER_VIV_FOR_NORTHERN_IRELAND = 'customer/create_account/viv_for_northern_ireland';

    /**
     * Flag for Northern Ireland VAT number check
     *
     * @var boolean
     */
    protected $checkNorthernIreland;

    /**
     * Send request to VAT validation service and return validation result
     *
     * @return Varien_Object
     */
    public function checkVatNumber()
    {
        // Default response
        $gatewayResponse = new Varien_Object(array(
            'is_valid' => false,
            'request_date' => '',
            'request_identifier' => '',
            'request_success' => false
        ));

        if (!extension_loaded('soap')) {
            Mage::logException(Mage::exception(
                'Mage_Core',
                Mage::helper('core')->__('PHP SOAP extension is required.')
            ));
            return $gatewayResponse;
        }

        if (empty($this->getCountryCode())) {
            return $gatewayResponse;
        }

        if (empty($this->getVatNumber())) {
            return $gatewayResponse;
        }

        if ($this->getValidationType() == 1 // With country code
            && !$this->vatNumberContainsCountryCode($this->getVatNumber(), $this->getCountryCode())
        ) {
            // No valid vat number because not the right countrycode is used
            return $gatewayResponse;
        }

        if (!$this->canCheckVatNumber()) {
            return $gatewayResponse;
        }

        try {
            $soapClient = $this->createVatNumberValidationSoapClient();

            $requestParams = array();
            $requestParams['countryCode'] = $this->getViesCountryCode();
            $requestParams['vatNumber'] = $this->getViesVatNumber();
            $requestParams['requesterCountryCode'] = $this->getViesRequesterCountryCode();
            $requestParams['requesterVatNumber'] = $this->getViesRequesterVatNumber();

            // Send request to service
            $result = $soapClient->checkVatApprox($requestParams);

            $gatewayResponse->setIsValid((boolean) $result->valid);
            $gatewayResponse->setRequestDate((string) $result->requestDate);
            $gatewayResponse->setRequestIdentifier((string) $result->requestIdentifier);
            $gatewayResponse->setRequestSuccess(true);
        } catch (Exception $exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestDate('');
            $gatewayResponse->setRequestIdentifier('');
        }

        return $gatewayResponse;
    }

    /**
     * Check if vat number should be validated
     *
     * @return boolean
     */
    public function shouldValidateVatNumber()
    {
        if (Mage::helper('core')->isCountryInEU($this->getCountryCode())) {
            return true;
        }

        if ($this->getCountryCode() == 'GB'
            && $this->validNorthernIrelandPostcode($this->getPostcode())
            && $this->checkVatNumberForNorthernIreland()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Enable / disable vat validation for Northern Ireland
     *
     * @param boolean $flag
     * @return $this
     */
    public function enableVatNumberCheckForNorthernIreland($flag = true)
    {
        $this->checkNorthernIreland = $flag;

        return $this;
    }

    /**
     * Set vat number to validate
     *
     * @param string $vatNumber
     * @return $this
     */
    public function setVatNumber($vatNumber)
    {
        return $this->setData('vat_number', str_replace(array(' ', '-'), array('', ''), $vatNumber));
    }

    /**
     * Set requester vat number
     *
     * @param string $vatNumber
     * @return $this
     */
    public function setRequesterVatNumber($vatNumber)
    {
        return $this->setData('requester_vat_number', str_replace(array(' ', '-'), array('', ''), $vatNumber));
    }

    /**
     * Get requester country code
     *
     * @return string
     */
    public function getRequesterCountryCode()
    {
        $countryCode = $this->getData('requester_country_code');
        if (is_null($countryCode)) {
            return '';
        }

        return (string) $countryCode;
    }

    /**
     * Get requester vat number
     *
     * @return string
     */
    public function getRequesterVatNumber()
    {
        $vatNumber = $this->getData('requester_vat_number');
        if (is_null($vatNumber)) {
            return '';
        }

        return (string) $vatNumber;
    }

    /**
     * Check if postcode is valid for Northern Ireland
     *
     * @param  string $postcode
     * @return boolean
     */
    protected function validNorthernIrelandPostcode($postcode)
    {
        if (empty($postcode)) {
            return false;
        }

        preg_match("/^BT\d{1,2}\s?[0-9][A-Z]{2}$/i", trim($postcode), $matches);

        return is_array($matches) && count($matches);
    }

    /**
     * Check if vat number contains country code
     *
     * @param  string $vatNumber
     * @param  string $countryCode
     * @return boolean
     */
    protected function vatNumberContainsCountryCode($vatNumber, $countryCode)
    {
        $vatNumberCountryCode = substr(strtoupper($vatNumber), 0, 2);
        if ($vatNumberCountryCode == 'XI') {
            $vatNumberCountryCode = 'GB';
        } elseif($vatNumberCountryCode == 'EL') {
            $vatNumberCountryCode = 'GR';
        }

        return $vatNumberCountryCode === $countryCode;
    }

    /**
     * Check if vat validation for Northern Ireland is enabled
     *
     * @return boolean
     */
    protected function checkVatNumberForNorthernIreland()
    {
        if (!is_bool($this->checkNorthernIreland)) {
            $this->checkNorthernIreland = (bool) (int) Mage::getStoreConfig(
                self::XML_PATH_CUSTOMER_VIV_FOR_NORTHERN_IRELAND, $this->getStore()
            );
        }

        return $this->checkNorthernIreland;
    }

    /**
     * Get vat validation type
     *
     * @return int
     */
    protected function getValidationType()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_CUSTOMER_VIV_VAT_NUMBER_FORMAT, $this->getStore());
    }

    /**
     * Get formatted vat number for vies check
     *
     * @return string
     */
    protected function getViesVatNumber()
    {
        // Remove country code from vat number to make vies check work
        if (in_array($this->getValidationType(), [1,2])
            && $this->vatNumberContainsCountryCode($this->getVatNumber(), $this->getCountryCode())
        ) {
            return substr($this->getVatNumber(), strlen($this->getCountryCode()));
        }

        return (string) $this->getVatNumber();
    }

    /**
     * Get formatted vat number for vies check
     *
     * @return string
     */
    protected function getViesRequesterVatNumber()
    {
        // Remove country code from vat number to make vies check work
        if ($this->vatNumberContainsCountryCode($this->getRequesterVatNumber(), $this->getRequesterCountryCode())) {
            return substr($this->getRequesterVatNumber(), strlen($this->getRequesterCountryCode()));
        }

        return (string) $this->getRequesterVatNumber();
    }

    /**
     * Get country code for vies check
     *
     * @return string
     */
    protected function getViesCountryCode()
    {
        // Fix for Greece
        if($this->getCountryCode() == 'GR') {
            return 'EL';
        }

        // Fix for Northern Ireland
        if ($this->getCountryCode() == 'GB'
            && $this->validNorthernIrelandPostcode($this->getPostcode())
            && $this->checkVatNumberForNorthernIreland()
        ) {
            return 'XI';
        }

        return (string) $this->getCountryCode();
    }

    /**
     * Get vies version of requester country code
     *
     * @return string
     */
    protected function getViesRequesterCountryCode()
    {
        if ($this->getRequesterCountryCode() == 'GB'
            && $this->validNorthernIrelandPostcode($this->getRequesterPostcode())
        ) {
            return 'XI';
        } elseif($this->getRequesterCountryCode() == 'GR') {
            return 'EL';
        }

        return (string) $this->getRequesterCountryCode();
    }

    /**
     * Check if parameters are valid to send to VAT validation service
     *
     * @return boolean
     */
    protected function canCheckVatNumber()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('core');

        if (!is_string($this->getCountryCode())
            || !is_string($this->getVatNumber())
            || !is_string($this->getRequesterCountryCode())
            || !is_string($this->getRequesterVatNumber())
            || empty($this->getCountryCode())
            || !(
                    $coreHelper->isCountryInEU($this->getCountryCode(), $this->getStore())
                    || $this->getViesCountryCode() == 'XI' // Northern Ireland
                )
            || empty($this->getVatNumber())
            || (empty($this->getRequesterCountryCode()) && !empty($this->getRequesterVatNumber()))
            || (!empty($this->getRequesterCountryCode()) && empty($this->getRequesterVatNumber()))
            || (
                !empty($this->getRequesterCountryCode())
                && !(
                    $coreHelper->isCountryInEU($this->getRequesterCountryCode(), $this->getStore())
                    || $this->getViesRequesterCountryCode() == 'XI' // Northern Ireland
                )
            )
        ) {
            return false;
        }

        return true;;
    }

    /**
     * Create SOAP client based on VAT validation service WSDL
     *
     * @param boolean $trace
     * @return SoapClient
     */
    protected function createVatNumberValidationSoapClient($trace = false)
    {
        return new SoapClient(self::VAT_VALIDATION_WSDL_URL, array('trace' => $trace));
    }
}
