<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Failure extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function getRealOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return   string
     */
    public function getErrorMessage()
    {
        // Mage::getSingleton('checkout/session')->unsErrorMessage();
        return Mage::getSingleton('checkout/session')->getErrorMessage();
    }

    /**
     * Continue shopping URL
     *
     *  @return   string
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
