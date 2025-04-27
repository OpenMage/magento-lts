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
 * @copyright  Copyright (c) 2015-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product model
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product _getResource()
 * @method Mage_Catalog_Model_Resource_Product getResource()
 * @method Mage_Catalog_Model_Resource_Product_Collection getCollection()
 *
 * @method $this setAddToCartUrl(string $value)
 * @method bool getAllowedInRss()
 * @method $this setAllowedInRss(bool $value)
 * @method bool getAllowedPriceInRss()
 * @method $this setAllowedPriceInRss(bool $value)
 * @method $this setAffectedCategoryIds(array $value)
 * @method array getAppliedRates()
 * @method $this setAppliedRates(array $value)
 * @method bool getAttributesConfigurationReadonly()
 * @method int getAttributeSetId()
 * @method $this setAttributeSetId(int $value)
 *
 * @method float getBaseRowTotal()
 * @method array getBundleOptionsData()
 * @method $this setBundleOptionsData(array $value)
 * @method array getBundleSelectionsData()
 * @method $this setBundleSelectionsData(array $value)
 *
 * @method bool getCanSaveBundleSelections()
 * @method $this setCanSaveBundleSelections(bool $value)
 * @method bool getCanSaveCustomOptions()
 * @method $this setCanSaveCustomOptions(bool $value)
 * @method bool getCanSaveConfigurableAttributes()
 * @method bool getCanShowPrice()
 * @method bool getCategoriesReadonly()
 * @method $this setCartQty(float $value)
 * @method $this setCategory(Mage_Catalog_Model_Category $value)
 * @method bool hasCategoryIds()
 * @method array getChildAttributeLabelMapping()
 * @method bool hasChildrenProducts()
 * @method Mage_Catalog_Model_Product[] getChildrenProducts()
 * @method $this setChildrenProducts(Mage_Catalog_Model_Product[] $value)
 * @method bool getCompositeReadonly()
 * @method bool hasConfigurableImagesFallbackArray()
 * @method array getConfigurableAttributesData()
 * @method array getConfigurableImagesFallbackArray()
 * @method $this setConfigurableImagesFallbackArray(array $value)
 * @method float getConfigurablePrice()
 * @method $this setConfigurablePrice(float $value)
 * @method array getConfigurableProductsData()
 * @method bool getConfigureMode()
 * @method $this setConfigureMode(bool $value)
 * @method float getCost()
 * @method string getCustomLayoutUpdate()
 * @method bool hasCustomerGroupId()
 * @method int getCustomerGroupId()
 * @method array getCrossSellLinkData()
 * @method $this setCrossSellLinkData(array $value)
 * @method bool hasCrossSellProducts()
 * @method $this setCrossSellProducts(array $value)
 * @method bool hasCrossSellProductIds()
 * @method $this setCrossSellProductIds(array $value)
 * @method $this setCustomerGroupId(int $value)
 *
 * @method int getEntityTypeId()
 * @method $this setExcludeUrlRewrite(bool $value)
 *
 * @method string getDescription()
 * @method bool getDisableAddToCart()
 * @method $this setDisableAddToCart(bool $value)
 * @method array getDownloadableData()
 * @method $this setDownloadableData(array $value)
 * @method Mage_Downloadable_Model_Link[] getDownloadableLinks()
 * @method $this setDownloadableLinks(Mage_Downloadable_Model_Link[] $value)
 * @method bool getDownloadableReadonly()
 * @method Mage_Downloadable_Model_Resource_Sample_Collection getDownloadableSamples()
 * @method $this setDownloadableSamples(Mage_Downloadable_Model_Resource_Sample_Collection $value)
 *
 * @method bool getForceReindexRequired()
 *
 * @method array getGroupedLinkData()
 * @method $this setGroupedLinkData(array $value)
 *
 * @method $this setHasError(bool $value)
 * @method null|bool getHasError()
 * @method bool getHasOptions()
 * @method $this setHasOptions(bool $value)
 *
 * @method string getImage()
 * @method bool getInventoryReadonly()
 * @method bool getIsChangedCategories()
 * @method $this setIsChangedCategories(bool $value)
 * @method bool getIsChangedWebsites()
 * @method $this setIsChangedWebsites(bool $value)
 * @method bool getIsCustomOptionChanged()
 * @method $this setIsCustomOptionChanged(bool $value)
 * @method bool getIsDefault()
 * @method bool getIsRelationsChanged()
 * @method $this setIsRelationsChanged(bool $value)
 * @method bool getIsDuplicate()
 * @method $this setIsDuplicate(bool $value)
 * @method $this setIsQtyDecimal(int $value)
 * @method $this setIsInStock(bool $value)
 * @method bool getIsMassupdate()
 * @method $this setIsMassupdate(bool $value)
 * @method bool hasIsRecurring()
 * @method bool getIsRecurring()
 * @method $this unsRecurringProfile()
 * @method $this setIsSalable(bool $value)
 * @method $this setIsSuperMode(bool $value)
 *
 * @method $this setLinksExist(bool $value)
 * @method bool getLinksPurchasedSeparately()
 * @method $this setLinksPurchasedSeparately(bool $value)
 * @method array getListSwatchAttrValues()
 *
 * @method array getMatchedRules()
 * @method bool hasMediaAttributes()
 * @method $this setMediaAttributes(array $value)
 * @method array getMediaGallery()
 * @method $this setMediaGallery(array $value)
 * @method string getMessage()
 * @method string getMetaDescription()
 * @method string getMetaKeyword()
 * @method string getMetaTitle()
 * @method $this hasMsrpEnabled()
 * @method bool getMsrpEnabled()
 * @method string getMsrpDisplayActualPriceType()
 *
 * @method $this setNeedStoreForReindex(bool $value)
 *
 * @method Mage_Bundle_Model_Option getOption()
 * @method $this setOption(Mage_Bundle_Model_Option $value)
 * @method int getOptionId()
 * @method bool getOptionsReadonly()
 * @method bool hasOptionsValidationFail()
 * @method $this setOptionsValidationFail(bool $value)
 * @method int getOriginalId()
 * @method $this setOriginalId(int $value)
 *
 * @method string getPageLayout()
 * @method bool getParentId()
 * @method $this setParentId(bool $value)
 * @method int getParentProductId()
 * @method array getParentProductIds()
 * @method $this setParentProductIds(array $value)
 * @method int getPopularity()
 * @method string getPosition()
 * @method bool hasPreconfiguredValues()
 * @method $this setPrice(float $value)
 * @method int getPriceType()
 * @method int getProductId()
 * @method array getProductOptions()
 * @method $this setProductOptions(array $value)
 * @method $this setProductTags(Mage_Tag_Model_Resource_Tag_Collection $value)
 * @method $this setProductUrl(string $value)
 *
 * @method $this setQuoteItemPrice(float $value)
 * @method $this setQuoteItemRowTotal(float $value)
 * @method $this setQuoteItemQty(int $value)
 * @method $this setQuoteQty(float $value)
 * @method float getQty()
 * @method $this setQty(float $value)
 *
 * @method $this setRatingSummary(Varien_Object $summary)
 * @method $this setRatingVotes(Mage_Rating_Model_Resource_Rating_Option_Vote_Collection $value)
 * @method string getRealPriceHtml()
 * @method $this setRealPriceHtml(string $value)
 * @method bool getRelatedReadonly()
 * @method $this setRelatedLinkData(array $value)
 * @method array getRecurringProfile()
 * @method array getRelatedLinkData()
 * @method bool hasRelatedProducts()
 * @method $this setRelatedProducts(array $value)
 * @method bool hasRelatedProductIds()
 * @method $this setRelatedProductIds(array $value)
 * @method bool getRequiredOptions()
 * @method $this setRequiredOptions(bool $value)
 * @method string getReviewId()
 *
 * @method string getSamplesTitle()
 * @method bool getSelectionCanChangeQty()
 * @method string getSelectionId()
 * @method string getSelectionPriceType()
 * @method float getSelectionPriceValue()
 * @method float getSelectionQty()
 * @method string getShipmentType()
 * @method string getShortDescription()
 * @method $this setShortDescription(string $value)
 * @method bool getSkipCheckRequiredOption()
 * @method $this setSkipCheckRequiredOption(bool $value)
 * @method $this unsSkipCheckRequiredOption()
 * @method $this setSku(string $value)
 * @method string getSmallImage()
 * @method $this setStatus(int $store)
 * @method bool getStickWithinParent()
 * @method array getStockData()
 * @method $this setStockData(array $value)
 * @method $this setStore(int $store)
 * @method $this setStoreId(int $store)
 * @method bool hasStoreIds()
 * @method $this setStoreIds(array $storeIds)
 * @method array getSwatchPrices()
 *
 * @method int getTaxClassId()
 * @method string getThumbnail()
 * @method float|null getTaxPercent()
 * @method $this setTaxPercent(float|null $value)
 * @method $this setTypeId(int $value)
 * @method bool getTypeHasOptions()
 * @method $this setTypeHasOptions(bool $value)
 * @method bool getTypeHasRequiredOptions()
 * @method $this setTypeHasRequiredOptions(bool $value)
 *
 * @method bool getUpsellReadonly()
 * @method array getUpSellLinkData()
 * @method $this setUpSellLinkData(array $value)
 * @method bool hasUpSellProducts()
 * @method $this setUpSellProducts(array $value)
 * @method bool hasUpSellProductIds()
 * @method $this setUpSellProductIds(array $value)
 * @method bool hasUrlDataObject()
 * @method Varien_Object getUrlDataObject()
 * @method $this setUrlDataObject(Varien_Object $value)
 * @method string getUrlKey()
 * @method $this setUrlKey(string $value)
 *
 * @method $this setUrlPath(string $value)
 * @method int getVisibility()
 * @method $this setVisibility(int $value)
 *
 * @method $this setWebsiteId(int $getWebsiteId)
 * @method bool hasWebsiteIds()
 * @method $this setWebsiteIds(array $value)
 * @method bool getWebsitesReadonly()
 * @method string getWeightType()
 * @method int getWishlistItemId()
 * @method bool hasWishlistStoreId()
 * @method int getWishlistStoreId()
 * @method $this setWishlistStoreId(int $value)
 */
