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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml config data model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Model_Config_Data extends Varien_Object
{
    /**
     * Config data for sections
     *
     * @var array
     */
    protected $_configData;

    /**
     * Root config node
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_configRoot;

    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @return Mage_Adminhtml_Model_Config_Data
     */
    public function save()
    {
        $this->_validate();
        $this->_getScope();

        Mage::dispatchEvent('model_config_data_save_before', array('object' => $this));

        $section = $this->getSection();
        $website = $this->getWebsite();
        $store   = $this->getStore();
        $groups  = $this->getGroups();
        $scope   = $this->getScope();
        $scopeId = $this->getScopeId();

        if (empty($groups)) {
            return $this;
        }

        $sections = Mage::getModel('adminhtml/config')->getSections();
        /* @var $sections Mage_Core_Model_Config_Element */

        $oldConfig = $this->_getConfig(true);

        $deleteTransaction = Mage::getModel('core/resource_transaction');
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = Mage::getModel('core/resource_transaction');
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

        // Extends for old config data
        $oldConfigAdditionalGroups = array();

        foreach ($groups as $group => $groupData) {
            /**
             * Map field names if they were cloned
             */
            $groupConfig = $sections->descend($section.'/groups/'.$group);

            if ($clonedFields = !empty($groupConfig->clone_fields)) {
                if ($groupConfig->clone_model) {
                    $cloneModel = Mage::getModel((string)$groupConfig->clone_model);
                } else {
                    Mage::throwException('Config form fieldset clone model required to be able to clone fields');
                }
                $mappedFields = array();
                $fieldsConfig = $sections->descend($section.'/groups/'.$group.'/fields');

                if ($fieldsConfig->hasChildren()) {
                    foreach ($fieldsConfig->children() as $field => $node) {
                        foreach ($cloneModel->getPrefixes() as $prefix) {
                            $mappedFields[$prefix['field'].(string)$field] = (string)$field;
                        }
                    }
                }
            }
            // set value for group field entry by fieldname
            // use extra memory
            $fieldsetData = array();
            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldsetData[$field] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/' . $field);
                if (!$fieldConfig && $clonedFields && isset($mappedFields[$field])) {
                    $fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/'
                        . $mappedFields[$field]);
                }
                if (!$fieldConfig) {
                    $node = $sections->xpath($section .'//' . $group . '[@type="group"]/fields/' . $field);
                    if ($node) {
                        $fieldConfig = $node[0];
                    }
                }

                /**
                 * Get field backend model
                 */
                $backendClass = (isset($fieldConfig->backend_model))? $fieldConfig->backend_model : false;
                if (!$backendClass) {
                    $backendClass = 'core/config_data';
                }

                /** @var $dataObject Mage_Core_Model_Config_Data */
                $dataObject = Mage::getModel($backendClass);
                if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
                    Mage::throwException('Invalid config field backend model: '.$backendClass);
                }

                $dataObject
                    ->setField($field)
                    ->setGroups($groups)
                    ->setGroupId($group)
                    ->setStoreCode($store)
                    ->setWebsiteCode($website)
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setFieldConfig($fieldConfig)
                    ->setFieldsetData($fieldsetData)
                ;

                if (!isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }

                $path = $section . '/' . $group . '/' . $field;

                /**
                 * Look for custom defined field path
                 */
                if (is_object($fieldConfig)) {
                    $configPath = (string)$fieldConfig->config_path;
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $groupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($oldConfigAdditionalGroups[$groupPath])) {
                            $oldConfig = $this->extendConfig($groupPath, true, $oldConfig);
                            $oldConfigAdditionalGroups[$groupPath] = true;
                        }
                        $path = $configPath;
                    }
                }

                $inherit = !empty($fieldData['inherit']);

                $dataObject->setPath($path)
                    ->setValue($fieldData['value']);

                if (isset($oldConfig[$path])) {
                    $dataObject->setConfigId($oldConfig[$path]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($dataObject);
                    }
                    else {
                        $deleteTransaction->addObject($dataObject);
                    }
                }
                elseif (!$inherit) {
                    $dataObject->unsConfigId();
                    $saveTransaction->addObject($dataObject);
                }
            }
        }

        $deleteTransaction->delete();
        $saveTransaction->save();

        return $this;
    }

    /**
     * Load config data for section
     *
     * @return array
     */
    public function load()
    {
        if (is_null($this->_configData)) {
            $this->_validate();
            $this->_getScope();
            $this->_configData = $this->_getConfig(false);
        }
        return $this->_configData;
    }

    /**
     * Extend config data with additional config data by specified path
     *
     * @param string $path Config path prefix
     * @param bool $full Simple config structure or not
     * @param array $oldConfig Config data to extend
     * @return array
     */
    public function extendConfig($path, $full = true, $oldConfig = array())
    {
        $extended = $this->_getPathConfig($path, $full);
        if (is_array($oldConfig) && !empty($oldConfig)) {
            return $oldConfig + $extended;
        }
        return $extended;
    }

    /**
     * Validate isset required parametrs
     *
     */
    protected function _validate()
    {
        if (is_null($this->getSection())) {
            $this->setSection('');
        }
        if (is_null($this->getWebsite())) {
            $this->setWebsite('');
        }
        if (is_null($this->getStore())) {
            $this->setStore('');
        }
    }

    /**
     * Get scope name and scopeId
     *
     */
    protected function _getScope()
    {
        if ($this->getStore()) {
            $scope   = 'stores';
            $scopeId = (int)Mage::getConfig()->getNode('stores/' . $this->getStore() . '/system/store/id');
            $scopeCode = $this->getStore();
        } elseif ($this->getWebsite()) {
            $scope   = 'websites';
            $scopeId = (int)Mage::getConfig()->getNode('websites/' . $this->getWebsite() . '/system/website/id');
            $scopeCode = $this->getWebsite();
        } else {
            $scope   = 'default';
            $scopeId = 0;
            $scopeCode = '';
        }
        $this->setScope($scope);
        $this->setScopeId($scopeId);
        $this->setScopeCode($scopeCode);
    }

    /**
     * Return formatted config data for current section
     *
     * @param bool $full Simple config structure or not
     * @return array
     */
    protected function _getConfig($full = true)
    {
        return $this->_getPathConfig($this->getSection(), $full);
    }

    /**
     * Return formatted config data for specified path prefix
     *
     * @param string $path Config path prefix
     * @param bool $full Simple config structure or not
     * @return array
     */
    protected function _getPathConfig($path, $full = true)
    {
        $configDataCollection = Mage::getModel('core/config_data')
            ->getCollection()
            ->addScopeFilter($this->getScope(), $this->getScopeId(), $path);

        $config = array();
        foreach ($configDataCollection as $data) {
            if ($full) {
                $config[$data->getPath()] = array(
                    'path'      => $data->getPath(),
                    'value'     => $data->getValue(),
                    'config_id' => $data->getConfigId()
                );
            }
            else {
                $config[$data->getPath()] = $data->getValue();
            }
        }
        return $config;
    }

    /**
     * Get config data value
     *
     * @param string $path
     * @param null|bool $inherit
     * @param null|array $configData
     * @return Varien_Simplexml_Element
     */
    public function getConfigDataValue($path, &$inherit = null, $configData = null)
    {
        $this->load();
        if (is_null($configData)) {
            $configData = $this->_configData;
        }
        if (array_key_exists($path, $configData)) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = $this->getConfigRoot()->descend($path);
            $inherit = true;
        }

        return $data;
    }

    /**
     * Get config root node for current scope
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfigRoot()
    {
        if (is_null($this->_configRoot)) {
            $this->load();
            $this->_configRoot = Mage::getConfig()->getNode(null, $this->getScope(), $this->getScopeCode());
        }
        return $this->_configRoot;
    }
}
