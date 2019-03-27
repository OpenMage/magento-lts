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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index data aggregation model
 *
 * Allow cache some aggregated data with tag dependency
 *
 * @method Mage_CatalogIndex_Model_Resource_Aggregation _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Aggregation getResource()
 * @method int getStoreId()
 * @method Mage_CatalogIndex_Model_Aggregation setStoreId(int $value)
 * @method string getCreatedAt()
 * @method Mage_CatalogIndex_Model_Aggregation setCreatedAt(string $value)
 * @method string getKey()
 * @method Mage_CatalogIndex_Model_Aggregation setKey(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Aggregation extends Mage_Core_Model_Abstract
{
    const CACHE_FLAG_NAME   = 'layered_navigation';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('catalogindex/aggregation');
    }

    protected function _isEnabled()
    {
        return Mage::app()->useCache(self::CACHE_FLAG_NAME);
    }

    /**
     * Get aggregated data by data key and store
     *
     * @param   string $key
     * @param   null|int|string|Mage_Core_Model_Store $store
     * @return  array|null
     */
    public function getCacheData($key, $store=null)
    {
        if (!$this->_isEnabled()) {
            return null;
        }

        $key    = $this->_processKey($key);
        $store  = Mage::app()->getStore($store);
        $data = $this->_getResource()->getCacheData($key, $store->getId());
        if (empty($data)) {
            return null;
        }
        return $data;
    }

    /**
     * Save aggregation data to cache
     *
     * @param   string $key
     * @param   array $tags
     * @param   null|int|string|Mage_Core_Model_Store $store
     * @return  Mage_CatalogIndex_Model_Aggregation
     */
    public function saveCacheData($data, $key, $tags, $store=null)
    {
        if (!$this->_isEnabled()) {
            return $this;
        }

        $key    = $this->_processKey($key);
        $tags   = $this->_processTags($tags);
        $store  = Mage::app()->getStore($store);

        $this->_getResource()->saveCacheData($data, $key, $tags, $store->getId());
        return $this;
    }

    /**
     * Delete cached aggregation data
     *
     * @param   array $tags
     * @param   int|null|string $store
     * @return  Mage_CatalogIndex_Model_Aggregation
     */
    public function clearCacheData($tags = array(), $store = null)
    {
        $tags    = $this->_processTags($tags);
        if ($store !== null) {
            $store = Mage::app()->getStore($store)->getId();
        }
        $this->_getResource()->clearCacheData($tags, $store);
        return $this;
    }

    /**
     * Clear all cache data related with products
     *
     * @param   int|array $productIds
     * @return  Mage_CatalogIndex_Model_Aggregation
     */
    public function clearProductData($productIds)
    {
        $categoryPaths = $this->_getResource()->getProductCategoryPaths($productIds);
        if (!empty($categoryPaths)) {
            $tags = array();
            foreach ($categoryPaths as $path) {
                $tags[] = Mage_Catalog_Model_Category::CACHE_TAG.':'.$path;
            }
            $this->clearCacheData($tags);
        }
        return $this;
    }

    /**
     * Prepare data key
     *
     * @param   string $key
     * @return  string
     */
    protected function _processKey($key)
    {
        return $key;
        return md5($key);
    }

    /**
     * Process tags array
     *
     * this method split tags like "category:1,2,3" to four
     * different tags: category, category1, category2, category3
     *
     * @param unknown_type $tags
     * @return unknown
     */
    protected function _processTags($tags)
    {
        $newTags = array();
        foreach ($tags as $tag) {
            $tagInfo = explode(':', $tag);
            if (count($tagInfo)==1) {
                $newTags[] = $tagInfo[0];
            } else {
                $tagVariants = explode('/', $tagInfo[1]);
                foreach ($tagVariants as $tagVariant) {
                    $newTags[] = $tagInfo[0] . $tagVariant;
                }
            }
        }
        return $newTags;
    }
}
