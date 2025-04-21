<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment getResource()
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method $this setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method $this setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method $this setComment(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method $this setStoreId(int $value)
 */
class Mage_Sales_Model_Order_Invoice_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Invoice instance
     *
     * @var Mage_Sales_Model_Order_Invoice
     */
    protected $_invoice;

    protected function _construct()
    {
        $this->_init('sales/order_invoice_comment');
    }

    /**
     * Declare invoice instance
     *
     * @return  Mage_Sales_Model_Order_Invoice_Comment
     */
    public function setInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getInvoice()) {
            return $this->getInvoice()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getInvoice()) {
            $this->setParentId($this->getInvoice()->getId());
        }

        return $this;
    }
}
