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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Invoice report resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report_Invoiced extends Mage_Sales_Model_Resource_Report_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_setResource('sales');
    }

    /**
     * Aggregate Invoiced data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Invoiced
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to   = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $this->_aggregateByOrderCreatedAt($from, $to);
        $this->_aggregateByInvoiceCreatedAt($from, $to);

        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE);
        return $this;
    }

    /**
     * Aggregate Invoiced data by invoice created_at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Invoiced
     */
    protected function _aggregateByInvoiceCreatedAt($from, $to)
    {
        $table       = $this->getTable('sales/invoiced_aggregated');
        $sourceTable = $this->getTable('sales/invoice');
        $orderTable  = $this->getTable('sales/order');
        $helper      = Mage::getResourceHelper('core');
        $adapter     = $this->_getWriteAdapter();

        $adapter->beginTransaction();

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
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('source_table' => $sourceTable),
                    'source_table.created_at', $from, $to
                )
            );
            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                => $periodExpr,
                'store_id'              => 'order_table.store_id',
                'order_status'          => 'order_table.status',
                'orders_count'          => new Zend_Db_Expr('COUNT(order_table.entity_id)'),
                'orders_invoiced'       => new Zend_Db_Expr('COUNT(order_table.entity_id)'),
                'invoiced'              => new Zend_Db_Expr('SUM(order_table.base_total_invoiced'
                    . ' * order_table.base_to_global_rate)'),
                'invoiced_captured'     => new Zend_Db_Expr('SUM(order_table.base_total_paid'
                    . ' * order_table.base_to_global_rate)'),
                'invoiced_not_captured' => new Zend_Db_Expr(
                    'SUM((order_table.base_total_invoiced - order_table.base_total_paid)'
                    . ' * order_table.base_to_global_rate)')
            );

            $select = $adapter->select();
            $select->from(array('source_table' => $sourceTable), $columns)
                ->joinInner(
                    array('order_table' => $orderTable),
                    $adapter->quoteInto(
                        'source_table.order_id = order_table.entity_id AND order_table.state <> ?',
                        Mage_Sales_Model_Order::STATE_CANCELED),
                    array()
                );

            $filterSubSelect = $adapter->select();
            $filterSubSelect->from(array('filter_source_table' => $sourceTable), 'MAX(filter_source_table.entity_id)')
                ->where('filter_source_table.order_id = source_table.order_id');

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->where('source_table.entity_id = (?)', new Zend_Db_Expr($filterSubSelect));
            unset($filterSubSelect);

            $select->group(array(
                $periodExpr,
                'order_table.store_id',
                'order_table.status'
            ));

            $select->having('orders_count > 0');
            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);
            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr(Mage_Core_Model_App::ADMIN_STORE_ID),
                'order_status'          => 'order_status',
                'orders_count'          => new Zend_Db_Expr('SUM(orders_count)'),
                'orders_invoiced'       => new Zend_Db_Expr('SUM(orders_invoiced)'),
                'invoiced'              => new Zend_Db_Expr('SUM(invoiced)'),
                'invoiced_captured'     => new Zend_Db_Expr('SUM(invoiced_captured)'),
                'invoiced_not_captured' => new Zend_Db_Expr('SUM(invoiced_not_captured)')
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Aggregate Invoiced data by order created_at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Invoiced
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table       = $this->getTable('sales/invoiced_aggregated_order');
        $sourceTable = $this->getTable('sales/order');
        $adapter     = $this->_getWriteAdapter();


            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    $sourceTable, 'created_at', $from, $to
                )
            );

            $columns = array(
                'period'                => $periodExpr,
                'store_id'              => 'store_id',
                'order_status'          => 'status',
                'orders_count'          => new Zend_Db_Expr('COUNT(base_total_invoiced)'),
                'orders_invoiced'       => new Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getCheckSql('base_total_invoiced > 0', 1, 0)
                    )
                ),
                'invoiced'              => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('base_total_invoiced',0),
                        $adapter->getIfNullSql('base_to_global_rate',0)
                    )
                ),
                'invoiced_captured'     => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('base_total_paid',0),
                        $adapter->getIfNullSql('base_to_global_rate',0)
                    )
                ),
                'invoiced_not_captured' => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('base_total_invoiced',0),
                        $adapter->getIfNullSql('base_total_paid',0),
                        $adapter->getIfNullSql('base_to_global_rate',0)
                    )
                )

            );

            $select = $adapter->select();
            $select->from($sourceTable, $columns)
                ->where('state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                $periodExpr,
                'store_id',
                'status'
            ));

            $select->having('orders_count > 0');

            $helper      = Mage::getResourceHelper('core');
            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);
            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr(Mage_Core_Model_App::ADMIN_STORE_ID),
                'order_status'          => 'order_status',
                'orders_count'          => new Zend_Db_Expr('SUM(orders_count)'),
                'orders_invoiced'       => new Zend_Db_Expr('SUM(orders_invoiced)'),
                'invoiced'              => new Zend_Db_Expr('SUM(invoiced)'),
                'invoiced_captured'     => new Zend_Db_Expr('SUM(invoiced_captured)'),
                'invoiced_not_captured' => new Zend_Db_Expr('SUM(invoiced_not_captured)')
            );

            $select->from($table, $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $helper      = Mage::getResourceHelper('core');
            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);


        return $this;
    }
}
