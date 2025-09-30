<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml refunded report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Sales_Refunded_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_refunded')
            ? 'sales/report_refunded_collection_refunded'
            : 'sales/report_refunded_collection_order';
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
            'header'    => Mage::helper('sales')->__('Number of Refunded Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
        ]);

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('refunded', [
            'header'        => Mage::helper('sales')->__('Total Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'refunded',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('online_refunded', [
            'header'        => Mage::helper('sales')->__('Online Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'online_refunded',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addColumn('offline_refunded', [
            'header'        => Mage::helper('sales')->__('Offline Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'offline_refunded',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ]);

        $this->addExportType('*/*/exportRefundedCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportRefundedExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
