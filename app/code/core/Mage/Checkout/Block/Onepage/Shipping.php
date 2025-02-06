<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Sales Quote Shipping Address instance
     *
     * @var Mage_Sales_Model_Quote_Address|null
     */
    protected $_address = null;

    /**
     * Initialize shipping address step
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping', [
            'label'     => Mage::helper('checkout')->__('Shipping Information'),
            'is_show'   => $this->isShow(),
        ]);

        parent::_construct();
    }

    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }

        return $this->_address;
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
}
