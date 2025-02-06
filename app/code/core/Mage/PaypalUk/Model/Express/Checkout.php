<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Express_Checkout extends Mage_Paypal_Model_Express_Checkout
{
    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_apiType = 'paypaluk/api_express_nvp';

    /**
     * Payment method tpye
     * @var string
     */
    protected $_methodType = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Set shipping method to quote, if needed
     * @param string $methodCode
     */
    public function updateShippingMethod($methodCode)
    {
        parent::updateShippingMethod($methodCode);
        $this->_quote->save();
    }
}
