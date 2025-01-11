<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Category|Mage_Catalog_Model_Resource_Category_Flat _getResource()
 * @method Mage_Catalog_Model_Resource_Category|Mage_Catalog_Model_Resource_Category_Flat getResource()
 * @method Mage_Catalog_Model_Resource_Category_Collection getCollection()
 *
 * @method array getAffectedCategoryIds()
 * @method $this setAffectedCategoryIds(array $categoryIds)
 * @method array getAffectedProductIds()
 * @method $this setAffectedProductIds(array $productIds)
 * @method $this setAttributeSetId(int $value)
 *
 * @method string getCategoryPath()
 * @method string getCategoryUrl()
 * @method $this setChildrenCount(int $value)
 * @method int getChildrenCount()
 * @method bool getCustomUseParentSettings()
 *
 * @method $this setDeletedChildrenIds(array $value)
 * @method bool getDisableFlat()
 * @method string getDisplayMode()
 * @method $this setDisplayMode(string $value)
 *
 * @method string getImage()
 * @method $this setIncludeInMenu(int $value)
 * @method bool getInitialSetupFlag()
 * @method $this setInitialSetupFlag(bool $value)
 * @method int getIsActive()
 * @method $this setIsActive(int $value)
 * @method int getIsAnchor()
 * @method $this setIsAnchor(int $value)
 * @method $this setIsChangedProductList(bool $bool)
 *
 * @method int getLandingPage()
 * @method bool hasLevel()
 * @method $this setLevel(int $value)
 *
 * @method string getMetaDescription()
 * @method string getMetaKeywords()
 * @method string getMetaTitle()
 * @method int getMovedCategoryId()
 *
 * @method $this setMovedCategoryId(int $value)
 * @method $this setName(string $value)
 *
 * @method bool hasProductCount()
 * @method $this setParentId(int $value)
 * @method string getPath()
 * @method $this setPath(string|int $value)
 * @method bool getProductsReadonly()
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method array getPostedProducts()
 * @method $this setPostedProducts(array $value)
 *
 * @method string getUrlKey()
 * @method $this setUrlKey(string $value)
 * @method $this setUrlPath(string $value)
 */
