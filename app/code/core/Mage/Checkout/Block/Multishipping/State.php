<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Multishipping checkout state
 *
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
