<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block of links in Order view page
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Info_Buttons extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/info/buttons.phtml');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Get url for printing order
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/print', ['order_id' => $order->getId()]);
        }
        return $this->getUrl('sales/order/print', ['order_id' => $order->getId()]);
    }

    /**
     * Get url for reorder action
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/reorder', ['order_id' => $order->getId()]);
        }
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
}
