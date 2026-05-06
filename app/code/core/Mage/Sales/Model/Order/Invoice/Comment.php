<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment            getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection getResourceCollection()
 */
class Mage_Sales_Model_Order_Invoice_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Invoice instance
     *
     * @var Mage_Sales_Model_Order_Invoice
     */
    protected $_invoice;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_invoice_comment');
    }

    /**
     * Declare invoice instance
     *
     * @return $this
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
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getInvoice()) {
            $this->setParentId($this->getInvoice()->getId());
        }

        return $this;
    }

    public function getComment(): string
    {
        return (string) $this->_getData('comment');
    }

    public function setComment(string $value): static
    {
        return $this->setData('comment', $value);
    }

    public function getIsCustomerNotified(): int
    {
        return (int) $this->_getData('is_customer_notified');
    }

    public function setIsCustomerNotified(int $value): static
    {
        return $this->setData('is_customer_notified', $value);
    }

    public function getIsVisibleOnFront(): int
    {
        return (int) $this->_getData('is_visible_on_front');
    }

    public function setIsVisibleOnFront(int $value): static
    {
        return $this->setData('is_visible_on_front', $value);
    }

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}
