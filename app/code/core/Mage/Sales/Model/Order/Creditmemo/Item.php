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
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item            _getResource()
 * @method string                                                     getAdditionalData()
 * @method bool                                                       getBackToStock()
 * @method float                                                      getBaseCost()
 * @method float                                                      getBaseDiscountAmount()
 * @method float                                                      getBaseHiddenTaxAmount()
 * @method float                                                      getBasePrice()
 * @method float                                                      getBasePriceInclTax()
 * @method float                                                      getBaseRowTotal()
 * @method float                                                      getBaseRowTotalInclTax()
 * @method float                                                      getBaseTaxAmount()
 * @method float                                                      getBaseWeeeTaxAppliedAmount()
 * @method float                                                      getBaseWeeeTaxAppliedRowAmount()
 * @method float                                                      getBaseWeeeTaxDisposition()
 * @method float                                                      getBaseWeeeTaxRowDisposition()
 * @method bool                                                       getCanReturnToStock()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection getCollection()
 * @method string                                                     getDescription()
 * @method float                                                      getDiscountAmount()
 * @method float                                                      getHiddenTaxAmount()
 * @method string                                                     getName()
 * @method int                                                        getOrderItemId()
 * @method int                                                        getParentId()
 * @method float                                                      getPrice()
 * @method float                                                      getPriceInclTax()
 * @method int                                                        getProductId()
 * @method float                                                      getQty()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item            getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection getResourceCollection()
 * @method float                                                      getRowTotal()
 * @method float                                                      getRowTotalInclTax()
 * @method string                                                     getSku()
 * @method float                                                      getTaxAmount()
 * @method string                                                     getWeeeTaxApplied()
 * @method float                                                      getWeeeTaxAppliedAmount()
 * @method float                                                      getWeeeTaxAppliedRowAmount()
 * @method float                                                      getWeeeTaxDisposition()
 * @method float                                                      getWeeeTaxRowDisposition()
 * @method bool                                                       hasBackToStock()
 * @method bool                                                       hasCanReturnToStock()
 * @method $this                                                      setAdditionalData(string $value)
 * @method $this                                                      setBackToStock(bool $value)
 * @method $this                                                      setBaseCost(float $value)
 * @method $this                                                      setBaseDiscountAmount(float $value)
 * @method $this                                                      setBaseHiddenTaxAmount(float $value)
 * @method $this                                                      setBasePrice(float $value)
 * @method $this                                                      setBasePriceInclTax(float $value)
 * @method $this                                                      setBaseRowTotal(float $value)
 * @method $this                                                      setBaseRowTotalInclTax(float $value)
 * @method $this                                                      setBaseTaxAmount(float $value)
 * @method $this                                                      setBaseWeeeTaxAppliedAmount(float $value)
 * @method $this                                                      setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method $this                                                      setBaseWeeeTaxDisposition(float $value)
 * @method $this                                                      setBaseWeeeTaxRowDisposition(float $value)
 * @method $this                                                      setCanReturnToStock(bool $value)
 * @method $this                                                      setDescription(string $value)
 * @method $this                                                      setDiscountAmount(float $value)
 * @method $this                                                      setHiddenTaxAmount(float $value)
 * @method $this                                                      setName(string $value)
 * @method $this                                                      setOrderItemId(int $value)
 * @method $this                                                      setParentId(int $value)
 * @method $this                                                      setPrice(float $value)
 * @method $this                                                      setPriceInclTax(float $value)
 * @method $this                                                      setProductId(int $value)
 * @method $this                                                      setRowTotal(float $value)
 * @method $this                                                      setRowTotalInclTax(float $value)
 * @method $this                                                      setSku(string $value)
 * @method $this                                                      setStoreId(int $value)
 * @method $this                                                      setTaxAmount(float $value)
 * @method $this                                                      setWeeeTaxApplied(string $value)
 * @method $this                                                      setWeeeTaxAppliedAmount(float $value)
 * @method $this                                                      setWeeeTaxAppliedRowAmount(float $value)
 * @method $this                                                      setWeeeTaxDisposition(float $value)
 * @method $this                                                      setWeeeTaxRowDisposition(float $value)
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
     * @return Varien_Object
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
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float) $qty;
        } else {
            $qty = (int) $qty;
        }

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
        if ((string) (float) $this->getQty() == (string) (float) $orderItem->getQtyToRefund()
                && !$orderItem->getQtyToInvoice()
        ) {
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

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

        return $this;
    }
}
