<?php
/**
 * One page checkout cart link
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Link extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', ['_secure' => true]);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    /**
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        /** @var Mage_Checkout_Helper_Data $helper */
        $helper = $this->helper('checkout');
        return $helper->canOnepageCheckout();
    }
}
