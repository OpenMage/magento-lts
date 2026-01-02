<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address helper
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Helper_Address extends Mage_Core_Helper_Abstract
{
    /**
     * VAT Validation parameters XML paths
     */
    public const XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT  = 'customer/create_account/viv_disable_auto_group_assign_default';

    public const XML_PATH_VIV_ON_EACH_TRANSACTION          = 'customer/create_account/viv_on_each_transaction';

    public const XML_PATH_VAT_VALIDATION_ENABLED           = 'customer/create_account/auto_group_assign';

    public const XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE = 'customer/create_account/tax_calculation_address_type';

    public const XML_PATH_VAT_FRONTEND_VISIBILITY          = 'customer/create_account/vat_frontend_visibility';

    protected $_moduleName = 'Mage_Customer';

    /**
     * Array of Customer Address Attributes
     *
     * @var null|array
     */
    protected $_attributes;

    /**
     * Customer address config node per website
     *
     * @var array
     */
    protected $_config          = [];

    /**
     * Customer Number of Lines in a Street Address per website
     *
     * @var array
     */
    protected $_streetLines     = [];

    protected $_formatTemplate  = [];

    /**
     * Addresses url
     */
    public function getBookUrl() {}

    public function getEditUrl() {}

    public function getDeleteUrl() {}

    public function getCreateUrl() {}

    /**
     * @param  mixed $renderer
     * @return mixed
     */
    public function getRenderer($renderer)
    {
        if (is_string($renderer) && $className = Mage::getConfig()->getBlockClassName($renderer)) {
            return new $className();
        }

        return $renderer;
    }

    /**
     * Return customer address config value by key and store
     *
     * @param  string                           $key
     * @param  int|Mage_Core_Model_Store|string $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $websiteId = Mage::app()->getStore($store)->getWebsiteId();

        if (!isset($this->_config[$websiteId])) {
            $this->_config[$websiteId] = Mage::getStoreConfig('customer/address', $store);
        }

        return isset($this->_config[$websiteId][$key]) ? (string) $this->_config[$websiteId][$key] : null;
    }

    /**
     * Return Number of Lines in a Street Address for store
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return int
     */
    public function getStreetLines($store = null)
    {
        $websiteId = Mage::app()->getStore($store)->getWebsiteId();
        if (!isset($this->_streetLines[$websiteId])) {
            /** @var Mage_Eav_Model_Attribute $attribute */
            $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'street');
            $lines = (int) $attribute->getMultilineCount();
            if ($lines <= 0) {
                $lines = 2;
            }

            $this->_streetLines[$websiteId] = min(20, $lines);
        }

        return $this->_streetLines[$websiteId];
    }

    /**
     * @param  string $code
     * @return string
     */
    public function getFormat($code)
    {
        $format = Mage::getSingleton('customer/address_config')->getFormatByCode($code);
        return $format->getRenderer() ? $format->getRenderer()->getFormat() : '';
    }

    /**
     * Determine if specified address config value can be shown
     *
     * @param  string $key
     * @return bool
     */
    public function canShowConfig($key)
    {
        return (bool) $this->getConfig($key);
    }

    /**
     * Return array of Customer Address Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = [];
            /** @var Mage_Eav_Model_Config $config */
            $config = Mage::getSingleton('eav/config');
            foreach ($config->getEntityAttributeCodes('customer_address') as $attributeCode) {
                $this->_attributes[$attributeCode] = $config->getAttribute('customer_address', $attributeCode);
            }
        }

        return $this->_attributes;
    }

    /**
     * Get string with frontend validation classes for attribute
     *
     * @param  string $attributeCode
     * @return string
     */
    public function getAttributeValidationClass($attributeCode)
    {
        /** @var Mage_Customer_Model_Attribute $attribute */
        $attribute = $this->_attributes[$attributeCode] ?? Mage::getSingleton('eav/config')->getAttribute('customer_address', $attributeCode);
        $class = $attribute ? $attribute->getFrontend()->getClass() : '';

        if (in_array($attributeCode, ['firstname', 'middlename', 'lastname', 'prefix', 'suffix', 'taxvat'])) {
            if ($class && !$attribute->getIsVisible()) {
                $class = ''; // address attribute is not visible thus its validation rules are not applied
            }

            /** @var Mage_Customer_Model_Attribute $customerAttribute */
            $customerAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
            $class = $customerAttribute && $customerAttribute->getIsVisible()
                ? $customerAttribute->getFrontend()->getClass() : '';
            $class = implode(' ', array_unique(array_filter(explode(' ', $class))));
        }

        return $class;
    }

    /**
     * Convert streets array to new street lines count
     * Examples of use:
     *  $origStreets = array('street1', 'street2', 'street3', 'street4')
     *  $toCount = 3
     *  Result:
     *   array('street1 street2', 'street3', 'street4')
     *  $toCount = 2
     *  Result:
     *   array('street1 street2', 'street3 street4')
     *
     * @param  array $origStreets
     * @param  int   $toCount
     * @return array
     */
    public function convertStreetLines($origStreets, $toCount)
    {
        $lines = [];
        if (!empty($origStreets) && $toCount > 0) {
            $countArgs = (int) floor(count($origStreets) / $toCount);
            $modulo = count($origStreets) % $toCount;
            $offset = 0;
            $neededLinesCount = 0;
            for ($i = 0; $i < $toCount; $i++) {
                $offset += $neededLinesCount;
                $neededLinesCount = $countArgs;
                if ($modulo > 0) {
                    ++$neededLinesCount;
                    --$modulo;
                }

                $values = array_slice($origStreets, $offset, $neededLinesCount);
                $lines[] = implode(' ', $values);
            }
        }

        return $lines;
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function isVatValidationEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_VAT_VALIDATION_ENABLED, $store);
    }

    /**
     * Retrieve disable auto group assign default value
     *
     * @return bool
     */
    public function getDisableAutoGroupAssignDefaultValue()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT);
    }

    /**
     * Retrieve 'validate on each transaction' value
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function getValidateOnEachTransaction($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_VIV_ON_EACH_TRANSACTION, $store);
    }

    /**
     * Retrieve customer address type on which tax calculation must be based
     *
     * @param  null|int|Mage_Core_Model_Store|string $store
     * @return string
     */
    public function getTaxCalculationAddressType($store = null)
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE, $store);
    }

    /**
     * Check if VAT ID address attribute has to be shown on frontend (on Customer Address management forms)
     *
     * @return bool
     */
    public function isVatAttributeVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_VAT_FRONTEND_VISIBILITY);
    }
}
