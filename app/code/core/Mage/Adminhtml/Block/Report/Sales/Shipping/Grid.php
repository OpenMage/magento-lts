<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml shipping report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Sales_Shipping_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_shipment')
            ? 'sales/report_shipping_collection_shipment'
            : 'sales/report_shipping_collection_order';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', [
            'header'            => Mage::helper('sales')->__('Period'),
            'index'             => 'period',
            'width'             => 100,
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'      => Mage::helper('sales')->__('Total'),
            'subtotals_label'   => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('shipping_description', [
            'header'    => Mage::helper('sales')->__('Carrier/Method'),
            'index'     => 'shipping_description',
            'sortable'  => false,
        ]);

        $this->addColumn('orders_count', [
            'header'    => Mage::helper('sales')->__('Number of Orders'),
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number',
            'width'     => 100,
            'sortable'  => false,
        ]);

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('total_shipping', [
            'header'        => Mage::helper('sales')->__('Total Sales Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('total_shipping_actual', [
            'header'        => Mage::helper('sales')->__('Total Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_actual',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addExportType('*/*/exportShippingCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportShippingExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
