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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog url model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Url
{
    /**
     * Number of characters allowed to be in URL path
     *
     * @var int
     */
    const MAX_REQUEST_PATH_LENGTH = 240;

    /**
     * Number of characters allowed to be in URL path
     * after MAX_REQUEST_PATH_LENGTH number of characters
     *
     * @var int
     */
    const ALLOWED_REQUEST_PATH_OVERFLOW = 10;

    /**
     * Resource model
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    protected $_resourceModel;

    /**
     * Categories cache for products
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Rewrite cache
     *
     * @var array
     */
    protected $_rewrites = array();

    /**
     * Current url rewrite rule
     *
     * @var Varien_Object
     */
    protected $_rewrite;

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_productUrlSuffix = array();

    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $_categoryUrlSuffix = array();

    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store|array
     */
    public function getStores($storeId = null)
    {
        return $this->getResource()->getStores($storeId);
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function getResource()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = Mage::getResourceModel('catalog/url');
        }
        return $this->_resourceModel;
    }

    /**
     * Retrieve Category model singleton
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategoryModel()
    {
        return $this->getResource()->getCategoryModel();
    }

    /**
     * Retrieve product model singleton
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        return $this->getResource()->getProductModel();
    }

    /**
     * Refresh rewrite urls
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshRewrites($store->getId());
            }
            return $this;
        }

        $this->refreshCategoryRewrite($this->getStores($storeId)->getRootCategoryId(), $storeId, false);
        $this->refreshProductRewrites($storeId);
        $this->getResource()->clearCategoryProduct($storeId);

        return $this;
    }

    /**
     * Refresh category rewrite
     *
     * @param Varien_Object $category
     * @param string $parentPath
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshCategoryRewrites(Varien_Object $category, $parentPath = null, $refreshProducts = true)
    {
        if ($category->getId() != $this->getStores($category->getStoreId())->getRootCategoryId()) {
            if ($category->getUrlKey() == '') {
                $urlKey = $this->getCategoryModel()->formatUrlKey($category->getName());
            }
            else {
                $urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
            }

            $idPath      = $this->generatePath('id', null, $category);
            $targetPath  = $this->generatePath('target', null, $category);
            $requestPath = $this->generatePath('request', null, $category, $parentPath);

            $rewriteData = array(
                'store_id'      => $category->getStoreId(),
                'category_id'   => $category->getId(),
                'product_id'    => null,
                'id_path'       => $idPath,
                'request_path'  => $requestPath,
                'target_path'   => $targetPath,
                'is_system'     => 1
            );

            $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);

            if ($category->getUrlKey() != $urlKey) {
                $category->setUrlKey($urlKey);
                $this->getResource()->saveCategoryAttribute($category, 'url_key');
            }
            if ($category->getUrlPath() != $requestPath) {
                $category->setUrlPath($requestPath);
                $this->getResource()->saveCategoryAttribute($category, 'url_path');
            }
        }
        else {
            if ($category->getUrlPath() != '') {
                $category->setUrlPath('');
                $this->getResource()->saveCategoryAttribute($category, 'url_path');
            }
        }

        if ($refreshProducts) {
            $this->_refreshCategoryProductRewrites($category);
        }

        foreach ($category->getChilds() as $child) {
            $this->_refreshCategoryRewrites($child, $category->getUrlPath() . '/', $refreshProducts);
        }

        return $this;
    }

    /**
     * Refresh product rewrite
     *
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category)
    {
        if ($category->getId() == $category->getPath()) {
            return $this;
        }
        if ($product->getUrlKey() == '') {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
        }
        else {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
        }

        $idPath      = $this->generatePath('id', $product, $category);
        $targetPath  = $this->generatePath('target', $product, $category);
        $requestPath = $this->generatePath('request', $product, $category);
        $categoryId = null;
        $updateKeys = true;
        if ($category->getUrlPath()) {
            $categoryId = $category->getId();
            $updateKeys = false;
        }

        $rewriteData = array(
            'store_id'      => $category->getStoreId(),
            'category_id'   => $categoryId,
            'product_id'    => $product->getId(),
            'id_path'       => $idPath,
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
            'is_system'     => 1
        );

        $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);

        if ($updateKeys && $product->getUrlKey() != $urlKey) {
            $product->setUrlKey($urlKey);
            $this->getResource()->saveProductAttribute($product, 'url_key');
        }
        if ($updateKeys && $product->getUrlPath() != $requestPath) {
            $product->setUrlPath($requestPath);
            $this->getResource()->saveProductAttribute($product, 'url_path');
        }

        return $this;
    }

    /**
     * Refresh products for catwgory
     *
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshCategoryProductRewrites(Varien_Object $category)
    {
        $originalRewrites = $this->_rewrites;
        $process = true;
        $lastEntityId = 0;
        while ($process == true) {
            $products = $this->getResource()->getProductsByCategory($category, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $this->_rewrites = $this->getResource()->prepareRewrites($category->getStoreId(), $category->getId(), array_keys($products));

            foreach ($products as $product) {
                $this->_refreshProductRewrite($product, $category);
            }
            unset($products);
        }
        $this->_rewrites = $originalRewrites;
        return $this;
    }

    /**
     * Refresh category and childs rewrites
     *
     * @param int $categoryId
     * @param int $storeId
     * @param bool $refreshProducts
     * @return Mage_Catalog_Model_Url
     */
    public function refreshCategoryRewrite($categoryId, $storeId = null, $refreshProducts = true)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshCategoryRewrite($categoryId, $store->getId(), $refreshProducts);
            }
            return $this;
        }

        $category = $this->getResource()->getCategory($categoryId, $storeId);
        if (!$category) {
            return $this;
        }
        $category = $this->getResource()->loadCategoryChilds($category);
        $categoryIds = array($category->getId());
        if ($category->getAllChilds()) {
            $categoryIds = array_merge($categoryIds, array_keys($category->getAllChilds()));
        }
        $this->_rewrites = $this->getResource()->prepareRewrites($storeId, $categoryIds);
        $this->_refreshCategoryRewrites($category, null, $refreshProducts);

        unset($category);
        $this->_rewrites = array();

        return $this;
    }

    /**
     * Refresh product and categories urls
     *
     * @param int $productId
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshProductRewrite($productId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshProductRewrite($productId, $store->getId());
            }
            return $this;
        }

        if ($product = $this->getResource()->getProduct($productId, $storeId)) {
            $storeRootCategoryId = $this->getStores($storeId)->getRootCategoryId();
            $categories = $this->getResource()->getCategories($product->getCategoryIds(), $storeId);
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, '', $productId);

            if (!isset($categories[$storeRootCategoryId])) {
                $categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);
            }

            foreach ($categories as $category) {
                $this->_refreshProductRewrite($product, $category);
            }

            unset($categories);
            unset($product);

//            $this->getResource()->clearCategoryProduct($storeId);
        }

        return $this;
    }

    public function refreshProductRewrites($storeId)
    {
        $this->_categories = array();
        $storeRootCategoryId = $this->getStores($storeId)->getRootCategoryId();
        $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;

        while ($process == true) {
            $products = $this->getResource()->getProductsByStore($storeId, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $this->_rewrites = array();
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, false, array_keys($products));

            $loadCategories = array();
            foreach ($products as $product) {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (!isset($this->_categories[$categoryId])) {
                        $loadCategories[$categoryId] = $categoryId;
                    }
                }
            }

            if ($loadCategories) {
                foreach ($this->getResource()->getCategories($loadCategories, $storeId) as $category) {
                    $this->_categories[$category->getId()] = $category;
                }
            }

            foreach ($products as $product) {
                $this->_refreshProductRewrite($product, $this->_categories[$storeRootCategoryId]);
                foreach ($product->getCategoryIds() as $categoryId) {
                    if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
                        $this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
                    }
                }
            }

            unset($products);
            $this->_rewrites = array();
        }

        $this->_categories = array();
        return $this;
    }

    /**
     * Get requestPath that was not used yet.
     *
     * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $idPath
     * @return string
     */
    public function getUnusedPath($storeId, $requestPath, $idPath)
    {
        if (empty($requestPath)) {
            $requestPath = '-';
        }
        elseif ($requestPath == $this->getProductUrlSuffix($storeId)) {
            $requestPath = '-' . $this->getProductUrlSuffix($storeId);
        }

        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            if ($this->_rewrites[$idPath]->getRequestPath() == $requestPath) {
                return $requestPath;
            }
        }
        else {
            $this->_rewrite = null;
        }

        $rewrite = $this->getResource()->getRewriteByRequestPath($requestPath, $storeId);
        if ($rewrite && $rewrite->getId()) {
            if ($rewrite->getIdPath() == $idPath) {
                $this->_rewrite = $rewrite;
                return $requestPath;
            }
            // retrieve url_suffix for product urls
            $productUrlSuffix = $this->getProductUrlSuffix($storeId);
            // match request_url abcdef1234(-12)(.html) pattern
            $match = array();
            if (!preg_match('#^([0-9a-z/-]+?)(-([0-9]+))?('.preg_quote($productUrlSuffix).')?$#i', $requestPath, $match)) {
                return $this->getUnusedPath($storeId, '-', $idPath);
            }
            $requestPath = $match[1].(isset($match[3])?'-'.($match[3]+1):'-1').(isset($match[4])?$match[4]:'');
            return $this->getUnusedPath($storeId, $requestPath, $idPath);
        }
        else {
            return $requestPath;
        }
    }

    /**
     * Retrieve product rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getProductUrlSuffix($storeId)
    {
        return Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
    }

    /**
     * Retrieve category rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId)
    {
        return Mage::helper('catalog/category')->getCategoryUrlSuffix($storeId);
    }

    /**
     * Generate either id path, request path or target path for product and/or category
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     * $parentPath used only for generating category path
     *
     * @param string $type
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @param string $parentPath
     * @return string
     * @throws Mage_Core_Exception
     */
    public function generatePath($type = 'target', $product = null, $category = null, $parentPath = null)
    {
        if (!$product && !$category) {
            Mage::throwException(Mage::helper('core')->__('Specify either category or product, or both.'));
        }

        // generate id_path
        if ('id' === $type) {
            if (!$product) {
                return 'category/' . $category->getId();
            }
            if ($category && $category->getUrlPath()) {
                return 'product/' . $product->getId() . '/' . $category->getId();
            }
            return 'product/' . $product->getId();
        }

        // generate request_path
        if ('request' === $type) {
            // for category
            if (!$product) {
                if ($category->getUrlKey() == '') {
                    $urlKey = $this->getCategoryModel()->formatUrlKey($category->getName());
                }
                else {
                    $urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
                }

                $categoryUrlSuffix = $this->getCategoryUrlSuffix($category->getStoreId());
                if (null === $parentPath) {
                    $parentPath = $this->getResource()->getCategoryParentPath($category);
                }
                elseif ($parentPath == '/') {
                    $parentPath = '';
                }
                $parentPath = Mage::helper('catalog/category')->getCategoryUrlPath($parentPath, true, $category->getStoreId());

                return $this->getUnusedPath($category->getStoreId(), $parentPath . $urlKey . $categoryUrlSuffix,
                    $this->generatePath('id', null, $category)
                );
            }

            // for product & category
            if (!$category) {
                Mage::throwException(Mage::helper('core')->__('Category object is required for determining product request path')); // why?
            }

            if ($product->getUrlKey() == '') {
                $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
            }
            else {
                $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
            }
            $productUrlSuffix  = $this->getProductUrlSuffix($category->getStoreId());
            if ($category->getUrlPath()) {
                $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(), false, $category->getStoreId());
                return $this->getUnusedPath($category->getStoreId(), $categoryUrl . '/' . $urlKey . $productUrlSuffix,
                    $this->generatePath('id', $product, $category)
                );
            }

            // for product only
            return $this->getUnusedPath($category->getStoreId(), $urlKey . $productUrlSuffix,
                $this->generatePath('id', $product)
            );
        }

        // generate target_path
        if (!$product) {
            return 'catalog/category/view/id/' . $category->getId();
        }
        if ($category && $category->getUrlPath()) {
            return 'catalog/product/view/id/' . $product->getId() . '/category/' . $category->getId();
        }
        return 'catalog/product/view/id/' . $product->getId();
    }
}