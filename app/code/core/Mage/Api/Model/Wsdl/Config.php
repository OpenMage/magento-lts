<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Wsdl config model
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Wsdl_Config extends Mage_Api_Model_Wsdl_Config_Base
{
    protected static $_namespacesPrefix = null;

    /**
     * @inheritDoc
     */
    public function __construct($sourceData = null)
    {
        $this->setCacheId(Mage::helper('api')->getCacheId());
        parent::__construct($sourceData);
    }

    /**
     * Return wsdl content
     *
     * @return bool|string
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
            self::$_namespacesPrefix = [];
            $config = Mage::getSingleton('api/config')->getNode('v2/wsdl/prefix')->children();
            foreach ($config as $prefix => $namespace) {
                self::$_namespacesPrefix[$namespace->asArray()] = $prefix;
            }
        }

        return self::$_namespacesPrefix;
    }

    /**
     * @return Varien_Simplexml_Config_Cache_Abstract|Zend_Cache_Core
     */
    public function getCache()
    {
        return Mage::app()->getCache();
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }

    /**
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null|false|int $lifetime
     * @return bool|Mage_Core_Model_App
     */
    protected function _saveCache($data, $id, $tags = [], $lifetime = false)
    {
        return Mage::app()->saveCache($data, $id, $tags, $lifetime);
    }

    /**
     * @param string $id
     * @return Mage_Core_Model_App
     */
    protected function _removeCache($id)
    {
        return Mage::app()->removeCache($id);
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->setCacheChecksum(null);
        $saveCache = true;

        if (Mage::app()->useCache('config')) {
            $loaded = $this->loadCache();
            if ($loaded) {
                return $this;
            }
        }

        $mergeWsdl = new Mage_Api_Model_Wsdl_Config_Base();
        $mergeWsdl->setHandler($this->getHandler());

        if (Mage::helper('api/data')->isComplianceWSI()) {
            /**
             * Exclude Mage_Api wsdl xml file because it used for previous version
             * of API wsdl declaration
             */
            $mergeWsdl->addLoadedFile(Mage::getConfig()->getModuleDir('etc', 'Mage_Api') . DS . 'wsi.xml');

            $baseWsdlFile = Mage::getConfig()->getModuleDir('etc', 'Mage_Api') . DS . 'wsi.xml';
            $this->loadFile($baseWsdlFile);
            Mage::getConfig()->loadModulesConfiguration('wsi.xml', $this, $mergeWsdl);
        } else {
            /**
             * Exclude Mage_Api wsdl xml file because it used for previous version
             * of API wsdl declaration
             */
            $mergeWsdl->addLoadedFile(Mage::getConfig()->getModuleDir('etc', 'Mage_Api') . DS . 'wsdl.xml');

            $baseWsdlFile = Mage::getConfig()->getModuleDir('etc', 'Mage_Api') . DS . 'wsdl2.xml';
            $this->loadFile($baseWsdlFile);
            Mage::getConfig()->loadModulesConfiguration('wsdl.xml', $this, $mergeWsdl);
        }

        if (Mage::app()->useCache('config')) {
            $this->saveCache(['config']);
        }

        return $this;
    }

    /**
     * Return Xml of node as string
     *
     * @return bool|string
     */
    public function getXmlString()
    {
        return $this->getNode()->asXML();
    }
}
