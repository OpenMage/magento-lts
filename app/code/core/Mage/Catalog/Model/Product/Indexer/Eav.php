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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Eav Indexer Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Catalog_Model_Resource_Product_Indexer_Eav _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Indexer_Eav getResource()
 * @method $this setEntityId(int $value)
 * @method int getAttributeId()
 * @method $this setAttributeId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getValue()
 * @method $this setValue(int $value)
 */
class Mage_Catalog_Model_Product_Indexer_Eav extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = [
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ],
        Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE,
        ],
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ],
        Mage_Core_Model_Store::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ]
    ];

    /**
     * The list of attributes that have an effect on other attributes
     *
     * @var array
     */
    protected $_dependentAttributes = [
        'status'
    ];

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Product Attributes');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Index product attributes for layered navigation building');
    }

    protected function _construct()
    {
        $this->_init('catalog/product_indexer_eav');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();

        if ($entity == Mage_Catalog_Model_Product::ENTITY) {
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
            }
        } elseif ($entity == Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerCatalogAttributeSaveEvent($event);
                    break;
            }
        } elseif ($entity == Mage_Catalog_Model_Convert_Adapter_Product::ENTITY) {
            $event->addNewData('catalog_product_eav_reindex_all', true);
        } elseif ($entity == Mage_Core_Model_Store::ENTITY) {
            if ($event->getType() === Mage_Index_Model_Event::TYPE_SAVE) {
                /** @var Mage_Core_Model_Store $store */
                $store = $event->getDataObject();
                if ($store->getOrigData('is_active') != $store->getIsActive() && $store->getIsActive()) {
                    $event->getProcess()->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                }
            }
        }
    }

    /**
     * Check is attribute indexable in EAV
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @return bool
     */
    protected function _attributeIsIndexable($attribute)
    {
        if (!$attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
        }

        return $attribute->isIndexable();
    }

    /**
     * Check that attribute has an effects on other attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @return bool
     */
    protected function _attributeIsDependent($attribute)
    {
        if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            $attribute = $attribute->getAttributeCode();
        }

        return in_array($attribute, $this->_dependentAttributes);
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerCatalogProductSaveEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product    = $event->getDataObject();
        $attributes = $product->getAttributes();
        $reindexEav = $product->getForceReindexRequired();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (($this->_attributeIsIndexable($attribute) || $this->_attributeIsDependent($attribute))
                && $product->dataHasChangedFor($attributeCode)
            ) {
                $reindexEav = true;
                break;
            }
        }

        if ($reindexEav) {
            $event->addNewData('reindex_eav', $reindexEav);
        }

        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product    = $event->getDataObject();

        $parentIds  = $this->_getResource()->getRelationsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_eav_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        $actionObject = $event->getDataObject();
        $attrData     = $actionObject->getAttributesData();
        $reindexEav   = false;

        // check if force reindex required
        if (isset($attrData['force_reindex_required']) && $attrData['force_reindex_required']) {
            $reindexEav = true;
        }

        // check if attributes changed
        if (is_array($attrData)) {
            foreach (array_keys($attrData) as $attributeCode) {
                if ($this->_attributeIsIndexable($attributeCode) || $this->_attributeIsDependent($attributeCode)) {
                    $reindexEav = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexEav = true;
        }

        // register affected products
        if ($reindexEav) {
            $event->addNewData('reindex_eav_product_ids', $actionObject->getProductIds());
        }

        return $this;
    }

    /**
     * Register data required by process attribute save in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerCatalogAttributeSaveEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        $attribute = $event->getDataObject();
        if ($attribute->isIndexable()) {
            $before = $attribute->getOrigData('is_filterable')
                || $attribute->getOrigData('is_filterable_in_search')
                || $attribute->getOrigData('is_visible_in_advanced_search');
            $after  = $attribute->getData('is_filterable')
                || $attribute->getData('is_filterable_in_search')
                || $attribute->getData('is_visible_in_advanced_search');

            if (!$before && $after || $before && !$after) {
                $event->addNewData('reindex_attribute', 1);
                $event->addNewData('attribute_index_type', $attribute->getIndexType());
                $event->addNewData('is_indexable', $after);
            }
        }

        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_product_eav_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_product_eav_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
