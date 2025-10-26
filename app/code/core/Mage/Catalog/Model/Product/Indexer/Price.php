<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */
/**
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Indexer_Price _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Indexer_Price getResource()
 * @method $this setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method $this setTaxClassId(int $value)
 * @method float getPrice()
 * @method $this setPrice(float $value)
 * @method float getFinalPrice()
 * @method $this setFinalPrice(float $value)
 * @method float getMinPrice()
 * @method $this setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method $this setMaxPrice(float $value)
 * @method float getTierPrice()
 * @method $this setTierPrice(float $value)
 */
class Mage_Catalog_Model_Product_Indexer_Price extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    public const EVENT_MATCH_RESULT_KEY = 'catalog_product_price_match_result';

    /**
     * Reindex price event type
     */
    public const EVENT_TYPE_REINDEX_PRICE = 'catalog_reindex_price';

    /**
     * Matched Entities instruction array
     *
     * @var array
     */
    protected $_matchedEntities = [
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            self::EVENT_TYPE_REINDEX_PRICE,
        ],
        Mage_Core_Model_Config_Data::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Customer_Model_Group::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
    ];

    protected $_relatedConfigSettings = [
        Mage_Catalog_Helper_Data::XML_PATH_PRICE_SCOPE,
        Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,
    ];

    protected function _construct()
    {
        $this->_init('catalog/product_indexer_price');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Product Prices');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Index product prices');
    }

    /**
     * Retrieve attribute list has an effect on product price
     *
     * @return array
     */
    protected function _getDependentAttributes()
    {
        return [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'tax_class_id',
            'status',
            'required_options',
            'force_reindex_required',
        ];
    }

    /**
     * Check if event can be matched by process.
     * Rewritten for checking configuration settings save (like price scope).
     *
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        if ($event->getEntity() == Mage_Core_Model_Config_Data::ENTITY) {
            $data = $event->getDataObject();
            if ($data && in_array($data->getPath(), $this->_relatedConfigSettings)) {
                $result = $data->isValueChanged();
            } else {
                $result = false;
            }
        } elseif ($event->getEntity() == Mage_Customer_Model_Group::ENTITY) {
            $result = $event->getDataObject() && $event->getDataObject()->isObjectNew();
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by catalog product delete process
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_price_parent_ids', $parentIds);
        }
    }

    /**
     * Register data required by catalog product save process
     */
    protected function _registerCatalogProductSaveEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product      = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = $product->getIsRelationsChanged() || $product->getIsCustomOptionChanged()
            || $product->dataHasChangedFor('tier_price_changed')
            || $product->getIsChangedWebsites()
            || $product->getForceReindexRequired();

        foreach ($attributes as $attributeCode) {
            $reindexPrice = $reindexPrice || $product->dataHasChangedFor($attributeCode);
        }

        if ($reindexPrice) {
            $event->addNewData('product_type_id', $product->getTypeId());
            $event->addNewData('reindex_price', 1);
        }
    }

    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexPrice = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexPrice = true;
        }

        // register affected products
        if ($reindexPrice) {
            $event->addNewData('reindex_price_product_ids', $actionObject->getProductIds());
        }
    }

    /**
     * Register data required by process in event object
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();

        if ($entity == Mage_Core_Model_Config_Data::ENTITY || $entity == Mage_Customer_Model_Group::ENTITY) {
            $process = $event->getProcess();
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        } elseif ($entity == Mage_Catalog_Model_Convert_Adapter_Product::ENTITY) {
            $event->addNewData('catalog_product_price_reindex_all', true);
        } elseif ($entity == Mage_Catalog_Model_Product::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_DELETE:
                    $this->_registerCatalogProductDeleteEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerCatalogProductSaveEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                    $this->_registerCatalogProductMassActionEvent($event);
                    break;
                case self::EVENT_TYPE_REINDEX_PRICE:
                    $event->addNewData('id', $event->getDataObject()->getId());
                    break;
            }

            // call product type indexers registerEvent
            $indexers = $this->_getResource()->getTypeIndexers();
            foreach ($indexers as $indexer) {
                $indexer->registerEvent($event);
            }
        }
    }

    /**
     * Process event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if ($event->getType() == self::EVENT_TYPE_REINDEX_PRICE) {
            $this->_getResource()->reindexProductIds($data['id']);
            return;
        }

        if (!empty($data['catalog_product_price_reindex_all'])) {
            $this->reindexAll();
        }

        if (empty($data['catalog_product_price_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
