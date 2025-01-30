<?php

/**
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
