<?php
/**
 * Invoice items collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Invoice_Item_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_invoice_item');
    }

    /**
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceFilter($invoiceId)
    {
        $this->addAttributeToFilter('parent_id', $invoiceId);
        return $this;
    }
}
