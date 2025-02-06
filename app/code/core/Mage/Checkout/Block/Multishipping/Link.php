<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Multishipping cart link
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Multishipping_Link extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/multishipping', ['_secure' => true]);
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!Mage::helper('checkout')->isMultishippingCheckoutAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
}
