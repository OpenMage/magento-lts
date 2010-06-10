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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bestsellers report resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Mysql4_Report_Bestsellers extends Mage_Sales_Model_Mysql4_Report_Abstract
{
    const AGGREGATION_DAILY    = 'daily';
    const AGGREGATION_MONTHLY  = 'monthly';
    const AGGREGATION_YEARLY   = 'yearly';

    protected function _construct()
    {
        $this->_init('sales/bestsellers_aggregated_' . self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Order
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        //$this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales/order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                         => "DATE(CONVERT_TZ(source_table.created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
                'store_id'                       => 'source_table.store_id',
                'product_id'                     => 'order_item.product_id',
                'product_name'                   => 'IFNULL(product_name.value, product_default_name.value)',
                'product_price'                  => 'IFNULL(product_price.value, product_default_price.value) * IFNULL(source_table.base_to_global_rate, 0)',
                'qty_ordered'                    => 'SUM(order_item.qty_ordered)',
            );

            $select = $this->_getWriteAdapter()->select();

            $select->from(array('source_table' => $this->getTable('sales/order')), $columns)
                ->joinInner(
                    array('order_item' => $this->getTable('sales/order_item')),
                    'order_item.order_id = source_table.entity_id',
                    array()
                )
                ->where('source_table.state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);


            /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product $product */
            $product = Mage::getResourceSingleton('catalog/product');

            $productTypes = array(
                Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
            );
            $select->joinInner(
                array('product' => $this->getTable('catalog/product')),
                'product.entity_id = order_item.product_id'
                . ' AND product.entity_type_id = ' . $product->getTypeId()
                . " AND product.type_id NOT IN('" . implode("', '", $productTypes) . "')",
                array()
            );

            // join product attributes Name & Price
            $attr = $product->getAttribute('name');
            $select->joinLeft(array('product_name' => $attr->getBackend()->getTable()),
                'product_name.entity_id = product.entity_id'
                . ' AND product_name.store_id = source_table.store_id'
                . ' AND product_name.entity_type_id = ' . $product->getTypeId()
                . ' AND product_name.attribute_id = ' . $attr->getAttributeId(),
                array())
                ->joinLeft(array('product_default_name' => $attr->getBackend()->getTable()),
                'product_default_name.entity_id = product.entity_id'
                . ' AND product_default_name.store_id = 0'
                . ' AND product_default_name.entity_type_id = ' . $product->getTypeId()
                . ' AND product_default_name.attribute_id = ' . $attr->getAttributeId(),
                array());

            $attr = $product->getAttribute('price');
            $select->joinLeft(array('product_price' => $attr->getBackend()->getTable()),
                'product_price.entity_id = product.entity_id'
                . ' AND product_price.store_id = source_table.store_id'
                . ' AND product_price.entity_type_id = ' . $product->getTypeId()
                . ' AND product_price.attribute_id = ' . $attr->getAttributeId(),
                array())
                ->joinLeft(array('product_default_price' => $attr->getBackend()->getTable()),
                'product_default_price.entity_id = product.entity_id'
                . ' AND product_default_price.store_id = 0'
                . ' AND product_default_price.entity_type_id = ' . $product->getTypeId()
                . ' AND product_default_price.attribute_id = ' . $attr->getAttributeId(),
                array());


            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'source_table.created_at'));
            }

            $select->group(new Zend_Db_Expr('1,2,3'));

            $select->useStraightJoin();  // important!
            $sql = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $this->_getWriteAdapter()->query($sql);

            $columns = array(
                'period'                         => 'period',
                'store_id'                       => new Zend_Db_Expr('0'),
                'product_id'                     => 'product_id',
                'product_name'                   => 'product_name',
                'product_price'                  => 'product_price',
                'qty_ordered'                    => 'SUM(qty_ordered)',
            );

            $select->reset();
            $select->from($this->getMainTable(), $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'product_id'
            ));

            $sql = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $this->_getWriteAdapter()->query($sql);


            // update rating
            $this->_updateRatingPos(self::AGGREGATION_DAILY);
            $this->_updateRatingPos(self::AGGREGATION_MONTHLY);
            $this->_updateRatingPos(self::AGGREGATION_YEARLY);


            $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE);
        } catch (Exception $e) {
            //$this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        //$this->_getWriteAdapter()->commit();
        return $this;
    }

    /**
     * Update rating position
     *
     * @param string $aggregation One of Mage_Sales_Model_Mysql4_Report_Bestsellers::AGGREGATION_XXX constants
     * @return Mage_Sales_Model_Mysql4_Report_Bestsellers
     */
    protected function _updateRatingPos($aggregation)
    {
        $aggregationTable = $this->getTable('sales/bestsellers_aggregated_' . $aggregation);

        $periodSubSelect = $this->_getWriteAdapter()->select();
        $ratingSubSelect = $this->_getWriteAdapter()->select();
        $ratingSelect = $this->_getWriteAdapter()->select();

        $periodCol = 't.period';
        if ($aggregation == self::AGGREGATION_MONTHLY) {
            $periodCol = "DATE_FORMAT(t.period, '%Y-%m-01')";
        } else if ($aggregation == self::AGGREGATION_YEARLY) {
            $periodCol = "DATE_FORMAT(t.period, '%Y-01-01')";
        }

        $columns = array(
            'period'        => 't.period',
            'store_id'      => 't.store_id',
            'product_id'    => 't.product_id',
            'product_name'  => 't.product_name',
            'product_price' => 't.product_price',
        );

        if ($aggregation == self::AGGREGATION_DAILY) {
            $columns['id'] = 't.id';  // to speed-up insert on duplicate key update
        }

        $cols = array_keys($columns);
        $cols[] = new Zend_Db_Expr('SUM(t.`qty_ordered`) AS `total_qty_ordered`');
        $periodSubSelect->from(array('t' => $this->getMainTable()), $cols)
            ->group(array('t.store_id', $periodCol, 't.product_id'))
            ->order(array('t.store_id', $periodCol, 'total_qty_ordered DESC'));

        $cols = $columns;
        $cols['qty_ordered'] = 't.total_qty_ordered';
        $cols['rating_pos']  = new Zend_Db_Expr("(@pos := IF(t.`store_id` <> @prevStoreId OR {$periodCol} <> @prevPeriod, 1, @pos+1))");
        $cols['prevStoreId'] = new Zend_Db_Expr('(@prevStoreId := t.`store_id`)');
        $cols['prevPeriod']  = new Zend_Db_Expr("(@prevPeriod := {$periodCol})");
        $ratingSubSelect->from($periodSubSelect, $cols);

        $cols = $columns;
        $cols['period']      = $periodCol;  // important!
        $cols['qty_ordered'] = 't.qty_ordered';
        $cols['rating_pos']  = 't.rating_pos';
        $ratingSelect->from($ratingSubSelect, $cols);

        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));

        $this->_getWriteAdapter()->query("SET @pos = 0, @prevStoreId = -1, @prevPeriod = '0000-00-00'");
        $this->_getWriteAdapter()->query($sql);

        return $this;
    }
}

