<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order invoice item collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Item_Collection extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_item_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_invoice_item_collection';

    protected function _construct()
    {
        $this->_init('sales/order_invoice_item');
    }

    /**
     * Set invoice filter
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceFilter($invoiceId)
    {
        $this->addFieldToFilter('parent_id', $invoiceId);
        return $this;
    }
}
