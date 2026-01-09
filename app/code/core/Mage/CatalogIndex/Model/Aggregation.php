<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Index data aggregation model
 *
 * Allow cache some aggregated data with tag dependency
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Aggregation _getResource()
 * @method string                                       getKey()
 * @method Mage_CatalogIndex_Model_Resource_Aggregation getResource()
 * @method int                                          getStoreId()
 * @method $this                                        setKey(string $value)
 * @method $this                                        setStoreId(int $value)
 */
class Mage_CatalogIndex_Model_Aggregation extends Mage_Core_Model_Abstract
{
    public const CACHE_FLAG_NAME   = 'layered_navigation';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/aggregation');
    }

    /**
     * @return array|bool
     */
    protected function _isEnabled()
    {
        return Mage::app()->useCache(self::CACHE_FLAG_NAME);
    }

    /**
     * Get aggregated data by data key and store
     *
     * @param  string                                $key
     * @param  null|int|Mage_Core_Model_Store|string $store
     * @return null|array
     */
    public function getCacheData($key, $store = null)
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
     * @param  array                                 $data
     * @param  string                                $key
     * @param  array                                 $tags
     * @param  null|int|Mage_Core_Model_Store|string $store
     * @return Mage_CatalogIndex_Model_Aggregation
     */
    public function saveCacheData($data, $key, $tags, $store = null)
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
     * @param  array                               $tags
     * @param  null|int|string                     $store
     * @return Mage_CatalogIndex_Model_Aggregation
     */
    public function clearCacheData($tags = [], $store = null)
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
     * @param  array                               $productIds
     * @return Mage_CatalogIndex_Model_Aggregation
     */
    public function clearProductData($productIds)
    {
        $categoryPaths = $this->_getResource()->getProductCategoryPaths($productIds);
        if (!empty($categoryPaths)) {
            $tags = [];
            foreach ($categoryPaths as $path) {
                $tags[] = Mage_Catalog_Model_Category::CACHE_TAG . ':' . $path;
            }

            $this->clearCacheData($tags);
        }

        return $this;
    }

    /**
     * Prepare data key
     *
     * @param  string $key
     * @return string
     */
    protected function _processKey($key)
    {
        return $key;
    }

    /**
     * Process tags array
     *
     * this method split tags like "category:1,2,3" to four
     * different tags: category, category1, category2, category3
     *
     * @param  array $tags
     * @return array
     */
    protected function _processTags($tags)
    {
        $newTags = [];
        foreach ($tags as $tag) {
            $tagInfo = explode(':', $tag);
            if (count($tagInfo) == 1) {
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
