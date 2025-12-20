<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order shipment tracks collection
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Shipment_Track   getItemById(int $value)
 * @method Mage_Sales_Model_Order_Shipment_Track[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Shipment_Track_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_track_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_shipment_track_collection';

    /**
     * Order field
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_shipment_track');
    }

    /**
     * Set shipment filter
     *
     * @param  int   $shipmentId
     * @return $this
     */
    public function setShipmentFilter($shipmentId)
    {
        $this->addFieldToFilter('parent_id', $shipmentId);
        return $this;
    }
}
