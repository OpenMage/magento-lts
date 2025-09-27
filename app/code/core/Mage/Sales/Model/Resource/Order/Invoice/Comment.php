<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order invoice comment resource
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Comment extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_comment_resource';

    protected function _construct()
    {
        $this->_init('sales/invoice_comment', 'entity_id');
    }
}
