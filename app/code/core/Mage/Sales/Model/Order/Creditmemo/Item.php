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
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item            _getResource()
 * @method bool                                                       getBackToStock()
 * @method bool                                                       getCanReturnToStock()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item            getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection getResourceCollection()
 * @method bool                                                       hasBackToStock()
 * @method bool                                                       hasCanReturnToStock()
 * @method $this                                                      setBackToStock(bool $value)
 * @method $this                                                      setCanReturnToStock(bool $value)
 */
class Mage_Sales_Model_Order_Creditmemo_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_creditmemo_item';

    protected $_eventObject = 'creditmemo_item';

    protected $_creditmemo = null;

    protected $_orderItem = null;

    protected function _construct()
    {
        $this->_init('sales/order_creditmemo_item');
    }

    /**
     * Declare creditmemo instance
     *
     * @return $this
     */
    public function setCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
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
     * Retrieve creditmemo instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
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
            if ($this->getCreditmemo()) {
                $this->_orderItem = $this->getCreditmemo()->getOrder()->getItemById($this->getOrderItemId());
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
        if ($qty <= $this->getOrderItem()->getQtyToRefund() || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to refund item "%s"', $this->getName()),
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

        $orderItem->setQtyRefunded($orderItem->getQtyRefunded() + $this->getQty());
        $orderItem->setTaxRefunded($orderItem->getTaxRefunded() + $this->getTaxAmount());
        $orderItem->setBaseTaxRefunded($orderItem->getBaseTaxRefunded() + $this->getBaseTaxAmount());
        $orderItem->setHiddenTaxRefunded($orderItem->getHiddenTaxRefunded() + $this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxRefunded($orderItem->getBaseHiddenTaxRefunded() + $this->getBaseHiddenTaxAmount());
        $orderItem->setAmountRefunded($orderItem->getAmountRefunded() + $this->getRowTotal());
        $orderItem->setBaseAmountRefunded($orderItem->getBaseAmountRefunded() + $this->getBaseRowTotal());
        $orderItem->setDiscountRefunded($orderItem->getDiscountRefunded() + $this->getDiscountAmount());
        $orderItem->setBaseDiscountRefunded($orderItem->getBaseDiscountRefunded() + $this->getBaseDiscountAmount());

        return $this;
    }

    /**
     * @return $this
     */
    public function cancel()
    {
        $this->getOrderItem()->setQtyRefunded(
            $this->getOrderItem()->getQtyRefunded() - $this->getQty(),
        );
        $this->getOrderItem()->setTaxRefunded(
            $this->getOrderItem()->getTaxRefunded()
                - $this->getOrderItem()->getBaseTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered(),
        );
        $this->getOrderItem()->setHiddenTaxRefunded(
            $this->getOrderItem()->getHiddenTaxRefunded()
                - $this->getOrderItem()->getHiddenTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered(),
        );
        return $this;
    }

    /**
     * Invoice item row total calculation
     *
     * @return $this
     */
    public function calcRowTotal()
    {
        $creditmemo           = $this->getCreditmemo();
        $orderItem            = $this->getOrderItem();
        $orderItemQtyInvoiced = $orderItem->getQtyInvoiced();

        $rowTotal            = $orderItem->getRowInvoiced() - $orderItem->getAmountRefunded();
        $baseRowTotal        = $orderItem->getBaseRowInvoiced() - $orderItem->getBaseAmountRefunded();
        $rowTotalInclTax     = $orderItem->getRowTotalInclTax();
        $baseRowTotalInclTax = $orderItem->getBaseRowTotalInclTax();

        if (!$this->isLast()) {
            $availableQty = $orderItemQtyInvoiced - $orderItem->getQtyRefunded();
            $rowTotal     = $creditmemo->roundPrice($rowTotal / $availableQty * $this->getQty());
            $baseRowTotal = $creditmemo->roundPrice($baseRowTotal / $availableQty * $this->getQty(), 'base');
        }

        $this->setRowTotal($rowTotal);
        $this->setBaseRowTotal($baseRowTotal);

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $orderItemQty = $orderItem->getQtyOrdered();
            $this->setRowTotalInclTax(
                $creditmemo->roundPrice($rowTotalInclTax / $orderItemQty * $this->getQty(), 'including'),
            );
            $this->setBaseRowTotalInclTax(
                $creditmemo->roundPrice($baseRowTotalInclTax / $orderItemQty * $this->getQty(), 'including_base'),
            );
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
        $orderItem = $this->getOrderItem();
        return (string) (float) $this->getQty() == (string) (float) $orderItem->getQtyToRefund()
                && !$orderItem->getQtyToInvoice();
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

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

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
