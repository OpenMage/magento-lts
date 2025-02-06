<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Shipment comments collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Shipment_Comment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_shipment_comment');
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
     * @param string $order
     * @return $this
     */
    public function setCreatedAtOrder($order = 'desc')
    {
        $this->setOrder('created_at', $order);
        return $this;
    }
}
