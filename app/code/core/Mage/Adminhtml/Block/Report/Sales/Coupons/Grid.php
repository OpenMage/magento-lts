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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml coupons report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Coupons_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridCoupons');
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/coupons_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('coupon_code', array(
            'header'    => $this->__('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code'
        ));

        $this->addColumn('uses', array(
            'header'    => $this->__('Number of Use'),
            'sortable'  => false,
            'index'     => 'uses',
            'total'     => 'sum',
            'type'      => 'number'
        ));

        $currency_code = $this->getCurrentCurrencyCode();

        $this->addColumn('subtotal', array(
            'header'        => $this->__('Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'subtotal',
            'total'         => 'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('discount', array(
            'header'        => $this->__('Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'discount',
            'total'         => 'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('total', array(
            'header'        => $this->__('Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'index'         => 'total',
            'total'         => 'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCouponsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}