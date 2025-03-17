<?php

/**
 * Flat sales order shipment item resource
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Shipment_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_item_resource';

    protected function _construct()
    {
        $this->_init('sales/shipment_item', 'entity_id');
    }
}
