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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Model_Layout_Update
{
    /**
     * Additional tag for cleaning layout cache convenience
     */
    const LAYOUT_GENERAL_CACHE_TAG = 'LAYOUT_GENERAL_CACHE_TAG';

    /**
     * Prefix used for actual XML storage (unprefixed is just the sha1 hash)
     */
    const XML_KEY_PREFIX = 'XML_';

    /**
     * Layout Update Simplexml Element Class Name
     *
     * @var string
     */
    protected $_elementClass;

    /**
     * @var SimpleXMLElement
     */
    protected $_packageLayout;

    /**
     * Cache key
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Cache prefix
     *
     * @var string
     */
    protected $_cachePrefix;

    /**
     * Cumulative array of update XML strings
     *
     * @var array
     */
    protected $_updates = [];

    /**
     * Handles used in this update
     *
     * @var array
     */
    protected $_handles = [];

    /**
     * Substitution values in structure array('from'=>array(), 'to'=>array())
     *
     * @var array
     */
    protected $_subst = [];

    public function __construct()
    {
        $subst = Mage::getConfig()->getPathVars();
        foreach ($subst as $k => $v) {
            $this->_subst['from'][] = '{{'.$k.'}}';
            $this->_subst['to'][] = $v;
        }
    }

    /**
     * @return string
     */
    public function getElementClass()
    {
        if (!$this->_elementClass) {
            $this->_elementClass = Mage::getConfig()->getModelClassName('core/layout_element');
        }
        return $this->_elementClass;
    }

    /**
     * @return $this
     */
    public function resetUpdates()
    {
        $this->_updates = [];
        return $this;
    }

    /**
     * @param string $update
     * @return $this
     */
    public function addUpdate($update)
    {
        $this->_updates[] = $update;
        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->_updates;
    }

    /**
     * @return string
     */
    public function asString()
    {
        return implode('', $this->_updates);
    }

    /**
     * @return $this
     */
    public function resetHandles()
    {
        $this->_handles = [];
        return $this;
    }

    /**
     * @param string $handle
     * @return $this
     */
    public function addHandle($handle)
    {
        if (is_array($handle)) {
            foreach ($handle as $h) {
                $this->_handles[$h] = 1;
            }
        } else {
            $this->_handles[$handle] = 1;
        }
        return $this;
    }

    /**
     * @param string $handle
     * @return $this
     */
    public function removeHandle($handle)
    {
        unset($this->_handles[$handle]);
        return $this;
    }

    /**
     * @return array
     */
    public function getHandles()
    {
        return array_keys($this->_handles);
    }

    /**
     * Get cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        if (!$this->_cacheId) {
            $this->_cacheId = 'LAYOUT_' . Mage::app()->getStore()->getId() . md5(implode('__', $this->getHandles()));
        }
        return $this->_cacheId;
    }

    /**
     * Set cache id
     *
     * @param string $cacheId
     * @return $this
     */
    public function setCacheId($cacheId)
    {
        $this->_cacheId = $cacheId;
        return $this;
    }

    /**
     * @return bool
     */
    public function loadCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }

        if (!$result = Mage::app()->loadCache($this->getCacheId())) {
            return false;
        }

        // The cache key is just a hash of the real content to de-duplicate the often large XML strings
        if (strlen($result) === 40) { // sha1
            if (!$result = Mage::app()->loadCache(self::XML_KEY_PREFIX . $result)) {
                return false;
            }
        }

        $this->addUpdate($result);

        return true;
    }

    /**
     * @return bool
     */
    public function saveCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }
        $str = $this->asString();
        $tags = $this->getHandles();

        // Cache key is sha1 hash of actual XML string
        $hash = sha1($str);
        $tags[] = self::LAYOUT_GENERAL_CACHE_TAG;
        $returnValue = Mage::app()->saveCache($hash, $this->getCacheId(), $tags, null);

        // Only save actual XML to cache if it doesn't already exist
        if (!Mage::app()->testCache(self::XML_KEY_PREFIX . $hash)) {
            $returnValue = Mage::app()->saveCache($str, self::XML_KEY_PREFIX . $hash, $tags, null);
        }

        return $returnValue;
    }

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @return $this
     */
    public function load($handles = [])
    {
        if (is_string($handles)) {
            $handles = [$handles];
        } elseif (!is_array($handles)) {
            throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid layout update handle'));
        }

        foreach ($handles as $handle) {
            $this->addHandle($handle);
        }

        if ($this->loadCache()) {
            return $this;
        }

        foreach ($this->getHandles() as $handle) {
            $this->merge($handle);
        }

        $this->saveCache();
        return $this;
    }

    /**
     * @return SimpleXMLElement
     */
    public function asSimplexml()
    {
        $updates = trim($this->asString());
        $updates = '<'.'?xml version="1.0"?'.'><layout>'.$updates.'</layout>';
        return simplexml_load_string($updates, $this->getElementClass());
    }

    /**
     * Merge layout update by handle
     *
     * @param string $handle
     * @return $this
     */
    public function merge($handle)
    {
        $packageUpdatesStatus = $this->fetchPackageLayoutUpdates($handle);
        if (Mage::app()->isInstalled()) {
            $this->fetchDbLayoutUpdates($handle);
        }
        return $this;
    }

    /**
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function fetchFileLayoutUpdates()
    {
        $storeId = Mage::app()->getStore()->getId();
        $elementClass = $this->getElementClass();
        $design = Mage::getSingleton('core/design_package');
        $cacheKey = 'LAYOUT_' . $design->getArea() . '_STORE' . $storeId . '_' . $design->getPackageName() . '_'
            . $design->getTheme('layout');

        $cacheTags = [self::LAYOUT_GENERAL_CACHE_TAG];
        if (Mage::app()->useCache('layout') && ($layoutStr = Mage::app()->loadCache($cacheKey))) {
            $this->_packageLayout = simplexml_load_string($layoutStr, $elementClass);
        }
        if (empty($layoutStr)) {
            $this->_packageLayout = $this->getFileLayoutUpdatesXml(
                $design->getArea(),
                $design->getPackageName(),
                $design->getTheme('layout'),
                $storeId
            );
            if (Mage::app()->useCache('layout')) {
                Mage::app()->saveCache($this->_packageLayout->asXML(), $cacheKey, $cacheTags, null);
            }
        }

        return $this;
    }

    /**
     * @param string $handle
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function fetchPackageLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/package_update: '.$handle;
        Varien_Profiler::start($_profilerKey);
        if (empty($this->_packageLayout)) {
            $this->fetchFileLayoutUpdates();
        }
        /** @var Varien_Simplexml_Element $updateXml */
        foreach ($this->_packageLayout->$handle as $updateXml) {
            $this->fetchRecursiveUpdates($updateXml);
            $this->addUpdate($updateXml->innerXml());
        }
        Varien_Profiler::stop($_profilerKey);

        return true;
    }

    /**
     * @param string $handle
     * @return bool
     */
    public function fetchDbLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/db_update: '.$handle;
        Varien_Profiler::start($_profilerKey);
        $updateStr = $this->_getUpdateString($handle);
        if (!$updateStr) {
            Varien_Profiler::stop($_profilerKey);
            return false;
        }
        $updateStr = '<update_xml>' . $updateStr . '</update_xml>';
        $updateStr = str_replace($this->_subst['from'], $this->_subst['to'], $updateStr);
        /** @var Varien_Simplexml_Element $updateXml */
        $updateXml = simplexml_load_string($updateStr, $this->getElementClass());
        $this->fetchRecursiveUpdates($updateXml);
        $this->addUpdate($updateXml->innerXml());

        Varien_Profiler::stop($_profilerKey);
        return true;
    }

    /**
     * Get update string
     *
     * @param string $handle
     * @return mixed
     */
    protected function _getUpdateString($handle)
    {
        return Mage::getResourceModel('core/layout')->fetchUpdatesByHandle($handle);
    }

    /**
     * @param SimpleXMLElement $updateXml
     * @return $this
     */
    public function fetchRecursiveUpdates($updateXml)
    {
        foreach ($updateXml->children() as $child) {
            if (strtolower($child->getName())=='update' && isset($child['handle'])) {
                $this->merge((string)$child['handle']);
                // Adding merged layout handle to the list of applied hanles
                $this->addHandle((string)$child['handle']);
            }
        }
        return $this;
    }

    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return SimpleXMLElement
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }
        /** @var Mage_Core_Model_Design_Package $design */
        $design = Mage::getSingleton('core/design_package');
        $layoutXml = null;
        $elementClass = $this->getElementClass();
        $updatesRoot = Mage::app()->getConfig()->getNode($area.'/layout/updates');
        Mage::dispatchEvent('core_layout_update_updates_get_after', ['updates' => $updatesRoot]);
        $updates = $updatesRoot->asArray();
        $themeUpdates = Mage::getSingleton('core/design_config')->getNode("$area/$package/$theme/layout/updates");
        if ($themeUpdates && is_array($themeUpdates->asArray())) {
            //array_values() to ensure that theme-specific layouts don't override, but add to module layouts
            $updates = array_merge($updates, array_values($themeUpdates->asArray()));
        }
        $updateFiles = [];
        foreach ($updates as $updateNode) {
            if (!empty($updateNode['file'])) {
                $module = isset($updateNode['@']['module']) ? $updateNode['@']['module'] : false;
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = $updateNode['file'];
            }
        }
        // custom local layout updates file - load always last
        $updateFiles[] = 'local.xml';
        $layoutStr = '';
        foreach ($updateFiles as $file) {
            $filename = $design->getLayoutFilename($file, [
                '_area'    => $area,
                '_package' => $package,
                '_theme'   => $theme
            ]);
            if (!is_readable($filename)) {
                continue;
            }
            $fileStr = file_get_contents($filename);
            $fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);
            /** @var Varien_Simplexml_Element $fileXml */
            $fileXml = simplexml_load_string($fileStr, $elementClass);
            if (!$fileXml instanceof SimpleXMLElement) {
                continue;
            }
            $layoutStr .= $fileXml->innerXml();
        }
        $layoutXml = simplexml_load_string('<layouts>'.$layoutStr.'</layouts>', $elementClass);
        return $layoutXml;
    }
}
