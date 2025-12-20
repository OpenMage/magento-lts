<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Multishipping cart link
 *
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
    protected function _toHtml()
    {
        if (!Mage::helper('checkout')->isMultishippingCheckoutAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
}
