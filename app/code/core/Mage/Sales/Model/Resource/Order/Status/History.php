<?php
/**
 * Flat sales order status history resource
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Status_History extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_status_history_resource';

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('sales/order_status_history', 'entity_id');
    }
}
