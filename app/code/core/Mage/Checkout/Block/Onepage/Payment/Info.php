<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Multishipping checkout payment information data
 *
 * @category   Mage
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
