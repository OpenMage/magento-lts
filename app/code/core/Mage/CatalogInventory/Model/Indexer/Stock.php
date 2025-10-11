<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * CatalogInventory Stock Status Indexer Model
 *
 * @package    Mage_CatalogInventory
 *
 * @method Mage_CatalogInventory_Model_Resource_Indexer_Stock _getResource()
 * @method Mage_CatalogInventory_Model_Resource_Indexer_Stock getResource()
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method int getStockId()
 * @method $this setStockId(int $value)
 * @method float getQty()
 * @method $this setQty(float $value)
 * @method int getStockStatus()
 * @method $this setStockStatus(int $value)
 */
class Mage_CatalogInventory_Model_Indexer_Stock extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    public const EVENT_MATCH_RESULT_KEY = 'cataloginventory_stock_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = [
        Mage_CatalogInventory_Model_Stock_Item::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE,
        ],
        Mage_Core_Model_Store::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Core_Model_Store_Group::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Core_Model_Config_Data::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
    ];

    /**
     * Related config settings
     *
     * @var array
     */
    protected $_relatedConfigSettings = [
        Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,
        Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK,
    ];

    protected function _construct()
    {
        $this->_init('cataloginventory/indexer_stock');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('cataloginventory')->__('Stock Status');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('cataloginventory')->__('Index Product Stock Status');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            /** @var Mage_Core_Model_Store $store */
            $store = $event->getDataObject();
            if ($store && $store->isObjectNew()) {
                $result = true;
            } else {
                $result = false;
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
            $configData = $event->getDataObject();
            if ($configData && in_array($configData->getPath(), $this->_relatedConfigSettings)) {
                $result = $configData->isValueChanged();
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
            case Mage_CatalogInventory_Model_Stock_Item::ENTITY:
                $this->_registerCatalogInventoryStockItemEvent($event);
                break;

            case Mage_Catalog_Model_Product::ENTITY:
                $this->_registerCatalogProductEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('cataloginventory_stock_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
            case Mage_Core_Model_Config_Data::ENTITY:
                $event->addNewData('cataloginventory_stock_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

                if ($event->getEntity() == Mage_Core_Model_Config_Data::ENTITY) {
                    $configData = $event->getDataObject();
                    if ($configData->getPath() == Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK) {
                        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price')
                            ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_attribute')
                            ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                    }
                }
                break;
        }
    }

    /**
     * Register data required by catalog product processes in event object
     */
    protected function _registerCatalogProductEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /** @var Mage_Catalog_Model_Product $product */
                $product = $event->getDataObject();
                if ($product && $product->getStockData()) {
                    $product->setForceReindexRequired(true);
                }
                break;
            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;
        }
    }

    /**
     * Register data required by cataloginventory stock item processes in event object
     */
    protected function _registerCatalogInventoryStockItemEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getType() === Mage_Index_Model_Event::TYPE_SAVE) {
            $this->_registerStockItemSaveEvent($event);
        }
    }

    /**
     * Register data required by stock item save process in event object
     *
     * @return $this
     */
    protected function _registerStockItemSaveEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $object */
        $object      = $event->getDataObject();

        $event->addNewData('reindex_stock', 1);
        $event->addNewData('product_id', $object->getProductId());

        // Saving stock item without product object
        // Register re-index price process if products out of stock hidden on Front-end
        if (!Mage::helper('cataloginventory')->isShowOutOfStock() && !$object->getProduct()) {
            $massObject = new Varien_Object();
            $massObject->setAttributesData(['force_reindex_required' => 1]);
            $massObject->setProductIds([$object->getProductId()]);
            Mage::getSingleton('index/indexer')->logEvent(
                $massObject,
                Mage_Catalog_Model_Product::ENTITY,
                Mage_Index_Model_Event::TYPE_MASS_ACTION,
            );
        }

        return $this;
    }

    /**
     * Register data required by product delete process in event object
     *
     * @return $this
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_stock_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by product mass action process in event object
     *
     * @return $this
     */
    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        $actionObject = $event->getDataObject();
        $attributes   = [
            'status',
        ];
        $reindexStock = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexStock = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexStock = true;
        }

        // register affected products
        if ($reindexStock) {
            $event->addNewData('reindex_stock_product_ids', $actionObject->getProductIds());
        }

        return $this;
    }

    /**
     * Process event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['cataloginventory_stock_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['cataloginventory_stock_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
