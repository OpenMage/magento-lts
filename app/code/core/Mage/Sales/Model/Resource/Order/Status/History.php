<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order status history resource
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Status_History extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_status_history_resource';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_status_history', 'entity_id');
    }
}
