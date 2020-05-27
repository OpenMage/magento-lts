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
 * Adminhtml coupons report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->addColumn('period', array(
            'header'            => Mage::helper('salesrule')->__('Period'),
            'index'             => 'period',
            'width'             => 100,
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'      => Mage::helper('salesrule')->__('Total'),
            'subtotals_label'   => Mage::helper('salesrule')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('coupon_code', array(
            'header'    => Mage::helper('salesrule')->__('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code'
        ));

        $this->addColumn('rule_name', array(
            'header'    => Mage::helper('salesrule')->__('Shopping Cart Price Rule'),
            'sortable'  => false,
            'index'     => 'rule_name'
        ));

        $this->addColumn('coupon_uses', array(
            'header'    => Mage::helper('salesrule')->__('Number of Uses'),
            'sortable'  => false,
            'index'     => 'coupon_uses',
            'total'     => 'sum',
            'type'      => 'number'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('subtotal_amount', array(
            'header'        => Mage::helper('salesrule')->__('Sales Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount',
            'rate'          => $rate,
        ));

        $this->addColumn('discount_amount', array(
            'header'        => Mage::helper('salesrule')->__('Sales Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount',
            'rate'          => $rate,
        ));

        $this->addColumn('total_amount', array(
            'header'        => Mage::helper('salesrule')->__('Sales Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount',
            'rate'          => $rate,
        ));

        $this->addColumn('subtotal_amount_actual', array(
            'header'        => Mage::helper('salesrule')->__('Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount_actual',
            'rate'          => $rate,
        ));

        $this->addColumn('discount_amount_actual', array(
            'header'        => Mage::helper('salesrule')->__('Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount_actual',
            'rate'          => $rate,
        ));

        $this->addColumn('total_amount_actual', array(
            'header'        => Mage::helper('salesrule')->__('Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount_actual',
            'rate'          => $rate,
        ));

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportCouponsExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Add price rule filter
     *
     * @param Mage_Reports_Model_Resource_Report_Collection_Abstract $collection
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

        return parent::_addCustomFilter($filterData, $collection);
    }
}
