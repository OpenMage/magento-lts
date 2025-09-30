<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Config_Source_Cart_Summary
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => Mage::helper('checkout')->__('Display number of items in cart')],
            ['value' => 1, 'label' => Mage::helper('checkout')->__('Display item quantities')],
        ];
    }
}
