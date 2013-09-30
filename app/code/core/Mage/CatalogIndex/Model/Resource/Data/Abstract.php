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
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Resource model CatalogIndex Data Abstract
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Data_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Attribute id by code cache
     *
     * @var array
     */
    protected $_attributeCodeIds     = array();

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
     * @return Mage_CatalogIndex_Model_Resource_Data_Abstract
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
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Retreive specified attribute data for specified products from specified store
     *
     * @param array $products
     * @param array $attributes
     * @param int $store
     * @return unknown
     */
    public function getAttributeData($products, $attributes, $store)
    {
        $suffixes = array('decimal', 'varchar', 'int', 'text', 'datetime');
        if (!is_array($products)) {
            $products = new Zend_Db_Expr($products);
        }
        $result = array();
        foreach ($suffixes as $suffix) {
            $tableName = "{$this->getTable('catalog/product')}_{$suffix}";
            $condition = "product.entity_id = c.entity_id AND c.store_id = {$store} AND c.attribute_id = d.attribute_id";
            $defaultCondition = "product.entity_id = d.entity_id AND d.store_id = 0";
            $fields = array(
                'entity_id',
                'type_id',
                'attribute_id'  => 'IF(c.value_id > 0, c.attribute_id, d.attribute_id)',
                'value'         => 'IF(c.value_id > 0, c.value, d.value)'
            );

            $select = $this->_getReadAdapter()->select()
                ->from(array('product'=>$this->getTable('catalog/product')), $fields)
                ->where('product.entity_id in (?)', $products)
                ->joinRight(array('d'=>$tableName), $defaultCondition, array())
                ->joinLeft(array('c'=>$tableName), $condition, array())
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
     * @param int $id
     * @param array $additionalWheres
     * @return mixed
     */
    public function fetchLinkInformation($store, $table, $idField, $whereField, $id, $additionalWheres = array())
    {
        $idsConditionSymbol = "= ?";
        if (is_array($id)) {
            $idsConditionSymbol = "in (?)";
        }

        $select = $this->_getReadAdapter()->select();
        $select->from(array('l'=>$this->getTable($table)), array("l.{$idField}"))
            ->where("l.{$whereField} {$idsConditionSymbol}", $id);
        foreach ($additionalWheres as $field=>$condition) {
            $select->where("l.$field = ?", $condition);
        }

        // add status filter
        $this->_addAttributeFilter($select, 'status', 'l', $idField, $store,
            Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        // add website filter
        if ($websiteId = Mage::app()->getStore($store)->getWebsiteId()) {
            $select->join(
                array('w' => $this->getTable('catalog/product_website')),
                "l.{$idField}=w.product_id AND w.website_id={$websiteId}",
                array()
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
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = array())
    {

    }

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

        $fields = array('customer_group_id', 'minimal_value'=>'MIN(value)');
        $select = $this->_getReadAdapter()->select()
            ->from(array('base'=>$this->getTable('catalogindex/price')), $fields)
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
        $fields = array(
            'entity_id',
            'type_id',
            'c.customer_group_id',
            'c.qty',
            'c.value',
            'c.all_groups',
        );
        $condition = "product.entity_id = c.entity_id";

        $select = $this->_getReadAdapter()->select()
            ->from(array('product'=>$this->getTable('catalog/product')), $fields)
            ->joinLeft(array('c'=>"{$this->getTable('catalog/product')}_tier_price"), $condition, array())
            ->where('product.entity_id in (?)', $products);
        if (Mage::helper('catalog')->isPriceGlobal())
        {
            $select->where('c.website_id=?', 0);
        }
        elseif (Mage::app()->getWebsite($website)->getBaseCurrencyCode() != Mage::app()->getBaseCurrencyCode()) {
            $select->where('c.website_id=?', $website);
        }
        else {
            $select->where('c.website_id IN(?)', array(0, $website));
        }

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Add attribute filter to select
     *
     * @param Varien_Db_Select $select
     * @param string $attributeCode
     * @param string $table the main table name or alias
     * @param string $field entity_id field name
     * @param int $store
     * @param int|string|array $value the filter value
     * @return Mage_CatalogIndex_Model_Resource_Data_Abstract
     */
    protected function _addAttributeFilter(Varien_Db_Select $select, $attributeCode, $table, $field, $store, $value)
    {
        $adapter = $this->_getReadAdapter();
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeTable = $attribute->getBackend()->getTable();
        if ($attribute->getBackendType() == 'static') {
            $tableAlias = sprintf('t_%s', $attribute->getAttributeCode());
            $joinCond   = join(' AND ', array(
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableAlias)
            ));
            $select
                ->join(
                    array($tableAlias => $attributeTable),
                    $joinCond,
                    array())
                ->where(sprintf('%s.%s IN(?)', $tableAlias, $attribute->getAttributeCode()), $value);
        }
        elseif ($attribute->isScopeGlobal()) {
            $tableAlias = sprintf('t_%s', $attribute->getAttributeCode());
            $joinCond   = join(' AND ', array(
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableAlias),
                $adapter->quoteInto(sprintf('`%s`.`attribute_id`=?', $tableAlias), $attribute->getAttributeId()),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableAlias), 0)
            ));
            $select
                ->join(
                    array($tableAlias => $attributeTable),
                    $joinCond,
                    array())
                ->where(sprintf('%s.value IN(?)', $tableAlias), $value);
        }
        else {
            $tableGlobal    = sprintf('t_global_%s', $attribute->getAttributeCode());
            $tableStore     = sprintf('t_store_%s', $attribute->getAttributeCode());
            $joinCondGlobal = join(' AND ', array(
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableGlobal),
                $adapter->quoteInto(sprintf('`%s`.`attribute_id`=?', $tableGlobal), $attribute->getAttributeId()),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableGlobal), 0)
            ));
            $joinCondStore  = join(' AND ', array(
                sprintf('`%s`.`entity_id`=`%s`.`entity_id`', $tableGlobal, $tableStore),
                sprintf('`%s`.`attribute_id`=`%s`.`attribute_id`', $tableGlobal, $tableStore),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableStore), $store)
            ));
            $whereCond      = sprintf('IF(`%s`.`value_id`>0, `%s`.`value`, `%s`.`value`) IN(?)',
                $tableStore, $tableStore, $tableGlobal);

            $select
                ->join(
                    array($tableGlobal => $attributeTable),
                    $joinCondGlobal,
                    array())
                ->joinLeft(
                    array($tableStore => $attributeTable),
                    $joinCondStore,
                    array())
                ->where($whereCond, $value);
        }

        return $this;
    }
}
