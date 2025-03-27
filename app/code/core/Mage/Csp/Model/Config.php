<?php
/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Csp
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */
class Mage_Csp_Model_Config extends Varien_Simplexml_Config
{
    public const CACHE_TAG = 'config_csp';

    /**
     * @inheritDoc
     */
    public function __construct($sourceData = null)
    {
        $this->setCacheId('config_csp');
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
        if (Mage::app()->useCache('config_csp')) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $this->loadString('<config/>');
        $config = Mage::getConfig()->loadModulesConfiguration('csp.xml', $this);

        $this->setXml($config->getNode());

        if (Mage::app()->useCache('config_api')) {
            $this->saveCache();
        }
        return $this;
    }

    /**
     * Retrieve all adapters
     *
     * @return array
     */
    public function getPolicies()
    {
        $policies = [];
        foreach ($this->getXpath('csp') as $config) {
            foreach ($config as $policy => $rules) {
                foreach ($rules as $host) {
                    $policies[$policy][] = (string) $host;
                }
            }
        }

        return $policies;
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

    /**
     * @param Varien_Simplexml_Config $config
     * @param bool $overwrite
     * @return $this
     */
    public function extend(Varien_Simplexml_Config $config, $overwrite = false)
    {
        if ($config instanceof Varien_Simplexml_Config) {
            $config = $config->getNode();
        }

        if (!$config instanceof Varien_Simplexml_Element) {
            return $this;
        }

        $this->_extendNode($this->getNode(), $config, $overwrite);

        return $this;
    }

    /**
     * Custom merging logic that preserves duplicate nodes.
     *
     * @param Varien_Simplexml_Element $baseNode
     * @param Mage_Core_Model_Config_Element $mergeNode
     * @param bool $overwrite
     */
    protected function _extendNode(Varien_Simplexml_Element $baseNode, Mage_Core_Model_Config_Element $mergeNode, $overwrite = false)
    {
        foreach ($mergeNode->children() as $key => $child) {
            if (isset($baseNode->$key)) {
                $newChild = $baseNode->addChild($key, (string)$child);
                foreach ($child->attributes() as $attrKey => $attrValue) {
                    $newChild->addAttribute($attrKey, (string)$attrValue);
                }
                $this->_extendNode($newChild, $child, $overwrite);
            } else {
                $newChild = $baseNode->addChild($key, (string)$child);
                foreach ($child->attributes() as $attrKey => $attrValue) {
                    $newChild->addAttribute($attrKey, (string)$attrValue);
                }
                $this->_extendNode($newChild, $child, $overwrite);
            }
        }
    }
}
