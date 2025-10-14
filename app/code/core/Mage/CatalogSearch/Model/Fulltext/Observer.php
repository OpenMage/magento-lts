<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * CatalogSearch Fulltext Observer
 *
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Model_Fulltext_Observer
{
    /**
     * Retrieve fulltext (indexer) model
     *
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    protected function _getFulltextModel()
    {
        return Mage::getSingleton('catalogsearch/fulltext');
    }

    /**
     * Update product index when product data updated
     *
     * @return $this
     */
    public function refreshProductIndex(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        $this->_getFulltextModel()
            ->rebuildIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Clean product index when product deleted or marked as unsearchable/invisible
     *
     * @return $this
     */
    public function cleanProductIndex(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        $this->_getFulltextModel()
            ->cleanIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Update all attribute-dependent index
     *
     * @return $this
     */
    public function eavAttributeChange(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        $entityType = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY);
        /** @var Mage_Eav_Model_Entity_Type $entityType */

        if ($attribute->getEntityTypeId() != $entityType->getId()) {
            return $this;
        }

        $delete = $observer->getEventName() == 'eav_entity_attribute_delete_after';

        if (!$delete && !$attribute->dataHasChangedFor('is_searchable')) {
            return $this;
        }

        $showNotice = false;
        if ($delete) {
            if ($attribute->getIsSearchable()) {
                $showNotice = true;
            }
        } elseif ($attribute->dataHasChangedFor('is_searchable')) {
            $showNotice = true;
        }

        if ($showNotice) {
            $url = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_cache');
            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('catalogsearch')->__('Attribute setting change related with Search Index. Please run <a href="%s">Rebuild Search Index</a> process.', $url),
            );
        }

        return $this;
    }

    /**
     * Rebuild index after import
     *
     * @return $this
     */
    public function refreshIndexAfterImport()
    {
        $this->_getFulltextModel()
            ->rebuildIndex();
        return $this;
    }

    /**
     * Refresh fulltext index when we add new store
     *
     * @return  Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshStoreIndex(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_getFulltextModel()->rebuildIndex($storeId);
        return $this;
    }

    /**
     * Catalog Product mass website update
     *
     * @return $this
     */
    public function catalogProductWebsiteUpdate(Varien_Event_Observer $observer)
    {
        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();
        $actionType = $observer->getEvent()->getAction();

        foreach ($websiteIds as $websiteId) {
            foreach (Mage::app()->getWebsite($websiteId)->getStoreIds() as $storeId) {
                if ($actionType == 'remove') {
                    $this->_getFulltextModel()
                        ->cleanIndex($storeId, $productIds)
                        ->resetSearchResults();
                } elseif ($actionType == 'add') {
                    $this->_getFulltextModel()
                        ->rebuildIndex($storeId, $productIds)
                        ->resetSearchResults();
                }
            }
        }

        return $this;
    }

    /**
     * Store delete processing
     *
     * @return $this
     */
    public function cleanStoreIndex(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /** @var Mage_Core_Model_Store $store */

        $this->_getFulltextModel()
            ->cleanIndex($store->getId());

        return $this;
    }
}
