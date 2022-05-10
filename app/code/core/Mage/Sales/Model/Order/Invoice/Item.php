<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Invoice_Item setParentId(int $value)
 * @method int getProductId()
 * @method Mage_Sales_Model_Order_Invoice_Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method Mage_Sales_Model_Order_Invoice_Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method Mage_Sales_Model_Order_Invoice_Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method Mage_Sales_Model_Order_Invoice_Item setDescription(string $value)
 * @method string getWeeeTaxApplied()
 * @method Mage_Sales_Model_Order_Invoice_Item setWeeeTaxApplied(string $value)
 * @method string getSku()
 * @method Mage_Sales_Model_Order_Invoice_Item setSku(string $value)
 * @method string getName()
 * @method Mage_Sales_Model_Order_Invoice_Item setName(string $value)
 * @method Mage_Sales_Model_Order_Invoice_Item setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_invoice_item';
    protected $_eventObject = 'invoice_item';

    /** @var Mage_Sales_Model_Order_Invoice */
    protected $_invoice = null;
    /** @var Mage_Sales_Model_Order_Item */
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init('sales/order_invoice_item');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = Mage::helper('sales')->getOldFieldMap('invoice_item');
        return $this;
    }

    /**
     * Declare invoice instance
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Item
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
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Invoice_Item
     */
    public function setOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
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
     * @return  Mage_Sales_Model_Order_Invoice_Item
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
        $qtyToInvoice = sprintf("%F", $this->getOrderItem()->getQtyToInvoice());
        $qty = sprintf("%F", $qty);
        if ($qty <= $qtyToInvoice || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to invoice item "%s"', $this->getName())
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
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()+$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()+$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()+$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()+$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()+$this->getBaseHiddenTaxAmount());

        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()+$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()+$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()+$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()+$this->getBaseRowTotal());
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
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()-$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()-$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()-$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()-$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()-$this->getBaseHiddenTaxAmount());


        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()-$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()-$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()-$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()-$this->getBaseRowTotal());
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
        if ((string)(float)$this->getQty() == (string)(float)$this->getOrderItem()->getQtyToInvoice()) {
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
        if (null ==! $this->_orderItem) {
            $this->_orderItem->save();
        }

        parent::_afterSave();
        return $this;
    }

    /**
     * @return float
     */
    public function getBasePrice()
    {
        return (float) $this->_getData('base_price');
    }

    /**
     * @return float
     */
    public function getBaseWeeeTaxRowDisposition()
    {
        return (float) $this->_getData('base_weee_tax_row_disposition');
    }

    /**
     * @return float
     */
    public function getWeeeTaxAppliedRowAmount()
    {
        return (float) $this->_getData('weee_tax_applied_row_amount');
    }

    /**
     * @return float
     */
    public function getBaseWeeeTaxAppliedAmount()
    {
        return (float) $this->_getData('base_weee_tax_applied_amount');
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return (float) $this->_getData('tax_amount');
    }

    /**
     * @return float
     */
    public function getBaseRowTotal()
    {
        return (float) $this->_getData('base_row_total');
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return (float) $this->_getData('discount_amount');
    }

    /**
     * @return float
     */
    public function getRowTotal()
    {
        return (float) $this->_getData('row_total');
    }

    /**
     * @return float
     */
    public function getWeeeTaxRowDisposition()
    {
        return (float) $this->_getData('weee_tax_row_disposition');
    }

    /**
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return (float) $this->_getData('base_discount_amount');
    }

    /**
     * @return float
     */
    public function getBaseWeeeTaxDisposition()
    {
        return (float) $this->_getData('base_weee_tax_disposition');
    }

    /**
     * @return float
     */
    public function getPriceInclTax()
    {
        return (float) $this->_getData('price_incl_tax');
    }

    /**
     * @return float
     */
    public function getWeeeTaxAppliedAmount()
    {
        return (float) $this->_getData('weee_tax_applied_amount');
    }

    /**
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return (float) $this->_getData('base_tax_amount');
    }

    /**
     * @return float
     */
    public function getBasePriceInclTax()
    {
        return (float) $this->_getData('base_price_incl_tax');
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return (float) $this->_getData('qty');
    }

    /**
     * @return float
     */
    public function getWeeeTaxDisposition()
    {
        return (float) $this->_getData('weee_tax_disposition');
    }

    /**
     * @return float
     */
    public function getBaseCost()
    {
        return (float) $this->_getData('base_cost');
    }

    /**
     * @return float
     */
    public function getBaseWeeeTaxAppliedRowAmount()
    {
        return (float) $this->_getData('base_weee_tax_applied_row_amount');
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return (float) $this->_getData('price');
    }

    /**
     * @return float
     */
    public function getBaseRowTotalInclTax()
    {
        return (float) $this->_getData('base_row_total_incl_tax');
    }

    /**
     * @return float
     */
    public function getRowTotalInclTax()
    {
        return (float) $this->_getData('row_total_incl_tax');
    }

    /**
     * @return float
     */
    public function getHiddenTaxAmount()
    {
        return (float) $this->_getData('hidden_tax_amount');
    }

    /**
     * @return float
     */
    public function getBaseHiddenTaxAmount()
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    /**
     * @return $this
     */
    public function setBasePrice($value)
    {
        return $this->setData('base_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseWeeeTaxRowDisposition($value)
    {
        return $this->setData('base_weee_tax_row_disposition', (float) $value);
    }

    /**
     * @return $this
     */
    public function setWeeeTaxAppliedRowAmount($value)
    {
        return $this->setData('weee_tax_applied_row_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseWeeeTaxAppliedAmount($value)
    {
        return $this->setData('base_weee_tax_applied_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setTaxAmount($value)
    {
        return $this->setData('tax_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseRowTotal($value)
    {
        return $this->setData('base_row_total', (float) $value);
    }

    /**
     * @return $this
     */
    public function setDiscountAmount($value)
    {
        return $this->setData('discount_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setRowTotal($value)
    {
        return $this->setData('row_total', (float) $value);
    }

    /**
     * @return $this
     */
    public function setWeeeTaxRowDisposition($value)
    {
        return $this->setData('weee_tax_row_disposition', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseDiscountAmount($value)
    {
        return $this->setData('base_discount_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseWeeeTaxDisposition($value)
    {
        return $this->setData('base_weee_tax_disposition', (float) $value);
    }

    /**
     * @return $this
     */
    public function setPriceInclTax($value)
    {
        return $this->setData('price_incl_tax', (float) $value);
    }

    /**
     * @return $this
     */
    public function setWeeeTaxAppliedAmount($value)
    {
        return $this->setData('weee_tax_applied_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseTaxAmount($value)
    {
        return $this->setData('base_tax_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBasePriceInclTax($value)
    {
        return $this->setData('base_price_incl_tax', (float) $value);
    }

    /**
     * @return $this
     */
    public function setWeeeTaxDisposition($value)
    {
        return $this->setData('weee_tax_disposition', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseCost($value)
    {
        return $this->setData('base_cost', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseWeeeTaxAppliedRowAmount($value)
    {
        return $this->setData('base_weee_tax_applied_row_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->setData('price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseRowTotalInclTax($value)
    {
        return $this->setData('base_row_total_incl_tax', (float) $value);
    }

    /**
     * @return $this
     */
    public function setRowTotalInclTax($value)
    {
        return $this->setData('row_total_incl_tax', (float) $value);
    }

    /**
     * @return $this
     */
    public function setHiddenTaxAmount($value)
    {
        return $this->setData('hidden_tax_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseHiddenTaxAmount($value)
    {
        return $this->setData('base_hidden_tax_amount', (float) $value);
    }
}
