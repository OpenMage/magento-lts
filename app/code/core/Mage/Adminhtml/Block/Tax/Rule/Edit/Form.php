<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Tax Rule Edit Form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('taxRuleForm');
        $this->setTitle(Mage::helper('tax')->__('Tax Rule Information'));
    }

    /**
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('tax_rule');
        $form   = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
        ]);

        $fieldset   = $form->addFieldset('base_fieldset', [
            'legend'    => Mage::helper('tax')->__('Tax Rule Information'),
        ]);

        $productClasses = Mage::getModel('tax/class')
            ->getCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
            ->toOptionArray();

        $customerClasses = Mage::getModel('tax/class')
            ->getCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
            ->toOptionArray();

        /**
         * Get rates array without memory leak
         */
        $rates = Mage::getModel('tax/calculation_rate')
            ->getCollection()
            ->getOptionRates();

        $fieldset->addField(
            'code',
            'text',
            [
                'name'      => 'code',
                'label'     => Mage::helper('tax')->__('Name'),
                'class'     => 'required-entry',
                'required'  => true,
            ],
        );

        $fieldset->addField(
            'tax_customer_class',
            'multiselect',
            [
                'name'      => 'tax_customer_class',
                'label'     => Mage::helper('tax')->__('Customer Tax Class'),
                'class'     => 'required-entry',
                'values'    => $customerClasses,
                'value'     => $model->getCustomerTaxClasses(),
                'required'  => true,
            ],
        );

        $fieldset->addField(
            'tax_product_class',
            'multiselect',
            [
                'name'      => 'tax_product_class',
                'label'     => Mage::helper('tax')->__('Product Tax Class'),
                'class'     => 'required-entry',
                'values'    => $productClasses,
                'value'     => $model->getProductTaxClasses(),
                'required'  => true,
            ],
        );

        $fieldset->addField(
            'tax_rate',
            'multiselect',
            [
                'name'      => 'tax_rate',
                'label'     => Mage::helper('tax')->__('Tax Rate'),
                'class'     => 'required-entry',
                'values'    => $rates,
                'value'     => $model->getRates(),
                'required'  => true,
            ],
        );
        $fieldset->addField(
            'priority',
            'text',
            [
                'name'      => 'priority',
                'label'     => Mage::helper('tax')->__('Priority'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPriority(),
                'required'  => true,
                'note'      => Mage::helper('tax')->__('Tax rates at the same priority are added, others are compounded.'),
            ],
        );

        $fieldset->addField(
            'calculate_subtotal',
            'checkbox',
            [
                'name'      => 'calculate_subtotal',
                'label'     => Mage::helper('tax')->__('Calculate off subtotal only'),
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
                'checked'   => $model->getCalculateSubtotal(),
            ],
        );
        $fieldset->addField(
            'position',
            'text',
            [
                'name'      => 'position',
                'label'     => Mage::helper('tax')->__('Sort Order'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPosition(),
                'required'  => true,
            ],
        );

        if ($model->getId() > 0) {
            $fieldset->addField(
                'tax_calculation_rule_id',
                'hidden',
                [
                    'name'      => 'tax_calculation_rule_id',
                    'value'     => $model->getId(),
                    'no_span'   => true,
                ],
            );
        }

        $form->addValues($model->getData());
        $form->setAction($this->getUrl('*/tax_rule/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
