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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Admin configuration model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Config extends Varien_Simplexml_Config
{

    /**
     * Enter description here...
     *
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
     * Enter description here...
     *
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
     * Retrive tabs
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

    protected function _initSectionsAndTabs()
    {
        $mergeConfig = Mage::getModel('core/config_base');

        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }

                $configFile = $config->getModuleDir('etc', $modName).DS.'system.xml';

                if ($mergeConfig->loadFile($configFile)) {
                    $config->extend($mergeConfig, true);
                }
            }
        }
        #$config->applyExtends();
        $this->_sections = $config->getNode('sections');
        
        $this->_tabs = $config->getNode('tabs');
    }



    /**
     * Enter description here...
     *
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
     * Enter description here...
     *
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
        }elseif ($websiteCode) {
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
     * Enter description here...
     *
     * @param Varien_Simplexml_Element $sectionNode
     * @param Varien_Simplexml_Element $groupNode
     * @param Varien_Simplexml_Element $fieldNode
     * @return string
     */
    function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null)
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

}
