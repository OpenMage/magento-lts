<?php

/**
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout cart link
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Link extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', ['_secure' => true]);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    /**
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        /** @var Mage_Checkout_Helper_Data $helper */
        $helper = $this->helper('checkout');
        return $helper->canOnepageCheckout();
    }
}
