<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Adminhtml report filter form for coupons report
 *
 * @package    Mage_Sales
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

        if ($fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->addField('price_rule_type', 'select', [
                'name'    => 'price_rule_type',
                'options' => [
                    Mage::helper('reports')->__('Any'),
                    Mage::helper('reports')->__('Specified'),
                ],
                'label'   => Mage::helper('reports')->__('Shopping Cart Price Rule'),
            ]);

            $rulesList = Mage::getResourceModel('salesrule/report_rule')->getUniqRulesNamesList();

            $rulesListOptions = [];

            foreach ($rulesList as $key => $ruleName) {
                $rulesListOptions[] = [
                    'label' => $ruleName,
                    'value' => $key,
                    'title' => $ruleName,
                ];
            }

            $fieldset->addField('rules_list', 'multiselect', [
                'name'      => 'rules_list',
                'values'    => $rulesListOptions,
                'display'   => 'none',
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
