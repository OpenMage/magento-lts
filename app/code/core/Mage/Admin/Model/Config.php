<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Configuration for Admin model
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Config extends Varien_Simplexml_Config
{
    /**
     * adminhtml.xml merged config
     *
     * @var Varien_Simplexml_Config
     */
    protected $_adminhtmlConfig;

    /**
     * Load config from merged adminhtml.xml files
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCacheId('adminhtml_acl_menu_config');

        $adminhtmlConfig = Mage::app()->loadCache($this->getCacheId());
        if ($adminhtmlConfig) {
            $this->_adminhtmlConfig = new Varien_Simplexml_Config($adminhtmlConfig);
        } else {
            $adminhtmlConfig = new Varien_Simplexml_Config();
            $adminhtmlConfig->loadString('<?xml version="1.0"?><config></config>');
            Mage::getConfig()->loadModulesConfiguration('adminhtml.xml', $adminhtmlConfig);
            $this->_adminhtmlConfig = $adminhtmlConfig;

            /**
             * @deprecated after 1.4.0.0-alpha2
             * support backwards compatibility with config.xml
             */
            $aclConfig  = Mage::getConfig()->getNode('adminhtml/acl');
            if ($aclConfig) {
                $adminhtmlConfig->getNode()->extendChild($aclConfig, true);
            }

            $menuConfig = Mage::getConfig()->getNode('adminhtml/menu');
            if ($menuConfig) {
                $adminhtmlConfig->getNode()->extendChild($menuConfig, true);
            }

            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(
                    $adminhtmlConfig->getXmlString(),
                    $this->getCacheId(),
                    [Mage_Core_Model_Config::CACHE_TAG],
                );
            }
        }
    }

    /**
     * Load Acl resources from config
     *
     * @param  Mage_Core_Model_Config_Element|Varien_Simplexml_Element $resource
     * @param  string                                                  $parentName
     * @return $this
     */
    public function loadAclResources(Mage_Admin_Model_Acl $acl, $resource = null, $parentName = null)
    {
        if (is_null($resource)) {
            $resource = $this->getAdminhtmlConfig()->getNode('acl/resources');
            $resourceName = null;
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName . '/') . $resource->getName();
            $acl->add(Mage::getModel('admin/acl_resource', $resourceName), $parentName);
        }

        if (isset($resource->all)) {
            $acl->add(Mage::getModel('admin/acl_resource', 'all'), null);
        }

        if (isset($resource->admin)) {
            $children = $resource->admin;
        } elseif (isset($resource->children)) {
            $children = $resource->children->children();
        }

        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            if ((string) $res->disabled === '1') {
                continue;
            }

            $this->loadAclResources($acl, $res, $resourceName);
        }

        return $this;
    }

    /**
     * Get acl assert config
     *
     * @param  string                                                                         $name
     * @return false|Mage_Core_Model_Config_Element|SimpleXMLElement|Varien_Simplexml_Element
     */
    public function getAclAssert($name = '')
    {
        $asserts = $this->getNode('admin/acl/asserts');
        if ($name === '') {
            return $asserts;
        }

        return $asserts->$name ?? false;
    }

    /**
     * Retrieve privilege set by name
     *
     * @param  string                                          $name
     * @return false|SimpleXMLElement|Varien_Simplexml_Element
     */
    public function getAclPrivilegeSet($name = '')
    {
        $sets = $this->getNode('admin/acl/privilegeSets');
        if ($name === '') {
            return $sets;
        }

        return $sets->$name ?? false;
    }

    /**
     * Retrieve xml config
     *
     * @return Varien_Simplexml_Config
     */
    public function getAdminhtmlConfig()
    {
        return $this->_adminhtmlConfig;
    }

    /**
     * Get menu item label by item path
     *
     * @param  string $path
     * @return string
     */
    public function getMenuItemLabel($path)
    {
        $moduleName = 'adminhtml';
        $menuNode = $this->getAdminhtmlConfig()->getNode('menu/' . str_replace('/', '/children/', trim($path, '/')));
        if ($menuNode->getAttribute('module')) {
            $moduleName = (string) $menuNode->getAttribute('module');
        }

        return Mage::helper($moduleName)->__((string) $menuNode->title);
    }
}
