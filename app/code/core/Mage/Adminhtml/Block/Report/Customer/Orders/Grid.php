<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers by orders report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Customer_Orders_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridOrdersCustomer');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/customer_orders_collection');
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => $this->__('Customer Name'),
            'sortable'  => false,
            'index'     => 'name',
        ]);

        $this->addColumn('orders_count', [
            'header'    => $this->__('Number of Orders'),
            'width'     => '100px',
            'sortable'  => false,
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number',
        ]);

        $baseCurrencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('orders_avg_amount', [
            'header'    => $this->__('Average Order Amount'),
            'width'     => '200px',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_avg_amount',
            'total'     => 'orders_sum_amount/orders_count',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
        ]);

        $this->addColumn('orders_sum_amount', [
            'header'    => $this->__('Total Order Amount'),
            'width'     => '200px',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
        ]);

        $this->addExportType('*/*/exportOrdersCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportOrdersExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
