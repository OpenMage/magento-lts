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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tax report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Tax_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
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
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'tax/report_updatedat_collection'
            : 'tax/report_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'            => Mage::helper('sales')->__('Period'),
            'index'             => 'period',
            'width'             => '100',
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'      => Mage::helper('sales')->__('Total'),
            'subtotals_label'   => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('sales')->__('Tax'),
            'index'     => 'code',
            'type'      => 'string',
            'sortable'  => false
        ));

        $this->addColumn('percent', array(
            'header'    => Mage::helper('sales')->__('Rate'),
            'index'     => 'percent',
            'type'      => 'number',
            'width'     => '100',
            'sortable'  => false
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('sales')->__('Number of Orders'),
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number',
            'width'     => '100',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('tax_base_amount_sum', array(
            'header'        => Mage::helper('sales')->__('Tax Amount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'tax_base_amount_sum',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->addExportType('*/*/exportTaxCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportTaxExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Preparing collection
     * Filter canceled statuses for orders in taxes
     *
     *@return $this
     */
    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        if(!$filterData->hasData('order_statuses')) {
            $orderConfig = Mage::getModel('sales/order_config');
            $statusValues = array();
            $canceledStatuses = $orderConfig->getStateStatuses(Mage_Sales_Model_Order::STATE_CANCELED);
            foreach ($orderConfig->getStatuses() as $code => $label) {
                if (!isset($canceledStatuses[$code])) {
                    $statusValues[] = $code;
                }
            }
            $filterData->setOrderStatuses($statusValues);
        }
        return parent::_prepareCollection();
    }
}
