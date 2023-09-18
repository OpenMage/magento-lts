<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Express NVP API wrappers model
 *
 * @category   Mage
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
