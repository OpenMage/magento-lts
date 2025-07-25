<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Catalog url model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Url extends Varien_Object
{
    /**
     * Number of characters allowed to be in URL path
     *
     * @var int
     */
    public const MAX_REQUEST_PATH_LENGTH = 240;

    /**
     * Number of characters allowed to be in URL path
     * after MAX_REQUEST_PATH_LENGTH number of characters
     *
     * @var int
     */
    public const ALLOWED_REQUEST_PATH_OVERFLOW = 10;

    /**
     * Resource model
     *
     * @var Mage_Catalog_Model_Resource_Url|null
     */
    protected $_resourceModel;

    /**
     * Categories cache for products
     *
     * @var array
     */
    protected $_categories = [];

    /**
     * Store root categories cache
     *
     * @var array
     */
    protected $_rootCategories = [];

    /**
     * Rewrite cache
     *
     * @var array
     */
    protected $_rewrites = [];

    /**
     * Current url rewrite rule
     *
     * @var Varien_Object|null
     */
    protected $_rewrite;

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_productUrlSuffix = [];

    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $_categoryUrlSuffix = [];

    /**
     * Flag to overwrite config settings for Catalog URL rewrites history maintenance
     *
     * @var bool
     */
    protected $_saveRewritesHistory = null;

    /**
    * Singleton of category model for building URL path
    *
    * @var Mage_Catalog_Model_Category
    */
    protected static $_categoryForUrlPath;

    protected ?string $locale = null;

    /**
     * Url instance
     *
     * @var Mage_Core_Model_Url
     */
    protected $_url;

    /**
     * Url rewrite instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * @var AsciiSlugger[]
     */
    protected ?array $slugger = null;

    /**
     * Retrieve Url instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if ($this->_url === null) {
            /** @var Mage_Core_Model_Url $model */
            $model = $this->_factory->getModel('core/url');
            $this->_url = $model;
        }
        return $this->_url;
    }

    /**
     * Retrieve Url rewrite instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if ($this->_urlRewrite === null) {
            $this->_urlRewrite = $this->_factory->getUrlRewriteInstance();
        }
        return $this->_urlRewrite;
    }

    /**
     * Adds url_path property for non-root category - to ensure that url path is not empty.
     *
     * Sometimes attribute 'url_path' can be empty, because url_path hasn't been generated yet,
     * in this case category is loaded with empty url_path and we should generate it manually.
     *
     * @param Varien_Object|Mage_Catalog_Model_Category $category
     */
    protected function _addCategoryUrlPath($category)
    {
        if (!($category instanceof Varien_Object) || $category->getUrlPath()) {
            return;
        }

        // This routine is not intended to be used with root categories,
        // but handle 'em gracefully - ensure them to have empty path.
        if ($category->getLevel() <= 1) {
            $category->setUrlPath('');
            return;
        }

        if (self::$_categoryForUrlPath === null) {
            self::$_categoryForUrlPath = Mage::getModel('catalog/category');
        }

        // Generate url_path
        $urlPath = self::$_categoryForUrlPath
            ->setData($category->getData())
            ->getUrlPath();
        $category->setUrlPath($urlPath);
    }

    /**
     * Retrieve stores array or store model
     *
     * @param int|null $storeId
     * @return Mage_Core_Model_Store|Mage_Core_Model_Store[]
     */
    public function getStores($storeId = null)
    {
        return $this->getResource()->getStores($storeId);
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Url
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
     * Returns store root category, uses caching for it
     *
     * @param int $storeId
     * @return Varien_Object
     */
    public function getStoreRootCategory($storeId)
    {
        if (!array_key_exists($storeId, $this->_rootCategories)) {
            $category = null;
            $store = $this->getStores($storeId);
            if ($store) {
                $rootCategoryId = $store->getRootCategoryId();
                $category = $this->getResource()->getCategory($rootCategoryId, $storeId);
            }
            $this->_rootCategories[$storeId] = $category;
        }
        return $this->_rootCategories[$storeId];
    }

    /**
     * Setter for $_saveRewritesHistory
     * Force Rewrites History save bypass config settings
     *
     * @param bool $flag
     * @return $this
     */
    public function setShouldSaveRewritesHistory($flag)
    {
        $this->_saveRewritesHistory = (bool) $flag;
        return $this;
    }

    /**
     * Indicate whether to save URL Rewrite History or not (create redirects to old URLs)
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $storeId Store View
     * @return bool
     */
    public function getShouldSaveRewritesHistory($storeId = null)
    {
        return $this->_saveRewritesHistory ?? Mage::helper('catalog')->shouldSaveUrlRewritesHistory($storeId);
    }

    /**
     * Refresh all rewrite urls for some store or for all stores
     * Used to make full reindexing of url rewrites
     *
     * @param int $storeId
     * @return $this
     */
    public function refreshRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshRewrites($store->getId());
            }
            return $this;
        }

        $this->clearStoreInvalidRewrites($storeId);
        $store = $this->getStores($storeId);
        if ($store instanceof Mage_Core_Model_Store) {
            $this->refreshCategoryRewrite($store->getRootCategoryId(), $storeId, false);
        }
        $this->refreshProductRewrites($storeId);
        $this->getResource()->clearCategoryProduct($storeId);

        return $this;
    }

    /**
     * Refresh category rewrite
     *
     * @param string $parentPath
     * @param bool $refreshProducts
     * @return $this
     */
    protected function _refreshCategoryRewrites(Varien_Object $category, $parentPath = null, $refreshProducts = true)
    {
        if ($category->getId() != $this->getStores($category->getStoreId())->getRootCategoryId()) {
            $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $category->getStoreId());
            $urlKey = $category->getUrlKey() == '' ? $category->getName() : $category->getUrlKey();
            $urlKey = $this->getCategoryModel()->setLocale($locale)->formatUrlKey($urlKey);
            $idPath      = $this->generatePath('id', null, $category);
            $targetPath  = $this->generatePath('target', null, $category);
            $requestPath = $this->getCategoryRequestPath($category, $parentPath);
            $rewriteData = [
                'store_id'      => $category->getStoreId(),
                'category_id'   => $category->getId(),
                'product_id'    => null,
                'id_path'       => $idPath,
                'request_path'  => $requestPath,
                'target_path'   => $targetPath,
                'is_system'     => 1,
            ];
            $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);
            if ($this->getShouldSaveRewritesHistory($category->getStoreId())) {
                $this->_saveRewriteHistory($rewriteData, $this->_rewrite);
            }
            if ($category->getUrlKey() != $urlKey) {
                $category->setUrlKey($urlKey);
                $this->getResource()->saveCategoryAttribute($category, 'url_key');
            }
            if ($category->getUrlPath() != $requestPath) {
                $category->setUrlPath($requestPath);
                $this->getResource()->saveCategoryAttribute($category, 'url_path');
            }
        } elseif ($category->getUrlPath() != '') {
            $category->setUrlPath('');
            $this->getResource()->saveCategoryAttribute($category, 'url_path');
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
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category)
    {
        if ($category->getId() == $category->getPath()) {
            return $this;
        }

        $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $product->getStoreId());
        $urlKey = $product->getUrlKey() == '' ? $product->getName() : $product->getUrlKey();
        $urlKey = $this->getProductModel()->setLocale($locale)->formatUrlKey($urlKey);

        $idPath      = $this->generatePath('id', $product, $category);
        $targetPath  = $this->generatePath('target', $product, $category);
        $requestPath = $this->getProductRequestPath($product, $category);

        $categoryId = null;
        $updateKeys = true;
        if ($category->getLevel() > 1) {
            $categoryId = $category->getId();
            $updateKeys = false;
        }

        $rewriteData = [
            'store_id'      => $category->getStoreId(),
            'category_id'   => $categoryId,
            'product_id'    => $product->getId(),
            'id_path'       => $idPath,
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
            'is_system'     => 1,
        ];

        $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);

        if ($this->getShouldSaveRewritesHistory($category->getStoreId())) {
            $this->_saveRewriteHistory($rewriteData, $this->_rewrite);
        }

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
     * Refresh products for category
     *
     * @return $this
     */
    protected function _refreshCategoryProductRewrites(Varien_Object $category)
    {
        $originalRewrites = $this->_rewrites;
        $process = true;
        $lastEntityId = 0;
        $firstIteration = true;
        while ($process == true) {
            $products = $this->getResource()->getProductsByCategory($category, $lastEntityId);
            if (!$products) {
                if ($firstIteration) {
                    $this->getResource()->deleteCategoryProductStoreRewrites(
                        $category->getId(),
                        [],
                        $category->getStoreId(),
                    );
                }
                $process = false;
                break;
            }

            // Prepare rewrites for generation
            $rootCategory = $this->getStoreRootCategory($category->getStoreId());
            $categoryIds = [$category->getId(), $rootCategory->getId()];
            $this->_rewrites = $this->getResource()->prepareRewrites(
                $category->getStoreId(),
                $categoryIds,
                array_keys($products),
            );

            foreach ($products as $product) {
                // Product always must have rewrite in root category
                $this->_refreshProductRewrite($product, $rootCategory);
                $this->_refreshProductRewrite($product, $category);
            }
            $firstIteration = false;
            unset($products);
        }
        $this->_rewrites = $originalRewrites;
        return $this;
    }

    /**
     * Refresh category and children rewrites
     * Called when reindexing all rewrites and as a reaction on category change that affects rewrites
     *
     * @param int $categoryId
     * @param int|null $storeId
     * @param bool $refreshProducts
     * @return $this
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

        // Load all children and refresh all categories
        $category = $this->getResource()->loadCategoryChilds($category);
        $categoryIds = [$category->getId()];
        if ($category->getAllChilds()) {
            $categoryIds = array_merge($categoryIds, array_keys($category->getAllChilds()));
        }
        $this->_rewrites = $this->getResource()->prepareRewrites($storeId, $categoryIds);
        $this->_refreshCategoryRewrites($category, null, $refreshProducts);

        unset($category);
        $this->_rewrites = [];

        return $this;
    }

    /**
     * Refresh product rewrite urls for one store or all stores
     * Called as a reaction on product change that affects rewrites
     *
     * @param int $productId
     * @param int|null $storeId
     * @return $this
     */
    public function refreshProductRewrite($productId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshProductRewrite($productId, $store->getId());
            }
            return $this;
        }

        $product = $this->getResource()->getProduct($productId, $storeId);
        if ($product) {
            $store = $this->getStores($storeId);
            $storeRootCategoryId = $store->getRootCategoryId();

            // List of categories the product is assigned to, filtered by being within the store's categories root
            $categories = $this->getResource()->getCategories($product->getCategoryIds(), $storeId) ?: [];
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, '', $productId);

            // Add rewrites for all needed categories
            // If product is assigned to any of store's categories -
            // we also should use store root category to create root product url rewrite
            if (!isset($categories[$storeRootCategoryId])) {
                $categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);
            }

            // Create product url rewrites
            foreach ($categories as $category) {
                $this->_refreshProductRewrite($product, $category);
            }

            // Remove all other product rewrites created earlier for this store - they're invalid now
            $excludeCategoryIds = array_keys($categories);
            $this->getResource()->clearProductRewrites($productId, $storeId, $excludeCategoryIds);

            unset($categories);
            unset($product);
        } else {
            // Product doesn't belong to this store - clear all its url rewrites including root one
            $this->getResource()->clearProductRewrites($productId, $storeId, []);
        }

        return $this;
    }

    /**
     * Refresh all product rewrites for designated store
     *
     * @param int|null $storeId
     * @return $this
     */
    public function refreshProductRewrites($storeId)
    {
        $store = $this->getStores($storeId);
        if (!$store instanceof Mage_Core_Model_Store) {
            return $this;
        }

        $this->_categories      = [];
        $storeRootCategoryId    = $store->getRootCategoryId();
        $storeRootCategoryPath  = $store->getRootCategoryPath();
        $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;

        while ($process == true) {
            $products = $this->getResource()->getProductsByStore($storeId, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, false, array_keys($products));

            $loadCategories = [];
            foreach ($products as $product) {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (!isset($this->_categories[$categoryId])) {
                        $loadCategories[$categoryId] = $categoryId;
                    }
                }
            }

            if ($loadCategories) {
                $categories = $this->getResource()->getCategories($loadCategories, $storeId) ?: [];
                foreach ($categories as $category) {
                    $this->_categories[$category->getId()] = $category;
                }
            }

            foreach ($products as $product) {
                $this->_refreshProductRewrite($product, $this->_categories[$storeRootCategoryId]);
                foreach ($product->getCategoryIds() as $categoryId) {
                    if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
                        if (!str_starts_with($this->_categories[$categoryId]['path'], $storeRootCategoryPath . '/')) {
                            continue;
                        }
                        $this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
                    }
                }
            }

            unset($products);
            $this->_rewrites = [];
        }

        $this->_categories = [];
        return $this;
    }

    /**
     * Deletes old rewrites for store, left from the times when store had some other root category
     *
     * @param int $storeId
     * @return $this
     */
    public function clearStoreInvalidRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->clearStoreInvalidRewrites($store->getId());
            }
            return $this;
        }

        $this->getResource()->clearStoreInvalidRewrites($storeId);
        return $this;
    }

    /**
     * Get requestPath that was not used yet.
     *
     * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
     *
     * @deprecated use $this->getUnusedPathByUrlKey() instead
     * @param int $storeId
     * @param string $requestPath
     * @param string $idPath
     * @return string
     */
    public function getUnusedPath($storeId, $requestPath, $idPath)
    {
        return $this->getUnusedPathByUrlKey($storeId, $requestPath, $idPath, '');
    }

    /**
     * Get requestPath that was not used yet.
     *
     * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $idPath
     * @param string $urlKey
     * @return string
     */
    public function getUnusedPathByUrlKey($storeId, $requestPath, $idPath, $urlKey)
    {
        if (str_contains($idPath, 'product')) {
            $suffix = $this->getProductUrlSuffix($storeId);
        } else {
            $suffix = $this->getCategoryUrlSuffix($storeId);
        }
        if (empty($requestPath)) {
            $requestPath = '-';
        } elseif ($requestPath == $suffix) {
            $requestPath = '-' . $suffix;
        }

        /**
         * Validate maximum length of request path
         */
        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            if ($this->_rewrites[$idPath]->getRequestPath() == $requestPath) {
                return $requestPath;
            }
        } else {
            $this->_rewrite = null;
        }

        $rewrite = $this->getResource()->getRewriteByRequestPath($requestPath, $storeId);
        if ($rewrite && $rewrite->getId()) {
            if ($rewrite->getIdPath() == $idPath) {
                $this->_rewrite = $rewrite;
                return $requestPath;
            }
            // match request_url abcdef1234(-12)(.html) pattern
            $match = [];
            $regularExpression = '#(?P<prefix>(.*/)?' . preg_quote($urlKey, '#') . ')(-(?P<increment>[0-9]+))?(?P<suffix>'
                . preg_quote($suffix, '#') . ')?$#i';
            if (!preg_match($regularExpression, $requestPath, $match)) {
                return $this->getUnusedPathByUrlKey($storeId, '-', $idPath, $urlKey);
            }
            $match['prefix'] = $match['prefix'] . '-';
            $match['suffix'] = $match['suffix'] ?? '';

            $lastRequestPath = $this->getResource()
                ->getLastUsedRewriteRequestIncrement($match['prefix'], $match['suffix'], $storeId);
            if ($lastRequestPath) {
                $match['increment'] = $lastRequestPath;
            }
            return $match['prefix']
                . (!empty($match['increment']) ? ((int) $match['increment'] + 1) : '1')
                . $match['suffix'];
        } else {
            return $requestPath;
        }
    }

    /**
     * Retrieve product rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getProductUrlSuffix($storeId)
    {
        return Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId)
    {
        return Mage::helper('catalog/category')->getCategoryUrlSuffix($storeId);
    }

    /**
     * Get unique category request path
     *
     * @param Varien_Object|Mage_Catalog_Model_Category $category
     * @param string $parentPath
     * @return string
     */
    public function getCategoryRequestPath($category, $parentPath)
    {
        $storeId = $category->getStoreId();
        $idPath  = $this->generatePath('id', null, $category);

        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();
        }

        $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $category->getStoreId());
        $urlKey = $category->getUrlKey() == '' ? $category->getName() : $category->getUrlKey();
        $urlKey = $this->getCategoryModel()->setLocale($locale)->formatUrlKey($urlKey);

        $categoryUrlSuffix = $this->getCategoryUrlSuffix($storeId);
        if ($parentPath === null) {
            $parentPath = $this->getResource()->getCategoryParentPath($category);
        } elseif ($parentPath == '/') {
            $parentPath = '';
        }
        $parentPath = Mage::helper('catalog/category')->getCategoryUrlPath($parentPath, true, $storeId);

        $requestPath = $parentPath . $urlKey;
        $regexp = '/^' . preg_quote($requestPath, '/') . '(\-[0-9]+)?' . preg_quote($categoryUrlSuffix, '/') . '$/i';
        if (isset($existingRequestPath) && preg_match($regexp, $existingRequestPath)) {
            return $existingRequestPath;
        }

        $fullPath = $requestPath . $categoryUrlSuffix;
        if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
            return $requestPath;
        }

        return $this->getUnusedPathByUrlKey($storeId, $fullPath, $this->generatePath('id', null, $category), $urlKey);
    }

    /**
     * Check if current generated request path is one of the old paths
     *
     * @param string $requestPath
     * @param string $idPath
     * @param int $storeId
     * @return bool
     */
    protected function _deleteOldTargetPath($requestPath, $idPath, $storeId)
    {
        $finalOldTargetPath = $this->getResource()->findFinalTargetPath($requestPath, $storeId);
        if ($finalOldTargetPath && $finalOldTargetPath == $idPath) {
            $this->getResource()->deleteRewriteRecord($requestPath, $storeId, true);
            return true;
        }

        return false;
    }

    /**
     * Get unique product request path
     *
     * @param Varien_Object|Mage_Catalog_Model_Product $product
     * @param Varien_Object|Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getProductRequestPath($product, $category)
    {
        $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $product->getStoreId());
        $urlKey = $product->getUrlKey() == '' ? $product->getName() : $product->getUrlKey();
        $urlKey = $this->getProductModel()->setLocale($locale)->formatUrlKey($urlKey);

        $storeId = $category->getStoreId();
        $suffix  = $this->getProductUrlSuffix($storeId);
        $idPath  = $this->generatePath('id', $product, $category);
        /**
         * Prepare product base request path
         */
        if ($category->getLevel() > 1) {
            // To ensure, that category has path either from attribute or generated now
            $this->_addCategoryUrlPath($category);
            $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath(
                $category->getUrlPath(),
                false,
                $storeId,
            );
            $requestPath = $categoryUrl . '/' . $urlKey;
        } else {
            $requestPath = $urlKey;
        }

        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        $this->_rewrite = null;
        /**
         * Check $requestPath should be unique
         */
        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();

            $regexp = '/^' . preg_quote($requestPath, '/') . '(\-[0-9]+)?' . preg_quote($suffix, '/') . '$/i';
            if (preg_match($regexp, $existingRequestPath)) {
                return $existingRequestPath;
            }

            $existingRequestPath = preg_replace('/' . preg_quote($suffix, '/') . '$/', '', $existingRequestPath);
            /**
             * Check if existing request past can be used
             */
            if ($product->getUrlKey() == '' && !empty($requestPath)
                && str_starts_with($existingRequestPath, $requestPath)
            ) {
                $existingRequestPath = preg_replace(
                    '/^' . preg_quote($requestPath, '/') . '/',
                    '',
                    $existingRequestPath,
                );
                if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
                    return $this->_rewrites[$idPath]->getRequestPath();
                }
            }

            $fullPath = $requestPath . $suffix;
            if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
                return $fullPath;
            }
        }
        /**
         * Check 2 variants: $requestPath and $requestPath . '-' . $productId
         */
        $validatedPath = $this->getResource()->checkRequestPaths(
            [$requestPath . $suffix, $requestPath . '-' . $product->getId() . $suffix],
            $storeId,
        );

        if ($validatedPath) {
            return $validatedPath;
        }
        /**
         * Use unique path generator
         */
        return $this->getUnusedPathByUrlKey($storeId, $requestPath . $suffix, $idPath, $urlKey);
    }

    /**
     * Generate either id path, request path or target path for product and/or category
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     * $parentPath used only for generating category path
     *
     * @param string $type
     * @param Varien_Object|Mage_Catalog_Model_Product $product
     * @param Varien_Object|Mage_Catalog_Model_Category $category
     * @param string $parentPath
     * @return string
     * @throws Mage_Core_Exception
     */
    public function generatePath($type = 'target', $product = null, $category = null, $parentPath = null)
    {
        if (!$product && !$category) {
            Mage::throwException(Mage::helper('core')->__('Please specify either a category or a product, or both.'));
        }

        // generate id_path
        if ($type === 'id') {
            if (!$product) {
                return 'category/' . $category->getId();
            }
            if ($category && $category->getLevel() > 1) {
                return 'product/' . $product->getId() . '/' . $category->getId();
            }
            return 'product/' . $product->getId();
        }

        // generate request_path
        if ($type === 'request') {
            // for category
            if (!$product) {
                $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $category->getStoreId());
                $urlKey = $category->getUrlKey() == '' ? $category->getName() : $category->getUrlKey();
                $urlKey = $this->getCategoryModel()->setLocale($locale)->formatUrlKey($urlKey);

                $categoryUrlSuffix = $this->getCategoryUrlSuffix($category->getStoreId());
                if ($parentPath === null) {
                    $parentPath = $this->getResource()->getCategoryParentPath($category);
                } elseif ($parentPath == '/') {
                    $parentPath = '';
                }
                $parentPath = Mage::helper('catalog/category')->getCategoryUrlPath(
                    $parentPath,
                    true,
                    $category->getStoreId(),
                );

                return $this->getUnusedPathByUrlKey(
                    $category->getStoreId(),
                    $parentPath . $urlKey . $categoryUrlSuffix,
                    $this->generatePath('id', null, $category),
                    $urlKey,
                );
            }

            // for product & category
            if (!$category) {
                Mage::throwException(Mage::helper('core')->__('A category object is required for determining the product request path.')); // why?
            }

            $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $product->getStoreId());
            $urlKey = $product->getUrlKey() == '' ? $product->getName() : $product->getUrlKey();
            $urlKey = $this->getProductModel()->setLocale($locale)->formatUrlKey($urlKey);

            $productUrlSuffix  = $this->getProductUrlSuffix($category->getStoreId());
            if ($category->getLevel() > 1) {
                // To ensure, that category has url path either from attribute or generated now
                $this->_addCategoryUrlPath($category);
                $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath(
                    $category->getUrlPath(),
                    false,
                    $category->getStoreId(),
                );
                return $this->getUnusedPathByUrlKey(
                    $category->getStoreId(),
                    $categoryUrl . '/' . $urlKey . $productUrlSuffix,
                    $this->generatePath('id', $product, $category),
                    $urlKey,
                );
            }

            // for product only
            return $this->getUnusedPathByUrlKey(
                $category->getStoreId(),
                $urlKey . $productUrlSuffix,
                $this->generatePath('id', $product),
                $urlKey,
            );
        }

        // generate target_path
        if (!$product) {
            return 'catalog/category/view/id/' . $category->getId();
        }
        if ($category && $category->getLevel() > 1) {
            return 'catalog/product/view/id/' . $product->getId() . '/category/' . $category->getId();
        }
        return 'catalog/product/view/id/' . $product->getId();
    }

    /**
     * Return unique string based on the time in microseconds.
     *
     * @return string
     */
    public function generateUniqueIdPath()
    {
        return str_replace('0.', '', str_replace(' ', '_', microtime()));
    }

    /**
     * Create Custom URL Rewrite for old product/category URL after url_key changed
     * It will perform permanent redirect from old URL to new URL
     *
     * @param array $rewriteData New rewrite data
     * @param Varien_Object $rewrite Rewrite model
     * @return $this
     */
    protected function _saveRewriteHistory($rewriteData, $rewrite)
    {
        if ($rewrite instanceof Varien_Object && $rewrite->getId()) {
            $rewriteData['target_path'] = $rewriteData['request_path'];
            $rewriteData['request_path'] = $rewrite->getRequestPath();
            $rewriteData['id_path'] = $this->generateUniqueIdPath();
            $rewriteData['is_system'] = 0;
            $rewriteData['options'] = 'RP'; // Redirect = Permanent
            $this->getResource()->saveRewriteHistory($rewriteData);
        }

        return $this;
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        return $this->getSlugger()->slug($str)->lower()->toString();
    }

    public function getSlugger(): AsciiSlugger
    {
        $locale = $this->getLocale();
        if (is_null($this->slugger) || !array_key_exists($locale, $this->slugger)) {
            $config = $this->getSluggerConfig($locale);
            $slugger = new AsciiSlugger('en', $config);
            $slugger->setLocale($locale);

            $this->slugger[$locale] = $slugger;
        }

        return $this->slugger[$locale];
    }

    final public function getSluggerConfig(?string $locale): array
    {
        $config = Mage::helper('catalog/product_url')->getConvertTableShort();

        if ($locale) {
            $convertNode = Mage::getConfig()->getNode('default/url/convert/' . $locale);
            if ($convertNode instanceof Mage_Core_Model_Config_Element) {
                $localeConfig = [];
                /** @var Mage_Core_Model_Config_Element $node */
                foreach ($convertNode->children() as $node) {
                    if (property_exists($node, 'from') && property_exists($node, 'to')) {
                        $localeConfig[(string) $node->from] = (string) $node->to;
                    }
                }
                $config = [$locale => $config + $localeConfig];
            }
        }

        return $config;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @return $this
     */
    public function setLocale(?string $locale)
    {
        $this->locale = $locale;
        return $this;
    }
}
