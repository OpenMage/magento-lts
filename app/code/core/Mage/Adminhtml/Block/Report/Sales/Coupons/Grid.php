<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml coupons report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Sales_Coupons_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
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
        if (($this->getFilterData()->getData('report_type') == 'updated_at_order')) {
            return 'salesrule/report_updatedat_collection';
        } else {
            return 'salesrule/report_collection';
        }
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', [
            'header'            => Mage::helper('salesrule')->__('Period'),
            'index'             => 'period',
            'width'             => 100,
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'      => Mage::helper('salesrule')->__('Total'),
            'subtotals_label'   => Mage::helper('salesrule')->__('Subtotal'),
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('coupon_code', [
            'header'    => Mage::helper('salesrule')->__('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code',
        ]);

        $this->addColumn('rule_name', [
            'header'    => Mage::helper('salesrule')->__('Shopping Cart Price Rule'),
            'sortable'  => false,
            'index'     => 'rule_name',
        ]);

        $this->addColumn('coupon_uses', [
            'header'    => Mage::helper('salesrule')->__('Number of Uses'),
            'sortable'  => false,
            'index'     => 'coupon_uses',
            'total'     => 'sum',
            'type'      => 'number',
        ]);

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('subtotal_amount', [
            'header'        => Mage::helper('salesrule')->__('Sales Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount',
            'rate'          => $rate,
        ]);

        $this->addColumn('discount_amount', [
            'header'        => Mage::helper('salesrule')->__('Sales Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount',
            'rate'          => $rate,
        ]);

        $this->addColumn('total_amount', [
            'header'        => Mage::helper('salesrule')->__('Sales Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount',
            'rate'          => $rate,
        ]);

        $this->addColumn('subtotal_amount_actual', [
            'header'        => Mage::helper('salesrule')->__('Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount_actual',
            'rate'          => $rate,
        ]);

        $this->addColumn('discount_amount_actual', [
            'header'        => Mage::helper('salesrule')->__('Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount_actual',
            'rate'          => $rate,
        ]);

        $this->addColumn('total_amount_actual', [
            'header'        => Mage::helper('salesrule')->__('Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount_actual',
            'rate'          => $rate,
        ]);

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportCouponsExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Add price rule filter
     *
     * @param Mage_SalesRule_Model_Resource_Report_Collection $collection
     * @param Varien_Object $filterData
     * @return Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addCustomFilter($collection, $filterData)
    {
        if ($filterData->getPriceRuleType()) {
            $rulesList = $filterData->getData('rules_list');
            if (isset($rulesList[0])) {
                $rulesIds = explode(',', $rulesList[0]);
                $collection->addRuleFilter($rulesIds);
            }
        }

        return parent::_addCustomFilter($collection, $filterData);
    }
}
