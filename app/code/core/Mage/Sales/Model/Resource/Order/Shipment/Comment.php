<?php
/**
 * Flat sales order shipment comment resource
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Shipment_Comment extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_comment_resource';

    protected function _construct()
    {
        $this->_init('sales/shipment_comment', 'entity_id');
    }
}
