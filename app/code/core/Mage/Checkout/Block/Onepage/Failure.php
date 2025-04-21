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
class Mage_Checkout_Block_Onepage_Failure extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function getRealOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return   string
     */
    public function getErrorMessage()
    {
        // Mage::getSingleton('checkout/session')->unsErrorMessage();
        return Mage::getSingleton('checkout/session')->getErrorMessage();
    }

    /**
     * Continue shopping URL
     *
     *  @return   string
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
