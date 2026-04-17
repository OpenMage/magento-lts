<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * Payflow Express NVP API wrappers model
 *
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Api_Express_Nvp extends Mage_PaypalUk_Model_Api_Nvp
{
    /**
     * Set specific data when negative line item case
     */
    protected function _setSpecificForNegativeLineItems()
    {
        $paypalNvp = new Mage_Paypal_Model_Api_Nvp();
        $this->_setExpressCheckoutResponse = $paypalNvp->_setExpressCheckoutResponse;
        $index = array_search('PPREF', $this->_doExpressCheckoutPaymentResponse);
        if ($index !== false) {
            unset($this->_doExpressCheckoutPaymentResponse[$index]);
        }

        $this->_doExpressCheckoutPaymentResponse[] = 'PAYMENTINFO_0_TRANSACTIONID';
        $this->_requiredResponseParams[self::DO_EXPRESS_CHECKOUT_PAYMENT][] = 'PAYMENTINFO_0_TRANSACTIONID';
    }
}
