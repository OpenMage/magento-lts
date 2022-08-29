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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog view layer model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
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
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'STORE_'.Mage::app()->getStore()->getId()
                . '_CAT_' . $this->getCurrentCategory()->getId()
                . '_CUSTGROUP_' . Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = [])
    {
        $additionalTags = array_merge($additionalTags, [
            Mage_Catalog_Model_Category::CACHE_TAG.$this->getCurrentCategory()->getId()
        ]);

        return $additionalTags;
    }

    /**
     * Retrieve current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
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
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addPriceData()
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId());

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
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
            $this->_stateKey = $this->getStateKey().$stateSuffix;
        }

        return $this;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return Mage_Catalog_Model_Category
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
     * @param mixed $category
     * @return $this
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
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection|array
     */
    public function getFilterableAttributes()
    {
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return [];
        }
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        Mage::getResourceSingleton('catalog/product')->getAttribute($attribute);
        return $attribute;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
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
     */
    protected function _getSetIds()
    {
        $key = $this->getStateKey().'_SET_IDS';
        $setIds = $this->getAggregator()->getCacheData($key);

        if ($setIds === null) {
            $setIds = $this->getProductCollection()->getSetIds();
            $this->getAggregator()->saveCacheData($setIds, $key, $this->getStateTags());
        }

        return $setIds;
    }
}
