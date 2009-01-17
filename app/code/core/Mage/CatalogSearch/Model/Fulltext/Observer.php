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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogSearch Fulltext Observer
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Fulltext_Observer
{
    /**
     * Update product index when product data updated
     *
     * @param Varien_Object $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshProductIndex($observer)
    {
        $product = $observer->getEvent()->getProduct();

        Mage::getModel('catalogsearch/fulltext')
            ->rebuildIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Clean product index when product deleted or marked as unsearchable/invisible
     *
     * @param Varien_Object $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function cleanProductIndex($observer)
    {
        $product = $observer->getEvent()->getProduct();

        Mage::getModel('catalogsearch/fulltext')
            ->cleanIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Update all attribute-dependant index
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function eavAttributeChange($observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
        /* @var $entityType Mage_Eav_Model_Entity_Type */

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
        }
        elseif ($attribute->dataHasChangedFor('is_searchable')) {
            $showNotice = true;
        }

        if ($showNotice) {
            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('catalogsearch')->__('Attribute setting change related with Search Index. Please run <a href="%s">Rebuild Search Index</a> process', Mage::getUrl('adminhtml/system_cache'))
            );
        }

        return $this;
    }

    /**
     * Rebuild index after import
     *
     * @param Varien_Object $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshIndexAfterImport($observer)
    {
        Mage::getModel('catalogsearch/fulltext')
            ->rebuildIndex();
        return $this;
    }

    /**
     * Refresh fulltext index when we add new store
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshStoreIndex($observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        Mage::getModel('catalogsearch/fulltext')->rebuildIndex($storeId);
        return $this;
    }
}