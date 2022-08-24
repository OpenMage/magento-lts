<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag Indexer Model
 *
 * @method Mage_Tag_Model_Resource_Indexer_Summary _getResource()
 * @method Mage_Tag_Model_Resource_Indexer_Summary getResource()
 * @method int getTagId()
 * @method Mage_Tag_Model_Indexer_Summary setTagId(int $value)
 * @method int getStoreId()
 * @method Mage_Tag_Model_Indexer_Summary setStoreId(int $value)
 * @method int getCustomers()
 * @method Mage_Tag_Model_Indexer_Summary setCustomers(int $value)
 * @method int getProducts()
 * @method Mage_Tag_Model_Indexer_Summary setProducts(int $value)
 * @method int getUses()
 * @method Mage_Tag_Model_Indexer_Summary setUses(int $value)
 * @method int getHistoricalUses()
 * @method Mage_Tag_Model_Indexer_Summary setHistoricalUses(int $value)
 * @method int getPopularity()
 * @method Mage_Tag_Model_Indexer_Summary setPopularity(int $value)
 * @method int getBasePopularity()
 * @method Mage_Tag_Model_Indexer_Summary setBasePopularity(int $value)
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Indexer_Summary extends Mage_Index_Model_Indexer_Abstract
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
        Mage_Tag_Model_Tag::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ],
        Mage_Tag_Model_Tag_Relation::ENTITY => [
            Mage_Index_Model_Event::TYPE_SAVE
        ]
    ];

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('tag/indexer_summary');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('tag')->__('Tag Aggregation Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('tag')->__('Rebuild Tag aggregation data');
    }

    /**
     * Retrieve attribute list that has an effect on tags
     *
     * @return array
     */
    protected function _getProductAttributesDependOn()
    {
        return [
            'visibility',
            'status',
            'website_ids'
        ];
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity() == Mage_Catalog_Model_Product::ENTITY) {
            $this->_registerCatalogProduct($event);
        } elseif ($event->getEntity() == Mage_Tag_Model_Tag::ENTITY) {
            $this->_registerTag($event);
        } elseif ($event->getEntity() == Mage_Tag_Model_Tag_Relation::ENTITY) {
            $this->_registerTagRelation($event);
        }
    }

    /**
     * Register data required by catalog product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductSaveEvent(Mage_Index_Model_Event $event)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $event->getDataObject();
        $reindexTag = $product->getForceReindexRequired();

        foreach ($this->_getProductAttributesDependOn() as $attributeCode) {
            $reindexTag = $reindexTag || $product->dataHasChangedFor($attributeCode);
        }

        if (!$product->isObjectNew() && $reindexTag) {
            $event->addNewData('tag_reindex_required', true);
        }
    }

    /**
     * Register data required by catalog product delete process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        $tagIds = Mage::getModel('tag/tag_relation')
            ->setProductId($event->getEntityPk())
            ->getRelatedTagIds();
        if ($tagIds) {
            $event->addNewData('tag_reindex_tag_ids', $tagIds);
        }
    }

    /**
     * Register data required by catalog product massaction process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getProductAttributesDependOn();
        $reindexTags  = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexTags = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexTags = true;
        }

        // register affected tags
        if ($reindexTags) {
            $tagIds = Mage::getModel('tag/tag_relation')
                ->setProductId($actionObject->getProductIds())
                ->getRelatedTagIds();
            if ($tagIds) {
                $event->addNewData('tag_reindex_tag_ids', $tagIds);
            }
        }
    }

    /**
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProduct(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $this->_registerCatalogProductSaveEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;
        }
    }

    /**
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerTag(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('tag_reindex_tag_id', $event->getEntityPk());
        }
    }

    /**
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerTagRelation(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('tag_reindex_tag_id', $event->getDataObject()->getTagId());
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $this->callEventHandler($event);
    }
}
