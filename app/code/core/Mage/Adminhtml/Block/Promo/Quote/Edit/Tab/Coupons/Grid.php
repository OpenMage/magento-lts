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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Coupon codes grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('couponCodesGrid');
        $this->setDefaultSort('created_at');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     *
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $priceRule = Mage::registry('current_promo_quote_rule');

        /**
         * @var Mage_SalesRule_Model_Resource_Coupon_Collection $collection
         */
        $collection = Mage::getResourceModel('salesrule/coupon_collection')
            ->addRuleToFilter($priceRule)
            ->addGeneratedCouponsFilter();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', [
            'header' => Mage::helper('salesrule')->__('Coupon Code'),
            'index'  => 'code'
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('salesrule')->__('Created On'),
            'index'  => 'created_at',
            'type'   => 'datetime',
            'align'  => 'center',
        ]);

        $this->addColumn('used', [
            'header'   => Mage::helper('salesrule')->__('Used'),
            'index'    => 'times_used',
            'width'    => '100',
            'type'     => 'options',
            'options'  => [
                Mage::helper('adminhtml')->__('No'),
                Mage::helper('adminhtml')->__('Yes')
            ],
            'renderer' => 'adminhtml/promo_quote_edit_tab_coupons_grid_column_renderer_used',
            'filter_condition_callback' => [
                Mage::getResourceModel('salesrule/coupon_collection'), 'addIsUsedFilterCallback'
            ]
        ]);

        $this->addColumn('times_used', [
            'header' => Mage::helper('salesrule')->__('Times Used'),
            'index'  => 'times_used',
            'width'  => '50',
            'type'   => 'number',
        ]);

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportCouponsXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * Configure grid mass actions
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
             'label'    => Mage::helper('adminhtml')->__('Delete'),
             'url'      => $this->getUrl('*/*/couponsMassDelete', ['_current' => true]),
             'confirm'  => Mage::helper('salesrule')->__('Are you sure you want to delete the selected coupon(s)?'),
             'complete' => 'refreshCouponCodesGrid'
        ]);

        return $this;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/couponsGrid', ['_current' => true]);
    }

    /**
     * @inheritdoc
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
