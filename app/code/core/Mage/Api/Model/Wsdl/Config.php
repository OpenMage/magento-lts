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
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wsdl config model
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Wsdl_Config extends Mage_Api_Model_Wsdl_Config_Base
{
    protected static $_namespacesPrefix = null;

    public function __construct($sourceData=null)
    {
        $this->setCacheId('wsdl_config_global');
        parent::__construct($sourceData);
    }

    /**
     * Return wsdl content
     *
     * @return string
     */
    public function getWsdlContent()
    {
        return $this->_xml->asXML();
    }

    /**
     * Return namespaces with their prefix
     *
     * @return array
     */
    public static function getNamespacesPrefix()
    {
        if (is_null(self::$_namespacesPrefix)) {
            self::$_namespacesPrefix = array();
            $config = Mage::getConfig()->getNode('api/v2/wsdl/prefix')->children();
            foreach ($config as $prefix => $namespace) {
                self::$_namespacesPrefix[$namespace->asArray()] = $prefix;
            }
        }
        return self::$_namespacesPrefix;
    }

    public function getCache()
    {
        return Mage::app()->getCache();
    }

    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }

    protected function _saveCache($data, $id, $tags=array(), $lifetime=false)
    {
        return Mage::app()->saveCache($data, $id, $tags, $lifetime);
    }

    protected function _removeCache($id)
    {
        return Mage::app()->removeCache($id);
    }

    public function init()
    {
        $this->setCacheChecksum(null);
        $saveCache = true;

        // check if local modules are disabled
        $disableLocalModules = (string)$this->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        if ($disableLocalModules) {
            /**
             * Reset include path
             */
            $codeDir = Mage::getConfig()->getOptions()->getCodeDir();
            $libDir = Mage::getConfig()->getOptions()->getLibDir();

            set_include_path(
                // excluded '/app/code/local'
                BP . DS . 'app' . DS . 'code' . DS . 'community' . PS .
                BP . DS . 'app' . DS . 'code' . DS . 'core' . PS .
                BP . DS . 'lib' . PS .
                Mage::registry('original_include_path')
            );
        }

        if (Mage::isInstalled()) {
            if (Mage::app()->useCache('config')) {
                $loaded = $this->loadCache();
                if ($loaded) {
                    return $this;
                }
            }
        }

        $mergeWsdl = new Mage_Api_Model_Wsdl_Config_Base();
        $mergeWsdl->setHandler($this->getHandler());

        $modules = Mage::getConfig()->getNode('modules')->children();

        $baseWsdlFile = Mage::getConfig()->getModuleDir('etc', "Mage_Api").DS.'wsdl2.xml';
        $this->loadFile($baseWsdlFile);

        foreach ($modules as $modName=>$module) {
            if ($module->is('active') && $modName != 'Mage_Api') {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }
                $wsdlFile = Mage::getConfig()->getModuleDir('etc', $modName).DS.'wsdl.xml';
                if ($mergeWsdl->loadFile($wsdlFile)) {
                    $this->extend($mergeWsdl, true);
                }
            }
        }

        if (Mage::app()->useCache('config')) {
            $this->saveCache(array('config'));
        }

        return $this;
    }

    /**
     * Return Xml of node as string
     *
     * @return string
     */
    public function getXmlString()
    {
        return $this->getNode()->asXML();
    }
}
