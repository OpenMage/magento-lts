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
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item _getResource()
 * @method string getAdditionalData()
 * @method float getBaseCost()
 * @method float getBaseDiscountAmount()
 * @method float getBaseHiddenTaxAmount()
 * @method float getBasePrice()
 * @method float getBasePriceInclTax()
 * @method float getBaseRowTotal()
 * @method float getBaseRowTotalInclTax()
 * @method float getBaseTaxAmount()
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method float getBaseWeeeTaxAppliedRowAmount()
 * @method float getBaseWeeeTaxDisposition()
 * @method float getBaseWeeeTaxRowDisposition()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item_Collection getCollection()
 * @method string getDescription()
 * @method float getDiscountAmount()
 * @method float getHiddenTaxAmount()
 * @method string getName()
 * @method int getOrderItemId()
 * @method int getParentId()
 * @method float getPrice()
 * @method float getPriceInclTax()
 * @method int getProductId()
 * @method float getQty()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item_Collection getResourceCollection()
 * @method float getRowTotal()
 * @method float getRowTotalInclTax()
 * @method string getSku()
 * @method float getTaxAmount()
 * @method string getWeeeTaxApplied()
 * @method float getWeeeTaxAppliedAmount()
 * @method float getWeeeTaxAppliedRowAmount()
 * @method float getWeeeTaxDisposition()
 * @method float getWeeeTaxRowDisposition()
 * @method $this setAdditionalData(string $value)
 * @method $this setBaseCost(float $value)
 * @method $this setBaseDiscountAmount(float $value)
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method $this setBasePrice(float $value)
 * @method $this setBasePriceInclTax(float $value)
 * @method $this setBaseRowTotal(float $value)
 * @method $this setBaseRowTotalInclTax(float $value)
 * @method $this setBaseTaxAmount(float $value)
 * @method $this setBaseWeeeTaxAppliedAmount(float $value)
 * @method $this setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method $this setBaseWeeeTaxDisposition(float $value)
 * @method $this setBaseWeeeTaxRowDisposition(float $value)
 * @method $this setDescription(string $value)
 * @method $this setDiscountAmount(float $value)
 * @method $this setHiddenTaxAmount(float $value)
 * @method $this setName(string $value)
 * @method $this setOrderItemId(int $value)
 * @method $this setParentId(int $value)
 * @method $this setPrice(float $value)
 * @method $this setPriceInclTax(float $value)
 * @method $this setProductId(int $value)
 * @method $this setRowTotal(float $value)
 * @method $this setRowTotalInclTax(float $value)
 * @method $this setSku(string $value)
 * @method $this setStoreId(int $value)
 * @method $this setTaxAmount(float $value)
 * @method $this setWeeeTaxApplied(string $value)
 * @method $this setWeeeTaxAppliedAmount(float $value)
 * @method $this setWeeeTaxAppliedRowAmount(float $value)
 * @method $this setWeeeTaxDisposition(float $value)
 * @method $this setWeeeTaxRowDisposition(float $value)
 */
class Mage_Sales_Model_Order_Invoice_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_invoice_item';

    protected $_eventObject = 'invoice_item';

    /**
     * @var Mage_Sales_Model_Order_Invoice
     */
    protected $_invoice = null;

    /**
     * @var null|Mage_Sales_Model_Order_Item
     */
    protected $_orderItem = null;

    public function _construct()
    {
        $this->_init('sales/order_invoice_item');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        // pre 1.6 fields names, old => new
        $this->_oldFieldsMap = [
            'base_weee_tax_applied_row_amount' => 'base_weee_tax_applied_row_amnt',
        ];
        return $this;
    }

    /**
     * Declare invoice instance
     *
     * @return  $this
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
     * Declare order item instance
     *
     * @return  $this
     */
    public function setOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        if ($this->getOrderItemId() != $item->getId()) {
            $this->setOrderItemId($item->getId());
        }

        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (is_null($this->_orderItem)) {
            if ($this->getInvoice()) {
                $this->_orderItem = $this->getInvoice()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = Mage::getModel('sales/order_item')
                    ->load($this->getOrderItemId());
            }
        }

        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param   float $qty
     * @return  $this
     */
    public function setQty($qty)
    {
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float) $qty;
        } else {
            $qty = (int) $qty;
        }

        $qty = $qty > 0 ? $qty : 0;
        /**
         * Check qty availability
         */
        $qtyToInvoice = sprintf('%F', $this->getOrderItem()->getQtyToInvoice());
        $qty = sprintf('%F', $qty);
        if ($qty <= $qtyToInvoice || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to invoice item "%s"', $this->getName()),
            );
        }

        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return $this
     */
    public function register()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced() + $this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced() + $this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced() + $this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced() + $this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced() + $this->getBaseHiddenTaxAmount());

        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced() + $this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced() + $this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced() + $this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced() + $this->getBaseRowTotal());
        return $this;
    }

    /**
     * Cancelling invoice item
     *
     * @return $this
     */
    public function cancel()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced() - $this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced() - $this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced() - $this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced() - $this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced() - $this->getBaseHiddenTaxAmount());

        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced() - $this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced() - $this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced() - $this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced() - $this->getBaseRowTotal());
        return $this;
    }

    /**
     * Invoice item row total calculation
     *
     * @return $this
     */
    public function calcRowTotal()
    {
        $invoice        = $this->getInvoice();
        $orderItem      = $this->getOrderItem();
        $orderItemQty   = $orderItem->getQtyOrdered();

        $rowTotal            = $orderItem->getRowTotal() - $orderItem->getRowInvoiced();
        $baseRowTotal        = $orderItem->getBaseRowTotal() - $orderItem->getBaseRowInvoiced();
        $rowTotalInclTax     = $orderItem->getRowTotalInclTax();
        $baseRowTotalInclTax = $orderItem->getBaseRowTotalInclTax();

        if (!$this->isLast()) {
            $availableQty = $orderItemQty - $orderItem->getQtyInvoiced();
            $rowTotal = $invoice->roundPrice($rowTotal / $availableQty * $this->getQty());
            $baseRowTotal = $invoice->roundPrice($baseRowTotal / $availableQty * $this->getQty(), 'base');
        }

        $this->setRowTotal($rowTotal);
        $this->setBaseRowTotal($baseRowTotal);

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $this->setRowTotalInclTax($invoice->roundPrice($rowTotalInclTax / $orderItemQty * $this->getQty(), 'including'));
            $this->setBaseRowTotalInclTax($invoice->roundPrice($baseRowTotalInclTax / $orderItemQty * $this->getQty(), 'including_base'));
        }

        return $this;
    }

    /**
     * Checking if the item is last
     *
     * @return bool
     */
    public function isLast()
    {
        if ((string) (float) $this->getQty() == (string) (float) $this->getOrderItem()->getQtyToInvoice()) {
            return true;
        }

        return false;
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

    /**
     * After object save
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if (!$this->_orderItem == null) {
            $this->_orderItem->save();
        }

        parent::_afterSave();
        return $this;
    }
}
