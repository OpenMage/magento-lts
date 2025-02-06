<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Class Mage_Checkout_Block_Success
 *
 * @category   Mage
 * @package    Mage_Checkout
 *
 * @method int getLastOrderId()
 */
class Mage_Checkout_Block_Success extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getRealOrderId()
    {
        $order = Mage::getModel('sales/order')->load($this->getLastOrderId());
        return $order->getIncrementId();
    }
}
