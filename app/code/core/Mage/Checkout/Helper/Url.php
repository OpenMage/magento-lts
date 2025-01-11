<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout url helper
 *
 * @category   Mage
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
