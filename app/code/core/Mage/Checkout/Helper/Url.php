<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Checkout url helper
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Helper_Url extends Mage_Core_Helper_Url
{
    protected $_moduleName = 'Mage_Checkout';

    /**
     * Retrieve shopping cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return $this->_getUrl('checkout/cart');
    }

    /**
     * Retrieve checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->_getUrl('checkout/onepage');
    }

    /**
     * Multi Shipping (MS) checkout urls
     */

    /**
     * Retrieve multishipping checkout url
     *
     * @return string
     */
    public function getMSCheckoutUrl()
    {
        return $this->_getUrl('checkout/multishipping');
    }

    /**
     * @return string
     */
    public function getMSLoginUrl()
    {
        return $this->_getUrl('checkout/multishipping/login', ['_secure' => true, '_current' => true]);
    }

    /**
     * @return string
     */
    public function getMSAddressesUrl()
    {
        return $this->_getUrl('checkout/multishipping/addresses');
    }

    /**
     * @return string
     */
    public function getMSShippingAddressSavedUrl()
    {
        return $this->_getUrl('checkout/multishipping_address/shippingSaved');
    }

    /**
     * @return string
     */
    public function getMSRegisterUrl()
    {
        return $this->_getUrl('checkout/multishipping/register');
    }

    /**
     * One Page (OP) checkout urls
     */
    public function getOPCheckoutUrl()
    {
        return $this->_getUrl('checkout/onepage');
    }

    /**
     * Url to Registration Page
     *
     * @return string
     */
    public function getRegistrationUrl()
    {
        return $this->_getUrl('customer/account/create');
    }
}