class Mage_Catalog_Model_Product extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    public const ENTITY          = 'catalog_product';
    public const CACHE_TAG       = 'catalog_product';
    protected $_cacheTag         = 'catalog_product';
    protected $_eventPrefix      = 'catalog_product';
    protected $_eventObject      = 'product';
    protected $_canAffectOptions = false;

    /**
     * Product type instance
     *
     * @var Mage_Catalog_Model_Product_Type_Abstract|null|false
     */
    protected $_typeInstance            = null;

    /**
     * Product type instance as singleton
     */
    protected $_typeInstanceSingleton   = null;

    /**
     * Product link instance
     *
     * @var Mage_Catalog_Model_Product_Link|null
     */
    protected $_linkInstance;

    /**
     * Product object customization (not stored in DB)
     *
     * @var array
     */
    protected $_customOptions = [];

    /**
     * Product Url Instance
     *
     * @var Mage_Catalog_Model_Product_Url|null
     */
    protected $_urlModel = null;

    protected static $_url;
    protected static $_urlRewrite;

    protected $_errors = [];

    protected $_optionInstance;

    protected $_options = [];

    /**
     * Product reserved attribute codes
     */
    protected $_reservedAttributes;

    /**
     * Flag for available duplicate function
     *
     * @var bool
     */
    protected $_isDuplicable = true;

    /**
     * Flag for get Price function
     *
     * @var bool
     */
    protected $_calculatePrice = true;

    /**
     * @var Mage_CatalogInventory_Model_Stock_Item|null
     */
    protected $_stockItem;

    /**
     * @var Mage_Review_Model_Review_Summary[]
     */
    protected $_reviewSummary = [];

    protected ?string $locale = null;

    /**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('catalog/product');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return (int) $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Get collection instance
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('catalog')->__('The model collection resource name is not defined.'));
        }
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel($this->_resourceCollectionName);
        $collection->setStoreId($this->getStoreId());
        return $collection;
    }

    /**
     * Get product url model
     *
     * @return Mage_Catalog_Model_Product_Url
     */
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('catalog/factory')->getProductUrlInstance();
        }
        return $this->_urlModel;
    }

    /**
     * Validate Product Data
     *
     * @todo implement full validation process with errors returning which are ignoring now
     *
     * @return $this
     */
    public function validate()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_validate_before', [$this->_eventObject => $this]);
        $this->_getResource()->validate($this);
        Mage::dispatchEvent($this->_eventPrefix . '_validate_after', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Get product name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Get product price through type instance
     *
     * @return float
     */
    public function getPrice()
    {
        if ($this->_calculatePrice || !$this->getData('price')) {
            return $this->getPriceModel()->getPrice($this);
        } else {
            return $this->getData('price');
        }
    }

    /**
     * Set Price calculation flag
     *
     * @param bool $calculate
     * @return $this
     */
    public function setPriceCalculation($calculate = true)
    {
        $this->_calculatePrice = $calculate;
        return $this;
    }

    /**
     * Get product type identifier
     *
     * @return string|null
     */
    public function getTypeId()
    {
        return $this->_getData('type_id');
    }

    /**
     * Get product status
     *
     * @return int
     */
    public function getStatus()
    {
        if (is_null($this->_getData('status'))) {
            $this->setData('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        }
        return $this->_getData('status');
    }

    /**
     * Retrieve type instance
     *
     * Type instance implement type depended logic
     *
     * @param  bool $singleton
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function getTypeInstance($singleton = false)
    {
        if ($singleton === true) {
            if (is_null($this->_typeInstanceSingleton)) {
                $this->_typeInstanceSingleton = Mage::getSingleton('catalog/product_type')
                    ->factory($this, true);
            }
            return $this->_typeInstanceSingleton;
        }

        if ($this->_typeInstance === null) {
            $this->_typeInstance = Mage::getSingleton('catalog/product_type')
                ->factory($this);
        }
        return $this->_typeInstance;
    }

    /**
     * Set type instance for external
     *
     * @param Mage_Catalog_Model_Product_Type_Abstract $instance  Product type instance
     * @param bool                                     $singleton Whether instance is singleton
     * @return $this
     */
    public function setTypeInstance($instance, $singleton = false)
    {
        if ($singleton === true) {
            $this->_typeInstanceSingleton = $instance;
        } else {
            $this->_typeInstance = $instance;
        }
        return $this;
    }

    /**
     * Retrieve link instance
     *
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function getLinkInstance()
    {
        if (!$this->_linkInstance) {
            $this->_linkInstance = Mage::getSingleton('catalog/product_link');
        }
        return $this->_linkInstance;
    }

    /**
     * Retrieve product id by sku
     *
     * @param   string $sku
     * @return  string
     */
    public function getIdBySku($sku)
    {
        return $this->_getResource()->getIdBySku($sku);
    }

    /**
     * Retrieve product category id
     *
     * @return int|false
     */
    public function getCategoryId()
    {
        if ($category = Mage::registry('current_category')) {
            return $category->getId();
        }
        return false;
    }

    /**
     * Retrieve product category
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        $category = $this->getData('category');
        if (is_null($category) && $this->getCategoryId()) {
            $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
            $this->setCategory($category);
        }
        return $category;
    }

    /**
     * Set assigned category IDs array to product
     *
     * @param array|int|string $ids the ID(s) as int, comma-separated string or array of ints
     * @return $this
     */
    public function setCategoryIds($ids)
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        } elseif (is_int($ids)) {
            $ids = (array) $ids;
        } elseif (!is_array($ids)) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid category IDs.'));
        }
        $ids = array_filter(array_map('\intval', $ids));
        $this->setData('category_ids', $ids);
        return $this;
    }

    /**
     * Retrieve assigned category Ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $wasLocked = false;
            if ($this->isLockedAttribute('category_ids')) {
                $wasLocked = true;
                $this->unlockAttribute('category_ids');
            }
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
            if ($wasLocked) {
                $this->lockAttribute('category_ids');
            }
        }

        return (array) $this->_getData('category_ids');
    }

    /**
     * Retrieve product categories
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        return $this->_getResource()->getCategoryCollection($this);
    }

    /**
     * Retrieve product websites identifiers
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        if (!$this->hasWebsiteIds()) {
            $ids = $this->_getResource()->getWebsiteIds($this);
            $this->setWebsiteIds($ids);
        }
        return $this->getData('website_ids');
    }

    /**
     * Get all sore ids where product is presented
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = [];
            if ($websiteIds = $this->getWebsiteIds()) {
                foreach ($websiteIds as $websiteId) {
                    $websiteStores = Mage::app()->getWebsite($websiteId)->getStoreIds();
                    $storeIds = array_merge($storeIds, $websiteStores);
                }
            }
            $this->setStoreIds($storeIds);
        }
        return $this->getData('store_ids');
    }

    /**
     * Retrieve product attributes
     * if $groupId is null - retrieve all product attributes
     *
     * @param int  $groupId   Retrieve attributes of the specified group
     * @param bool $skipSuper Not used
     * @return array
     */
    public function getAttributes($groupId = null, $skipSuper = false)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute[] $productAttributes */
        $productAttributes = $this->getTypeInstance(true)->getEditableAttributes($this);
        if ($groupId) {
            $attributes = [];
            foreach ($productAttributes as $attribute) {
                if ($attribute->isInGroup($this->getAttributeSetId(), $groupId)) {
                    $attributes[] = $attribute;
                }
            }
        } else {
            $attributes = $productAttributes;
        }

        return $attributes;
    }

    /**
     * @return string
     */
    public function getLinksTitle()
    {
        return (string) $this->_getData('links_title');
    }

    /**
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        return $this->_stockItem;
    }

    /**
     * @return bool
     */
    public function hasStockItem()
    {
        return (bool) $this->_stockItem;
    }

    /**
     * @param Mage_CatalogInventory_Model_Stock_Item $stockItem
     * @return $this
     */
    public function setStockItem($stockItem)
    {
        $this->_stockItem = $stockItem;
        return $this;
    }

    /**
     * Check product options and type options and save them, too
     *
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $this->cleanCache();
        $this->setTypeHasOptions(false);
        $this->setTypeHasRequiredOptions(false);

        $this->getTypeInstance(true)->beforeSave($this);

        $hasOptions         = false;
        $hasRequiredOptions = false;

        /**
         * $this->_canAffectOptions - set by type instance only
         * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
         * or in type instance as well
         */
        $this->canAffectOptions($this->_canAffectOptions && $this->getCanSaveCustomOptions());
        if ($this->getCanSaveCustomOptions()) {
            $options = $this->getProductOptions();
            if (is_array($options)) {
                $this->setIsCustomOptionChanged(true);
                foreach ($this->getProductOptions() as $option) {
                    $this->getOptionInstance()->addOption($option);
                    if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
                        if (!empty($option['file_extension'])) {
                            $fileExtension = $option['file_extension'];
                            if (strcmp($fileExtension, Mage::helper('core')->removeTags($fileExtension)) !== 0) {
                                Mage::throwException(Mage::helper('catalog')->__('Invalid custom option(s).'));
                            }
                        }
                        $hasOptions = true;
                    }
                }
                foreach ($this->getOptionInstance()->getOptions() as $option) {
                    if ($option['is_require'] == '1') {
                        $hasRequiredOptions = true;
                        break;
                    }
                }
            }
        }

        /**
         * Set true, if any
         * Set false, ONLY if options have been affected by Options tab and Type instance tab
         */
        if ($hasOptions || (bool) $this->getTypeHasOptions()) {
            $this->setHasOptions(true);
            if ($hasRequiredOptions || (bool) $this->getTypeHasRequiredOptions()) {
                $this->setRequiredOptions(true);
            } elseif ($this->canAffectOptions()) {
                $this->setRequiredOptions(false);
            }
        } elseif ($this->canAffectOptions()) {
            $this->setHasOptions(false);
            $this->setRequiredOptions(false);
        }
        return parent::_beforeSave();
    }

    /**
     * Check/set if options can be affected when saving product
     * If value specified, it will be set.
     *
     * @param   bool $value
     * @return  bool
     */
    public function canAffectOptions($value = null)
    {
        if ($value !== null) {
            $this->_canAffectOptions = (bool) $value;
        }
        return $this->_canAffectOptions;
    }

    /**
     * Saving product type related data and init index
     *
     * @inheritDoc
     */
    protected function _afterSave()
    {
        $this->getLinkInstance()->saveProductRelations($this);
        $this->getTypeInstance(true)->save($this);

        /**
         * Product Options
         */
        $this->getOptionInstance()->setProduct($this)
            ->saveOptions();

        return parent::_afterSave();
    }

    /**
     * Clear cache related with product and protect delete from not admin
     * Register indexing event before delete product
     *
     * @inheritDoc
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        $this->cleanCache();

        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after product delete commit
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');

        $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE);
        return $this;
    }

    /**
     * Load product options if they exists
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /**
         * Load product options
         */
        if ($this->getHasOptions()) {
            foreach ($this->getProductOptionsCollection() as $option) {
                $option->setProduct($this);
                $this->addOption($option);
            }
        }
        return $this;
    }

    /**
     * Clear cache related with product id
     *
     * @return $this
     */
    public function cleanCache()
    {
        if ($this->getId()) {
            Mage::app()->cleanCache('catalog_product_' . $this->getId());
        }
        return $this;
    }

    /**
     * Get product price model
     *
     * @return Mage_Bundle_Model_Product_Price
     */
    public function getPriceModel()
    {
        return Mage::getSingleton('catalog/product_type')->priceFactory($this->getTypeId());
    }

    /**
     * Get product group price
     *
     * @return float
     */
    public function getGroupPrice()
    {
        return $this->getPriceModel()->getGroupPrice($this);
    }

    /**
     * Get product tier price by qty
     *
     * @param   float $qty
     * @return  float|array
     */
    public function getTierPrice($qty = null)
    {
        return $this->getPriceModel()->getTierPrice($qty, $this);
    }

    /**
     * Count how many tier prices we have for the product
     *
     * @return  int
     */
    public function getTierPriceCount()
    {
        return $this->getPriceModel()->getTierPriceCount($this);
    }

    /**
     * Get formatted by currency tier price
     *
     * @param   double $qty
     * @return  array | double
     */
    public function getFormatedTierPrice($qty = null)
    {
        return $this->getPriceModel()->getFormatedTierPrice($qty, $this);
    }

    /**
     * Get formatted by currency product price
     *
     * @return  array|double
     */
    public function getFormatedPrice()
    {
        return $this->getPriceModel()->getFormatedPrice($this);
    }

    /**
     * Sets final price of product
     *
     * This func is equal to magic 'setFinalPrice()', but added as a separate func, because in cart with bundle
     * products it's called very often in Item->getProduct(). So removing chain of magic with more cpu consuming
     * algorithms gives nice optimization boost.
     *
     * @param float|null $price Price amount
     * @return $this
     */
    public function setFinalPrice($price)
    {
        $this->_data['final_price'] = $price;
        return $this;
    }

    /**
     * Get product final price
     *
     * @param double $qty
     * @return double
     */
    public function getFinalPrice($qty = null)
    {
        $price = $this->_getData('final_price');
        return $price ?? $this->getPriceModel()->getFinalPrice($qty, $this);
    }

    /**
     * Returns calculated final price
     *
     * @return float|null
     */
    public function getCalculatedFinalPrice()
    {
        return $this->_getData('calculated_final_price');
    }

    /**
     * Returns minimal price
     *
     * @return float
     */
    public function getMinimalPrice()
    {
        return max($this->_getData('minimal_price'), 0);
    }

    /**
     * Returns special price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        return $this->_getData('special_price');
    }

    /**
     * Returns starting date of the special price
     *
     * @return mixed
     */
    public function getSpecialFromDate()
    {
        return $this->_getData('special_from_date');
    }

    /**
     * Returns end date of the special price
     *
     * @return mixed
     */
    public function getSpecialToDate()
    {
        return $this->_getData('special_to_date');
    }

    /*******************************************************************************
     ** Linked products API
     */
    /**
     * Retrieve array of related roducts
     *
     * @return Mage_Catalog_Model_Product[]
     */
    public function getRelatedProducts()
    {
        if (!$this->hasRelatedProducts()) {
            $products = [];
            $collection = $this->getRelatedProductCollection();
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setRelatedProducts($products);
        }
        return $this->getData('related_products');
    }

    /**
     * Retrieve related products identifiers
     *
     * @return array
     */
    public function getRelatedProductIds()
    {
        if (!$this->hasRelatedProductIds()) {
            $ids = [];
            foreach ($this->getRelatedProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setRelatedProductIds($ids);
        }
        return $this->getData('related_product_ids');
    }

    /**
     * Retrieve collection related product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getRelatedProductCollection()
    {
        $collection = $this->getLinkInstance()->useRelatedLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection related link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getRelatedLinkCollection()
    {
        $collection = $this->getLinkInstance()->useRelatedLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Retrieve array of up sell products
     *
     * @return Mage_Catalog_Model_Product[]
     */
    public function getUpSellProducts()
    {
        if (!$this->hasUpSellProducts()) {
            $products = [];
            foreach ($this->getUpSellProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setUpSellProducts($products);
        }
        return $this->getData('up_sell_products');
    }

    /**
     * Retrieve up sell products identifiers
     *
     * @return array
     */
    public function getUpSellProductIds()
    {
        if (!$this->hasUpSellProductIds()) {
            $ids = [];
            foreach ($this->getUpSellProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setUpSellProductIds($ids);
        }
        return $this->getData('up_sell_product_ids');
    }

    /**
     * Retrieve collection up sell product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getUpSellProductCollection()
    {
        $collection = $this->getLinkInstance()->useUpSellLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection up sell link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getUpSellLinkCollection()
    {
        $collection = $this->getLinkInstance()->useUpSellLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Retrieve array of cross sell products
     *
     * @return array
     */
    public function getCrossSellProducts()
    {
        if (!$this->hasCrossSellProducts()) {
            $products = [];
            foreach ($this->getCrossSellProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setCrossSellProducts($products);
        }
        return $this->getData('cross_sell_products');
    }

    /**
     * Retrieve cross sell products identifiers
     *
     * @return array
     */
    public function getCrossSellProductIds()
    {
        if (!$this->hasCrossSellProductIds()) {
            $ids = [];
            foreach ($this->getCrossSellProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setCrossSellProductIds($ids);
        }
        return $this->getData('cross_sell_product_ids');
    }

    /**
     * Retrieve collection cross sell product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getCrossSellProductCollection()
    {
        $collection = $this->getLinkInstance()->useCrossSellLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection cross sell link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getCrossSellLinkCollection()
    {
        $collection = $this->getLinkInstance()->useCrossSellLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Retrieve collection grouped link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getGroupedLinkCollection()
    {
        $collection = $this->getLinkInstance()->useGroupedLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /*******************************************************************************
     ** Media API
     */
    /**
     * Retrieve attributes for media gallery
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute[]
     */
    public function getMediaAttributes()
    {
        if (!$this->hasMediaAttributes()) {
            $mediaAttributes = [];
            foreach ($this->getAttributes() as $attribute) {
                if ($attribute->getFrontend()->getInputType() == 'media_image') {
                    $mediaAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
            $this->setMediaAttributes($mediaAttributes);
        }
        return $this->getData('media_attributes');
    }

    /**
     * Retrieve media gallery images
     *
     * @return Varien_Data_Collection
     */
    public function getMediaGalleryImages()
    {
        if (!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
            $images = new Varien_Data_Collection();
            foreach ($this->getMediaGallery('images') as $image) {
                if ($image['disabled']) {
                    continue;
                }
                $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                $image['id'] = $image['value_id'] ?? null;
                $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                $images->addItem(new Varien_Object($image));
            }
            $this->setData('media_gallery_images', $images);
        }

        return $this->getData('media_gallery_images');
    }

    /**
     * Add image to media gallery
     *
     * @param string        $file              file path of image in file system
     * @param string|array  $mediaAttribute    code of attribute with type 'media_image',
     *                                          leave blank if image should be only in gallery
     * @param bool       $move              if true, it will move source file
     * @param bool       $exclude           mark image as disabled in product page view
     * @return $this
     */
    public function addImageToMediaGallery($file, $mediaAttribute = null, $move = false, $exclude = true)
    {
        $attributes = $this->getTypeInstance(true)->getSetAttributes($this);
        if (!isset($attributes['media_gallery'])) {
            return $this;
        }
        $mediaGalleryAttribute = $attributes['media_gallery'];
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $mediaGalleryAttribute */
        $mediaGalleryAttribute->getBackend()->addImage($this, $file, $mediaAttribute, $move, $exclude);
        return $this;
    }

    /**
     * Retrieve product media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    public function getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    public function duplicate()
    {
        $this->getWebsiteIds();
        $this->getCategoryIds();

        /** @var Mage_Catalog_Model_Product $newProduct */
        $newProduct = Mage::getModel('catalog/product')->setData($this->getData())
            ->setIsDuplicate(true)
            ->setOriginalId($this->getId())
            ->setSku(null)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setId(null)
            ->setStoreId(Mage::app()->getStore()->getId());

        Mage::dispatchEvent(
            'catalog_model_product_duplicate',
            ['current_product' => $this, 'new_product' => $newProduct],
        );

        /* Prepare Related*/
        $data = [];
        $this->getLinkInstance()->useRelatedLinks();
        $attributes = [];
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getRelatedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setRelatedLinkData($data);

        /* Prepare UpSell*/
        $data = [];
        $this->getLinkInstance()->useUpSellLinks();
        $attributes = [];
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getUpSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setUpSellLinkData($data);

        /* Prepare Cross Sell */
        $data = [];
        $this->getLinkInstance()->useCrossSellLinks();
        $attributes = [];
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getCrossSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setCrossSellLinkData($data);

        /* Prepare Grouped */
        $data = [];
        $this->getLinkInstance()->useGroupedLinks();
        $attributes = [];
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getGroupedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setGroupedLinkData($data);

        $newProduct->save();

        $this->getOptionInstance()->duplicate($this->getId(), $newProduct->getId());
        $this->getResource()->duplicate($this->getId(), $newProduct->getId());

        // TODO - duplicate product on all stores of the websites it is associated with
        /*if ($storeIds = $this->getWebsiteIds()) {
            foreach ($storeIds as $storeId) {
                $this->setStoreId($storeId)
                   ->load($this->getId());

                $newProduct->setData($this->getData())
                    ->setSku(null)
                    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    ->setId($newId)
                    ->save();
            }
        }*/
        return $newProduct;
    }

    /**
     * Is product grouped
     *
     * @return bool
     */
    public function isSuperGroup()
    {
        return $this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
    }

    /**
     * Alias for isConfigurable()
     *
     * @return bool
     */
    public function isSuperConfig()
    {
        return $this->isConfigurable();
    }
    /**
     * Check is product grouped
     *
     * @return bool
     */
    public function isGrouped()
    {
        return $this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
    }

    /**
     * Check is product configurable
     *
     * @return bool
     */
    public function isConfigurable()
    {
        return $this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }

    /**
     * Whether product configurable or grouped
     *
     * @return bool
     */
    public function isSuper()
    {
        return $this->isConfigurable() || $this->isGrouped();
    }

    /**
     * Returns visible status IDs in catalog
     *
     * @return array
     */
    public function getVisibleInCatalogStatuses()
    {
        return Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
    }

    /**
     * Retrieve visible statuses
     *
     * @return array
     */
    public function getVisibleStatuses()
    {
        return Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
    }

    /**
     * Check Product visilbe in catalog
     *
     * @return bool
     */
    public function isVisibleInCatalog()
    {
        return in_array($this->getStatus(), $this->getVisibleInCatalogStatuses());
    }

    /**
     * Retrieve visible in site visibilities
     *
     * @return array
     */
    public function getVisibleInSiteVisibilities()
    {
        return Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds();
    }

    /**
     * Check Product visible in site
     *
     * @return bool
     */
    public function isVisibleInSiteVisibility()
    {
        return in_array($this->getVisibility(), $this->getVisibleInSiteVisibilities());
    }

    /**
     * Checks product can be duplicated
     *
     * @return bool
     */
    public function isDuplicable()
    {
        return $this->_isDuplicable;
    }

    /**
     * Set is duplicable flag
     *
     * @param bool $value
     * @return $this
     */
    public function setIsDuplicable($value)
    {
        $this->_isDuplicable = (bool) $value;
        return $this;
    }

    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable()
    {
        Mage::dispatchEvent('catalog_product_is_salable_before', [
            'product'   => $this,
        ]);

        $salable = $this->isAvailable();

        $object = new Varien_Object([
            'product'    => $this,
            'is_salable' => $salable,
        ]);
        Mage::dispatchEvent('catalog_product_is_salable_after', [
            'product'   => $this,
            'salable'   => $object,
        ]);
        return $object->getIsSalable();
    }

    /**
     * Check whether the product type or stock allows to purchase the product
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getTypeInstance(true)->isSalable($this)
            || Mage::helper('catalog/product')->getSkipSaleableCheck();
    }

    /**
     * Is product salable detecting by product type
     *
     * @return bool
     */
    public function getIsSalable()
    {
        $productType = $this->getTypeInstance(true);
        if (method_exists($productType, 'getIsSalable')) {
            return $productType->getIsSalable($this);
        }
        if ($this->hasData('is_salable')) {
            return $this->getData('is_salable');
        }

        return $this->isSalable();
    }

    /**
     * Check is a virtual product
     * Data helper wrapper
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getIsVirtual();
    }

    /**
     * Whether the product is a recurring payment
     *
     * @return bool
     */
    public function isRecurring()
    {
        return $this->getIsRecurring() == '1';
    }

    /**
     * Alias for isSalable()
     *
     * @return bool
     */
    public function isSaleable()
    {
        return $this->isSalable();
    }

    /**
     * Whether product available in stock
     *
     * @return bool
     */
    public function isInStock()
    {
        return $this->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
    }

    /**
     * Get attribute text by its code
     *
     * @param string $attributeCode of the attribute
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        return $this->getResource()
            ->getAttribute($attributeCode)
                ->getSource()
                    ->getOptionText($this->getData($attributeCode));
    }

    /**
     * Returns array with dates for custom design
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
     * Retrieve Product URL
     *
     * @param  bool $useSid
     * @return string
     */
    public function getProductUrl($useSid = null)
    {
        return $this->getUrlModel()->getProductUrl($this, $useSid);
    }

    /**
     * Retrieve URL in current store
     *
     * @param array $params the route params
     * @return string
     */
    public function getUrlInStore($params = [])
    {
        return $this->getUrlModel()->getUrlInStore($this, $params);
    }

    /**
     * Formats URL key
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
     * Retrieve Product Url Path (include category)
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getUrlPath($category = null)
    {
        return $this->getUrlModel()->getUrlPath($this, $category);
    }

    /**
     * Save current attribute with code $code and assign new value
     *
     * @param string $code  Attribute code
     * @param mixed  $value New attribute value
     * @param int    $store Store ID
     */
    public function addAttributeUpdate($code, $value, $store)
    {
        $oldValue = $this->getData($code);
        $oldStore = $this->getStoreId();

        $this->setData($code, $value);
        $this->setStoreId($store);
        $this->getResource()->saveAttribute($this, $code);

        $this->setData($code, $oldValue);
        $this->setStoreId($oldStore);
    }

    /**
     * Renders the object to array
     *
     * @param array $arrAttributes Attribute array
     * @return array
     */
    public function toArray(array $arrAttributes = [])
    {
        $data = parent::toArray($arrAttributes);
        if ($stock = $this->getStockItem()) {
            $data['stock_item'] = $stock->toArray();
        }
        unset($data['stock_item']['product']);
        return $data;
    }

    /**
     * Same as setData(), but also initiates the stock item (if it is there)
     *
     * @param array $data Array to form the object from
     * @return $this
     */
    public function fromArray($data)
    {
        if (isset($data['stock_item'])) {
            if ($this->isModuleEnabled('Mage_CatalogInventory', 'catalog')) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')
                    ->setData($data['stock_item'])
                    ->setProduct($this);
                $this->setStockItem($stockItem);
            }
            unset($data['stock_item']);
        }
        $this->setData($data);
        return $this;
    }

    /**
     * @deprecated after 1.4.2.0
     * @return $this
     */
    public function loadParentProductIds()
    {
        return $this->setParentProductIds([]);
    }

    /**
     * Delete product
     *
     * @return $this
     */
    public function delete()
    {
        parent::delete();
        Mage::dispatchEvent($this->_eventPrefix . '_delete_after_done', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Returns request path
     *
     * @return string
     */
    public function getRequestPath()
    {
        if (!$this->_getData('request_path')) {
            $this->getProductUrl();
        }
        return $this->_getData('request_path');
    }

    /**
     * Custom function for other modules
     * @return int
     */

    public function getGiftMessageAvailable()
    {
        return $this->_getData('gift_message_available');
    }

    /**
     * Returns rating summary
     *
     * @return mixed
     */
    public function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    /**
     * Check is product composite
     *
     * @return bool
     */
    public function isComposite()
    {
        return $this->getTypeInstance(true)->isComposite($this);
    }

    /**
     * Check if product can be configured
     *
     * @return bool
     */
    public function canConfigure()
    {
        $options = $this->getOptions();
        return !empty($options) || $this->getTypeInstance(true)->canConfigure($this);
    }

    /**
     * Retrieve sku through type instance
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getTypeInstance(true)->getSku($this);
    }

    /**
     * Retrieve weight through type instance
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->getTypeInstance(true)->getWeight($this);
    }

    /**
     * Retrieve option instance
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOptionInstance()
    {
        if (!$this->_optionInstance) {
            $this->_optionInstance = Mage::getSingleton('catalog/product_option');
        }
        return $this->_optionInstance;
    }

    /**
     * Retrieve options collection of product
     *
     * @return Mage_Catalog_Model_Resource_Product_Option_Collection
     */
    public function getProductOptionsCollection()
    {
        return $this->getOptionInstance()
            ->getProductOptionCollection($this);
    }

    /**
     * Add option to array of product options
     *
     * @return $this
     */
    public function addOption(Mage_Catalog_Model_Product_Option $option)
    {
        $this->_options[$option->getId()] = $option;
        return $this;
    }

    /**
     * Get option from options array of product by given option id
     *
     * @param string $optionId
     * @return Mage_Catalog_Model_Product_Option|null
     */
    public function getOptionById($optionId)
    {
        return $this->_options[$optionId] ?? null;
    }

    /**
     * Get all options of product
     *
     * @return Mage_Catalog_Model_Product_Option[]
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Retrieve is a virtual product
     *
     * @return bool
     */
    public function getIsVirtual()
    {
        return $this->getTypeInstance(true)->isVirtual($this);
    }

    /**
     * Add custom option information to product
     *
     * @param   string $code    Option code
     * @param   mixed  $value   Value of the option
     * @param   int    $product Product ID
     * @return  $this
     */
    public function addCustomOption($code, $value, $product = null)
    {
        $product = $product ? $product : $this;
        $option = Mage::getModel('catalog/product_configuration_item_option')
            ->addData([
                'product_id' => $product->getId(),
                'product'   => $product,
                'code'      => $code,
                'value'     => $value,
            ]);
        $this->_customOptions[$code] = $option;
        return $this;
    }

    /**
     * Sets custom options for the product
     *
     * @param array $options Array of options
     */
    public function setCustomOptions(array $options)
    {
        $this->_customOptions = $options;
    }

    /**
     * Get all custom options of the product
     *
     * @return array
     */
    public function getCustomOptions()
    {
        return $this->_customOptions;
    }

    /**
     * Get product custom option info
     *
     * @param   string $code
     * @return  Mage_Sales_Model_Quote_Item_Option|null
     */
    public function getCustomOption($code)
    {
        return $this->_customOptions[$code] ?? null;
    }

    /**
     * Checks if there custom option for this product
     *
     * @return bool
     */
    public function hasCustomOptions()
    {
        if (count($this->_customOptions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check availability display product in category
     *
     * @param   int $categoryId
     * @return  string
     */
    public function canBeShowInCategory($categoryId)
    {
        return $this->_getResource()->canBeShowInCategory($this, $categoryId);
    }

    /**
     * Retrieve category ids where product is available
     *
     * @return array
     */
    public function getAvailableInCategories()
    {
        return $this->_getResource()->getAvailableInCategories($this);
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
     * Return Catalog Product Image helper instance
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _getImageHelper()
    {
        return Mage::helper('catalog/image');
    }

    /**
     * Return re-sized image URL
     *
     * @deprecated since 1.1.5
     * @return string
     */
    public function getImageUrl()
    {
        return (string) $this->_getImageHelper()->init($this, 'image')->resize(265);
    }

    /**
     * Return re-sized small image URL
     *
     * @deprecated since 1.1.5
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getSmallImageUrl($width = 88, $height = 77)
    {
        return (string) $this->_getImageHelper()->init($this, 'small_image')->resize($width, $height);
    }

    /**
     * Return re-sized thumbnail image URL
     *
     * @deprecated since 1.1.5
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbnailUrl($width = 75, $height = 75)
    {
        return (string) $this->_getImageHelper()->init($this, 'thumbnail')->resize($width, $height);
    }

    /**
     *  Returns system reserved attribute codes
     *
     *  @return array Reserved attribute names
     */
    public function getReservedAttributes()
    {
        if ($this->_reservedAttributes === null) {
            $_reserved = ['position'];
            $methods = get_class_methods(self::class);
            foreach ($methods as $method) {
                if (preg_match('/^get([A-Z]{1}.+)/', $method, $matches)) {
                    $method = $matches[1];
                    $tmp = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $method));
                    $_reserved[] = $tmp;
                }
            }
            $_allowed = [
                'type_id','calculated_final_price','request_path','rating_summary',
            ];
            $this->_reservedAttributes = array_diff($_reserved, $_allowed);
        }
        return $this->_reservedAttributes;
    }

    /**
     *  Check whether attribute reserved or not
     *
     *  @param Mage_Catalog_Model_Entity_Attribute $attribute Attribute model object
     *  @return bool
     */
    public function isReservedAttribute($attribute)
    {
        return $attribute->getIsUserDefined()
            && in_array($attribute->getAttributeCode(), $this->getReservedAttributes());
    }

    /**
     * Set original loaded data if needed
     *
     * @param string $key
     * @param mixed $data
     * @return Varien_Object
     */
    public function setOrigData($key = null, $data = null)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return parent::setOrigData($key, $data);
        }

        return $this;
    }

    /**
     * Reset all model data
     *
     * @return $this
     */
    public function reset()
    {
        $this->unlockAttributes();
        $this->_clearData();
        return $this;
    }

    /**
     * Get cache tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTagsWithCategories()
    {
        $tags = $this->getCacheTags();
        $affectedCategoryIds = $this->_getResource()->getCategoryIdsWithAnchors($this);
        foreach ($affectedCategoryIds as $categoryId) {
            $tags[] = Mage_Catalog_Model_Category::CACHE_TAG . '_' . $categoryId;
        }
        return $tags;
    }

    /**
     * Remove model object related cache
     *
     * @return Mage_Core_Model_Abstract
     */
    public function cleanModelCache()
    {
        $tags = $this->getCacheIdTagsWithCategories();
        if ($tags !== false) {
            Mage::app()->cleanCache($tags);
        }
        return $this;
    }

    /**
     * Check for empty SKU on each product
     *
     * @return bool|null
     */
    public function isProductsHasSku(array $productIds)
    {
        $products = $this->_getResource()->getProductsSku($productIds);
        if (count($products)) {
            foreach ($products as $product) {
                if (!strlen($product['sku'])) {
                    return false;
                }
            }
            return true;
        }
        return null;
    }

    /**
     * Parse buyRequest into options values used by product
     *
     * @return Varien_Object
     */
    public function processBuyRequest(Varien_Object $buyRequest)
    {
        $options = new Varien_Object();

        /* add product custom options data */
        $customOptions = $buyRequest->getOptions();
        if (is_array($customOptions)) {
            foreach ($customOptions as $key => $value) {
                if ($value === '') {
                    unset($customOptions[$key]);
                }
            }
            $options->setOptions($customOptions);
        }

        /* add product type selected options data */
        $type = $this->getTypeInstance(true);
        $typeSpecificOptions = $type->processBuyRequest($this, $buyRequest);
        $options->addData($typeSpecificOptions);

        /* check correctness of product's options */
        $options->setErrors($type->checkProductConfiguration($this, $buyRequest));

        return $options;
    }

    /**
     * Get preconfigured values from product
     *
     * @return Varien_Object
     */
    public function getPreconfiguredValues()
    {
        $preconfiguredValues = $this->getData('preconfigured_values');
        if (!$preconfiguredValues) {
            $preconfiguredValues = new Varien_Object();
        }

        return $preconfiguredValues;
    }

    /**
     * Prepare product custom options.
     * To be sure that all product custom options does not has ID and has product instance
     *
     * @return $this
     */
    public function prepareCustomOptions()
    {
        foreach ($this->getCustomOptions() as $option) {
            if (!is_object($option->getProduct()) || $option->getId()) {
                $this->addCustomOption($option->getCode(), $option->getValue());
            }
        }

        return $this;
    }

    /**
     * Clearing references on product
     *
     * @return $this
     */
    protected function _clearReferences()
    {
        $this->_clearOptionReferences();
        return $this;
    }

    /**
     * Clearing product's data
     *
     * @return $this
     */
    protected function _clearData()
    {
        foreach ($this->_data as $data) {
            if (is_object($data) && method_exists($data, 'reset')) {
                $data->reset();
            }
        }

        $this->setData([]);
        $this->setOrigData();
        $this->_customOptions         = [];
        $this->_optionInstance        = null;
        $this->_options               = [];
        $this->_canAffectOptions      = false;
        $this->_errors                = [];
        $this->_defaultValues         = [];
        $this->_storeValuesFlags      = [];
        $this->_lockedAttributes      = [];
        $this->_typeInstance          = null;
        $this->_typeInstanceSingleton = null;
        $this->_linkInstance          = null;
        $this->_reservedAttributes    = null;
        $this->_isDuplicable          = true;
        $this->_calculatePrice        = true;
        $this->_stockItem             = null;
        $this->_isDeleteable          = true;
        $this->_isReadonly            = false;

        return $this;
    }

    /**
     * Clearing references to product from product's options
     *
     * @return $this
     */
    protected function _clearOptionReferences()
    {
        /**
         * unload product options
         */
        if (!empty($this->_options)) {
            foreach ($this->_options as $key => $option) {
                $option->setProduct();
                $option->clearInstance();
            }
        }

        return $this;
    }

    /**
     * Retrieve product entities info as array
     *
     * @param string|array $columns One or several columns
     * @return array
     */
    public function getProductEntitiesInfo($columns = null)
    {
        return $this->_getResource()->getProductEntitiesInfo($columns);
    }

    /**
     * Checks whether product has disabled status
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
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

    /**
     * Checks event attribute for initialization as an event object
     *
     * @return bool
     */
    public function getEvent()
    {
        $event = parent::getEvent();
        if (is_string($event)) {
            $event = false;
        }

        return $event;
    }

    /**
     * @param int $storeId
     * @return Mage_Review_Model_Review_Summary
     */
    public function getReviewSummary($storeId = null)
    {
        $storeId = $storeId ?? Mage::app()->getStore()->getId();
        if (empty($this->_reviewSummary[$storeId])) {
            $this->_reviewSummary[$storeId] = Mage::getModel('review/review_summary')
                ->setStoreId($storeId)
                ->load($this->getId());
        }
        return $this->_reviewSummary[$storeId];
    }
}
