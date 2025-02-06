<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Paypal Express Onepage checkout block for Shipping Address
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Express_Review_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn() || $this->getQuote()->getShippingAddress()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = Mage::getModel('sales/quote_address');
            }
        }

        return $this->_address;
    }
}
