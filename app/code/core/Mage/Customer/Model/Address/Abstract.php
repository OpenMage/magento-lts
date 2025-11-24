<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Address abstract model
 *
 * @package    Mage_Customer
 *
 * @method string getCity()
 * @method string getCountryId()
 * @method string getCustomerId()
 * @method string getFirstname()
 * @method bool getForceProcess()
 * @method bool getIsCustomerSaveTransaction()
 * @method bool getIsDefaultBilling()
 * @method bool getIsDefaultShipping()
 * @method bool getIsPrimaryBilling()
 * @method bool getIsPrimaryShipping()
 * @method string getLastname()
 * @method string getMiddlename()
 * @method int getParentId()
 * @method string getPostcode()
 * @method string getPrefix()
 * @method bool getShouldIgnoreValidation()
 * @method string getSuffix()
 * @method string getTelephone()
 * @method string getVatId()
 * @method int getVatIsValid()
 * @method string getVatRequestDate()
 * @method string getVatRequestId()
 * @method int getVatRequestSuccess()
 * @method $this setCity(string $value)
 * @method $this setCountryId(string $value)
 * @method $this setFirstname(string $value)
 * @method $this setForceProcess(bool $value)
 * @method $this setIsCustomerSaveTransaction(bool $value)
 * @method $this setIsDefaultBilling(bool $value)
 * @method $this setIsDefaultShipping(bool $value)
 * @method $this setIsPrimaryBilling(bool $value)
 * @method $this setIsPrimaryShipping(bool $value)
 * @method $this setLastname(string $value)
 * @method $this setMiddlename(string $value)
 * @method $this setParentId(int $value)
 * @method $this setPostcode(string $value)
 * @method $this setPrefix(string $value)
 * @method $this setRegion(string $value)
 * @method $this setStoreId(int $value)
 * @method $this setSuffix(string $value)
 * @method $this setTelephone(string $value)
 * @method $this unsRegion()
 */
