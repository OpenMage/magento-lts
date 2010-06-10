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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Sales_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'sales/report_order_updatedat_collection'
            : 'sales/report_order_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => Mage::helper('sales')->__('Period'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'  => Mage::helper('sales')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('sales')->__('Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('total_qty_ordered', array(
            'header'    => Mage::helper('sales')->__('Sales Items'),
            'index'     => 'total_qty_ordered',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('total_qty_invoiced', array(
            'header'    => Mage::helper('sales')->__('Items'),
            'index'     => 'total_qty_invoiced',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('total_income_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Total'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_income_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('total_revenue_amount', array(
            'header'        => Mage::helper('sales')->__('Revenue'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_revenue_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        $this->addColumn('total_profit_amount', array(
            'header'        => Mage::helper('sales')->__('Profit'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_profit_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        $this->addColumn('total_invoiced_amount', array(
            'header'        => Mage::helper('sales')->__('Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_invoiced_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('total_paid_amount', array(
            'header'        => Mage::helper('sales')->__('Paid'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_paid_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        $this->addColumn('total_refunded_amount', array(
            'header'        => Mage::helper('sales')->__('Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_refunded_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('total_tax_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Tax'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_tax_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('total_tax_amount_actual', array(
            'header'        => Mage::helper('sales')->__('Tax'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_tax_amount_actual',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        $this->addColumn('total_shipping_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('total_shipping_amount_actual', array(
            'header'        => Mage::helper('sales')->__('Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_amount_actual',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        $this->addColumn('total_discount_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Discount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_discount_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('total_discount_amount_actual', array(
            'header'        => Mage::helper('sales')->__('Discount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_discount_amount_actual',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));

        $this->addColumn('total_canceled_amount', array(
            'header'        => Mage::helper('sales')->__('Canceled'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_canceled_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));


        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel'));

        return parent::_prepareColumns();
    }
}
