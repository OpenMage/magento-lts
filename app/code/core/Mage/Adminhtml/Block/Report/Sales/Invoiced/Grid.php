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
 * Adminhtml invoiced report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Invoiced_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_invoice')
            ? 'sales/report_invoiced_collection_invoiced'
            : 'sales/report_invoiced_collection_order';
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
            'header'    => Mage::helper('reports')->__('Number of Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('orders_invoiced', array(
            'header'    => Mage::helper('reports')->__('Number of Invoiced Orders'),
            'index'     => 'orders_invoiced',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currency_code = $this->getCurrentCurrencyCode();

        $this->addColumn('invoiced', array(
            'header'        => Mage::helper('reports')->__('Total Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'invoiced',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('invoiced_captured', array(
            'header'        => Mage::helper('reports')->__('Total Invoiced Captured'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'invoiced_captured',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addColumn('invoiced_not_captured', array(
            'header'        => Mage::helper('reports')->__('Total Invoiced not Captured'),
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'invoiced_not_captured',
            'total'         => 'sum',
            'sortable'      => false
        ));

        $this->addExportType('*/*/exportInvoicedCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportInvoicedExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}
