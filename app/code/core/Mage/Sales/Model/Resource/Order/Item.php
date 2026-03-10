<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order item resource
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_item_resource';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_item', 'item_id');
    }
}
