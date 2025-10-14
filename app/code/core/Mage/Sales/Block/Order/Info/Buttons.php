<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Block of links in Order view page
 *
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
