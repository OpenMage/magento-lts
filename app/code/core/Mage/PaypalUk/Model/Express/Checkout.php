<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 *
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
