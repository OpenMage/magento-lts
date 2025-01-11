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
