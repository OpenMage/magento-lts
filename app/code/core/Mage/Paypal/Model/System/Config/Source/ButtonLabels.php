<?php
class Mage_Paypal_Model_System_Config_Source_ButtonLabels
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_PAYPAL,
                'label' => Mage::helper('paypal')->__('PayPal (Recommended)')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_CHECKOUT,
                'label' => Mage::helper('paypal')->__('Checkout')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_BUYNOW,
                'label' => Mage::helper('paypal')->__('Buy Now')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_PAY,
                'label' => Mage::helper('paypal')->__('Pay with PayPal')
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_INSTALLMENT,
                'label' => Mage::helper('paypal')->__('PayPal Installment')
            ]
        ];
    }
}
