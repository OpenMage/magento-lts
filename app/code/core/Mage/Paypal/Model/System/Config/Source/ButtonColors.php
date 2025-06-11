<?php
class Mage_Paypal_Model_System_Config_Source_ButtonColors
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_COLOR_GOLD,
                'label' => Mage::helper('paypal')->__('Gold')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_COLOR_BLUE,
                'label' => Mage::helper('paypal')->__('Blue')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_COLOR_SILVER,
                'label' => Mage::helper('paypal')->__('Silver')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_COLOR_WHITE,
                'label' => Mage::helper('paypal')->__('White')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_COLOR_BLACK,
                'label' => Mage::helper('paypal')->__('Black')
            ]
        ];
    }
}
