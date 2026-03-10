<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog view layer model
 *
 * @package    Mage_Catalog
 *
 * @method $this setStore(int $value)
 */
class Mage_Catalog_Model_Layer extends Varien_Object
{
    /**
     * Product collections array
     *
     * @var array
     */
    protected $_productCollections = [];

    /**
     * Key which can be used for load/save aggregation data
     *
     * @var string
     */
    protected $_stateKey = null;

    /**
     * Get data aggregation object
     *
     * @return Mage_CatalogIndex_Model_Aggregation
     */
    public function getAggregator()
    {
        return Mage::getSingleton('catalogindex/aggregation');
    }

    /**
     * Get layer state key
     *
     * @return string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'STORE_' . Mage::app()->getStore()->getId()
                . '_CAT_' . $this->getCurrentCategory()->getId()
                . '_CUSTGROUP_' . Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getStateTags(array $additionalTags = [])
    {
        return array_merge($additionalTags, [
            Mage_Catalog_Model_Category::CACHE_TAG . $this->getCurrentCategory()->getId(),
        ]);
    }

    /**
     * Retrieve current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     * @throws Mage_Core_Exception
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * Initialize product collection
     *
     * @param  Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addPriceData()
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId());
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }

    /**
     * Apply layer
     * Method is colling after apply all filters, can be used
     * for prepare some index data before getting information
     * about existing intexes
     *
     * @return $this
     */
    public function apply()
    {
        $stateSuffix = '';
        foreach ($this->getState()->getFilters() as $filterItem) {
            $stateSuffix .= '_' . $filterItem->getFilter()->getRequestVar()
                . '_' . $filterItem->getValueString();
        }

        if (!empty($stateSuffix)) {
            $this->_stateKey = $this->getStateKey() . $stateSuffix;
        }

        return $this;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return Mage_Catalog_Model_Category
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if (is_null($category)) {
            if ($category = Mage::registry('current_category')) {
                $this->setData('current_category', $category);
            } else {
                $category = Mage::getModel('catalog/category')->load($this->getCurrentStore()->getRootCategoryId());
                $this->setData('current_category', $category);
            }
        }

        return $category;
    }

    /**
     * Change current category object
     *
     * @param  mixed               $category
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setCurrentCategory($category)
    {
        if (is_numeric($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }

        if (!$category instanceof Mage_Catalog_Model_Category) {
            Mage::throwException(Mage::helper('catalog')->__('Category must be an instance of Mage_Catalog_Model_Category.'));
        }

        if (!$category->getId()) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid category.'));
        }

        if ($category->getId() != $this->getCurrentCategory()->getId()) {
            $this->setData('current_category', $category);
        }

        return $this;
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute[]
     * @throws Mage_Core_Exception
     */
    public function getFilterableAttributes()
    {
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return [];
        }

        $eavConfig = Mage::getSingleton('eav/config');
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes */
        $attributes = [];
        foreach ($setIds as $setId) {
            $setAttributeIds = $eavConfig->getAttributeSetAttributeIds($setId);
            foreach ($setAttributeIds as $attributeId) {
                if (!isset($attributes[$attributeId])) {
                    $attribute = $eavConfig->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeId);
                    if (!$this->_filterFilterableAttributes($attribute)) {
                        continue;
                    }

                    if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute && $attribute->getIsFilterable()) {
                        $attributes[$attributeId] = $attribute;
                    }
                }
            }
        }

        uasort($attributes, function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });

        return $attributes;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        Mage::getResourceSingleton('catalog/product')->getAttribute($attribute);
        return $attribute;
    }

    /**
     * Add filters to attribute collection
     *
     * @param  Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
    }

    /**
     * Filter which attributes are included in getFilterableAttributes
     */
    protected function _filterFilterableAttributes(Mage_Catalog_Model_Resource_Eav_Attribute $attribute): bool
    {
        return $attribute->getIsFilterable() > 0;
    }

    /**
     * Retrieve layer state object
     *
     * @return Mage_Catalog_Model_Layer_State
     */
    public function getState()
    {
        $state = $this->getData('state');
        if (is_null($state)) {
            Varien_Profiler::start(__METHOD__);
            $state = Mage::getModel('catalog/layer_state');
            $this->setData('state', $state);
            Varien_Profiler::stop(__METHOD__);
        }

        return $state;
    }

    /**
     * Get attribute sets identifiers of current product set
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _getSetIds()
    {
        $key = $this->getStateKey() . '_SET_IDS';
        $setIds = $this->getAggregator()->getCacheData($key);

        if ($setIds === null) {
            $setIds = $this->getProductCollection()->getSetIds();
            $this->getAggregator()->saveCacheData($setIds, $key, $this->getStateTags());
        }

        return $setIds;
    }
}