class Mage_Customer_Model_Address_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Possible customer address types
     */
    public const TYPE_BILLING  = 'billing';

    public const TYPE_SHIPPING = 'shipping';

    /**
     * Prefix of model events
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_address';

    /**
     * Name of event object
     *
     * @var string
     */
    protected $_eventObject = 'customer_address';

    /**
     * List of errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Directory country models
     *
     * @var array
     */
    protected static $_countryModels = [];

    /**
     * Directory region models
     *
     * @var array
     */
    protected static $_regionModels = [];

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        $name = '';
        $config = Mage::getSingleton('eav/config');
        if ($config->getAttribute('customer_address', 'prefix')->getIsVisible() && $this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }

        $name .= $this->getFirstname();
        if ($config->getAttribute('customer_address', 'middlename')->getIsVisible() && $this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }

        $name .=  ' ' . $this->getLastname();
        if ($config->getAttribute('customer_address', 'suffix')->getIsVisible() && $this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }

        return $name;
    }

    /**
     * get address street
     *
     * @param   int $line address line index
     * @return  array|string
     */
    public function getStreet($line = 0)
    {
        $street = parent::getData('street');
        if ($line === -1) {
            return $street;
        } else {
            $arr = is_array($street) ? $street : explode("\n", (string) $street);
            if ($line === 0 || $line === null) {
                return $arr;
            } elseif (isset($arr[$line - 1])) {
                return $arr[$line - 1];
            } else {
                return '';
            }
        }
    }

    /**
     * @return string
     */
    public function getStreet1()
    {
        return $this->getStreet(1);
    }

    /**
     * @return string
     */
    public function getStreet2()
    {
        return $this->getStreet(2);
    }

    /**
     * @return string
     */
    public function getStreet3()
    {
        return $this->getStreet(3);
    }

    /**
     * @return string
     */
    public function getStreet4()
    {
        return $this->getStreet(4);
    }

    /**
     * @return string
     */
    public function getStreetFull()
    {
        return $this->getData('street');
    }

    /**
     * @param string $street
     * @return Mage_Customer_Model_Address_Abstract
     */
    public function setStreetFull($street)
    {
        return $this->setStreet($street);
    }

    /**
     * set address street
     *
     * @param array|string $street
     * @return $this
     */
    public function setStreet($street)
    {
        if (is_array($street)) {
            $street = trim(implode("\n", $street));
        }

        $this->setData('street', $street);
        return $this;
    }

    /**
     * Create fields street1, street2, etc.
     *
     * To be used in controllers for views data
     */
    public function explodeStreetAddress()
    {
        $streetLines = $this->getStreet();
        foreach ($streetLines as $i => $line) {
            $this->setData('street' . ($i + 1), $line);
        }

        return $this;
    }

    /**
     * To be used when processing _POST
     */
    public function implodeStreetAddress()
    {
        $this->setStreet($this->getData('street'));
        return $this;
    }

    /**
     * Retrieve region name
     *
     * @return string
     */
    public function getRegion()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');

        if ($regionId) {
            if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $region = $this->getRegionModel($regionId)->getName();
                $this->setData('region', $region);
            }
        }

        if (!empty($region) && is_string($region)) {
            $this->setData('region', $region);
        } elseif (!$regionId && is_numeric($region)) {
            if ($this->getRegionModel($region)->getCountryId() == $this->getCountryId()) {
                $this->setData('region', $this->getRegionModel($region)->getName());
                $this->setData('region_id', $region);
            }
        } elseif ($regionId && !$region) {
            if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $this->setData('region', $this->getRegionModel($regionId)->getName());
            }
        }

        return $this->getData('region');
    }

    /**
     * Return 2 letter state code if available, otherwise full region name
     */
    public function getRegionCode()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');

        if (!$regionId && is_numeric($region)) {
            if ($this->getRegionModel($region)->getCountryId() == $this->getCountryId()) {
                $this->setData('region_code', $this->getRegionModel($region)->getCode());
            }
        } elseif ($regionId) {
            if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $this->setData('region_code', $this->getRegionModel($regionId)->getCode());
            }
        } elseif (is_string($region)) {
            $this->setData('region_code', $region);
        }

        return $this->getData('region_code');
    }

    /**
     * @return int
     */
    public function getRegionId()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');
        if (!$regionId) {
            if (is_numeric($region)) {
                $this->setData('region_id', $region);
                $this->unsRegion();
            } else {
                $regionModel = Mage::getModel('directory/region')
                    ->loadByCode($this->getRegionCode(), $this->getCountryId());
                $this->setData('region_id', $regionModel->getId());
            }
        }

        return $this->getData('region_id');
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        /*if ($this->getData('country_id') && !$this->getData('country')) {
            $this->setData('country', Mage::getModel('directory/country')
                ->load($this->getData('country_id'))->getIso2Code());
        }
        return $this->getData('country');*/
        $country = $this->getCountryId();
        return $country ? $country : $this->getData('country');
    }

    /**
     * Retrieve country model
     *
     * @return Mage_Directory_Model_Country
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function getCountryModel()
    {
        if (!isset(self::$_countryModels[$this->getCountryId()])) {
            self::$_countryModels[$this->getCountryId()] = Mage::getModel('directory/country')
                ->load($this->getCountryId());
        }

        return self::$_countryModels[$this->getCountryId()];
    }

    /**
     * Retrieve country model
     *
     * @param null|int $region
     * @return Mage_Directory_Model_Country
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function getRegionModel($region = null)
    {
        if (is_null($region)) {
            $region = $this->getRegionId();
        }

        if (!isset(self::$_regionModels[$region])) {
            self::$_regionModels[$region] = Mage::getModel('directory/region')->load($region);
        }

        return self::$_regionModels[$region];
    }

    /**
     * @deprecated for public function format
     */
    public function getHtmlFormat()
    {
        return $this->getConfig()->getFormatByCode('html');
    }

    /**
     * @param bool $html
     * @return string
     * @deprecated for public function format
     */
    public function getFormated($html = false)
    {
        return $this->format($html ? 'html' : 'text');
        //Mage::getModel('directory/country')->load($this->getCountryId())->formatAddress($this, $html);
    }

    /**
     * @param string $type
     * @return null|string
     */
    public function format($type)
    {
        if (!($formatType = $this->getConfig()->getFormatByCode($type))
            || !$formatType->getRenderer()
        ) {
            return null;
        }

        Mage::dispatchEvent('customer_address_format', ['type' => $formatType, 'address' => $this]);
        return $formatType->getRenderer()->render($this);
    }

    /**
     * Retrieve address config object
     *
     * @return Mage_Customer_Model_Address_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('customer/address_config');
    }

    /**
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->getRegion();
        return $this;
    }

    /**
     * Validate address attribute values
     *
     * @return array|bool
     */
    public function validate()
    {
        $this->_resetErrors();

        $this->implodeStreetAddress();

        $this->_basicCheck();

        Mage::dispatchEvent('customer_address_validation_after', ['address' => $this]);

        $errors = $this->_getErrors();

        $this->_resetErrors();

        if (empty($errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }

        return $errors;
    }

    /**
     * Perform basic validation
     */
    protected function _basicCheck()
    {
        if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $this->addError(Mage::helper('customer')->__('Please enter the first name.'));
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $this->addError(Mage::helper('customer')->__('Please enter the last name.'));
        }

        if (!Zend_Validate::is($this->getStreet(1), 'NotEmpty')) {
            $this->addError(Mage::helper('customer')->__('Please enter the street.'));
        }

        if (!Zend_Validate::is($this->getCity(), 'NotEmpty')) {
            $this->addError(Mage::helper('customer')->__('Please enter the city.'));
        }

        if (!Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
            $this->addError(Mage::helper('customer')->__('Please enter the telephone number.'));
        }

        $havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
        if (!in_array($this->getCountryId(), $havingOptionalZip)
            && !Zend_Validate::is($this->getPostcode(), 'NotEmpty')
        ) {
            $this->addError(Mage::helper('customer')->__('Please enter the zip/postal code.'));
        }

        if (!Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
            $this->addError(Mage::helper('customer')->__('Please enter the country.'));
        }

        if ($this->getCountryModel()->getRegionCollection()->getSize()
            && !Zend_Validate::is($this->getRegionId(), 'NotEmpty')
            && Mage::helper('directory')->isRegionRequired($this->getCountryId())
        ) {
            $this->addError(Mage::helper('customer')->__('Please enter the state/province.'));
        }
    }

    /**
     * Add error
     *
     * @param string $error
     * @return $this
     */
    public function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Retrieve errors
     *
     * @return array
     */
    protected function _getErrors()
    {
        return $this->_errors;
    }

    /**
     * Reset errors array
     *
     * @return $this
     */
    protected function _resetErrors()
    {
        $this->_errors = [];
        return $this;
    }
}
