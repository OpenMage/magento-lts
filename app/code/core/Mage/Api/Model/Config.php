<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice api config model
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Config extends Varien_Simplexml_Config
{
    public const CACHE_TAG         = 'config_api';

    /**
     * @inheritDoc
     */
    public function __construct($sourceData = null)
    {
        $this->setCacheId('config_api');
        $this->setCacheTags([self::CACHE_TAG]);
        $this->setCacheChecksum(null);

        parent::__construct($sourceData);
        $this->_construct();
    }

    /**
     * Init configuration for webservices api
     *
     * @return $this
     */
    protected function _construct()
    {
        if (Mage::app()->useCache('config_api')) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $config = Mage::getConfig()->loadModulesConfiguration('api.xml');
        $this->setXml($config->getNode('api'));

        if (Mage::app()->useCache('config_api')) {
            $this->saveCache();
        }

        return $this;
    }

    /**
     * Retrieve adapter aliases from config.
     *
     * @return array
     */
    public function getAdapterAliases()
    {
        $aliases = [];

        foreach ($this->getNode('adapter_aliases')->children() as $alias => $adapter) {
            $aliases[$alias] = [
                (string) $adapter->suggest_class, // model class name
                (string) $adapter->suggest_method, // model method name
            ];
        }

        return $aliases;
    }

    /**
     * Retrieve all adapters
     *
     * @return array
     */
    public function getAdapters()
    {
        $adapters = [];
        foreach ($this->getNode('adapters')->children() as $adapterName => $adapter) {
            /** @var Varien_Simplexml_Element $adapter */
            if (isset($adapter->use)) {
                $adapter = $this->getNode('adapters/' . $adapter->use);
            }

            $adapters[$adapterName] = $adapter;
        }

        return $adapters;
    }

    /**
     * Retrieve active adapters
     *
     * @return array
     */
    public function getActiveAdapters()
    {
        $adapters = [];
        foreach ($this->getAdapters() as $adapterName => $adapter) {
            if (!isset($adapter->active) || $adapter->active == '0') {
                continue;
            }

            if (isset($adapter->required) && isset($adapter->required->extensions)) {
                foreach ($adapter->required->extensions->children() as $extension => $data) {
                    if (!extension_loaded($extension)) {
                        continue;
                    }
                }
            }

            $adapters[$adapterName] = $adapter;
        }

        return $adapters;
    }

    /**
     * Retrieve handlers
     *
     * @return SimpleXMLElement
     */
    public function getHandlers()
    {
        return $this->getNode('handlers')->children();
    }

    /**
     * Retrieve resources
     *
     * @return SimpleXMLElement
     */
    public function getResources()
    {
        return $this->getNode('resources')->children();
    }

    /**
     * Retrieve resources alias
     *
     * @return SimpleXMLElement
     */
    public function getResourcesAlias()
    {
        return $this->getNode('resources_alias')->children();
    }

    /**
     * Load Acl resources from config
     *
     * @param Mage_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return $this
     */
    public function loadAclResources(Mage_Api_Model_Acl $acl, $resource = null, $parentName = null)
    {
        $resourceName = null;
        if (is_null($resource)) {
            $resource = $this->getNode('acl/resources');
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName . '/') . $resource->getName();
            $acl->add(Mage::getModel('api/acl_resource', $resourceName), $parentName);
        }

        $children = $resource->children();

        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            if ($res->getName() != 'title' && $res->getName() != 'sort_order') {
                $this->loadAclResources($acl, $res, $resourceName);
            }
        }

        return $this;
    }

    /**
     * Get acl assert config
     *
     * @param string $name
     * @return bool|Mage_Core_Model_Config_Element|SimpleXMLElement
     */
    public function getAclAssert($name = '')
    {
        $asserts = $this->getNode('acl/asserts');
        if ($name === '') {
            return $asserts;
        }

        return $asserts->$name ?? false;
    }

    /**
     * Retrieve privilege set by name
     *
     * @param string $name
     * @return bool|Mage_Core_Model_Config_Element|SimpleXMLElement
     */
    public function getAclPrivilegeSet($name = '')
    {
        $sets = $this->getNode('acl/privilegeSets');
        if ($name === '') {
            return $sets;
        }

        return $sets->$name ?? false;
    }

    /**
     * @param null|string $resourceName
     * @return array
     */
    public function getFaults($resourceName = null)
    {
        if (is_null($resourceName)
            || !isset($this->getResources()->$resourceName)
            || !isset($this->getResources()->$resourceName->faults)
        ) {
            $faultsNode = $this->getNode('faults');
        } else {
            $faultsNode = $this->getResources()->$resourceName->faults;
        }

        /** @var Varien_Simplexml_Element $faultsNode */

        $translateModule = 'api';
        if (isset($faultsNode['module'])) {
            $translateModule = (string) $faultsNode['module'];
        }

        $faults = [];
        foreach ($faultsNode->children() as $faultName => $fault) {
            $faults[$faultName] = [
                'code'    => (string) $fault->code,
                'message' => Mage::helper($translateModule)->__((string) $fault->message),
            ];
        }

        return $faults;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
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
     * @param bool $lifetime
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
}
