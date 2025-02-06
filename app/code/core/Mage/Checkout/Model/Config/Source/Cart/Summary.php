<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
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
