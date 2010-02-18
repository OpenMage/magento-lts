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

/**
 * Order entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Mysql4_Order extends Mage_Eav_Model_Entity_Abstract
{

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('order');
        $read = $resource->getConnection('sales_read');
        $write = $resource->getConnection('sales_write');
        $this->setConnection($read, $write);
    }

    /**
     * Count existent products of order items by specified product types
     *
     * @param int $orderId
     * @param array $productTypeIds
     * @param bool $isProductTypeIn
     * @return array
     */
    public function aggregateProductsByTypes($orderId, $productTypeIds = array(), $isProductTypeIn = false)
    {
        $select = $this->getReadConnection()->select()
            ->from(array('o' => $this->getTable('sales/order_item')), new Zend_Db_Expr('o.product_type, COUNT(*)'))
            ->joinInner(array('p' => $this->getTable('catalog/product')), 'o.product_id=p.entity_id', array())
            ->where('o.order_id=?', $orderId)
            ->group('(1)')
        ;
        if ($productTypeIds) {
            $select->where($this->getReadConnection()->quoteInto(
                sprintf('(o.product_type %s (?))', ($isProductTypeIn ? 'IN' : 'NOT IN')),
                $productTypeIds
            ));
        }
        return $this->getReadConnection()->fetchPairs($select);
    }

    /**
     * Aggregate Orders data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Order
     */
    public function aggregate($from = null, $to = null)
    {
        $writeAdapter = $this->getWriteConnection();
        try {
            if (!is_null($from)) {
                $from = $this->formatDate($from);
            }
            if (!is_null($to)) {
                $from = $this->formatDate($to);
            }

            $tableName = $this->getTable('sales/order_aggregated_created');

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
                'period'                    => 'DATE(e.created_at)',
                'store_id'                  => 'e.store_id',
                'order_status'              => 'e.status',
                'orders_count'              => 'COUNT(e.entity_id)',
                'total_qty_ordered'         => 'SUM(e.total_qty_ordered)',
                'base_profit_amount'        => 'SUM(IFNULL(e.base_subtotal_invoiced, 0) * e.base_to_global_rate) + SUM(IFNULL(e.base_discount_refunded, 0) * e.base_to_global_rate) - SUM(IFNULL(e.base_subtotal_refunded, 0) * e.base_to_global_rate) - SUM(IFNULL(e.base_discount_invoiced, 0) * e.base_to_global_rate) - SUM(IFNULL(e.base_total_invoiced_cost, 0) * e.base_to_global_rate)',
                'base_subtotal_amount'      => 'SUM(e.base_subtotal * e.base_to_global_rate)',
                'base_tax_amount'           => 'SUM(e.base_tax_amount * e.base_to_global_rate)',
                'base_shipping_amount'      => 'SUM(e.base_shipping_amount * e.base_to_global_rate)',
                'base_discount_amount'      => 'SUM(e.base_discount_amount * e.base_to_global_rate)',
                'base_grand_total_amount'   => 'SUM(e.base_grand_total * e.base_to_global_rate)',
                'base_invoiced_amount'      => 'SUM(e.base_total_paid * e.base_to_global_rate)',
                'base_refunded_amount'      => 'SUM(e.base_total_refunded * e.base_to_global_rate)',
                'base_canceled_amount'      => 'SUM(IFNULL(e.subtotal_canceled, 0) * e.base_to_global_rate)'
            );

            $select = $writeAdapter->select()
                ->from(array('e' => $this->getTable('sales/order')), $columns)
                ->where('e.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ));

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(e.created_at) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(new Zend_Db_Expr('1,2,3'));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();
            $columns = array(
                'period'                    => 'period',
                'store_id'                  => new Zend_Db_Expr('0'),
                'order_status'              => 'order_status',
                'orders_count'              => 'SUM(orders_count)',
                'total_qty_ordered'         => 'SUM(total_qty_ordered)',
                'base_profit_amount'        => 'SUM(base_profit_amount)',
                'base_subtotal_amount'      => 'SUM(base_subtotal_amount)',
                'base_tax_amount'           => 'SUM(base_tax_amount)',
                'base_shipping_amount'      => 'SUM(base_shipping_amount)',
                'base_discount_amount'      => 'SUM(base_discount_amount)',
                'base_grand_total_amount'   => 'SUM(base_grand_total_amount)',
                'base_invoiced_amount'      => 'SUM(base_invoiced_amount)',
                'base_refunded_amount'      => 'SUM(base_refunded_amount)',
                'base_canceled_amount'      => 'SUM(base_canceled_amount)'
            );
            $select->from($tableName, $columns)
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

            $reportsFlagModel = Mage::getModel('reports/flag');
            $reportsFlagModel->setReportFlagCode(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);
            $reportsFlagModel->loadSelf();
            $reportsFlagModel->save();

        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }
}

