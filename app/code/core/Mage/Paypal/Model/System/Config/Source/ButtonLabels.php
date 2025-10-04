<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Model_System_Config_Source_ButtonLabels
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_PAYPAL,
                'label' => Mage::helper('paypal')->__('PayPal (Recommended)'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_CHECKOUT,
                'label' => Mage::helper('paypal')->__('Checkout'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_BUYNOW,
                'label' => Mage::helper('paypal')->__('Buy Now'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_PAY,
                'label' => Mage::helper('paypal')->__('Pay with PayPal'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LABEL_INSTALLMENT,
                'label' => Mage::helper('paypal')->__('PayPal Installment'),
            ],
        ];
    }
}
