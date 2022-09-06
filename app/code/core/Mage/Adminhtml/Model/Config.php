<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin configuration model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Config extends Varien_Simplexml_Config
{
    /**
     * @var string
     */
    protected $_cacheId = 'mage_adminhtml_config_system_xml';

    /**
     * @var Mage_Core_Model_Config_Base
     */
    protected $_config;

    /**
     * @var Varien_Simplexml_Element
     */
    protected $_sections;

    /**
     * Tabs
     *
     * @var Varien_Simplexml_Element
     */
    protected $_tabs;

    /**
     * @param string $sectionCode
     * @param string $websiteCode
     * @param string $storeCode
     * @return Varien_Simplexml_Element
     */
    public function getSections($sectionCode=null, $websiteCode=null, $storeCode=null)
    {
        if (empty($this->_sections)) {
            $this->_initSectionsAndTabs();
        }

        return $this->_sections;
    }

    /**
     * Retrieve tabs
     *
     * @return Varien_Simplexml_Element
     */
    public function getTabs()
    {
        if (empty($this->_tabs)) {
            $this->_initSectionsAndTabs();
        }

        return $this->_tabs;
    }

    public function __construct()
    {
        $this->_cacheChecksum = null;
        $this->setCache(Mage::app()->getCache());
        $this->setCacheTags([Mage_Core_Model_Config::CACHE_TAG]);
        $usesCache = Mage::app()->useCache('config');
        if (!$usesCache || !$this->loadCache()) {
            $this->_config = Mage::getConfig()->loadModulesConfiguration('system.xml')
                ->applyExtends();
            if ($usesCache) {
                $this->saveCache();
            }
        }
    }

    /**
     * @param array|null $tags
     * @return $this|Mage_Adminhtml_Model_Config
     */
    public function saveCache($tags=null)
    {
        if ($this->getCacheSaved()) {
            return $this;
        }
        if (is_null($tags)) {
            $tags = $this->_cacheTags;
        }
        $xmlString = $this->_config->getXmlString();
        $this->_saveCache($xmlString, $this->getCacheId(), $tags, $this->getCacheLifetime());
        $this->setCacheSaved(true);
        return $this;
    }

    /**
     * @return bool
     */
    public function loadCache()
    {
        $xmlString = $this->_loadCache($this->getCacheId());
        $class = Mage::getConfig()->getModelClassName('core/config_base');
        $this->_config = new $class();
        libxml_use_internal_errors(true);
        if (!empty($xmlString) && $this->_config->loadString($xmlString)) {
            return true;
        }
        libxml_clear_errors();
        return false;
    }

    /**
     * Init modules configuration
     */
    protected function _initSectionsAndTabs()
    {
        $config = $this->_config;
        Mage::dispatchEvent('adminhtml_init_system_config', ['config' => $config]);
        $this->_sections = $config->getNode('sections');
        $this->_tabs = $config->getNode('tabs');
    }

    /**
     * @param string $sectionCode
     * @param string $websiteCode
     * @param string $storeCode
     * @return Varien_Simplexml_Element
     */
    public function getSection($sectionCode=null, $websiteCode=null, $storeCode=null)
    {
        if ($sectionCode){
            return  $this->getSections()->$sectionCode;
        } elseif ($websiteCode) {
            return  $this->getSections()->$websiteCode;
        } elseif ($storeCode) {
            return  $this->getSections()->$storeCode;
        }
    }

    /**
     * @param Varien_Simplexml_Element $node
     * @param string $websiteCode
     * @param string $storeCode
     * @param boolean $isField
     * @return boolean
     */
    public function hasChildren ($node, $websiteCode=null, $storeCode=null, $isField=false)
    {
        $showTab = false;
        if ($storeCode) {
            if (isset($node->show_in_store)) {
                if ((int)$node->show_in_store) {
                    $showTab=true;
                }
            }
        } elseif ($websiteCode) {
            if (isset($node->show_in_website)) {
                if ((int)$node->show_in_website) {
                    $showTab=true;
                }
            }
        } elseif (isset($node->show_in_default)) {
                if ((int)$node->show_in_default) {
                    $showTab=true;
                }
        }
        if ($showTab) {
            if (isset($node->groups)) {
                foreach ($node->groups->children() as $children){
                    if ($this->hasChildren ($children, $websiteCode, $storeCode)) {
                        return true;
                    }

                }
            }elseif (isset($node->fields)) {

                foreach ($node->fields->children() as $children){
                    if ($this->hasChildren ($children, $websiteCode, $storeCode, true)) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Get translate module name
     *
     * @param Varien_Simplexml_Element $sectionNode
     * @param Varien_Simplexml_Element $groupNode
     * @param Varien_Simplexml_Element $fieldNode
     * @return string
     */
    public function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null)
    {
        $moduleName = 'adminhtml';
        if (is_object($sectionNode) && method_exists($sectionNode, 'attributes')) {
            $sectionAttributes = $sectionNode->attributes();
            $moduleName = isset($sectionAttributes['module']) ? (string)$sectionAttributes['module'] : $moduleName;
        }
        if (is_object($groupNode) && method_exists($groupNode, 'attributes')) {
            $groupAttributes = $groupNode->attributes();
            $moduleName = isset($groupAttributes['module']) ? (string)$groupAttributes['module'] : $moduleName;
        }
        if (is_object($fieldNode) && method_exists($fieldNode, 'attributes')) {
            $fieldAttributes = $fieldNode->attributes();
            $moduleName = isset($fieldAttributes['module']) ? (string)$fieldAttributes['module'] : $moduleName;
        }

        return $moduleName;
    }

    /**
     * System configuration section, fieldset or field label getter
     *
     * @param string $sectionName
     * @param string $groupName
     * @param string $fieldName
     * @return string
     */
    public function getSystemConfigNodeLabel($sectionName, $groupName = null, $fieldName = null)
    {
        $sectionName = trim($sectionName, '/');
        $path = '//sections/' . $sectionName;
        $groupNode = $fieldNode = null;
        $sectionNode = $this->_sections->xpath($path);
        if (!empty($groupName)) {
            $path .= '/groups/' . trim($groupName, '/');
            $groupNode = $this->_sections->xpath($path);
        }
        if (!empty($fieldName)) {
            if (!empty($groupName)) {
                $path .= '/fields/' . trim($fieldName, '/');
                $fieldNode = $this->_sections->xpath($path);
            }
            else {
                Mage::throwException(Mage::helper('adminhtml')->__('The group node name must be specified with field node name.'));
            }
        }
        $moduleName = $this->getAttributeModule($sectionNode, $groupNode, $fieldNode);
        $systemNode = $this->_sections->xpath($path);
        foreach ($systemNode as $node) {
            return Mage::helper($moduleName)->__((string)$node->label);
        }
        return '';
    }

    /**
     * Look for encrypted node entries in all system.xml files and return them
     *
     * @return array $paths
     */
    public function getEncryptedNodeEntriesPaths($explodePathToEntities = false)
    {
        $paths = [];
        $configSections = $this->getSections();
        if ($configSections) {
            foreach ($configSections->xpath('//sections/*/groups/*/fields/*/backend_model') as $node) {
                if ((string)$node === 'adminhtml/system_config_backend_encrypted') {
                    $section = $node->getParent()->getParent()->getParent()->getParent()->getParent()->getName();
                    $group   = $node->getParent()->getParent()->getParent()->getName();
                    $field   = $node->getParent()->getName();
                    if ($explodePathToEntities) {
                        $paths[] = ['section' => $section, 'group' => $group, 'field' => $field];
                    }
                    else {
                        $paths[] = $section . '/' . $group . '/' . $field;
                    }
                }
            }
        }
        return $paths;
    }
}
