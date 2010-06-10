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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rule report resource model
 *
 */

class Mage_SalesRule_Model_Mysql4_Report_Rule extends Mage_Reports_Model_Mysql4_Report_Abstract
{
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }


    /**
     * Aggregate Coupons data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Mysql4_Report_Rule
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $this->_aggregateByOrderCreatedAt($from, $to);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE);
        return $this;
    }

    /**
     * Aggregate coupons reports by order created at as range
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Mysql4_Report_Rule
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table = $this->getTable('salesrule/coupon_aggregated');
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
                'period'                  => "DATE(CONVERT_TZ(created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
                'store_id'                => 'store_id',
                'order_status'            => 'status',
                'coupon_code'             => 'coupon_code',
                'coupon_uses'             => 'COUNT(`entity_id`)',
                'subtotal_amount'         => 'SUM((`base_subtotal` - IFNULL(`base_subtotal_canceled`, 0)) * `base_to_global_rate`)',
                'discount_amount'         => 'SUM((`base_discount_amount` - IFNULL(`base_discount_canceled`, 0)) * `base_to_global_rate`)',
                'total_amount'            => 'SUM((`base_subtotal` - IFNULL(`base_subtotal_canceled`, 0) - IFNULL(`base_discount_amount` - IFNULL(`base_discount_canceled`, 0), 0)) * `base_to_global_rate`)',
                'subtotal_amount_actual'  => 'SUM((`base_subtotal_invoiced` - IFNULL(`base_subtotal_refunded`, 0)) * `base_to_global_rate`)',
                'discount_amount_actual'  => 'SUM((`base_discount_invoiced` - IFNULL(`base_discount_refunded`, 0)) * `base_to_global_rate`)',
                'total_amount_actual'     => 'SUM((`base_subtotal_invoiced` - IFNULL(`base_subtotal_refunded`, 0) - IFNULL(`base_discount_invoiced` - IFNULL(`base_discount_refunded`, 0), 0)) * `base_to_global_rate`)',
            );

            $select = $this->_getWriteAdapter()->select();
            $select->from($sourceTable, $columns)
                 ->where('coupon_code <> ?', '');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'created_at'));
            }

            $select->group(array(
                'period',
                'store_id',
                'order_status',
                'coupon_code'
            ));

            $select->having('coupon_uses > 0');

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'                  => 'period',
                'store_id'                => new Zend_Db_Expr('0'),
                'order_status'            => 'order_status',
                'coupon_code'             => 'coupon_code',
                'coupon_uses'             => 'SUM(`coupon_uses`)',
                'subtotal_amount'         => 'SUM(`subtotal_amount`)',
                'discount_amount'         => 'SUM(`discount_amount`)',
                'total_amount'            => 'SUM(`total_amount`)',
                'subtotal_amount_actual'  => 'SUM(`subtotal_amount_actual`)',
                'discount_amount_actual'  => 'SUM(`discount_amount_actual`)',
                'total_amount_actual'     => 'SUM(`total_amount_actual`)',
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status',
                'coupon_code'
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
