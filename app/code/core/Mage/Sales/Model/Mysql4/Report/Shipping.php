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

class Mage_Sales_Model_Mysql4_Report_Shipping extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_setResource(array('read', 'write'));
    }

    /**
     * Aggregate Shipping data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Shipping
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
        $this->_aggregateByShippingCreatedAt($from, $to);

        $reportsFlagModel = Mage::getModel('reports/flag');
        $reportsFlagModel->setReportFlagCode(Mage_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE);
        $reportsFlagModel->loadSelf();
        $reportsFlagModel->save();

        return $this;
    }

    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('sales/shipping_aggregated_order');
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
                $subQuery->from(array('so'=>$this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $columns = array(
                'period'                => "DATE(created_at)",
                'store_id'              => 'store_id',
                'order_status'          => 'status',
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'COUNT(entity_id)',
                'total_shipping'        => 'SUM(`base_shipping_amount` * `base_to_global_rate`)'
            );

            $select = $writeAdapter->select()
                ->from($this->getTable('sales/order'), $columns)
                ->where('state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ))
                ->where('is_virtual = 0');

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(created_at) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    "DATE(created_at)",
                    'store_id',
                    'order_status',
                    'shipping_description'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'order_status'          => 'order_status',
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'SUM(orders_count)',
                'total_shipping'        => 'SUM(total_shipping)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status',
                    'shipping_description'
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

    protected function _aggregateByShippingCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('sales/shipping_aggregated');
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
                $subQuery->from(array('so'=>$this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $columns = array(
                'period'                => "DATE(soe.created_at)",
                'store_id'              => 'so.store_id',
                'order_status'          => 'so.status',
                'shipping_description'  => 'so.shipping_description',
                'orders_count'          => 'COUNT(so.entity_id)',
                'total_shipping'        => 'SUM(so.`base_shipping_amount` * so.`base_to_global_rate`)'
            );

            $shipment = Mage::getResourceSingleton('sales/order_shipment');
            $shipmentAttr = $shipment->getAttribute('order_id');

            $select = $writeAdapter->select()
                    ->from(array('soe' => $this->getTable('sales/order_entity')), $columns)
                    ->where('state <> ?', 'canceled');


            $select->joinInner(array('soei' => $this->getTable($shipmentAttr->getBackend()->getTable())), "`soei`.`entity_id` = `soe`.`entity_id`
                AND `soei`.`attribute_id` = {$shipmentAttr->getAttributeId()}
                AND `soei`.`entity_type_id` = `soe`.`entity_type_id`",
                array()
            );

            $select->joinInner(array('so' => $this->getTable('sales/order')),
                '`soei`.`value` = `so`.`entity_id`  AND `so`.base_total_invoiced > 0',
                array()
            );

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(soe.created_at) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    "DATE(soe.created_at)",
                    'store_id',
                    'order_status',
                    'shipping_description'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'order_status'          => 'order_status',
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'SUM(orders_count)',
                'total_shipping'        => 'SUM(total_shipping)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status',
                    'shipping_description'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

        } catch (Exception $e) {
            $writeAdapter->rollback();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }
}
