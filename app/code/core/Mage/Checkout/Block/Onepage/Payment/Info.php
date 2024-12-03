<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
