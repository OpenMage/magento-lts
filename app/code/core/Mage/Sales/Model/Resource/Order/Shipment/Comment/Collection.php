<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order shipment comments collection
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Shipment_Comment getItemById(int $value)
 * @method Mage_Sales_Model_Order_Shipment_Comment[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Shipment_Comment_Collection extends Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_comment_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_shipment_comment_collection';

    protected function _construct()
    {
        $this->_init('sales/order_shipment_comment');
    }

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentFilter($shipmentId)
    {
        return $this->setParentFilter($shipmentId);
    }
}
