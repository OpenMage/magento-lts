<?php
class Mage_Paypal_Model_System_Config_Source_ButtonLayouts
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LAYOUT_VERTICAL,
                'label' => Mage::helper('paypal')->__('Vertical')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LAYOUT_HORIZONTAL,
                'label' => Mage::helper('paypal')->__('Horizontal')
            ]
        ];
    }
}
