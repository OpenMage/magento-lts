<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Config data model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Config_Data _getResource()
 * @method Mage_Core_Model_Resource_Config_Data_Collection getCollection()
 * @method string getField()
 * @method false|SimpleXMLElement|Varien_Simplexml_Element getFieldConfig()
 *
 * @method int|string getGroupId()
 * @method string getPath()
 * @method Mage_Core_Model_Resource_Config_Data getResource()
 * @method Mage_Core_Model_Resource_Config_Data_Collection getResourceCollection()
 * @method string getScope()
 * @method int getScopeId()
 * @method string getStoreCode()
 * @method string getValue()
 * @method string getWebsiteCode()
 * @method $this setConfigId(string $value)
 * @method $this setField(string $value)
 * @method $this setFieldConfig(false|SimpleXMLElement|Varien_Simplexml_Element $value)
 * @method $this setFieldsetData(array $value)
 * @method $this setGroupId(int|string $value)
 * @method $this setGroups(array $value)
 * @method $this setPath(string $value)
 * @method $this setScope(string $value)
 * @method $this setScopeId(int $value)
 * @method $this setStoreCode(string $value)
 * @method $this setValue(string $value)
 * @method $this setWebsiteCode(string $value)
 * @method $this unsConfigId()
 * @method $this unsValue()
 */
class Mage_Core_Model_Config_Data extends Mage_Core_Model_Abstract
{
    public const ENTITY = 'core_config_data';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_config_data';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'config_data';

    /**
     * Varien model constructor
     */
    protected function _construct()
    {
        $this->_init('core/config_data');
    }

    /**
     * Add availability call after load as public
     * @return $this
     */
    public function afterLoad()
    {
        $this->_afterLoad();
        return $this;
    }

    /**
     * Check if config data value was changed
     *
     * @return bool
     */
    public function isValueChanged()
    {
        return $this->getValue() != $this->getOldValue();
    }

    /**
     * Get old value from existing config
     *
     * @return string
     */
    public function getOldValue()
    {
        $storeCode   = $this->getStoreCode();
        $websiteCode = $this->getWebsiteCode();
        $path        = $this->getPath();

        if ($storeCode) {
            return Mage::app()->getStore($storeCode)->getConfig($path);
        }

        if ($websiteCode) {
            return Mage::app()->getWebsite($websiteCode)->getConfig($path);
        }

        return (string) Mage::getConfig()->getNode('default/' . $path);
    }

    /**
     * Get value by key for new user data from <section>/groups/<group>/fields/<field>
     *
     * @param string $key
     * @return string
     */
    public function getFieldsetDataValue($key)
    {
        $data = $this->_getData('fieldset_data');
        return (is_array($data) && isset($data[$key])) ? $data[$key] : null;
    }
}
