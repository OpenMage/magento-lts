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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Sales_Model_Mysql4_Report_Invoiced extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_setResource(array('read', 'write'));
    }

    /**
     * Aggregate Invoiced data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Invoiced
     */
    public function aggregate($from = null, $to = null)
    {
        if (!is_null($from)) {
            $from = $this->formatDate($from);
        }
        if (!is_null($to)) {
            $from = $this->formatDate($to);
        }
        $this->_aggregateByOrderCreatedAt($from, $to);
        $this->_aggregateByInvoiceCreatedAt($from, $to);

        $reportsFlagModel = Mage::getModel('reports/flag');
        $reportsFlagModel->setReportFlagCode(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE);
        $reportsFlagModel->loadSelf();
        $reportsFlagModel->save();

        return $this;
    }

    /**
     * Aggregate Invoiced data by invoice created_at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Invoiced
     */
    protected function _aggregateByInvoiceCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('sales/invoiced_aggregated');
            $writeAdapter = $this->_getWriteAdapter();

            $writeAdapter->beginTransaction();

            if (is_null($from) && is_null($to)) {
                $writeAdapter->query("TRUNCATE TABLE {$tableName}");
            } else {
                $where = (!is_null($from)) ? "so.updated_at >= '{$from}'" : '';
                if (!is_null($to)) {
                    $where .= (!empty($where)) ? " AND so.updated_at <= '{$to}'" : "so.updated_at <= '{$to}'";
                }

                $subQuery = $writeAdapter->select();
                $subQuery->from(array('so' => $this->getTable('sales/order') ), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $invoice = Mage::getResourceSingleton('sales/order_invoice');
            $invoiceAttr = $invoice->getAttribute('order_id');

            $columns = array(
                'period'                => "DATE(soe.created_at)",
                'store_id'              => 'so.store_id',
                'order_status'          => 'so.status',
                'orders_count'          => 'COUNT(so.entity_id)',
                'orders_invoiced'       => 'COUNT(so.entity_id)',
                'invoiced'              => 'SUM(so.base_total_invoiced * so.base_to_global_rate)',
                'invoiced_captured'     => 'SUM(so.base_total_paid * so.base_to_global_rate)',
                'invoiced_not_captured' => 'SUM((so.base_total_invoiced - so.base_total_paid) * so.base_to_global_rate)'
            );

            $select = $writeAdapter->select()
                ->from(array('soe' => $this->getTable('sales/order_entity')), $columns)
                ->where('state <> ?', 'canceled');

            $select->joinInner(array('soei' => $this->getTable($invoiceAttr->getBackend()->getTable())),
                "`soei`.`entity_id` = `soe`.`entity_id`
                AND `soei`.`attribute_id` = {$invoiceAttr->getAttributeId()}
                AND `soei`.`entity_type_id` = `soe`.`entity_type_id`",
                array()
            );

            $select->joinInner(array(
                'so' => $this->getTable('sales/order')),
                '`soei`.`value` = `so`.`entity_id`  AND `so`.base_total_invoiced > 0',
                array()
            );

            if (!is_null($from) || !is_null($to)) {
                $select->where("DATE(soe.created_at) IN(?)", new Zend_Db_Expr($subQuery));
            }

            $select->group(array(
                "DATE(`soe`.created_at)",
                'store_id',
                'order_status'
            ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'order_status'          => 'order_status',
                'orders_count'          => 'SUM(orders_count)',
                'orders_invoiced'       => 'SUM(orders_invoiced)',
                'invoiced'              => 'SUM(invoiced)',
                'invoiced_captured'     => 'SUM(invoiced_captured)',
                'invoiced_not_captured' => 'SUM(invoiced_not_captured)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }

    /**
     * Aggregate Invoiced data by order created_at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Invoiced
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('sales/invoiced_aggregated_order');
            $writeAdapter = $this->_getWriteAdapter();

            $writeAdapter->beginTransaction();

            if (is_null($from) && is_null($to)) {
                $writeAdapter->query("TRUNCATE TABLE {$tableName}");
            } else {
                $where = (!is_null($from)) ? "so.updated_at >= '{$from}'" : '';
                if (!is_null($to)) {
                    $where .= (!empty($where)) ? " AND so.updated_at <= '{$to}'" : "so.updated_at <= '{$to}'";
                }

                $subQuery = $writeAdapter->select();
                $subQuery->from(array('so' => $this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $columns = array(
                'period'                => "DATE(created_at)",
                'store_id'              => 'store_id',
                'order_status'          => 'status',
                'orders_count'          => 'COUNT(`base_total_invoiced`)',
                'orders_invoiced'       => 'SUM(IF(`base_total_invoiced` > 0, 1, 0))',
                'invoiced'              => 'SUM(`base_total_invoiced` * `base_to_global_rate`)',
                'invoiced_captured'     => 'SUM(`base_total_paid` * `base_to_global_rate`)',
                'invoiced_not_captured' => 'SUM((`base_total_invoiced` - `base_total_paid`) * `base_to_global_rate`)'
            );

            $select = $writeAdapter->select()
                ->from($this->getTable('sales/order'), $columns)
                ->where('state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);

            if (!is_null($from) || !is_null($to)) {
                $select->where("DATE(created_at) IN(?)", new Zend_Db_Expr($subQuery));
            }

            $select->group(array(
                "DATE(created_at)",
                'store_id',
                'order_status'
            ));

            $select->having('orders_count > 0');

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'order_status'          => 'order_status',
                'orders_count'          => 'SUM(orders_count)',
                'orders_invoiced'       => 'SUM(orders_invoiced)',
                'invoiced'              => 'SUM(invoiced)',
                'invoiced_captured'     => 'SUM(invoiced_captured)',
                'invoiced_not_captured' => 'SUM(invoiced_not_captured)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }


}