class Mage_Catalog_Model_Category extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    public const ENTITY                = 'catalog_category';
    /**
     * Category display modes
     */
    public const DM_PRODUCT            = 'PRODUCTS';
    public const DM_PAGE               = 'PAGE';
    public const DM_MIXED              = 'PRODUCTS_AND_PAGE';
    public const TREE_ROOT_ID          = 1;

    public const CACHE_TAG             = 'catalog_category';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix     = 'catalog_category';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject     = 'category';

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag        = self::CACHE_TAG;

    /**
     * URL Model instance
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_url;

    /**
     * URL rewrite model
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected static $_urlRewrite;

    /**
     * Use flat resource model flag
     *
     * @var bool
     */
    protected $_useFlatResource = false;

    /**
     * Category design attributes
     *
     * @var array
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private $_designAttributes  = [
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'page_layout',
        'custom_layout_update',
        'custom_apply_to_products',
    ];

    /**
     * Category tree model
     *
     * @var Mage_Catalog_Model_Resource_Category_Tree|null
     */
    protected $_treeModel = null;

    /**
     * Category Url instance
     *
     * @var Mage_Catalog_Model_Category_Url
     */
    protected $_urlModel;


    protected ?string $locale = null;

    /**
     * Initialize resource mode
     */
    protected function _construct()
    {
        // If Flat Data enabled then use it but only on frontend
        /** @var Mage_Catalog_Helper_Category_Flat $flatHelper */
        $flatHelper = Mage::helper('catalog/category_flat');
        if ($flatHelper->isAccessible() && !Mage::app()->getStore()->isAdmin() && $flatHelper->isBuilt(true)
            && !$this->getDisableFlat()
        ) {
            $this->_init('catalog/category_flat');
            $this->_useFlatResource = true;
        } else {
            $this->_init('catalog/category');
        }
    }

    /**
     * Retrieve URL instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }

    /**
     * Get url rewrite model
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (!self::$_urlRewrite) {
            self::$_urlRewrite = Mage::getSingleton('core/factory')->getUrlRewriteInstance();
        }
        return self::$_urlRewrite;
    }

    /**
     * Retrieve category tree model
     *
     * @return Mage_Catalog_Model_Resource_Category_Tree
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('catalog/category_tree');
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Tree
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('catalog/category_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move category
     *
     * @param   int $parentId new parent category id
     * @param   int $afterCategoryId category id after which we have put current category
     * @return  Mage_Catalog_Model_Category
     */
    public function move($parentId, $afterCategoryId)
    {
        /**
         * Validate new parent category id. (category model is used for backward
         * compatibility in event params)
         */
        $parent = Mage::getModel('catalog/category')
            ->setStoreId($this->getStoreId())
            ->load($parentId);

        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('catalog')->__('Category move operation is not possible: the new parent category was not found.'),
            );
        }

        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('catalog')->__('Category move operation is not possible: the current category was not found.'),
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('catalog')->__('Category move operation is not possible: parent category is equal to child category.'),
            );
        }

        /**
         * Setting affected category ids for third party engine index refresh
        */
        $this->setMovedCategoryId($this->getId());

        $eventParams = [
            $this->_eventObject => $this,
            'parent'        => $parent,
            'category_id'   => $this->getId(),
            'prev_parent_id' => $this->getParentId(),
            'parent_id'     => $parentId,
        ];
        $moveComplete = false;

        $this->_getResource()->beginTransaction();
        try {
            /**
             * catalog_category_tree_move_before and catalog_category_tree_move_after
             * events declared for backward compatibility
             */
            Mage::dispatchEvent('catalog_category_tree_move_before', $eventParams);
            Mage::dispatchEvent($this->_eventPrefix . '_move_before', $eventParams);

            $this->getResource()->changeParent($this, $parent, $afterCategoryId);

            Mage::dispatchEvent($this->_eventPrefix . '_move_after', $eventParams);
            Mage::dispatchEvent('catalog_category_tree_move_after', $eventParams);

            // Set data for indexer
            $this->setAffectedCategoryIds([$this->getId(), $this->getParentId(), $parentId]);

            $moveComplete = true;

            $this->_getResource()->commit();
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::dispatchEvent('category_move', $eventParams);
            Mage::getSingleton('index/indexer')->processEntityAction(
                $this,
                self::ENTITY,
                Mage_Index_Model_Event::TYPE_SAVE,
            );
            Mage::app()->cleanCache([self::CACHE_TAG]);
        }

        return $this;
    }

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * Get category products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        return Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter($this);
    }

    /**
     * Retrieve all customer attributes
     *
     * @param bool $noDesignAttributes
     * @return array
     * @todo Use with Flat Resource
     */
    public function getAttributes($noDesignAttributes = false)
    {
        $result = $this->getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();

        if ($noDesignAttributes) {
            foreach (array_keys($result) as $k) {
                if (in_array($k, $this->_designAttributes)) {
                    unset($result[$k]);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve array of product id's for category
     *
     * array($productId => $position)
     *
     * @return array
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('products_position');
        if (is_null($array)) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of store ids for category
     *
     * @return array
     */
    public function getStoreIds()
    {
        if ($this->getInitialSetupFlag()) {
            return [];
        }

        if ($storeIds = $this->getData('store_ids')) {
            return $storeIds;
        }

        if (!$this->getId()) {
            return [];
        }

        $nodes = [];
        foreach ($this->getPathIds() as $id) {
            $nodes[] = $id;
        }

        $storeIds = [];
        $storeCollection = Mage::getModel('core/store')->getCollection()->loadByCategoryIds($nodes);
        /** @var Mage_Core_Model_Store $store */
        foreach ($storeCollection as $store) {
            $storeId = $store->getId();
            $storeIds[$storeId] = $storeId;
        }

        $entityStoreId = $this->getStoreId();
        if (!in_array($entityStoreId, $storeIds)) {
            array_unshift($storeIds, $entityStoreId);
        }
        if (!in_array(0, $storeIds)) {
            array_unshift($storeIds, 0);
        }

        $this->setData('store_ids', $storeIds);
        return $storeIds;
    }

    /**
     * Retrieve Layout Update Handle name
     *
     * @return string
     */
    public function getLayoutUpdateHandle()
    {
        $layout = 'catalog_category_';
        if ($this->getIsAnchor()) {
            $layout .= 'layered';
        } else {
            $layout .= 'default';
        }
        return $layout;
    }

    /**
     * Return store id.
     *
     * If store id is undefined for category return current active store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return (int) $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Set store id
     *
     * @param string|int|Mage_Core_Model_Store $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        if (!is_numeric($storeId)) {
            $storeId = Mage::app()->getStore($storeId)->getId();
        }
        $this->setData('store_id', $storeId);
        $this->getResource()->setStoreId($storeId);
        return $this;
    }

    /**
     * Get category url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getUrlModel()->getCategoryUrl($this);
    }

    /**
     * Get product url model
     *
     * @return Mage_Catalog_Model_Category_Url
     */
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('catalog/factory')->getCategoryUrlInstance();
        }
        return $this->_urlModel;
    }

    /**
     * Retrieve category id URL
     *
     * @return string
     */
    public function getCategoryIdUrl()
    {
        Varien_Profiler::start('REGULAR: ' . __METHOD__);
        $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $this->getStoreId());
        $urlKey = $this->getUrlKey() ? $this->getUrlKey() : $this->setLocale($locale)->formatUrlKey($this->getName());
        $url = $this->getUrlInstance()->getUrl('catalog/category/view', [
            's'  => $urlKey,
            'id' => $this->getId(),
        ]);
        Varien_Profiler::stop('REGULAR: ' . __METHOD__);
        return $url;
    }

    /**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        return $this->getUrlModel()->setLocale($this->getLocale())->formatUrlKey($str);
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Retrieve image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = false;
        if ($image = $this->getImage()) {
            $url = Mage::getBaseUrl('media') . 'catalog/category/' . $image;
        }
        return $url;
    }

    /**
     * Retrieve URL path
     *
     * @return string
     */
    public function getUrlPath()
    {
        $path = $this->getData('url_path');
        if ($path) {
            return $path;
        }

        $path = $this->getUrlKey();

        if ($this->getParentId()) {
            $parentPath = Mage::getModel('catalog/category')->load($this->getParentId())->getCategoryPath();
            $path = $parentPath . '/' . $path;
        }

        $this->setUrlPath($path);

        return $path;
    }

    /**
     * Get parent category object
     *
     * @return $this
     */
    public function getParentCategory()
    {
        if (!$this->hasData('parent_category')) {
            $this->setData('parent_category', Mage::getModel('catalog/category')->load($this->getParentId()));
        }
        return $this->_getData('parent_category');
    }

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return (int) array_pop($parentIds);
    }

    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * Retrieve dates for custom design (from & to)
     *
     * @return array
     */
    public function getCustomDesignDate()
    {
        $result = [];
        $result['from'] = $this->getData('custom_design_from');
        $result['to'] = $this->getData('custom_design_to');

        return $result;
    }

    /**
     * Retrieve design attributes array
     *
     * @return array
     */
    public function getDesignAttributes()
    {
        $result = [];
        foreach ($this->_designAttributes as $attrName) {
            $result[] = $this->_getAttribute($attrName);
        }
        return $result;
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private function _getAttribute($attributeCode)
    {
        if (!$this->_useFlatResource) {
            $attribute = $this->getResource()->getAttribute($attributeCode);
        } else {
            $attribute = Mage::getSingleton('catalog/config')
                ->getAttribute(self::ENTITY, $attributeCode);
        }
        return $attribute;
    }

    /**
     * Get all children categories IDs
     *
     * @param bool $asArray return result as array instead of comma-separated list of IDs
     * @return array|string
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Retrieve children ids comma separated
     *
     * @return string
     */
    public function getChildren()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * Retrieve Stores where isset category Path
     * Return comma separated string
     *
     * @return string
     */
    public function getPathInStore()
    {
        $result = [];
        //$path = $this->getTreeModelInstance()->getPath($this->getId());
        $path = array_reverse($this->getPathIds());
        foreach ($path as $itemId) {
            if ($itemId == Mage::app()->getStore()->getRootCategoryId()) {
                break;
            }
            $result[] = $itemId;
        }
        return implode(',', $result);
    }

    /**
     * Check category id existing
     *
     * @param   int $id
     * @return  bool
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array categories ids which are part of category path
     * Result array contain id of current category because it is part of the path
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', (string) $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', (string) $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify category ids
     *
     * @return array
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * Retrieve Is Category has children flag
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * Retrieve Request Path
     *
     * @return string
     */
    public function getRequestPath()
    {
        if (!$this->_getData('request_path')) {
            $this->getUrl();
        }
        return $this->_getData('request_path');
    }

    /**
     * Retrieve Name data wrapper
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Before delete process
     *
     * @inheritDoc
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException("Can't delete root category.");
        }
        return parent::_beforeDelete();
    }

    /**
     * Retrieve anchors above
     *
     * @return array
     */
    public function getAnchorsAbove()
    {
        $anchors = [];
        $path = $this->getPathIds();

        if (in_array($this->getId(), $path)) {
            unset($path[array_search($this->getId(), $path)]);
        }

        if ($this->_useFlatResource) {
            $anchors = $this->_getResource()->getAnchorsAbove($path, $this->getStoreId());
        } else {
            if (!Mage::registry('_category_is_anchor_attribute')) {
                $model = $this->_getAttribute('is_anchor');
                Mage::register('_category_is_anchor_attribute', $model);
            }

            if ($isAnchorAttribute = Mage::registry('_category_is_anchor_attribute')) {
                $anchors = $this->getResource()->findWhereAttributeIs($path, $isAnchorAttribute, 1);
            }
        }
        return $anchors;
    }

    /**
     * Retrieve count products of category
     *
     * @return int
     */
    public function getProductCount()
    {
        if (!$this->hasProductCount()) {
            $count = $this->_getResource()->getProductCount($this); // load product count
            $this->setData('product_count', $count);
        }
        return $this->getData('product_count');
    }

    /**
     * Retrieve categories by parent
     *
     * @param int $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return array|Mage_Catalog_Model_Resource_Category_Collection|Varien_Data_Collection|Varien_Data_Tree_Node_Collection
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->getResource()
            ->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent categories of current category
     *
     * @return Mage_Catalog_Model_Category[]
     */
    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this);
    }

    /**
     * Return children categories of current category
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getChildrenCategories()
    {
        return $this->getResource()->getChildrenCategories($this);
    }

    /**
     * Return children categories of current category
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getChildrenCategoriesWithInactive()
    {
        return $this->getResource()->getChildrenCategoriesWithInactive($this);
    }

    /**
     * Return parent category of current category with own custom design settings
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getParentDesignCategory()
    {
        return $this->getResource()->getParentDesignCategory($this);
    }

    /**
     * Check category is in Root Category list
     *
     * @return bool
     */
    public function isInRootCategoryList()
    {
        return $this->getResource()->isInRootCategoryList($this);
    }

    /**
     * Retrieve Available int Product Listing sort by
     *
     * @return null|array
     */
    public function getAvailableSortBy()
    {
        $available = $this->getData('available_sort_by');
        if (empty($available)) {
            return [];
        }
        if ($available && !is_array($available)) {
            $available = explode(',', $available);
        }
        return $available;
    }

    /**
     * Retrieve Available Product Listing  Sort By
     * code as key, value - name
     *
     * @return array
     */
    public function getAvailableSortByOptions()
    {
        $availableSortBy = [];
        $defaultSortBy   = Mage::getSingleton('catalog/config')
            ->getAttributeUsedForSortByArray();
        if ($this->getAvailableSortBy()) {
            foreach ($this->getAvailableSortBy() as $sortBy) {
                if (isset($defaultSortBy[$sortBy])) {
                    $availableSortBy[$sortBy] = $defaultSortBy[$sortBy];
                }
            }
        }

        if (!$availableSortBy) {
            $availableSortBy = $defaultSortBy;
        }

        return $availableSortBy;
    }

    /**
     * Retrieve Product Listing Default Sort By
     *
     * @return string
     */
    public function getDefaultSortBy()
    {
        $sortBy = $this->getData('default_sort_by');
        $available = $this->getAvailableSortByOptions();

        // When not set or not available use default from system config
        if (!$sortBy || !isset($available[$sortBy])) {
            $sortBy = Mage::getSingleton('catalog/config')
                ->getProductListDefaultSortBy($this->getStoreId());
        }

        // If even the sort from system config not set or unavailable, use the first of available
        if (!$sortBy || !isset($available[$sortBy])) {
            $sortBy = array_keys($available);
            $sortBy = $sortBy[0];
        }

        return $sortBy;
    }

    /**
     * Validate attribute values
     *
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     * @return true|array
     */
    public function validate()
    {
        return $this->_getResource()->validate($this);
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        return $this;
    }
}
