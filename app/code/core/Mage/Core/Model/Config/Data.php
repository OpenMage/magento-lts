<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config data model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Core_Model_Resource_Config_Data _getResource()
 * @method Mage_Core_Model_Resource_Config_Data getResource()
 * @method Mage_Core_Model_Resource_Config_Data_Collection getCollection()
 * @method $this setConfigId(string $value)
 * @method $this unsConfigId()
 * @method string getField()
 * @method $this setField(string $value)
 * @method false|SimpleXMLElement|Varien_Simplexml_Element getFieldConfig()
 * @method $this setFieldConfig(false|SimpleXMLElement|Varien_Simplexml_Element $value)
 * @method $this setFieldsetData(array $value)
 * @method int|string getGroupId()
 * @method $this setGroupId(int|string $value)
 * @method $this setGroups(array $value)
 * @method string getPath()
 * @method $this setPath(string $value)
 * @method string getScope()
 * @method $this setScope(string $value)
 * @method int getScopeId()
 * @method $this setScopeId(int $value)
 * @method string getStoreCode()
 * @method $this setStoreCode(string $value)
 * @method string getValue()
 * @method $this setValue(string $value)
 * @method $this unsValue()
 * @method string getWebsiteCode()
 * @method $this setWebsiteCode(string $value)
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
