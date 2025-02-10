<?php
/**
 * One page checkout status
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Shipping_Method extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping_method', [
            'label'     => Mage::helper('checkout')->__('Shipping Method'),
            'is_show'   => $this->isShow(),
        ]);
        parent::_construct();
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
