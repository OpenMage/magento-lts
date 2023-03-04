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
 * Catalog Layer Decimal attribute Filter Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Layer_Filter_Decimal extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_index_eav_decimal', 'entity_id');
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @param float $range
     * @param int $index
     * @return $this
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = [
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId())
        ];

        $collection->getSelect()->join(
            [$tableAlias => $this->getMainTable()],
            implode(' AND ', $conditions),
            []
        );

        $collection->getSelect()
            ->where("{$tableAlias}.value >= ?", ($range * ($index - 1)))
            ->where("{$tableAlias}.value < ?", ($range * $index));

        return $this;
    }

    /**
     * Retrieve array of minimal and maximal values
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @return array
     */
    public function getMinMax($filter)
    {
        $select     = $this->_getSelect($filter);
        $adapter    = $this->_getReadAdapter();

        $select->columns([
            'min_value' => new Zend_Db_Expr('MIN(decimal_index.value)'),
            'max_value' => new Zend_Db_Expr('MAX(decimal_index.value)'),
        ]);

        $result     = $adapter->fetchRow($select);

        return [$result['min_value'], $result['max_value']];
    }

    /**
     * Retrieve clean select with joined index table
     * Joined table has index
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        $collection = $filter->getLayer()->getProductCollection();

        // clone select from collection with filters
        $select = clone $collection->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $attributeId = $filter->getAttributeModel()->getId();
        $storeId     = $collection->getStoreId();

        $select->join(
            ['decimal_index' => $this->getMainTable()],
            'e.entity_id = decimal_index.entity_id' .
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.attribute_id = ?', $attributeId) .
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.store_id = ?', $storeId),
            []
        );

        return $select;
    }

    /**
     * Retrieve array with products counts per range
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @param int $range
     * @return array
     */
    public function getCount($filter, $range)
    {
        $select     = $this->_getSelect($filter);
        $adapter    = $this->_getReadAdapter();

        $countExpr  = new Zend_Db_Expr("COUNT(*)");
        $rangeExpr  = new Zend_Db_Expr("FLOOR(decimal_index.value / {$range}) + 1");

        $select->columns([
            'decimal_range' => $rangeExpr,
            'count' => $countExpr
        ]);
        $select->group($rangeExpr);

        return $adapter->fetchPairs($select);
    }
}
