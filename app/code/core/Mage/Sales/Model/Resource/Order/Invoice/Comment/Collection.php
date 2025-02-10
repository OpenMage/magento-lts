<?php
/**
 * Flat sales order invoice comment collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection extends Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_comment_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_invoice_comment_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('sales/order_invoice_comment');
    }

    /**
     * Set invoice filter
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceFilter($invoiceId)
    {
        return $this->setParentFilter($invoiceId);
    }
}
