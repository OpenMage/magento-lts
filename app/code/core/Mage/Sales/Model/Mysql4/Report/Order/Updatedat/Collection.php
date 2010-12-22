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
 * Report order updated_at collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{

    protected $_periodFormat;
    protected $_inited = false;
    protected $_selectedColumns = array(
        'orders_count'                   => 'COUNT(e.entity_id)',
        'total_qty_ordered'              => 'IFNULL(SUM(oi.total_qty_ordered), 0)',
        'total_qty_invoiced'             => 'IFNULL(SUM(oi.total_qty_invoiced), 0)',
        'total_income_amount'            => 'IFNULL(SUM((e.base_grand_total - IFNULL(e.base_total_canceled, 0)) * e.base_to_global_rate), 0)',
        'total_revenue_amount'           => 'IFNULL(SUM((e.base_total_paid - IFNULL(e.base_total_refunded, 0)) * e.base_to_global_rate), 0)',
        'total_profit_amount'            => 'IFNULL(SUM((e.base_total_paid - IFNULL(e.base_total_refunded, 0) - IFNULL(e.base_tax_invoiced, 0) - IFNULL(e.base_shipping_invoiced, 0) - IFNULL(e.base_total_invoiced_cost, 0)) * e.base_to_global_rate), 0)',
        'total_invoiced_amount'          => 'IFNULL(SUM(e.base_total_invoiced * e.base_to_global_rate), 0)',
        'total_canceled_amount'          => 'IFNULL(SUM(e.base_total_canceled * e.base_to_global_rate), 0)',
        'total_paid_amount'              => 'IFNULL(SUM(e.base_total_paid * e.base_to_global_rate), 0)',
        'total_refunded_amount'          => 'IFNULL(SUM(e.base_total_refunded * e.base_to_global_rate), 0)',
        'total_tax_amount'               => 'IFNULL(SUM((e.base_tax_amount - IFNULL(e.base_tax_canceled, 0)) * e.base_to_global_rate), 0)',
        'total_tax_amount_actual'        => 'IFNULL(SUM((e.base_tax_invoiced - IFNULL(e.base_tax_refunded, 0)) * e.base_to_global_rate), 0)',
        'total_shipping_amount'          => 'IFNULL(SUM((e.base_shipping_amount - IFNULL(e.base_shipping_canceled, 0)) * e.base_to_global_rate), 0)',
        'total_shipping_amount_actual'   => 'IFNULL(SUM((e.base_shipping_invoiced - IFNULL(e.base_shipping_refunded, 0)) * e.base_to_global_rate), 0)',
        'total_discount_amount'          => 'IFNULL(SUM((ABS(e.base_discount_amount) - IFNULL(e.base_discount_canceled, 0)) * e.base_to_global_rate), 0)',
        'total_discount_amount_actual'   => 'IFNULL(SUM((e.base_discount_invoiced - IFNULL(e.base_discount_refunded, 0)) * e.base_to_global_rate), 0)',
    );

    /**
     * Initialize custom resource model
     *
     * @param array $parameters
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order', 'entity_id');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        if ($nullCheck) {
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Retrieve array of columns to select
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        if (!$this->isTotals()) {
            if ('month' == $this->_period) {
                $this->_periodFormat = 'DATE_FORMAT(e.updated_at, \'%Y-%m\')';
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = 'EXTRACT(YEAR FROM e.updated_at)';
            } else {
                $this->_periodFormat = 'DATE(e.updated_at)';
            }
            $this->_selectedColumns += array('period' => $this->_periodFormat);
        }
        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }

        $columns = $this->_getSelectedColumns();

        $mainTable = $this->getResource()->getMainTable();

        $selectOrderItem = $this->getConnection()->select()
            ->from($this->getTable('sales/order_item'), array(
                'order_id'           => 'order_id',
                'total_qty_ordered'  => 'SUM(qty_ordered - IFNULL(qty_canceled, 0))',
                'total_qty_invoiced' => 'SUM(qty_invoiced)',
            ))
            ->where('parent_item_id IS NULL')
            ->group('order_id');

        $select = $this->getSelect()
            ->from(array('e' => $mainTable), $columns)
            ->join(array('oi' => $selectOrderItem), 'oi.order_id = e.entity_id', array())
            ->where('e.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ));

        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        if ($this->_to !== null) {
            $select->where('DATE(e.updated_at) <= DATE(?)', $this->_to);
        }

        if ($this->_from !== null) {
            $select->where('DATE(e.updated_at) >= DATE(?)', $this->_from);
        }

        if (!$this->isTotals()) {
            $select->group($this->_periodFormat);
        }

        $this->_inited = true;
        return $this;
    }

    /**
     * Load
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        $this->setApplyFilters(false);
        return parent::load($printQuery, $logQuery);
    }

}
