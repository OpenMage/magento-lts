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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration data model
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_ConfigData extends Mage_Core_Model_Abstract
{
    /**
     * Default category
     */
    const DEFAULT_CATEGORY = 'default';

    /**
     * Configuration prefix
     */
    const CONFIG_PREFIX = 'app_';

    /**
     * Delete on update paths array
     *
     * @var array
     */
    protected $_deleteOnUpdate = array();

    /**
     * Configuration data
     *
     * @var array
     */
    protected $_configuration = array();

    /**
     * Initialize configuration data
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/configData');
    }

    /**
     * Create an array that will be stored in configuration data
     *
     * Create an array: application id with a prefix as key and
     * configuration data as value
     *
     * @param int $applicationId
     * @param array $configData
     * @return array
     */
    protected function _assignConfig($applicationId, $configData)
    {
        return array(self::CONFIG_PREFIX . $applicationId => $configData);
    }

    /**
     * Prepare posted data to store at configuration.
     *
     * Posted data have to be in predefined format:
     * - array('category:config/path/param' => 'value')
     * where : is a separator of category
     * - array('config/path/param' => 'value')
     * if key doesn't have a separator category will be set as default
     *
     * @param array $configuration posted data array
     * @return array configuration data array
     */
    protected function _prepareData($configuration)
    {
        $configData = array();
        foreach ($configuration as $key => $val) {
            list($category, $path) = explode(':', $key, 2) + array('', '');
            if (empty($path)) {
                $path = $category;
                $category = self::DEFAULT_CATEGORY;
            }
            $val = is_array($val) ? implode(',', $val) : $val;
            $configData[strtolower($category)][strtolower($path)] = $val;
        }
        return $configData;
    }

    /**
     * Prepare and set configuration data
     *
     * @param int $applicationId
     * @param array $configData
     * @param bool $replace
     * @return Mage_XmlConnect_Model_ConfigData
     */
    public function setConfigData($applicationId, array $configData, $replace = true)
    {
        $configData = $this->_prepareData($configData);
        $arrayToStore = $this->_assignConfig($applicationId, $configData);
        if ($replace) {
            $this->_configuration = array_merge($this->_configuration, $arrayToStore);
        } else {
            $this->_configuration = $this->_configuration + $arrayToStore;
        }
        return $this;
    }

    /**
     * Get configuration data
     *
     * @param int $applicationId
     * @return array
     */
    public function getConfigData($applicationId = 0)
    {
        if ($applicationId && isset($this->_configuration[self::CONFIG_PREFIX . $applicationId])) {
            return $this->_configuration[self::CONFIG_PREFIX . $applicationId];
        }
        return $this->_configuration;
    }

    /**
     * Save predefined configuration data
     *
     * @return Mage_XmlConnect_Model_ConfigData
     */
    public function initSaveConfig()
    {
        foreach ($this->_configuration as $application => $data) {
            $applicationId = str_ireplace(self::CONFIG_PREFIX, '', $application);
            $this->_deleteOnUpdate($applicationId);
            foreach ($data as $category => $config) {
                $this->saveConfig($applicationId, $config, $category);
            }
        }
        return $this;
    }

    /**
     * Save configuration data by given params
     *
     * @param int $applicationId
     * @param array $configData
     * @param string $category
     * @return Mage_XmlConnect_Model_ConfigData
     */
    public function saveConfig($applicationId, array $configData, $category = self::DEFAULT_CATEGORY)
    {
        foreach ($configData as $path => $value) {
            if (!is_scalar($value)) {
                Mage::throwException(Mage::helper('xmlconnect')->__('Unsupported value type received'));
            }
            $this->getResource()->saveConfig($applicationId, $category, $path, $value);
        }
        return $this;
    }

    /**
     * Get delete on update array paths
     *
     * @return array
     */
    public function getDeleteOnUpdate()
    {
        return $this->_deleteOnUpdate;
    }

    /**
     * Set delete on update array paths
     *
     * @param array $pathsToDelete
     * @return Mage_XmlConnect_Model_ConfigData
     */
    public function setDeleteOnUpdate(array $pathsToDelete)
    {
        $this->_deleteOnUpdate = array_merge($this->_deleteOnUpdate, $pathsToDelete);
        return $this;
    }

    /**
     * Delete group of records those have to be deleted with update process
     *
     * @param int $applicationId
     * @return Mage_XmlConnect_Model_ConfigData
     */
    protected function _deleteOnUpdate($applicationId)
    {
        foreach ($this->_deleteOnUpdate as $category => $path) {
            $this->deleteConfig($applicationId, $category, $path, true);
        }
        return $this;
    }

    /**
     * Delete config record
     *
     * @param int $applicationId
     * @param string $category
     * @param string $path
     * @param bool $pathLike
     * @return Mage_XmlConnect_Model_ConfigData
     */
    public function deleteConfig($applicationId, $category = '', $path = '', $pathLike = false)
    {
        $this->getResource()->deleteConfig($applicationId, $category, $path, $pathLike);
        return $this;
    }

    /**
     * Load Configuration data by filter params
     *
     * @param int $applicationId
     * @param string $category
     * @param string $path
     * @param bool $pathLike
     * @return array
     */
    public function loadApplicationData($applicationId, $category = '', $path = '', $pathLike = true)
    {
        /** @var $collection Mage_XmlConnect_Model_Resource_ConfigData_Collection */
        $collection = $this->getCollection();
        $collection->addApplicationIdFilter($applicationId);

        if ($category) {
            $collection->addCategoryFilter($category);
        }

        if ($path) {
            $collection->addPathFilter($path, $pathLike);
        }

        return $collection->toOptionArray();
    }

    /**
     * Load configuration node value
     *
     * @param int $applicationId
     * @param string $category
     * @param string $path
     * @return mixed
     */
    public function loadScalarValue($applicationId, $category, $path)
    {
        /** @var $collection Mage_XmlConnect_Model_Resource_ConfigData_Collection */
        $collection = $this->getCollection();
        $collection->addApplicationIdFilter($applicationId);

        if ($category) {
            $collection->addCategoryFilter($category);
        }

        if ($path) {
            $collection->addPathFilter($path, false);
        }

        return ($result = $collection->fetchItem()) ? $result->getValue() : null;
    }

    /**
     * Update old pages records in database
     * For data upgrade usage only
     *
     * @see data upgrade file: mysql4-data-upgrade-1.6.0.0-1.6.0.0.1.php
     * @param array $records
     * @return null
     */
    public function pagesUpgradeOldConfig($records)
    {
        $newConfig = array();
        /** @var $applicationModel Mage_XmlConnect_Model_Application */
        $applicationModel = Mage::getModel('xmlconnect/application');
        $deprecatedFlag = Mage_XmlConnect_Model_Application::DEPRECATED_CONFIG_FLAG;

        foreach ($records as $applicationId) {
            /** @var $applicationModel Mage_XmlConnect_Model_Application */
            $applicationModel->load($applicationId);
            $configData = $this->loadApplicationData($applicationId);

            foreach ($configData[$deprecatedFlag] as $deprecatedConfigKey => $deprecatedConfigValue) {
                $pagesConfigPath = 'native/pages/';
                if (strpos($deprecatedConfigKey, $pagesConfigPath) !== false) {
                    $pagePath = substr($deprecatedConfigKey, strlen($pagesConfigPath));
                    list($id, $type) = explode('/', $pagePath);
                    $newConfig[$id][$type] = $deprecatedConfigValue;

                    $this->deleteConfig($applicationId, $deprecatedFlag, $deprecatedConfigKey);
                }
            }

            foreach ($newConfig as $id => $page) {
                if (empty($page['label']) || empty($page['id'])) {
                    continue;
                }
                $path = 'staticpage/' . $id;

                $this->getResource()->saveConfig(
                    $applicationId, Mage_XmlConnect_Model_Application::STATIC_PAGE_CATEGORY, $path, serialize($page)
                );
            }
        }
    }
}
