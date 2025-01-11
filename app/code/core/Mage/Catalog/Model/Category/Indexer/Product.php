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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category products indexer model.
 * Responsibility for system actions:
 *  - Product save (changed assigned categories list)
 *  - Category save (changed assigned products list or category move)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Category_Indexer_Product _getResource()
 * @method Mage_Catalog_Model_Resource_Category_Indexer_Product getResource()
 * @method int getCategoryId()
 * @method $this setCategoryId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method int getIsParent()
 * @method $this setIsParent(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getVisibility()
 * @method $this setVisibility(int $value)
 */
class Mage_Catalog_Model_Category_Indexer_Product extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    public const EVENT_MATCH_RESULT_KEY = 'catalog_category_product_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = [
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ],
        Mage_Catalog_Model_Category::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Core_Model_Store::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Core_Model_Store_Group::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
    ];

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
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data      = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store && ($store->isObjectNew() || $store->dataHasChangedFor('group_id'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
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
     * Check if category ids was changed
     *
     * @return Mage_Catalog_Model_Category_Indexer_Product
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
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
     */
    protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
        $eventType = $event->getType();
        if ($eventType == Mage_Index_Model_Event::TYPE_SAVE) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = $event->getDataObject();
            /**
             * Check if product categories data was changed
             */
            if ($product->getIsChangedCategories() || $product->dataHasChangedFor('status')
                || $product->dataHasChangedFor('visibility') || $product->getIsChangedWebsites()
            ) {
                $event->addNewData('category_ids', $product->getCategoryIds());
            }
        } elseif ($eventType == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            $actionObject = $event->getDataObject();
            $attributes   = ['status', 'visibility'];
            $rebuildIndex = false;

            // check if attributes changed
            $attrData = $actionObject->getAttributesData();
            if (is_array($attrData)) {
                foreach ($attributes as $attributeCode) {
                    if (array_key_exists($attributeCode, $attrData)) {
                        $rebuildIndex = true;
                        break;
                    }
                }
            }

            // check changed websites
            if ($actionObject->getWebsiteIds()) {
                $rebuildIndex = true;
            }

            // register affected products
            if ($rebuildIndex) {
                $event->addNewData('product_ids', $actionObject->getProductIds());
            }
        }
    }

    /**
     * Register event data during category save process
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
