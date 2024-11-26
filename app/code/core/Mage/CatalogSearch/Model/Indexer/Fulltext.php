<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogSearch fulltext indexer model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Model_Indexer_Fulltext extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    public const EVENT_MATCH_RESULT_KEY = 'catalogsearch_fulltext_match_result';

    /**
     * List of searchable attributes
     *
     * @var null|array
     */
    protected $_searchableAttributes = null;

    /**
     * Retrieve resource instance
     *
     * @return Mage_CatalogSearch_Model_Resource_Indexer_Fulltext
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalogsearch/indexer_fulltext');
    }

    /**
     * Indexer must be match entities
     *
     * @var array
     */
    protected $_matchedEntities = [
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
        ],
        Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
        ],
        Mage_Core_Model_Store::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        ],
        Mage_Core_Model_Store_Group::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ],
        Mage_Core_Model_Config_Data::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ],
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ],
        Mage_Catalog_Model_Category::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ]
    ];

    /**
     * Related Configuration Settings for match
     *
     * @var array
     */
    protected $_relatedConfigSettings = [
        Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE
    ];

    /**
     * Retrieve Fulltext Search instance
     *
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('catalogsearch/fulltext');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalogsearch')->__('Catalog Search Index');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalogsearch')->__('Rebuild Catalog product fulltext search index');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat catalog product is enabled and specific save
     * attribute, store, store_group
     *
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $attribute      = $event->getDataObject();

            if (!$attribute) {
                $result = false;
            } elseif ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
                $result = $attribute->dataHasChangedFor('is_searchable');
            } elseif ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = $attribute->getIsSearchable();
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Store::ENTITY) {
            if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = true;
            } else {
                /** @var Mage_Core_Model_Store $store */
                $store = $event->getDataObject();
                if ($store && $store->isObjectNew()) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            /** @var Mage_Core_Model_Store_Group $storeGroup */
            $storeGroup = $event->getDataObject();
            if ($storeGroup && $storeGroup->dataHasChangedFor('website_id')) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Config_Data::ENTITY) {
            $data = $event->getDataObject();
            if ($data && in_array($data->getPath(), $this->_relatedConfigSettings)) {
                $result = $data->isValueChanged();
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
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Mage_Catalog_Model_Product::ENTITY:
                $this->_registerCatalogProductEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('catalogsearch_fulltext_reindex_all', true);
                break;

            case Mage_Core_Model_Config_Data::ENTITY:
            case Mage_Core_Model_Store::ENTITY:
            case Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $event->addNewData('catalogsearch_fulltext_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCatalogCategoryEvent($event);
                break;
        }
    }

    /**
     * Get data required for category'es products reindex
     *
     * @return Mage_CatalogSearch_Model_Indexer_Fulltext
     */
    protected function _registerCatalogCategoryEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /** @var Mage_Catalog_Model_Category $category */
                $category   = $event->getDataObject();
                $productIds = $category->getAffectedProductIds();
                if ($productIds) {
                    $event->addNewData('catalogsearch_category_update_product_ids', $productIds);
                    $event->addNewData('catalogsearch_category_update_category_ids', [$category->getId()]);
                } else {
                    $movedCategoryId = $category->getMovedCategoryId();
                    if ($movedCategoryId) {
                        $event->addNewData('catalogsearch_category_update_product_ids', []);
                        $event->addNewData('catalogsearch_category_update_category_ids', [$movedCategoryId]);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Register data required by catatalog product process in event object
     *
     * @return Mage_CatalogSearch_Model_Indexer_Fulltext
     */
    protected function _registerCatalogProductEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /** @var Mage_Catalog_Model_Product $product */
                $product = $event->getDataObject();

                $event->addNewData('catalogsearch_update_product_id', $product->getId());
                break;
            case Mage_Index_Model_Event::TYPE_DELETE:
                /** @var Mage_Catalog_Model_Product $product */
                $product = $event->getDataObject();

                $event->addNewData('catalogsearch_delete_product_id', $product->getId());
                break;
            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                /** @var Varien_Object $actionObject */
                $actionObject = $event->getDataObject();
                $attrData     = $actionObject->getAttributesData();
                $rebuildIndex = false;
                $reindexData  = [];

                // check if force reindex required
                if (isset($attrData['force_reindex_required']) && $attrData['force_reindex_required']) {
                    $rebuildIndex = true;
                    $reindexData['catalogsearch_force_reindex'] = $attrData['force_reindex_required'];
                }

                // check if status changed
                if (isset($attrData['status'])) {
                    $rebuildIndex = true;
                    $reindexData['catalogsearch_status'] = $attrData['status'];
                }

                // check changed websites
                if ($actionObject->getWebsiteIds()) {
                    $rebuildIndex = true;
                    $reindexData['catalogsearch_website_ids'] = $actionObject->getWebsiteIds();
                    $reindexData['catalogsearch_action_type'] = $actionObject->getActionType();
                }

                $searchableAttributes = [];
                if (is_array($attrData)) {
                    $searchableAttributes = array_intersect($this->_getSearchableAttributes(), array_keys($attrData));
                }

                if (count($searchableAttributes) > 0) {
                    $rebuildIndex = true;
                    $reindexData['catalogsearch_force_reindex'] = true;
                }

                // register affected products
                if ($rebuildIndex) {
                    $reindexData['catalogsearch_product_ids'] = $actionObject->getProductIds();
                    foreach ($reindexData as $k => $v) {
                        $event->addNewData($k, $v);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Retrieve searchable attributes list
     *
     * @return array
     */
    protected function _getSearchableAttributes()
    {
        if (is_null($this->_searchableAttributes)) {
            /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributeCollection */
            $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
            $attributeCollection->addIsSearchableFilter();

            $this->_searchableAttributes = [];
            foreach ($attributeCollection as $attribute) {
                $this->_searchableAttributes[] = $attribute->getAttributeCode();
            }
        }

        return $this->_searchableAttributes;
    }

    /**
     * Check if product is composite
     *
     * @param int $productId
     * @return bool
     */
    protected function _isProductComposite($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        return $product->isComposite();
    }

    /**
     * Process event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if (!empty($data['catalogsearch_fulltext_reindex_all'])) {
            $this->reindexAll();
        } elseif (!empty($data['catalogsearch_delete_product_id'])) {
            $productId = $data['catalogsearch_delete_product_id'];

            if (!$this->_isProductComposite($productId)) {
                $parentIds = $this->_getResource()->getRelationsByChild($productId);
                if (!empty($parentIds)) {
                    $this->_getIndexer()->rebuildIndex(null, $parentIds);
                }
            }

            $this->_getIndexer()->cleanIndex(null, $productId)
                ->resetSearchResults();
        } elseif (!empty($data['catalogsearch_update_product_id'])) {
            $productId = $data['catalogsearch_update_product_id'];
            $productIds = [$productId];

            if (!$this->_isProductComposite($productId)) {
                $parentIds = $this->_getResource()->getRelationsByChild($productId);
                if (!empty($parentIds)) {
                    $productIds = array_merge($productIds, $parentIds);
                }
            }

            $this->_getIndexer()->rebuildIndex(null, $productIds)
                ->resetSearchResults();
        } elseif (!empty($data['catalogsearch_product_ids'])) {
            // mass action
            $productIds = $data['catalogsearch_product_ids'];
            $parentIds = $this->_getResource()->getRelationsByChild($productIds);
            if (!empty($parentIds)) {
                $productIds = array_merge($productIds, $parentIds);
            }

            if (!empty($data['catalogsearch_website_ids'])) {
                $websiteIds = $data['catalogsearch_website_ids'];
                $actionType = $data['catalogsearch_action_type'];

                foreach ($websiteIds as $websiteId) {
                    foreach (Mage::app()->getWebsite($websiteId)->getStoreIds() as $storeId) {
                        if ($actionType == 'remove') {
                            $this->_getIndexer()
                                ->cleanIndex($storeId, $productIds)
                                ->resetSearchResults();
                        } elseif ($actionType == 'add') {
                            $this->_getIndexer()
                                ->rebuildIndex($storeId, $productIds)
                                ->resetSearchResults();
                        }
                    }
                }
            }
            if (isset($data['catalogsearch_status'])) {
                $status = $data['catalogsearch_status'];
                if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                    $this->_getIndexer()
                        ->rebuildIndex(null, $productIds)
                        ->resetSearchResults();
                } else {
                    $this->_getIndexer()
                        ->cleanIndex(null, $productIds)
                        ->resetSearchResults();
                }
            }
            if (isset($data['catalogsearch_force_reindex'])) {
                $this->_getIndexer()
                    ->rebuildIndex(null, $productIds)
                    ->resetSearchResults();
            }
        } elseif (isset($data['catalogsearch_category_update_product_ids'])) {
            $productIds = $data['catalogsearch_category_update_product_ids'];
            $categoryIds = $data['catalogsearch_category_update_category_ids'];

            $this->_getIndexer()
                ->updateCategoryIndex($productIds, $categoryIds);
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $resourceModel = $this->_getIndexer()->getResource();
        $resourceModel->beginTransaction();
        try {
            $this->_getIndexer()->rebuildIndex();
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
    }
}
