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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application configuration model
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Configuration extends Mage_Core_Model_Abstract
{
    /**
     * Admin application config flag for is active param
     */
    const CONFIG_PATH_AA_SETTINGS = 'xmlconnect/admin_app/settings';

    /**
     * Core config data collection
     *
     * @var Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected $_configDataCollection;

    /**
     * XmlConnect data collection
     *
     * @var Mage_XmlConnect_Model_Resource_ConfigData_Collection
     */
    protected $_configConnectDataCollection;

    /**
     * Core config model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configDataModel;

    /**
     * Admin application settings
     *
     * @var array
     */
    protected $_adminApplicationSettings;

    /**
     * Application model
     *
     * @var Mage_XmlConnect_Model_Application
     */
    protected $_applicationModel;

    /**
     * Get is active admin application flag
     *
     * @return bool
     */
    public function isActiveAdminApp()
    {
        $isActiveSetting = $this->_getAdminApplicationSettings(
            Mage_XmlConnect_Model_Configuration::CONFIG_PATH_AA_SETTINGS . '/is_active'
        );
        return $isActiveSetting ? (bool)$isActiveSetting['value'] : false;
    }

    /**
     * Save is active admin application param
     *
     * @param int $isActive
     * @return Mage_XmlConnect_Model_Configuration
     */
    public function saveIsActiveAdminApp($isActive)
    {
        $this->_getConfigDataModel()->saveConfig(
            Mage_XmlConnect_Model_Configuration::CONFIG_PATH_AA_SETTINGS . '/is_active', (int)$isActive
        );
        return $this;
    }

    /**
     * Get admin application settings data
     *
     * @param null|string $path to config node
     * @return array
     */
    protected function _getAdminApplicationSettings($path = null)
    {
        if (null === $this->_adminApplicationSettings) {
            $adminApplicationSettings = $this->_getConfigDataCollection()
                    ->addPathFilter(self::CONFIG_PATH_AA_SETTINGS)
                    ->getData();

            foreach ($adminApplicationSettings as $setting) {
                $this->_adminApplicationSettings[$setting['path']] = $setting;
            }
        }

        if ($path !== null && isset($this->_adminApplicationSettings[$path])) {
            return $this->_adminApplicationSettings[$path];
        } else {
            return $this->_adminApplicationSettings;
        }
    }

    /**
     * Get core config data collection
     *
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getConfigDataCollection()
    {
        if (null === $this->_configDataCollection) {
            $this->_configDataCollection = Mage::getModel('core/resource_config_data_collection');
        } else {
            $this->_configDataCollection->clear()->getSelect()->reset();
        }
        return $this->_configDataCollection;
    }

    /**
     * Get core config data model
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getConfigDataModel()
    {
        if (null === $this->_configDataModel) {
            $this->_configDataModel = Mage::getModel('core/config');
        }
        return $this->_configDataModel;
    }

    /**
     * Get device static pages
     *
     * @return array
     */
    public function getDeviceStaticPages()
    {
        return $this->_getConfigDataByCategory(Mage_XmlConnect_Model_Application::STATIC_PAGE_CATEGORY, true);
    }

    /**
     * Get previous localization hash from storage
     *
     * @return string|null
     */
    public function getPreviousLocalizationHash()
    {
        $localizationHashSetting = $this->_getAdminApplicationSettings(
            Mage_XmlConnect_Model_Configuration::CONFIG_PATH_AA_SETTINGS . '/localization_hash'
        );
        return $localizationHashSetting ? $localizationHashSetting['value'] : null;

    }

    /**
     * Save localization hash in configuration storage
     *
     * @param string $hash
     * @return Mage_XmlConnect_Model_Configuration
     */
    public function setPreviousLocalizationHash($hash)
    {
        $this->_getConfigDataModel()->saveConfig(
            Mage_XmlConnect_Model_Configuration::CONFIG_PATH_AA_SETTINGS . '/localization_hash', $hash
        );
        return $this;
    }

    /**
     * Get xmlconnect saved config data by category
     *
     * @param string $category
     * @param bool $unSerialize
     * @return array
     */
    protected function _getConfigDataByCategory($category = null, $unSerialize = false)
    {
        $configuration = $this->_getConnectConfigDataCollection()->addArrayFilter(array(
            'application_id' => $this->getApplicationModel()->getId(),
            'category' => $category,
        ))->toOptionArray();
        if (!empty($configuration)) {
            return $this->_formatConfigData($configuration[$category], $unSerialize, $category);
        } else {
            return array();
        }
    }

    /**
     * Format config data
     *
     * @param array $configuration
     * @param bool $unSerialize
     * @return array
     */
    protected function _formatConfigData($configuration, $unSerialize = false)
    {
        $result = array();
        foreach ($configuration as $node => $values) {
            if ($unSerialize) {
                $values = unserialize($values);
            }
            $result[$node] = $values;
        }
        return $result;
    }

    /**
     * Get xmlconnect config data collection
     *
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getConnectConfigDataCollection()
    {
        if (null === $this->_configConnectDataCollection) {
            $this->_configConnectDataCollection = Mage::getModel('xmlconnect/resource_configData_collection');
        } else {
            $this->_configConnectDataCollection->clear()->getSelect()->reset(Zend_Db_Select::WHERE);
        }
        return $this->_configConnectDataCollection;
    }

    /**
     * Get application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    public function getApplicationModel()
    {
        if (null === $this->_applicationModel) {
            $this->setApplicationModel();
        }
        return $this->_applicationModel;
    }

    /**
     * Set application model
     *
     * @return Mage_XmlConnect_Model_Configuration
     */
    public function setApplicationModel()
    {
        $this->_applicationModel = Mage::helper('xmlconnect')->getApplication();
        return $this;
    }
}
