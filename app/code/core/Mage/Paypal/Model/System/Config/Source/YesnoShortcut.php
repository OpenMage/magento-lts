<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 * @category   Mage
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
