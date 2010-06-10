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

class Mage_Sales_Model_Mysql4_Report_Refunded extends Mage_Sales_Model_Mysql4_Report_Abstract
{
    protected function _construct()
    {
        $this->_setResource('sales');
    }

    /**
     * Aggregate Refunded data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Refunded
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $this->_aggregateByOrderCreatedAt($from, $to);
        $this->_aggregateByRefundCreatedAt($from, $to);

        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE);
        return $this;
    }

    /**
     * Aggregate refunded data by order created at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Refunded
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table = $this->getTable('sales/refunded_aggregated_order');
        $sourceTable = $this->getTable('sales/order');
        $this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'            => "DATE(CONVERT_TZ(created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
                'store_id'          => 'store_id',
                'order_status'      => 'status',
                'orders_count'      => 'COUNT(`total_refunded`)',
                'refunded'          => 'SUM(`base_total_refunded` * `base_to_global_rate`)',
                'online_refunded'   => 'SUM(`base_total_online_refunded` * `base_to_global_rate`)',
                'offline_refunded'  => 'SUM(`base_total_offline_refunded` * `base_to_global_rate`)'
            );

            $select = $this->_getWriteAdapter()->select();
            $select->from($sourceTable, $columns)
                ->where('state <> ?', Mage_Sales_Model_Order::STATE_CANCELED)
                ->where('base_total_refunded > 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'created_at'));
            }

            $select->group(array(
                'period',
                'store_id',
                'order_status'
            ));

            $select->having('orders_count > 0');

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'            => 'period',
                'store_id'          => new Zend_Db_Expr('0'),
                'order_status'      => 'order_status',
                'orders_count'      => 'SUM(orders_count)',
                'refunded'          => 'SUM(refunded)',
                'online_refunded'   => 'SUM(online_refunded)',
                'offline_refunded'  => 'SUM(offline_refunded)'
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        $this->_getWriteAdapter()->commit();
        return $this;
    }

    /**
     * Aggregate refunded data by creaditmemo created at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Refunded
     */
    protected function _aggregateByRefundCreatedAt($from, $to)
    {
        $table = $this->getTable('sales/refunded_aggregated');
        $sourceTable = $this->getTable('sales/creditmemo');
        $orderTable = $this->getTable('sales/order');
        $this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeRelatedSelect(
                    $sourceTable, $orderTable, array('order_id'=>'entity_id'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'            => "DATE(CONVERT_TZ(source_table.created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
                'store_id'          => 'order_table.store_id',
                'order_status'      => 'order_table.status',
                'orders_count'      => 'COUNT(order_table.`entity_id`)',
                'refunded'          => 'SUM(order_table.`base_total_refunded` * order_table.`base_to_global_rate`)',
                'online_refunded'   => 'SUM(order_table.`base_total_online_refunded` * order_table.`base_to_global_rate`)',
                'offline_refunded'  => 'SUM(order_table.`base_total_offline_refunded` * order_table.`base_to_global_rate`)'
            );

            $select = $this->_getWriteAdapter()->select();
            $select->from(array('source_table' => $sourceTable), $columns)
                ->joinInner(
                    array('order_table' => $orderTable),
                    $this->_getWriteAdapter()->quoteInto(
                        'source_table.order_id = order_table.entity_id AND order_table.state <> ? AND order_table.base_total_refunded > 0',
                        Mage_Sales_Model_Order::STATE_CANCELED),
                    array()
                );

            $filterSubSelect = $this->_getWriteAdapter()->select();
            $filterSubSelect->from(array('filter_source_table' => $sourceTable), 'MAX(filter_source_table.entity_id)')
                ->where('filter_source_table.order_id = source_table.order_id');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'source_table.created_at'));
            }

            $select->where('source_table.entity_id = (?)', new Zend_Db_Expr($filterSubSelect));
            unset($filterSubSelect);

            $select->group(array(
                'period',
                'store_id',
                'order_status'
            ));

            $select->having('orders_count > 0');

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'            => 'period',
                'store_id'          => new Zend_Db_Expr('0'),
                'order_status'      => 'order_status',
                'orders_count'      => 'SUM(orders_count)',
                'refunded'          => 'SUM(refunded)',
                'online_refunded'   => 'SUM(online_refunded)',
                'offline_refunded'  => 'SUM(offline_refunded)'
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }
        $this->_getWriteAdapter()->commit();
        return $this;
    }
}
