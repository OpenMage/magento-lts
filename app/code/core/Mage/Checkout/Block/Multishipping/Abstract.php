<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Mustishipping checkout base abstract block
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Multishipping_Abstract extends Mage_Core_Block_Template
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }
}
