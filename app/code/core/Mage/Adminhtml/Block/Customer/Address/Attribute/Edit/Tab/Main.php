<?php

class Mage_Adminhtml_Block_Customer_Address_Attribute_Edit_Tab_Main
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $helper = Mage::helper('customer');
        $attribute = $this->getAttributeObject();
        $form = $this->getForm();

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', [
            'legend' => $helper->__('Frontend Properties')
        ]);
        $fieldset->addField('sort_order', 'text', [
            'name' => 'sort_order',
            'label' => $helper->__('Sort Order'),
            'title' => $helper->__('Sort Order'),
            'class' => 'validate-digits'
        ]);

        $fieldset->addField('used_in_forms', 'multiselect', array(
            'name'         => 'used_in_forms',
            'label'        => $helper->__('Forms to Use In'),
            'title'        => $helper->__('Forms to Use In'),
            'value'        => $attribute->getUsedInForms(),
            'can_be_empty' => true,
            'values'       => [
                [
                    'label' => Mage::helper('customer')->__('Customer Address Registration'),
                    'value' => 'customer_register_address'
                ],
                [
                    'label' => Mage::helper('customer')->__('Customer Account Address'),
                    'value' => 'customer_address_edit'
                ]
            ]
        ))->setSize(4);

        Mage::dispatchEvent('adminhtml_customer_address_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attribute
        ));

        return $this;
    }

    public function getTabLabel()
    {
        return Mage::helper('customer')->__('Properties');
    }

    public function getTabTitle()
    {
        return Mage::helper('customer')->__('Properties');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
