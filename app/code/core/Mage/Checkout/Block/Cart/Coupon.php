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
class Mage_Checkout_Block_Cart_Coupon extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }

    /**
     * Return "discount" form action url
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/couponPost', ['_secure' => $this->_isSecure()]);
    }
}
