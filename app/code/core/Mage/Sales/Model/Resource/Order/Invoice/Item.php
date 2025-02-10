<?php
/**
 * Flat sales order invoice item resource
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_item_resource';

    protected function _construct()
    {
        $this->_init('sales/invoice_item', 'entity_id');
    }
}
