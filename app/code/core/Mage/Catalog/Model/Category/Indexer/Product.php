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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category products indexer model.
 * Responsibility for system actions:
 *  - Product save (changed assigned categories list)
 *  - Category save (changed assigned products list or category move)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 */
class Mage_Catalog_Model_Category_Indexer_Product extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Catalog_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Store::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Store_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('catalog/category_indexer_product');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Category Products');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Indexed category/products association');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store->isObjectNew() || $store->dataHasChangedFor('group_id')) {
                return true;
            }
            return false;
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id');
            if (!$storeGroup->isObjectNew() && $hasDataChanges) {
                return true;
            }
            return false;
        }
        return parent::matchEvent($event);
    }


    /**
     * Register data required by process in event object
     * Check if category ids was changed
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();
        switch ($entity) {
            case Mage_Catalog_Model_Product::ENTITY:
               $this->_registerProductEvent($event);
                break;

            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('catalog_category_product_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
        $product = $event->getDataObject();
        /**
         * Check if product categories data was changed
         */
        if ($product->getIsChangedCategories() || $product->dataHasChangedFor('status')
            || $product->dataHasChangedFor('visibility')) {
            $event->addNewData('category_ids', $product->getCategoryIds());
        }
    }

    /**
     * Register event data during category save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCategoryEvent(Mage_Index_Model_Event $event)
    {
        $category = $event->getDataObject();
        /**
         * Check if product categories data was changed
         */
        if ($category->getIsChangedProductList()) {
            $event->addNewData('products_was_changed', true);
        }
        /**
         * Check if category has another affected category ids (category move result)
         */
        if ($category->getAffectedCategoryIds()) {
            $event->addNewData('affected_category_ids', $category->getAffectedCategoryIds());
        }
    }

    /**
     * Process event data and save to index
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_category_product_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_category_product_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
