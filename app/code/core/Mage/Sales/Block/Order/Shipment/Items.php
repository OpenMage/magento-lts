<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order view items block
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Shipment_Items extends Mage_Sales_Block_Items_Abstract
{
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
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return string
     */
    public function getPrintShipmentUrl($shipment)
    {
        return Mage::getUrl('*/*/printShipment', ['shipment_id' => $shipment->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getPrintAllShipmentsUrl($order)
    {
        return Mage::getUrl('*/*/printShipment', ['order_id' => $order->getId()]);
    }

    /**
     * Get html of shipment comments block
     *
     * @param   Mage_Sales_Model_Order_Shipment $shipment
     * @return  string
     */
    public function getCommentsHtml($shipment)
    {
        $html = '';
        $comments = $this->getChild('shipment_comments');
        if ($comments) {
            $comments->setEntity($shipment)
                ->setTitle(Mage::helper('sales')->__('About Your Shipment'));
            $html = $comments->toHtml();
        }

        return $html;
    }
}
