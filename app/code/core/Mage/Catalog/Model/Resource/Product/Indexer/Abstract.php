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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Indexer Abstract Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Resource_Product_Indexer_Abstract extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Retrieve catalog_product attribute instance by attribute code
     *
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
    }

    /**
     * Add attribute join condition to select and return Zend_Db_Expr
     * attribute value definition
     * If $condition is not empty apply limitation for select
     *
     * @param Varien_Db_Select $select
     * @param string $attrCode              the attribute code
     * @param string|Zend_Db_Expr $entity   the entity field or expression for condition
     * @param string|Zend_Db_Expr $store    the store field or expression for condition
     * @param Zend_Db_Expr $condition       the limitation condition
     * @param bool $required                if required or has condition used INNER join, else - LEFT
     * @return Zend_Db_Expr                 the attribute value expression
     */
    protected function _addAttributeToSelect($select, $attrCode, $entity, $store, $condition = null, $required = false)
    {
        $attribute      = $this->_getAttribute($attrCode);
        $attributeId    = $attribute->getAttributeId();
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $this->_getReadAdapter();
        $joinType       = !is_null($condition) || $required ? 'join' : 'joinLeft';

        if ($attribute->isScopeGlobal()) {
            $alias = 'ta_' . $attrCode;
            $select->$joinType(
                array($alias => $attributeTable),
                "{$alias}.entity_id = {$entity} AND {$alias}.attribute_id = {$attributeId}"
                    . " AND {$alias}.store_id = 0",
                array()
            );
            $expression = new Zend_Db_Expr("{$alias}.value");
        } else {
            $dAlias = 'tad_' . $attrCode;
            $sAlias = 'tas_' . $attrCode;

            $select->$joinType(
                array($dAlias => $attributeTable),
                "{$dAlias}.entity_id = {$entity} AND {$dAlias}.attribute_id = {$attributeId}"
                    . " AND {$dAlias}.store_id = 0",
                array()
            );
            $select->joinLeft(
                array($sAlias => $attributeTable),
                "{$sAlias}.entity_id = {$entity} AND {$sAlias}.attribute_id = {$attributeId}"
                    . " AND {$sAlias}.store_id = {$store}",
                array()
            );
            $expression = $adapter->getCheckSql($adapter->getIfNullSql("{$sAlias}.value_id", -1) . ' > 0',
                "{$sAlias}.value", "{$dAlias}.value");
        }

        if (!is_null($condition)) {
            $select->where("{$expression}{$condition}");
        }

        return $expression;
    }

    /**
     * Add website data join to select
     * If add default store join also limitation of only has default store website
     * Joined table has aliases
     *  cw for website table,
     *  csg for store group table (joined by website default group)
     *  cs for store table (joined by website default store)
     *
     * @param Varien_Db_Select $select              the select object
     * @param bool $store                           add default store join
     * @param string|Zend_Db_Expr $joinCondition    the limitation for website_id
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Abstract
     */
    protected function _addWebsiteJoinToSelect($select, $store = true, $joinCondition = null)
    {
        if (!is_null($joinCondition)) {
            $joinCondition = 'cw.website_id = ' . $joinCondition;
        }

        $select->join(
            array('cw' => $this->getTable('core/website')),
            $joinCondition,
            array()
        );

        if ($store) {
            $select->join(
                array('csg' => $this->getTable('core/store_group')),
                'csg.group_id = cw.default_group_id',
                array())
            ->join(
                array('cs' => $this->getTable('core/store')),
                'cs.store_id = csg.default_store_id',
                array());
        }

        return $this;
    }

    /**
     * Add join for catalog/product_website table
     * Joined table has alias pw
     *
     * @param Varien_Db_Select $select          the select object
     * @param string|Zend_Db_Expr $website      the limitation of website_id
     * @param string|Zend_Db_Expr $product      the limitation of product_id
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Abstract
     */
    protected function _addProductWebsiteJoinToSelect($select, $website, $product)
    {
        $select->join(
            array('pw' => $this->getTable('catalog/product_website')),
            "pw.product_id = {$product} AND pw.website_id = {$website}",
            array()
        );

        return $this;
    }

    /**
     * Retrieve product relations by children
     *
     * @param int|array $childIds
     * @return array
     */
    public function getRelationsByChild($childIds)
    {
        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), 'parent_id')
            ->where('child_id IN(?)', $childIds);

        return $write->fetchCol($select);
    }

    /**
     * Retrieve product relations by parents
     *
     * @param int|array $parentIds
     * @return array
     */
    public function getRelationsByParent($parentIds)
    {
        if (!is_array($parentIds)) {
            $parentIds = array($parentIds);
        }

        $result = array();
        if (!empty($parentIds)) {
            $write = $this->_getWriteAdapter();
            $select = $write->select()
                ->from($this->getTable('catalog/product_relation'), 'child_id')
                ->where('parent_id IN(?)', $parentIds);
            $result = $write->fetchCol($select);
        }

        return $result;
    }
}
