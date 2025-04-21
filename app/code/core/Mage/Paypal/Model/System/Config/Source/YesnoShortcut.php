<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_YesnoShortcut
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => Mage::helper('paypal')->__('Yes (PayPal recommends this option)')],
            ['value' => 0, 'label' => Mage::helper('paypal')->__('No')],
        ];
    }
}
