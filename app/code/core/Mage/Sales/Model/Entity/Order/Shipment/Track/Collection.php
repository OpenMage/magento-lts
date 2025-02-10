<?php
/**
 * Order shipment track collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Shipment_Track_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('sales/order_shipment_track');
    }

    /**
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentFilter($shipmentId)
    {
        $this->addAttributeToFilter('parent_id', $shipmentId);
        return $this;
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderFilter($orderId)
    {
        $this->addAttributeToFilter('order_id', $orderId);
        return $this;
    }
}
