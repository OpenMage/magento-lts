<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Compare Items Resource Collection
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Compare_Item_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Customer Filter
     *
     * @var int
     */
    protected $_customerId               = 0;

    /**
     * Visitor Filter
     *
     * @var int
     */
    protected $_visitorId                = 0;

    /**
     * Comparable attributes cache
     *
     * @var null|Mage_Eav_Model_Entity_Attribute_Abstract[]
     */
    protected $_comparableAttributes;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/product_compare_item', 'catalog/product');
        $this->_initTables();
    }

    /**
     * Set customer filter to collection
     *
     * @param  int   $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = (int) $customerId;
        $this->_addJoinToSelect();
        return $this;
    }

    /**
     * Set visitor filter to collection
     *
     * @param  int   $visitorId
     * @return $this
     */
    public function setVisitorId($visitorId)
    {
        $this->_visitorId = (int) $visitorId;
        $this->_addJoinToSelect();
        return $this;
    }

    /**
     * Retrieve customer filter applied to collection
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_customerId;
    }

    /**
     * Retrieve visitor filter applied to collection
     *
     * @return int
     */
    public function getVisitorId()
    {
        return $this->_visitorId;
    }

    /**
     * Retrieve condition for join filters
     *
     * @return array
     */
    public function getConditionForJoin()
    {
        if ($this->getCustomerId()) {
            return ['customer_id' => $this->getCustomerId()];
        }

        if ($this->getVisitorId()) {
            return ['visitor_id' => $this->getVisitorId()];
        }

        return ['customer_id' => ['null' => true],'visitor_id' => '0'];
    }

    /**
     * Add join to select
     *
     * @return $this
     */
    public function _addJoinToSelect()
    {
        $this->joinTable(
            ['t_compare' => 'catalog/compare_item'],
            'product_id=entity_id',
            [
                'product_id'    => 'product_id',
                'customer_id'   => 'customer_id',
                'visitor_id'    => 'visitor_id',
                'item_store_id' => 'store_id',
                'catalog_compare_item_id' => 'catalog_compare_item_id',
            ],
            $this->getConditionForJoin(),
        );

        $this->_productLimitationFilters['store_table']  = 't_compare';

        return $this;
    }

    /**
     * Retrieve compare products attribute set ids
     *
     * @return array
     */
    protected function _getAttributeSetIds()
    {
        // prepare compare items table conditions
        $compareConds = [
            'compare.product_id=entity.entity_id',
        ];
        if ($this->getCustomerId()) {
            $compareConds[] = $this->getConnection()
                ->quoteInto('compare.customer_id = ?', $this->getCustomerId());
        } else {
            $compareConds[] = $this->getConnection()
                ->quoteInto('compare.visitor_id = ?', $this->getVisitorId());
        }

        // prepare website filter
        $websiteId    = (int) Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        $websiteConds = [
            'website.product_id = entity.entity_id',
            $this->getConnection()->quoteInto('website.website_id = ?', $websiteId),
        ];

        // retrieve attribute sets
        $select = $this->getConnection()->select()
            ->distinct(true)
            ->from(
                ['entity' => $this->getEntity()->getEntityTable()],
                'attribute_set_id',
            )
            ->join(
                ['website' => $this->getTable('catalog/product_website')],
                implode(' AND ', $websiteConds),
                [],
            )
            ->join(
                ['compare' => $this->getTable('catalog/compare_item')],
                implode(' AND ', $compareConds),
                [],
            );
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve attribute ids by set ids
     *
     * @return array
     */
    protected function _getAttributeIdsBySetIds(array $setIds)
    {
        $select = $this->getConnection()->select()
            ->distinct(true)
            ->from($this->getTable('eav/entity_attribute'), 'attribute_id')
            ->where('attribute_set_id IN(?)', $setIds);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve Merged comparable attributes for compared product items
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract[]
     */
    public function getComparableAttributes()
    {
        if (is_null($this->_comparableAttributes)) {
            $this->_comparableAttributes = [];
            $setIds = $this->_getAttributeSetIds();
            if ($setIds) {
                $eavConfig = Mage::getSingleton('eav/config');
                $attributeIds = $eavConfig->getAttributeSetAttributeIds($setIds);
                $this->_comparableAttributes = [];
                $attributeSortInfo = [];
                foreach ($attributeIds as $attributeId) {
                    $attribute = $eavConfig->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeId);
                    if ($attribute->getData('is_comparable')) {
                        $this->_comparableAttributes[$attribute->getAttributeCode()] = $attribute;
                        $attributeSortInfo[$attribute->getAttributeCode()] = $eavConfig->getAttributeSetGroupInfo($attributeId, $setIds);
                    }
                }

                uasort($this->_comparableAttributes, function ($a, $b) use ($attributeSortInfo) {
                    /** @var Mage_Eav_Model_Entity_Attribute_Abstract $a */
                    /** @var Mage_Eav_Model_Entity_Attribute_Abstract $b */

                    $aSort = $attributeSortInfo[$a->getAttributeCode()]; // contains group_id, group_sort, sort
                    $bSort = $attributeSortInfo[$b->getAttributeCode()]; // contains group_id, group_sort, sort

                    return $aSort['group_sort'] <=> $bSort['group_sort'] ?: $aSort['sort'] <=> $bSort['sort'];
                });
            }
        }

        return $this->_comparableAttributes;
    }

    /**
     * Load Comparable attributes
     *
     * @return $this
     */
    public function loadComparableAttributes()
    {
        $comparableAttributes = $this->getComparableAttributes();
        $attributes = [];
        foreach ($comparableAttributes as $attribute) {
            $attributes[] = $attribute->getAttributeCode();
        }

        $this->addAttributeToSelect($attributes);

        return $this;
    }

    /**
     * Use product as collection item
     *
     * @return $this
     */
    public function useProductItem()
    {
        $this->setObject('catalog/product');

        $this->setFlag('url_data_object', true);
        $this->setFlag('do_not_use_category_id', true);

        return $this;
    }

    /**
     * Retrieve product ids from collection
     *
     * @return array
     */
    public function getProductIds()
    {
        $ids = [];
        foreach ($this->getItems() as $item) {
            $ids[] = $item->getProductId();
        }

        return $ids;
    }

    /**
     * Clear compare items by condition
     *
     * @return $this
     */
    public function clear()
    {
        Mage::getResourceSingleton('catalog/product_compare_item')
            ->clearItems($this->getVisitorId(), $this->getCustomerId());
        Mage::dispatchEvent('catalog_product_compare_item_collection_clear');

        return $this;
    }

    /**
     * Retrieve is flat enabled flag
     * Overwrite disable flat for compared item if required EAV resource
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        if (!Mage::helper('catalog/product_compare')->getAllowUsedFlat()) {
            return false;
        }

        return parent::isEnabledFlat();
    }
}
