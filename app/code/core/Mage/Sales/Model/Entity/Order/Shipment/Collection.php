<?php
/**
 * Shipment collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Shipment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_shipment');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function setOrderFilter($order)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            $this->addAttributeToFilter('order_id', $order->getId());
        } else {
            $this->addAttributeToFilter('order_id', $order);
        }

        return $this;
    }
}
