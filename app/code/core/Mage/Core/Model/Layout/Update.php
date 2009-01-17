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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Layout_Update
{
    /**
     * Additional tag for cleaning layout cache convenience
     */
    const LAYOUT_GENERAL_CACHE_TAG = 'LAYOUT_GENERAL_CACHE_TAG';

    /**
     * Layout Update Simplexml Element Class Name
     *
     * @var string
     */
    protected $_elementClass;

    /**
     * @var Simplexml_Element
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
    protected $_updates = array();

    /**
     * Handles used in this update
     *
     * @var array
     */
    protected $_handles = array();

    /**
     * Substitution values in structure array('from'=>array(), 'to'=>array())
     *
     * @var array
     */
    protected $_subst = array();

    public function __construct()
    {
        $subst = Mage::getConfig()->getPathVars();
        foreach ($subst as $k=>$v) {
            $this->_subst['from'][] = '{{'.$k.'}}';
            $this->_subst['to'][] = $v;
        }
    }

    public function getElementClass()
    {
        if (!$this->_elementClass) {
            $this->_elementClass = Mage::getConfig()->getModelClassName('core/layout_element');
        }
        return $this->_elementClass;
    }

    public function resetUpdates()
    {
        $this->_updates = array();
        return $this;
    }

    public function addUpdate($update)
    {
        $this->_updates[] = $update;
        return $this;
    }

    public function asArray()
    {
        return $this->_updates;
    }

    public function asString()
    {
        return implode('', $this->_updates);
    }

    public function resetHandles()
    {
        $this->_handles = array();
        return $this;
    }

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

    public function removeHandle($handle)
    {
        unset($this->_handles[$handle]);
        return $this;
    }

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
            $this->_cacheId = Mage::app()->getStore()->getId().'_'.md5(join('__', $this->getHandles()));
        }
        return $this->_cacheId;
    }

    /**
     * Set cache id
     *
     * @param string $cacheId
     * @return Mage_Core_Model_Layout_Update
     */
    public function setCacheId($cacheId)
    {
        $this->_cacheId = $cacheId;
        return $this;
    }

    public function loadCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }

        if (!$result = Mage::app()->loadCache($this->getCacheId())) {
            return false;
        }

        $this->addUpdate($result);

        return true;
    }

    public function saveCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }
        $str = $this->asString();
        $tags = $this->getHandles();
        $tags[] = self::LAYOUT_GENERAL_CACHE_TAG;
        return Mage::app()->saveCache($str, $this->getCacheId(), $tags, null);
    }

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @return Mage_Core_Model_Layout_Update
     */
    public function load($handles=array())
    {
        if (is_string($handles)) {
            $handles = array($handles);
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
     * @return Mage_Core_Model_Layout_Update
     */
    public function merge($handle)
    {
        if (!$this->fetchPackageLayoutUpdates($handle)
            && !$this->fetchDbLayoutUpdates($handle)) {
            #$this->removeHandle($handle);
        }
        return $this;
    }

    public function fetchFileLayoutUpdates()
    {
        $elementClass = $this->getElementClass();

        $design = Mage::getSingleton('core/design_package');
        $area = $design->getArea();
        $storeId = Mage::app()->getStore()->getId();
        $cacheKey = 'LAYOUT_'.$area.'_STORE'.$storeId.'_'.$design->getPackageName().'_'.$design->getTheme('layout');
#echo "TEST:".$cacheKey;
        $cacheTags = array('layout');

        if (Mage::app()->useCache('layout') && ($layoutStr = Mage::app()->loadCache($cacheKey))) {
            $this->_packageLayout = simplexml_load_string($layoutStr, $elementClass);
        }

        if (empty($layoutStr)) {
            $updatesRoot = Mage::app()->getConfig()->getNode($area.'/layout/updates');
            $updateFiles = array();
            foreach ($updatesRoot->children() as $updateNode) {
                if ($updateNode->file) {
                    $module = $updateNode->getAttribute('module');
                    if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module)) {
                        continue;
                    }
                    $updateFiles[] = (string)$updateNode->file;
                }
            }

            // custom local layout updates file - load always last
            $updateFiles[] = 'local.xml';

            $layoutStr = '';
            #$layoutXml = new $elementClass('<layouts/>');
            foreach ($updateFiles as $file) {
                $filename = $design->getLayoutFilename($file);
                if (!is_readable($filename)) {
                    continue;
                }
                $fileStr = file_get_contents($filename);
                $fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);
                $fileXml = simplexml_load_string($fileStr, $elementClass);
                if (!$fileXml instanceof SimpleXMLElement) {
                    continue;
                }
                $layoutStr .= $fileXml->innerXml();

                #$layoutXml->appendChild($fileXml);
            }
            $layoutXml = simplexml_load_string('<layouts>'.$layoutStr.'</layouts>', $elementClass);

            $this->_packageLayout = $layoutXml;

            if (Mage::app()->useCache('layout')) {
                Mage::app()->saveCache($this->_packageLayout->asXml(), $cacheKey, $cacheTags, null);
            }
        }

        return $this;
    }

    public function fetchPackageLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/package_update: '.$handle;
        Varien_Profiler::start($_profilerKey);

        if (empty($this->_packageLayout)) {
            $this->fetchFileLayoutUpdates();
        }
        foreach ($this->_packageLayout->$handle as $updateXml) {
#echo '<textarea style="width:600px; height:400px;">'.$handle.':'.print_r($updateXml,1).'</textarea>';
            $this->fetchRecursiveUpdates($updateXml);

            $this->addUpdate($updateXml->innerXml());
        }

        Varien_Profiler::stop($_profilerKey);

        return true;
    }

    public function fetchDbLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/db_update: '.$handle;
        Varien_Profiler::start($_profilerKey);

        try {
            $updateStr = Mage::getResourceModel('core/layout')->fetchUpdatesByHandle($handle);
            if (!$updateStr) {
                return false;
            }
            $updateStr = str_replace($this->_subst['from'], $this->_subst['to'], $updateStr);
            $updateXml = simplexml_load_string($updateStr, $this->getElementClass());
            $this->fetchRecursiveUpdates($updateXml);

            $this->addUpdate($update);
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {

        }

        Varien_Profiler::stop($_profilerKey);
        return true;
    }

    public function fetchRecursiveUpdates($updateXml)
    {
        foreach ($updateXml->children() as $child) {
            if (strtolower($child->getName())=='update' && isset($child['handle'])) {
                $this->merge((string)$child['handle']);
            }
        }
        return $this;
    }
}
