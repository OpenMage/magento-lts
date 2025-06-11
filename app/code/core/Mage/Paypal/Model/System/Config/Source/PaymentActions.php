<?php

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

class Mage_Paypal_Model_System_Config_Source_PaymentActions
{

    public function toOptionArray()
    {
        return [
            [
                'value' => strtolower(CheckoutPaymentIntent::AUTHORIZE),
                'label' => Mage::helper('paypal')->__('Authorize')
            ],
            [
                'value' => strtolower(CheckoutPaymentIntent::CAPTURE),
                'label' => Mage::helper('paypal')->__('Capture')
            ]
        ];
    }
}
