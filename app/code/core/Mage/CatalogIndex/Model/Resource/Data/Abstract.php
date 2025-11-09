<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Resource model CatalogIndex Data Abstract
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Data_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Attribute id by code cache
     *
     * @var array
     */
    protected $_attributeCodeIds     = [];

    /**
     * Link select object
     *
     * @var Zend_Db_Select
     */
    protected $_linkSelect           = null;

    /**
     * Set link select
     *
     * @param Zend_Db_Select $select
     * @return $this
     */
    protected function _setLinkSelect($select)
    {
        $this->_linkSelect = $select;
        return $this;
    }

    /**
     * Get link select
     *
     * @return Zend_Db_Select $select
     */
    protected function _getLinkSelect()
    {
        return $this->_linkSelect;
    }

    /**
     * Init resource
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Retrieve specified attribute data for specified products from specified store
     *
     * @param array|string $products
     * @param array $attributes
     * @param int $store
     * @return array
     */
    public function getAttributeData($products, $attributes, $store)
    {
        $suffixes = ['decimal', 'varchar', 'int', 'text', 'datetime'];
        if (!is_array($products)) {
            $products = new Zend_Db_Expr($products);
        }

        $result = [];
        foreach ($suffixes as $suffix) {
            $tableName = "{$this->getTable('catalog/product')}_{$suffix}";
            $condition = "product.entity_id = c.entity_id AND c.store_id = {$store} AND c.attribute_id = d.attribute_id";
            $defaultCondition = 'product.entity_id = d.entity_id AND d.store_id = 0';
            $fields = [
                'entity_id',
                'type_id',
                'attribute_id'  => 'IF(c.value_id > 0, c.attribute_id, d.attribute_id)',
                'value'         => 'IF(c.value_id > 0, c.value, d.value)',
            ];

            $select = $this->_getReadAdapter()->select()
                ->from(['product' => $this->getTable('catalog/product')], $fields)
                ->where('product.entity_id in (?)', $products)
                ->joinRight(['d' => $tableName], $defaultCondition, [])
                ->joinLeft(['c' => $tableName], $condition, [])
                ->where('c.attribute_id IN (?) OR d.attribute_id IN (?)', $attributes);
            $part = $this->_getReadAdapter()->fetchAll($select);

            if (is_array($part)) {
                $result = array_merge($result, $part);
            }
        }

        return $result;
    }

    /**
     * Returns an array of product children/parents
     *
     * @param int $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param array|int $id
     * @param array $additionalWheres
     * @return array
     */
    public function fetchLinkInformation($store, $table, $idField, $whereField, $id, $additionalWheres = [])
    {
        $idsConditionSymbol = '= ?';
        if (is_array($id)) {
            $idsConditionSymbol = 'in (?)';
        }

        $select = $this->_getReadAdapter()->select();
        $select->from(['l' => $this->getTable($table)], ["l.{$idField}"])
            ->where("l.{$whereField} {$idsConditionSymbol}", $id);
        foreach ($additionalWheres as $field => $condition) {
            $select->where("l.$field = ?", $condition);
        }

        // add status filter
        $this->_addAttributeFilter(
            $select,
            'status',
            'l',
            $idField,
            $store,
            Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
        );
        // add website filter
        if ($websiteId = Mage::app()->getStore($store)->getWebsiteId()) {
            $select->join(
                ['w' => $this->getTable('catalog/product_website')],
                "l.{$idField}=w.product_id AND w.website_id={$websiteId}",
                [],
            );
        }

        $this->_setLinkSelect($select);
        $this->_prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres);

        return $this->_getWriteAdapter()->fetchCol($this->_getLinkSelect());
    }

    /**
     * Prepare select statement before 'fetchLinkInformation' function result fetch
     *
     * @param int $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param int $id
     * @param array $additionalWheres
     */
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = []) {}

    /**
     * Return minimal prices for specified products
     *
     * @param array $products
     * @param array $priceAttributes
     * @param int $store
     * @return mixed
     */
    public function getMinimalPrice($products, $priceAttributes, $store)
    {
        $website = Mage::app()->getStore($store)->getWebsiteId();

        $fields = ['customer_group_id', 'minimal_value' => 'MIN(value)'];
        $select = $this->_getReadAdapter()->select()
            ->from(['base' => $this->getTable('catalogindex/price')], $fields)
            ->where('base.entity_id in (?)', $products)
            ->where('base.attribute_id in (?)', $priceAttributes)
            ->where('base.website_id = ?', $website)
            ->group('base.customer_group_id');
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Return tier prices for specified product in specified website
     *
     * @param array $products
     * @param int $website
     * @return mixed
     */
    public function getTierPrices($products, $website)
    {
        $fields = [
            'entity_id',
            'type_id',
            'c.customer_group_id',
            'c.qty',
            'c.value',
            'c.all_groups',
        ];
        $condition = 'product.entity_id = c.entity_id';

        $select = $this->_getReadAdapter()->select()
            ->from(['product' => $this->getTable('catalog/product')], $fields)
            ->joinLeft(['c' => "{$this->getTable('catalog/product')}_tier_price"], $condition, [])
            ->where('product.entity_id in (?)', $products);
        if (Mage::helper('catalog')->isPriceGlobal()) {
            $select->where('c.website_id=?', 0);
        } elseif (Mage::app()->getWebsite($website)->getBaseCurrencyCode() != Mage::app()->getBaseCurrencyCode()) {
            $select->where('c.website_id=?', $website);
        } else {
            $select->where('c.website_id IN(?)', [0, $website]);
        }

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Add attribute filter to select
     *
     * @param string $attributeCode
     * @param string $table the main table name or alias
     * @param string $field entity_id field name
     * @param int $store
     * @param array|int|string $value the filter value
     * @return $this
     */
    protected function _addAttributeFilter(Varien_Db_Select $select, $attributeCode, $table, $field, $store, $value)
    {
        $adapter = $this->_getReadAdapter();
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        $attributeTable = $attribute->getBackend()->getTable();
        if ($attribute->getBackendType() == 'static') {
            $tableAlias = sprintf('t_%s', $attribute->getAttributeCode());
            $joinCond = implode(' AND ', [
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableAlias),
            ]);
            $select
                ->join(
                    [$tableAlias => $attributeTable],
                    $joinCond,
                    [],
                )
                ->where(sprintf('%s.%s IN(?)', $tableAlias, $attribute->getAttributeCode()), $value);
        } elseif ($attribute->isScopeGlobal()) {
            $tableAlias = sprintf('t_%s', $attribute->getAttributeCode());
            $joinCond = implode(' AND ', [
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableAlias),
                $adapter->quoteInto(sprintf('`%s`.`attribute_id`=?', $tableAlias), $attribute->getAttributeId()),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableAlias), 0),
            ]);
            $select
                ->join(
                    [$tableAlias => $attributeTable],
                    $joinCond,
                    [],
                )
                ->where(sprintf('%s.value IN(?)', $tableAlias), $value);
        } else {
            $tableGlobal    = sprintf('t_global_%s', $attribute->getAttributeCode());
            $tableStore     = sprintf('t_store_%s', $attribute->getAttributeCode());
            $joinCondGlobal = implode(' AND ', [
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableGlobal),
                $adapter->quoteInto(sprintf('`%s`.`attribute_id`=?', $tableGlobal), $attribute->getAttributeId()),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableGlobal), 0),
            ]);
            $joinCondStore = implode(' AND ', [
                sprintf('`%s`.`entity_id`=`%s`.`entity_id`', $tableGlobal, $tableStore),
                sprintf('`%s`.`attribute_id`=`%s`.`attribute_id`', $tableGlobal, $tableStore),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableStore), $store),
            ]);
            $whereCond      = sprintf(
                'IF(`%s`.`value_id`>0, `%s`.`value`, `%s`.`value`) IN(?)',
                $tableStore,
                $tableStore,
                $tableGlobal,
            );

            $select
                ->join(
                    [$tableGlobal => $attributeTable],
                    $joinCondGlobal,
                    [],
                )
                ->joinLeft(
                    [$tableStore => $attributeTable],
                    $joinCondStore,
                    [],
                )
                ->where($whereCond, $value);
        }

        return $this;
    }
}
