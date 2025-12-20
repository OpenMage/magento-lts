<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog category helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Category extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_CATEGORY_URL_SUFFIX          = 'catalog/seo/category_url_suffix';

    public const XML_PATH_USE_CATEGORY_CANONICAL_TAG   = 'catalog/seo/category_canonical_tag';

    public const XML_PATH_CATEGORY_ROOT_ID             = 'catalog/category/root_id';

    protected $_moduleName = 'Mage_Catalog';

    /**
     * Store categories cache
     *
     * @var array
     */
    protected $_storeCategories = [];

    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $_categoryUrlSuffix = [];

    /**
     * Retrieve current store categories
     *
     * @param  bool|string                                                                                                   $sorted
     * @param  bool                                                                                                          $asCollection
     * @param  bool                                                                                                          $toLoad
     * @return array|Mage_Catalog_Model_Resource_Category_Collection|Varien_Data_Collection|Varien_Data_Tree_Node_Collection
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $parent     = Mage::app()->getStore()->getRootCategoryId();
        $cacheKey   = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('catalog/category');
        /** @var Mage_Catalog_Model_Category $category */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return new Varien_Data_Collection();
            }

            return [];
        }

        $recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }

    /**
     * Retrieve category url
     *
     * @param  Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            return $category->getUrl();
        }

        return Mage::getModel('catalog/category')
            ->setData($category->getData())
            ->getUrl();
    }

    /**
     * Check if a category can be shown
     *
     * @param  int|Mage_Catalog_Model_Category $category
     * @return bool
     */
    public function canShow($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }

        if (!$category->isInRootCategoryList()) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve category rewrite sufix for store
     *
     * @param  int    $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_categoryUrlSuffix[$storeId])) {
            $this->_categoryUrlSuffix[$storeId] = Mage::getStoreConfig(self::XML_PATH_CATEGORY_URL_SUFFIX, $storeId);
        }

        return $this->_categoryUrlSuffix[$storeId];
    }

    /**
     * Retrieve clear url for category as parent
     *
     * @param string $urlPath
     * @param bool   $slash
     * @param int    $storeId
     *
     * @return string
     */
    public function getCategoryUrlPath($urlPath, $slash = false, $storeId = null)
    {
        if (!$this->getCategoryUrlSuffix($storeId)) {
            return $urlPath;
        }

        if ($slash) {
            $regexp     = '#(' . preg_quote($this->getCategoryUrlSuffix($storeId), '#') . ')/$#i';
            $replace    = '/';
        } else {
            $regexp     = '#(' . preg_quote($this->getCategoryUrlSuffix($storeId), '#') . ')$#i';
            $replace    = '';
        }

        return preg_replace($regexp, $replace, $urlPath);
    }

    /**
     * Check if <link rel="canonical"> can be used for category
     *
     * @param  null|bool|int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_CATEGORY_CANONICAL_TAG, $store);
    }
}
