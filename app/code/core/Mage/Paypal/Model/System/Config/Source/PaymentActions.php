<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

class Mage_Paypal_Model_System_Config_Source_PaymentActions
{
    public function toOptionArray()
    {
        return [
            [
                'value' => strtolower(CheckoutPaymentIntent::AUTHORIZE),
                'label' => Mage::helper('paypal')->__('Authorize'),
            ],
            [
                'value' => strtolower(CheckoutPaymentIntent::CAPTURE),
                'label' => Mage::helper('paypal')->__('Capture'),
            ],
        ];
    }
}
