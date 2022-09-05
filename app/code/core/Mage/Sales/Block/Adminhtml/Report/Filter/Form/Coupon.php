<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Adminhtml report filter form for coupons report
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon extends Mage_Sales_Block_Adminhtml_Report_Filter_Form
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->addField('price_rule_type', 'select', [
                'name'    => 'price_rule_type',
                'options' => [
                    Mage::helper('reports')->__('Any'),
                    Mage::helper('reports')->__('Specified')
                ],
                'label'   => Mage::helper('reports')->__('Shopping Cart Price Rule'),
            ]);

            $rulesList = Mage::getResourceModel('salesrule/report_rule')->getUniqRulesNamesList();

            $rulesListOptions = [];

            foreach ($rulesList as $key => $ruleName) {
                $rulesListOptions[] = [
                    'label' => $ruleName,
                    'value' => $key,
                    'title' => $ruleName
                ];
            }

            $fieldset->addField('rules_list', 'multiselect', [
                'name'      => 'rules_list',
                'values'    => $rulesListOptions,
                'display'   => 'none'
            ], 'price_rule_type');

            /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $block */
            $block = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
            $this->setChild('form_after', $block
                ->addFieldMap($htmlIdPrefix . 'price_rule_type', 'price_rule_type')
                ->addFieldMap($htmlIdPrefix . 'rules_list', 'rules_list')
                ->addFieldDependence('rules_list', 'price_rule_type', '1'));
        }

        return $this;
    }
}
