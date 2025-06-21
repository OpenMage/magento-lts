<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Model_System_Config_Source_ButtonShapes
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_SHAPE_RECT,
                'label' => Mage::helper('paypal')->__('Rectangle'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_SHAPE_PILL,
                'label' => Mage::helper('paypal')->__('Pill'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_SHAPE_SHARP,
                'label' => Mage::helper('paypal')->__('Sharp'),
            ],
        ];
    }
}
