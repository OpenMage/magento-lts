<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Csp
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Csp_Model_Config extends Varien_Simplexml_Config
{
    public const CACHE_TYPE = 'config';
    public const CACHE_ID = 'config_csp';
    public const CACHE_TAG = 'config_csp';

    /**
     * @inheritDoc
     */
    public function __construct($sourceData = null)
    {
        $this->setCacheId(self::CACHE_ID);
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
    protected function _construct(): self
    {
        if ($this->hasUseCache() && $this->loadCache()) {
            return $this;
        }

        $this->loadString('<config/>');
        $config = Mage::getConfig()->loadModulesConfiguration('csp.xml', $this);

        $node = $config->getNode();
        if ($node) {
            $this->setXml($node);
        }

        if ($this->hasUseCache()) {
            $this->saveCache();
        }
        return $this;
    }

    /**
     * Retrieve all adapters
     * @return array<string, array<int, string>>
     */
    public function getPolicies(): array
    {
        $policies = [];

        $xpaths = $this->getXpath('csp/policy');
        if (!$xpaths) {
            return $policies;
        }

        foreach ($xpaths as $config) {
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
     */
    protected function _loadCache($id): bool
    {
        return (bool) Mage::app()->loadCache($id);
    }

    /**
     * @param string $data
     * @param string $id
     * @param array<int, string> $tags
     * @param false|int $lifetime
     */
    protected function _saveCache($data, $id, $tags = [], $lifetime = false): bool
    {
        Mage::app()->saveCache($data, $id, $tags, $lifetime);
        return true;
    }

    /**
     * @param string $id
     */
    protected function _removeCache($id): void
    {
        Mage::app()->removeCache($id);
    }

    /**
     * @param bool $overwrite
     * @return $this
     */
    public function extend(Varien_Simplexml_Config $config, $overwrite = false): self
    {
        $config = $config->getNode();

        if (!$config instanceof Varien_Simplexml_Element) {
            return $this;
        }

        $node = $this->getNode();
        if ($node) {
            $this->_extendNode($node, $config, $overwrite);
        }

        return $this;
    }

    /**
     * Custom merging logic that preserves duplicate nodes.
     */
    protected function _extendNode(Varien_Simplexml_Element $baseNode, Varien_Simplexml_Element $mergeNode, bool $overwrite = false): void
    {
        foreach ($mergeNode->children() as $key => $child) {
            $newChild = $baseNode->addChild($key, (string) $child);
            foreach ($child->attributes() as $attrKey => $attrValue) {
                $newChild->addAttribute($attrKey, (string) $attrValue);
            }
            $this->_extendNode($newChild, $child, $overwrite);
        }
    }

    /**
     * @return array<mixed>|bool|null
     */
    protected function hasUseCache(): array|bool|null
    {
        return Mage::app()->useCache(self::CACHE_TYPE);
    }
}
