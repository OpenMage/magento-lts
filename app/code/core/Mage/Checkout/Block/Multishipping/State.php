<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Multishipping checkout state
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Multishipping_State extends Mage_Core_Block_Template
{
    /**
     * @return array
     */
    public function getSteps()
    {
        return Mage::getSingleton('checkout/type_multishipping_state')->getSteps();
    }
}
