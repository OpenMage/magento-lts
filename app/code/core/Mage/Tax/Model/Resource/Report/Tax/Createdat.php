<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax report resource model with aggregation by created at
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Report_Tax_Createdat extends Mage_Reports_Model_Resource_Report_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByOrder('created_at', $from, $to);
    }

    /**
     * Aggregate Tax data by orders
     *
     * @throws Exception
     * @param string $aggregationField
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    protected function _aggregateByOrder($aggregationField, $from, $to)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales/order'),
                    'created_at',
                    'updated_at',
                    $from,
                    $to,
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $writeAdapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    ['e' => $this->getTable('sales/order')],
                    'e.' . $aggregationField,
                    $from,
                    $to,
                ),
            );

            $columns = [
                'period'                => $periodExpr,
                'store_id'              => 'e.store_id',
                'code'                  => 'tax.code',
                'order_status'          => 'e.status',
                'percent'               => 'MAX(tax.' . $writeAdapter->quoteIdentifier('percent') . ')',
                'orders_count'          => 'COUNT(DISTINCT e.entity_id)',
                'tax_base_amount_sum'   => 'SUM(tax.base_amount * e.base_to_global_rate)',
            ];

            $select = $writeAdapter->select();
            $select->from(['tax' => $this->getTable('tax/sales_order_tax')], $columns)
                ->joinInner(['e' => $this->getTable('sales/order')], 'e.entity_id = tax.order_id', [])
                ->useStraightJoin();

            $select->where('e.state NOT IN (?)', [
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage_Sales_Model_Order::STATE_NEW,
            ]);

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group([$periodExpr, 'e.store_id', 'code', 'tax.percent', 'e.status']);

            $insertQuery = $writeAdapter->insertFromSelect($select, $this->getMainTable(), array_keys($columns));
            $writeAdapter->query($insertQuery);

            $select->reset();

            $columns = [
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr((string) Mage_Core_Model_App::ADMIN_STORE_ID),
                'code'                  => 'code',
                'order_status'          => 'order_status',
                'percent'               => 'MAX(' . $writeAdapter->quoteIdentifier('percent') . ')',
                'orders_count'          => 'SUM(orders_count)',
                'tax_base_amount_sum'   => 'SUM(tax_base_amount_sum)',
            ];

            $select
                ->from($this->getMainTable(), $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(['period', 'code', 'percent', 'order_status']);
            $insertQuery = $writeAdapter->insertFromSelect($select, $this->getMainTable(), array_keys($columns));
            $writeAdapter->query($insertQuery);
            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        return $this;
    }
}
