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
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_CatalogIndex_Model_Mysql4_Data_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Attribute id by code cache
     *
     * @var array
     */
    protected $_attributeCodeIds = array();

    /**
     * Link select object
     *
     * @var Zend_Db_Select
     */
    protected $_linkSelect = null;

    /**
     * Set link select
     *
     * @param Zend_Db_Select $select
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
            $fields = array('entity_id', 'type_id', 'attribute_id'=>'IFNULL(c.attribute_id, d.attribute_id)', 'value'=>'IFNULL(c.value, d.value)');

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

    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = array()) {}

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
        $fields = array('customer_group_id', 'minimal_value'=>'MIN(value)');
        $select = $this->_getReadAdapter()->select()
            ->from(array('base'=>$this->getTable('catalogindex/price')), $fields)
            ->where('base.entity_id in (?)', $products)
            ->where('base.attribute_id in (?)', $priceAttributes)
            ->where('base.store_id = ?', $store)
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
            ->where('product.entity_id in (?)', $products)
            ->where('(c.website_id = ?', $website)
            ->orWhere('c.website_id = 0)');

        return $this->_getReadAdapter()->fetchAll($select);
    }
}