<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogSearch Fulltext Observer
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Varien_Event_Observer $observer
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
     * @param Varien_Event_Observer $observer
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
     * Update all attribute-dependant index
     *
     * @param Varien_Event_Observer $observer
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
                Mage::helper('catalogsearch')->__('Attribute setting change related with Search Index. Please run <a href="%s">Rebuild Search Index</a> process.', $url)
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
     * @param   Varien_Event_Observer $observer
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
     * @param Varien_Event_Observer $observer
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
     * @param Varien_Event_Observer $observer
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
