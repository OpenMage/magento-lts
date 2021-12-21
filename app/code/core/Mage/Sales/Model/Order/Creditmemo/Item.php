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
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item _getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection getCollection()
 *
 * @method string getAdditionalData()
 * @method $this setAdditionalData(string $value)
 *
 * @method bool hasBackToStock()
 * @method bool getBackToStock()
 * @method $this setBackToStock(bool $value)
 *
 * @method bool hasCanReturnToStock()
 * @method bool getCanReturnToStock()
 * @method $this setCanReturnToStock(bool $value)
 *
 * @method string getDescription()
 * @method $this setDescription(string $value)
 *
 *
 * @method string getName()
 * @method $this setName(string $value)
 *
 * @method int getOrderItemId()
 * @method $this setOrderItemId(int $value)
 *
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 *
 *
 *
 * @method string getSku()
 * @method $this setSku(string $value)
 * @method $this setStoreId(int $value)
 *
 *
 * @method string getWeeeTaxApplied()
 * @method $this setWeeeTaxApplied(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_creditmemo_item';
    protected $_eventObject = 'creditmemo_item';
    protected $_creditmemo = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init('sales/order_creditmemo_item');
    }

    /**
     * Declare creditmemo instance
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  $this
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
        $this->_oldFieldsMap = Mage::helper('sales')->getOldFieldMap('creditmemo_item');
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
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  $this
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
        if ($qty <= $this->getOrderItem()->getQtyToRefund() || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to refund item "%s"', $this->getName())
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
            $this->getOrderItem()->getQtyRefunded()-$this->getQty()
        );
        $this->getOrderItem()->setTaxRefunded(
            $this->getOrderItem()->getTaxRefunded()
                - $this->getOrderItem()->getBaseTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered()
        );
        $this->getOrderItem()->setHiddenTaxRefunded(
            $this->getOrderItem()->getHiddenTaxRefunded()
                - $this->getOrderItem()->getHiddenTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered()
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
                $creditmemo->roundPrice($rowTotalInclTax / $orderItemQty * $this->getQty(), 'including')
            );
            $this->setBaseRowTotalInclTax(
                $creditmemo->roundPrice($baseRowTotalInclTax / $orderItemQty * $this->getQty(), 'including_base')
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
        if ((string)(float)$this->getQty() == (string)(float)$orderItem->getQtyToRefund()
                && !$orderItem->getQtyToInvoice()) {
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
    public function getBaseDiscountAmount()
    {
        return (float) $this->_getData('base_discount_amount');
    }

    /**
     * @return float
     */
    public function getBaseHiddenTaxAmount()
    {
        return (float) $this->_getData('base_hidden_tax_amount');
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
    public function getBasePriceInclTax()
    {
        return (float) $this->_getData('base_price_incl_tax');
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
    public function getBaseRowTotalInclTax()
    {
        return (float) $this->_getData('base_row_total_incl_tax');
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
    public function getBaseWeeeTaxAppliedAmount()
    {
        return (float) $this->_getData('base_weee_tax_applied_amount');
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
    public function getBaseWeeeTaxDisposition()
    {
        return (float) $this->_getData('base_weee_tax_disposition');
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
    public function getDiscountAmount()
    {
        return (float) $this->_getData('discount_amount');
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
    public function getPrice()
    {
        return (float) $this->_getData('price');
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
    public function getQty()
    {
        return (float) $this->_getData('qty');
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
    public function getRowTotalInclTax()
    {
        return (float) $this->_getData('row_total_incl_tax');
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
    public function getWeeeTaxAppliedAmount()
    {
        return (float) $this->_getData('weee_tax_applied_amount');
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
    public function getWeeeTaxDisposition()
    {
        return (float) $this->_getData('weee_tax_disposition');
    }

    /**
     * @return float
     */
    public function getWeeeTaxRowDisposition()
    {
        return (float) $this->_getData('weee_tax_row_disposition');
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
    public function setBaseDiscountAmount($value)
    {
        return $this->setData('base_discount_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseHiddenTaxAmount($value)
    {
        return $this->setData('base_hidden_tax_amount', (float) $value);
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
    public function setBasePriceInclTax($value)
    {
        return $this->setData('base_price_incl_tax', (float) $value);
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
    public function setBaseRowTotalInclTax($value)
    {
        return $this->setData('base_row_total_incl_tax', (float) $value);
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
    public function setBaseWeeeTaxAppliedAmount($value)
    {
        return $this->setData('base_weee_tax_applied_amount', (float) $value);
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
    public function setBaseWeeeTaxDisposition($value)
    {
        return $this->setData('base_weee_tax_disposition', (float) $value);
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
    public function setDiscountAmount($value)
    {
        return $this->setData('discount_amount', (float) $value);
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
    public function setPrice($value)
    {
        return $this->setData('price', (float) $value);
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
    public function setRowTotal($value)
    {
        return $this->setData('row_total', (float) $value);
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
    public function setTaxAmount($value)
    {
        return $this->setData('tax_amount', (float) $value);
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
    public function setWeeeTaxAppliedRowAmount($value)
    {
        return $this->setData('weee_tax_applied_row_amount', (float) $value);
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
    public function setWeeeTaxRowDisposition($value)
    {
        return $this->setData('weee_tax_row_disposition', (float) $value);
    }
}
