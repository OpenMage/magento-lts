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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
            'totals_label'  => Mage::helper('adminhtml')->__('Total')
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('sales')->__('Number of Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('total_qty_ordered', array(
            'header'    => Mage::helper('sales')->__('Items Ordered'),
            'index'     => 'total_qty_ordered',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currency_code = $this->getCurrentCurrencyCode();

        $this->addColumn('base_profit_amount', array(
            'header'        => Mage::helper('sales')->__('Profit'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_profit_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_subtotal_amount', array(
            'header'        => Mage::helper('sales')->__('Subtotal'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_subtotal_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_tax_amount', array(
            'header'        => Mage::helper('sales')->__('Tax'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_tax_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_shipping_amount', array(
            'header'        => Mage::helper('sales')->__('Shipping'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_shipping_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_discount_amount', array(
            'header'        => Mage::helper('sales')->__('Discounts'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_discount_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_grand_total_amount', array(
            'header'        => Mage::helper('sales')->__('Total'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_grand_total_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_invoiced_amount', array(
            'header'        => Mage::helper('sales')->__('Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_invoiced_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_refunded_amount', array(
            'header'        => Mage::helper('sales')->__('Refunded'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_refunded_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('base_canceled_amount', array(
            'header'        => Mage::helper('sales')->__('Canceled'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'base_canceled_amount',
            'total'         => 'sum',
            'sortable'      => false
        ));


        $this->addExportType('*/*/exportSalesCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}
