<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

use Mage_Catalog_Model_Category as Category;
use Mage_Core_Model_Store as Store;
use Mage_Core_Model_Store_Group as StoreGroup;
use Mage_Index_Model_Event as Event;

/**
 * Catalog Category Flat Indexer Model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Indexer_Flat extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    public const EVENT_MATCH_RESULT_KEY = 'catalog_category_flat_match_result';

    /**
     * Matched entity events
     *
     * @var array
     */
    protected $_matchedEntities = [
        Category::ENTITY => [
            Event::TYPE_SAVE,
        ],
        Store::ENTITY => [
            Event::TYPE_SAVE,
            Event::TYPE_DELETE,
        ],
        StoreGroup::ENTITY => [
            Event::TYPE_SAVE,
        ],
    ];

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible()
    {
        /** @var Mage_Catalog_Helper_Category_Flat $categoryFlatHelper */
        $categoryFlatHelper = Mage::helper('catalog/category_flat');
        return $categoryFlatHelper->isEnabled() || !$categoryFlatHelper->isBuilt();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Category Flat Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Reorganize EAV category structure to flat structure');
    }

    /**
     * Retrieve Catalog Category Flat Indexer model
     *
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _getIndexer()
    {
        return Mage::getResourceSingleton('catalog/category_flat');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat catalog category is enabled and specific save
     * category, store, store_group
     *
     * @return bool
     */
    public function matchEvent(Event $event)
    {
        /** @var Mage_Catalog_Helper_Category_Flat $categoryFlatHelper */
        $categoryFlatHelper = Mage::helper('catalog/category_flat');
        if (!$categoryFlatHelper->isAccessible() || !$categoryFlatHelper->isBuilt()) {
            return false;
        }

        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Store::ENTITY) {
            if ($event->getType() == Event::TYPE_DELETE) {
                $result = true;
            } elseif ($event->getType() == Event::TYPE_SAVE) {
                /** @var Store $store */
                $store = $event->getDataObject();
                if ($store
                    && (
                        $store->isObjectNew()
                        || $store->dataHasChangedFor('group_id')
                        || $store->dataHasChangedFor('root_category_id')
                    )
                ) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } elseif ($entity == StoreGroup::ENTITY) {
            /** @var StoreGroup $storeGroup */
            $storeGroup = $event->getDataObject();
            if ($storeGroup
                && ($storeGroup->dataHasChangedFor('website_id') || $storeGroup->dataHasChangedFor('root_category_id'))
            ) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     */
    protected function _registerEvent(Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Category::ENTITY:
                $this->_registerCatalogCategoryEvent($event);
                break;

            case Store::ENTITY:
                if ($event->getType() == Event::TYPE_DELETE) {
                    $this->_registerCoreStoreEvent($event);
                    break;
                }
                // no break
            case StoreGroup::ENTITY:
                $event->addNewData('catalog_category_flat_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
    }

    /**
     * Register data required by catalog category process in event object
     *
     * @return $this
     */
    protected function _registerCatalogCategoryEvent(Event $event)
    {
        if ($event->getType() === Event::TYPE_SAVE) {
            /** @var Category $category */
            $category = $event->getDataObject();
            /**
             * Check if category has another affected category ids (category move result)
             */
            $affectedCategoryIds = $category->getAffectedCategoryIds();
            if ($affectedCategoryIds) {
                $event->addNewData('catalog_category_flat_affected_category_ids', $affectedCategoryIds);
            } else {
                $event->addNewData('catalog_category_flat_category_id', $category->getId());
            }
        }
        return $this;
    }

    /**
     * Register core store delete process
     *
     * @return $this
     */
    protected function _registerCoreStoreEvent(Event $event)
    {
        if ($event->getType() == Event::TYPE_DELETE) {
            /** @var Store $store */
            $store = $event->getDataObject();
            $event->addNewData('catalog_category_flat_delete_store_id', $store->getId());
        }
        return $this;
    }

    /**
     * Process event
     */
    protected function _processEvent(Event $event)
    {
        $data = $event->getNewData();

        if (!empty($data['catalog_category_flat_reindex_all'])) {
            $this->reindexAll();
        } elseif (!empty($data['catalog_category_flat_category_id'])) {
            // catalog_product save
            $categoryId = $data['catalog_category_flat_category_id'];
            $this->_getIndexer()->synchronize($categoryId);
        } elseif (!empty($data['catalog_category_flat_affected_category_ids'])) {
            $categoryIds = $data['catalog_category_flat_affected_category_ids'];
            $this->_getIndexer()->move($categoryIds);
        } elseif (!empty($data['catalog_category_flat_delete_store_id'])) {
            $storeId = $data['catalog_category_flat_delete_store_id'];
            $this->_getIndexer()->deleteStores($storeId);
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $this->_getIndexer()->reindexAll();
    }
}
