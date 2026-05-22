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
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item            getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item_Collection getResourceCollection()
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

    protected function _construct()
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
     * Declare order item instance
     *
     * @return $this
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
     * @param  float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $qty = $this->getOrderItem()->getIsQtyDecimal() ? (float) $qty : (int) $qty;
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
        return (string) (float) $this->getQty() === (string) (float) $this->getOrderItem()->getQtyToInvoice();
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

    /**
     * After object save
     *
     * @return $this
     */
    #[Override]
    protected function _afterSave()
    {
        if (!$this->_orderItem == null) {
            $this->_orderItem->save();
        }

        parent::_afterSave();
        return $this;
    }

    public function getAdditionalData(): string
    {
        return (string) $this->_getData('additional_data');
    }

    public function getBaseCost(): float
    {
        return (float) $this->_getData('base_cost');
    }

    public function getBaseDiscountAmount(): float
    {
        return (float) $this->_getData('base_discount_amount');
    }

    public function getBaseHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    public function getBasePrice(): float
    {
        return (float) $this->_getData('base_price');
    }

    public function getBasePriceInclTax(): float
    {
        return (float) $this->_getData('base_price_incl_tax');
    }

    public function getBaseRowTotal(): float
    {
        return (float) $this->_getData('base_row_total');
    }

    public function getBaseRowTotalInclTax(): float
    {
        return (float) $this->_getData('base_row_total_incl_tax');
    }

    public function getBaseTaxAmount(): float
    {
        return (float) $this->_getData('base_tax_amount');
    }

    public function getBaseWeeeTaxAppliedAmount(): float
    {
        return (float) $this->_getData('base_weee_tax_applied_amount');
    }

    public function getBaseWeeeTaxAppliedRowAmount(): float
    {
        return (float) $this->_getData('base_weee_tax_applied_row_amount');
    }

    public function getBaseWeeeTaxDisposition(): float
    {
        return (float) $this->_getData('base_weee_tax_disposition');
    }

    public function getBaseWeeeTaxRowDisposition(): float
    {
        return (float) $this->_getData('base_weee_tax_row_disposition');
    }

    public function getDescription(): string
    {
        return (string) $this->_getData('description');
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->_getData('discount_amount');
    }

    public function getHiddenTaxAmount(): float
    {
        return (float) $this->_getData('hidden_tax_amount');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function getOrderItemId(): int
    {
        return (int) $this->_getData('order_item_id');
    }

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function getPrice(): float
    {
        return (float) $this->_getData('price');
    }

    public function getPriceInclTax(): float
    {
        return (float) $this->_getData('price_incl_tax');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getQty(): float
    {
        return (float) $this->_getData('qty');
    }

    public function getRowTotal(): float
    {
        return (float) $this->_getData('row_total');
    }

    public function getRowTotalInclTax(): float
    {
        return (float) $this->_getData('row_total_incl_tax');
    }

    public function getSku(): string
    {
        return (string) $this->_getData('sku');
    }

    public function getTaxAmount(): float
    {
        return (float) $this->_getData('tax_amount');
    }

    public function getWeeeTaxApplied(): string
    {
        return (string) $this->_getData('weee_tax_applied');
    }

    public function getWeeeTaxAppliedAmount(): float
    {
        return (float) $this->_getData('weee_tax_applied_amount');
    }

    public function getWeeeTaxAppliedRowAmount(): float
    {
        return (float) $this->_getData('weee_tax_applied_row_amount');
    }

    public function getWeeeTaxDisposition(): float
    {
        return (float) $this->_getData('weee_tax_disposition');
    }

    public function getWeeeTaxRowDisposition(): float
    {
        return (float) $this->_getData('weee_tax_row_disposition');
    }

    public function setAdditionalData(string $value): static
    {
        return $this->setData('additional_data', $value);
    }

    public function setBaseCost(float $value): static
    {
        return $this->setData('base_cost', $value);
    }

    public function setBaseDiscountAmount(float $value): static
    {
        return $this->setData('base_discount_amount', $value);
    }

    public function setBaseHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_hidden_tax_amount', $value);
    }

    public function setBasePrice(float $value): static
    {
        return $this->setData('base_price', $value);
    }

    public function setBasePriceInclTax(float $value): static
    {
        return $this->setData('base_price_incl_tax', $value);
    }

    public function setBaseRowTotal(float $value): static
    {
        return $this->setData('base_row_total', $value);
    }

    public function setBaseRowTotalInclTax(float $value): static
    {
        return $this->setData('base_row_total_incl_tax', $value);
    }

    public function setBaseTaxAmount(float $value): static
    {
        return $this->setData('base_tax_amount', $value);
    }

    public function setBaseWeeeTaxAppliedAmount(float $value): static
    {
        return $this->setData('base_weee_tax_applied_amount', $value);
    }

    public function setBaseWeeeTaxAppliedRowAmount(float $value): static
    {
        return $this->setData('base_weee_tax_applied_row_amount', $value);
    }

    public function setBaseWeeeTaxDisposition(float $value): static
    {
        return $this->setData('base_weee_tax_disposition', $value);
    }

    public function setBaseWeeeTaxRowDisposition(float $value): static
    {
        return $this->setData('base_weee_tax_row_disposition', $value);
    }

    public function setDescription(string $value): static
    {
        return $this->setData('description', $value);
    }

    public function setDiscountAmount(float $value): static
    {
        return $this->setData('discount_amount', $value);
    }

    public function setHiddenTaxAmount(float $value): static
    {
        return $this->setData('hidden_tax_amount', $value);
    }

    public function setName(string $value): static
    {
        return $this->setData('name', $value);
    }

    public function setOrderItemId(int $value): static
    {
        return $this->setData('order_item_id', $value);
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function setPrice(float $value): static
    {
        return $this->setData('price', $value);
    }

    public function setPriceInclTax(float $value): static
    {
        return $this->setData('price_incl_tax', $value);
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setRowTotal(float $value): static
    {
        return $this->setData('row_total', $value);
    }

    public function setRowTotalInclTax(float $value): static
    {
        return $this->setData('row_total_incl_tax', $value);
    }

    public function setSku(string $value): static
    {
        return $this->setData('sku', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function setTaxAmount(float $value): static
    {
        return $this->setData('tax_amount', $value);
    }

    public function setWeeeTaxApplied(string $value): static
    {
        return $this->setData('weee_tax_applied', $value);
    }

    public function setWeeeTaxAppliedAmount(float $value): static
    {
        return $this->setData('weee_tax_applied_amount', $value);
    }

    public function setWeeeTaxAppliedRowAmount(float $value): static
    {
        return $this->setData('weee_tax_applied_row_amount', $value);
    }

    public function setWeeeTaxDisposition(float $value): static
    {
        return $this->setData('weee_tax_disposition', $value);
    }

    public function setWeeeTaxRowDisposition(float $value): static
    {
        return $this->setData('weee_tax_row_disposition', $value);
    }
}
