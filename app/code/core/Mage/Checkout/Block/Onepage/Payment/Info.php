<?php
/**
 * Multishipping checkout payment information data
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Payment_Info extends Mage_Payment_Block_Info_Container
{
    /**
     * Retrieve payment info model
     *
     * @return Mage_Payment_Model_Info|false
     */
    public function getPaymentInfo()
    {
        $info = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
        if ($info->getMethod()) {
            return $info;
        }
        return false;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if ($block = $this->getChild($this->_getInfoBlockName())) {
            $html = $block->toHtml();
        }
        return $html;
    }
}
