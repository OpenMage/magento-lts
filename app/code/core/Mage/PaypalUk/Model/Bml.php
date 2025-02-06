<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */

/**
 * PayPal Bill Me Later method
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Bml extends Mage_Paypal_Model_Bml
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code  = Mage_Paypal_Model_Config::METHOD_WPP_PE_BML;

    /**
     * Checkout payment form
     * @var string
     */
    protected $_formBlockType = 'paypaluk/bml_form';

    /**
     * Checkout redirect URL getter for onepage checkout
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypaluk/bml/start');
    }
}
