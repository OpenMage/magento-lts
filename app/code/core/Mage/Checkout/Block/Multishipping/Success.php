<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Multishipping checkout success information
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Multishipping_Success extends Mage_Checkout_Block_Multishipping_Abstract
{
    /**
     * @return array|false
     */
    public function getOrderIds()
    {
        $ids = Mage::getSingleton('core/session')->getOrderIds(true);
        if ($ids && is_array($ids)) {
            return $ids;
        }
        return false;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getViewOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view/', ['order_id' => $orderId, '_secure' => true]);
    }

    /**
     * @return string
     */
    public function getContinueUrl()
    {
        return Mage::getBaseUrl();
    }
}
