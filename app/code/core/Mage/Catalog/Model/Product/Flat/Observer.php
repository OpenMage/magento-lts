<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Flat observer
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Flat_Observer
{
    /**
     * Retrieve Catalog Product Flat Helper
     *
     * @return Mage_Catalog_Helper_Product_Flat
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog/product_flat');
    }

    /**
     * Retrieve Catalog Product Flat Indexer model
     *
     * @return Mage_Catalog_Model_Product_Flat_Indexer
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('catalog/product_flat_indexer');
    }

    /**
     * Catalog Entity attribute after save process
     *
     * @return $this
     */
    public function catalogEntityAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $attribute = $observer->getEvent()->getAttribute();
        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */

        $enableBefore   = ($attribute->getOrigData('backend_type') == 'static')
            || ($this->_getHelper()->isAddFilterableAttributes() && $attribute->getOrigData('is_filterable') > 0)
            || ($attribute->getOrigData('used_in_product_listing') == 1)
            || ($attribute->getOrigData('used_for_sort_by') == 1);
        $enableAfter    = ($attribute->getData('backend_type') == 'static')
            || ($this->_getHelper()->isAddFilterableAttributes() && $attribute->getData('is_filterable') > 0)
            || ($attribute->getData('used_in_product_listing') == 1)
            || ($attribute->getData('used_for_sort_by') == 1);

        if (!$enableAfter && !$enableBefore) {
            return $this;
        }

        if ($enableBefore && !$enableAfter) {
            // delete attribute data from flat
            $this->_getIndexer()->prepareDataStorage();
        } else {
            $this->_getIndexer()->updateAttribute($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * Catalog Product Status Update
     *
     * @return $this
     */
    public function catalogProductStatusUpdate(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $productId  = $observer->getEvent()->getProductId();
        $status     = $observer->getEvent()->getStatus();
        $storeId    = $observer->getEvent()->getStoreId();
        $storeId    = $storeId > 0 ? $storeId : null;

        $this->_getIndexer()->updateProductStatus($productId, $status, $storeId);

        return $this;
    }

    /**
     * Catalog Product Website(s) update
     *
     * @return $this
     */
    public function catalogProductWebsiteUpdate(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();

        foreach ($websiteIds as $websiteId) {
            $website = Mage::app()->getWebsite($websiteId);
            foreach ($website->getStores() as $store) {
                if ($observer->getEvent()->getAction() == 'remove') {
                    $this->_getIndexer()->removeProduct($productIds, $store->getId());
                } else {
                    $this->_getIndexer()->updateProduct($productIds, $store->getId());
                }
            }
        }

        return $this;
    }

    /**
     * Catalog Product After Save
     *
     * @return $this
     */
    public function catalogProductSaveAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $product   = $observer->getEvent()->getProduct();
        $productId = $product->getId();

        $this->_getIndexer()->saveProduct($productId);

        return $this;
    }

    /**
     * Add new store flat process
     *
     * @return $this
     */
    public function storeAdd(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /** @var Mage_Core_Model_Store $store */
        $this->_getIndexer()->rebuild($store->getId());

        return $this;
    }

    /**
     * Store edit action, check change store group
     *
     * @return $this
     */
    public function storeEdit(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /** @var Mage_Core_Model_Store $store */
        if ($store->dataHasChangedFor('group_id')) {
            $this->_getIndexer()->rebuild($store->getId());
        }

        return $this;
    }

    /**
     * Store delete after process
     *
     * @return $this
     */
    public function storeDelete(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /** @var Mage_Core_Model_Store $store */

        $this->_getIndexer()->deleteStore($store->getId());

        return $this;
    }

    /**
     * Store Group Save process
     *
     * @return $this
     */
    public function storeGroupSave(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $group = $observer->getEvent()->getGroup();
        /** @var Mage_Core_Model_Store_Group $group */

        if ($group->dataHasChangedFor('website_id')) {
            foreach ($group->getStores() as $store) {
                /** @var Mage_Core_Model_Store $store */
                $this->_getIndexer()->rebuild($store->getId());
            }
        }

        return $this;
    }

    /**
     * Catalog Product Import After process
     *
     * @return $this
     */
    public function catalogProductImportAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $this->_getIndexer()->rebuild();

        return $this;
    }

    /**
     * Customer Group save after process
     *
     * @return $this
     */
    public function customerGroupSaveAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $customerGroup = $observer->getEvent()->getObject();
        /** @var Mage_Customer_Model_Group $customerGroup */
        if ($customerGroup->dataHasChangedFor($customerGroup->getIdFieldName())
            || $customerGroup->dataHasChangedFor('tax_class_id')
        ) {
            $this->_getIndexer()->updateEventAttributes();
        }
        return $this;
    }

    /**
     * Update category ids in flat
     *
     * @deprecated 1.3.2.2
     * @return $this
     */
    public function catalogCategoryChangeProducts(Varien_Event_Observer $observer)
    {
        return $this;
    }
}
