<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
        $this->addColumn('period', [
            'header'        => Mage::helper('sales')->__('Period'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'  => Mage::helper('sales')->__('Total'),
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('orders_count', [
            'header'    => Mage::helper('sales')->__('Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
        ]);

        $this->addColumn('total_qty_ordered', [
            'header'    => Mage::helper('sales')->__('Sales Items'),
            'index'     => 'total_qty_ordered',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
        ]);

        $this->addColumn('total_qty_invoiced', [
            'header'    => Mage::helper('sales')->__('Items'),
            'index'     => 'total_qty_invoiced',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
            'visibility_filter' => ['show_actual_columns'],
        ]);

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('total_income_amount', [
            'header'        => Mage::helper('sales')->__('Sales Total'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_income_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_revenue_amount', [
            'header'            => Mage::helper('sales')->__('Revenue'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_revenue_amount',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => ['show_actual_columns'],
            'rate'              => $rate,
        ]);

        $this->addColumn('total_profit_amount', [
            'header'            => Mage::helper('sales')->__('Profit'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_profit_amount',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => ['show_actual_columns'],
            'rate'              => $rate,
        ]);

        $this->addColumn('total_invoiced_amount', [
            'header'        => Mage::helper('sales')->__('Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_invoiced_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_paid_amount', [
            'header'            => Mage::helper('sales')->__('Paid'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_paid_amount',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => ['show_actual_columns'],
            'rate'              => $rate,
        ]);

        $this->addColumn('total_refunded_amount', [
            'header'        => Mage::helper('sales')->__('Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_refunded_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_tax_amount', [
            'header'        => Mage::helper('sales')->__('Sales Tax'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_tax_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_tax_amount_actual', [
            'header'            => Mage::helper('sales')->__('Tax'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_tax_amount_actual',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => ['show_actual_columns'],
            'rate'              => $rate,
        ]);

        $this->addColumn('total_shipping_amount', [
            'header'        => Mage::helper('sales')->__('Sales Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_shipping_amount_actual', [
            'header'            => Mage::helper('sales')->__('Shipping'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_shipping_amount_actual',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => ['show_actual_columns'],
            'rate'              => $rate,
        ]);

        $this->addColumn('total_discount_amount', [
            'header'        => Mage::helper('sales')->__('Sales Discount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_discount_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_discount_amount_actual', [
            'header'            => Mage::helper('sales')->__('Discount'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_discount_amount_actual',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => ['show_actual_columns'],
            'rate'              => $rate,
        ]);

        $this->addColumn('total_canceled_amount', [
            'header'        => Mage::helper('sales')->__('Canceled'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_canceled_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
