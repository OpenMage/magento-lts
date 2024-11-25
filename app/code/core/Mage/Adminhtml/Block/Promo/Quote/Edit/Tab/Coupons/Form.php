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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Coupons generation parameters form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        /**
         * @var Mage_SalesRule_Helper_Coupon $couponHelper
         */
        $couponHelper = Mage::helper('salesrule/coupon');

        $model = Mage::registry('current_promo_quote_rule');
        $ruleId = $model->getId();

        $form->setHtmlIdPrefix('coupons_');

        $gridBlock = $this->getLayout()->getBlock('promo_quote_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset = $form->addFieldset('information_fieldset', ['legend' => Mage::helper('salesrule')->__('Coupons Information')]);
        $fieldset->addClass('ignore-validate');

        $fieldset->addField('rule_id', 'hidden', [
            'name'     => 'rule_id',
            'value'    => $ruleId,
        ]);

        $fieldset->addField('qty', 'text', [
            'name'     => 'qty',
            'label'    => Mage::helper('salesrule')->__('Coupon Qty'),
            'title'    => Mage::helper('salesrule')->__('Coupon Qty'),
            'required' => true,
            'class'    => 'validate-digits validate-greater-than-zero',
        ]);

        $fieldset->addField('length', 'text', [
            'name'     => 'length',
            'label'    => Mage::helper('salesrule')->__('Code Length'),
            'title'    => Mage::helper('salesrule')->__('Code Length'),
            'required' => true,
            'note'     => Mage::helper('salesrule')->__('Excluding prefix, suffix and separators.'),
            'value'    => $couponHelper->getDefaultLength(),
            'class'    => 'validate-digits validate-greater-than-zero',
        ]);

        $fieldset->addField('format', 'select', [
            'label'    => Mage::helper('salesrule')->__('Code Format'),
            'name'     => 'format',
            'options'  => $couponHelper->getFormatsList(),
            'required' => true,
            'value'    => $couponHelper->getDefaultFormat(),
        ]);

        $fieldset->addField('prefix', 'text', [
            'name'  => 'prefix',
            'label' => Mage::helper('salesrule')->__('Code Prefix'),
            'title' => Mage::helper('salesrule')->__('Code Prefix'),
            'value' => $couponHelper->getDefaultPrefix(),
        ]);

        $fieldset->addField('suffix', 'text', [
            'name'  => 'suffix',
            'label' => Mage::helper('salesrule')->__('Code Suffix'),
            'title' => Mage::helper('salesrule')->__('Code Suffix'),
            'value' => $couponHelper->getDefaultSuffix(),
        ]);

        $fieldset->addField('dash', 'text', [
            'name'  => 'dash',
            'label' => Mage::helper('salesrule')->__('Dash Every X Characters'),
            'title' => Mage::helper('salesrule')->__('Dash Every X Characters'),
            'note'  => Mage::helper('salesrule')->__('If empty no separation.'),
            'value' => $couponHelper->getDefaultDashInterval(),
            'class' => 'validate-digits',
        ]);

        $idPrefix = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField('generate_button', 'note', [
            'text' => $this->getButtonHtml(
                Mage::helper('salesrule')->__('Generate'),
                "generateCouponCodes('{$idPrefix}' ,'{$generateUrl}', '{$gridBlockJsObject}')",
                'generate',
            ),
        ]);

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_promo_quote_edit_tab_coupons_form_prepare_form', ['form' => $form]);

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('*/*/generate');
    }
}
