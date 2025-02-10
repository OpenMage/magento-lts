<?php
/**
 * One page checkout status
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
