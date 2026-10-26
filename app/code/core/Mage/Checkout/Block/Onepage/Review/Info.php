<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * One page checkout order review
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Review_Info extends Mage_Sales_Block_Items_Abstract
{
    /**
     * @return array
     */
    public function getItems()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getTotals();
    }
}
