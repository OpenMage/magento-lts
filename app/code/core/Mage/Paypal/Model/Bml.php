<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal Bill Me Later method
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Bml extends Mage_Paypal_Model_Express
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code  = Mage_Paypal_Model_Config::METHOD_BML;

    /**
     * Checkout payment form
     * @var string
     */
    protected $_formBlockType = 'paypal/bml_form';

    /**
     * Checkout redirect URL getter for onepage checkout
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypal/bml/start');
    }
}
