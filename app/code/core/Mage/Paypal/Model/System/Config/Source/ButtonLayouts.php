<?php

declare(strict_types=1);
/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Model_System_Config_Source_ButtonLayouts
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LAYOUT_VERTICAL,
                'label' => Mage::helper('paypal')->__('Vertical'),
            ],
            [
                'value' => Mage_Paypal_Model_Config::BUTTON_LAYOUT_HORIZONTAL,
                'label' => Mage::helper('paypal')->__('Horizontal'),
            ],
        ];
    }
}
