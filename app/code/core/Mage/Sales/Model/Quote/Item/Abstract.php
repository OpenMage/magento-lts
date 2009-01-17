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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote item abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Quote_Item_Abstract extends Mage_Core_Model_Abstract
{
    protected $_parentItem  = null;
    protected $_children    = array();
    protected $_messages    = array();

    abstract function getQuote();

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getParentItem()) {
            $this->setParentItemId($this->getParentItem()->getId());
        }
        return $this;
    }


    /**
     * Set parent item
     *
     * @param  Mage_Sales_Model_Quote_Item $parentItem
     * @return Mage_Sales_Model_Quote_Item
     */
    public function setParentItem($parentItem)
    {
        if ($parentItem) {
            $this->_parentItem = $parentItem;
            $parentItem->addChild($this);
        }
        return $this;
    }

    /**
     * Get parent item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getParentItem()
    {
        return $this->_parentItem;
    }

    /**
     * Get chil items
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Add child item
     *
     * @param  Mage_Sales_Model_Quote_Item_Abstract $child
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function addChild($child)
    {
        $this->setHasChildren(true);
        $this->_children[] = $child;
        return $this;
    }

    /**
     * Set masseges for quote item
     *
     * @param mixed $messages
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function setMessage($messages) {
        if (!is_array($messages)) {
            $messages = array($messages);
        }
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
        return $this;
    }

    /**
     * Add message of quote item to array of messages
     *
     * @param string $message
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function addMessage($message)
    {
        $this->_messages[] = $message;
        return $this;
    }

    /**
     * Get messages array of quote item
     *
     * @return array
     */
    public function getMessage($string = true)
    {
        if ($string) {
            return join("\n", $this->_messages);
        }
        return $this->_messages;
    }

    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Checking item data
     *
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function checkData()
    {
        $this->setHasError(false);
        $this->unsMessage();

        $qty = $this->getData('qty');
        try {
            $this->setQty($qty);
        }
        catch (Mage_Core_Exception $e){
            $this->setHasError(true);
            $this->setMessage($e->getMessage());
        }
        catch (Exception $e){
            $this->setHasError(true);
            $this->setMessage(Mage::helper('sales')->__('Item qty declare error'));
        }

        try {
            $this->getProduct()->getTypeInstance()->checkProductBuyState();
        } catch (Mage_Core_Exception $e) {
            $this->setHasError(true);
            $this->setMessage($e->getMessage());
            $this->getQuote()->setHasError(true);
            $this->getQuote()->addMessage(Mage::helper('sales')->__('Some of the products below don\'t have all the required options. Please remove them and add again with all the required options.'));
        } catch (Exception $e) {
            $this->setHasError(true);
            $this->setMessage(Mage::helper('sales')->__('Item options declare error'));
            $this->getQuote()->setHasError(true);
            $this->getQuote()->addMessage(Mage::helper('sales')->__('Items options declare error.'));
        }

        return $this;
    }

    /**
     * Calculate item row total price
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcRowTotal()
    {
        $qty = $this->getQty();

        if ($this->getParentItem()) {
            $qty = $qty*$this->getParentItem()->getQty();
        }

        $total      = $this->getCalculationPrice()*$qty;
        $baseTotal  = $this->getBaseCalculationPrice()*$qty;

        $this->setRowTotal($this->getStore()->roundPrice($total));
        $this->setBaseRowTotal($this->getStore()->roundPrice($baseTotal));

        return $this;
    }

    /**
     * Calculate item tax amount
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function calcTaxAmount()
    {
        $store = $this->getStore();

        if (!Mage::helper('tax')->priceIncludesTax($store)) {
            if (Mage::helper('tax')->applyTaxAfterDiscount($store)) {
                $rowTotal       = $this->getRowTotalWithDiscount();
                $rowBaseTotal   = $this->getBaseRowTotalWithDiscount();
            } else {
                $rowTotal       = $this->getRowTotal();
                $rowBaseTotal   = $this->getBaseRowTotal();
            }

            $taxPercent = $this->getTaxPercent()/100;

            $this->setTaxAmount($store->roundPrice($rowTotal * $taxPercent));
            $this->setBaseTaxAmount($store->roundPrice($rowBaseTotal * $taxPercent));


            $rowTotal       = $this->getRowTotal();
            $rowBaseTotal   = $this->getBaseRowTotal();
            $this->setTaxBeforeDiscount($store->roundPrice($rowTotal * $taxPercent));
            $this->setBaseTaxBeforeDiscount($store->roundPrice($rowBaseTotal * $taxPercent));
        } else {
            if (Mage::helper('tax')->applyTaxAfterDiscount($store)) {
                $totalBaseTax = $this->getBaseTaxAmount();
                $totalTax = $this->getTaxAmount();

                if ($totalTax && $totalBaseTax) {
                    $totalTax -= $this->getDiscountAmount()*($this->getTaxPercent()/100);
                    $totalBaseTax -= $this->getBaseDiscountAmount()*($this->getTaxPercent()/100);

                    $this->setBaseTaxAmount($totalBaseTax);
                    $this->setTaxAmount($totalTax);
                }
            }
        }

        if (Mage::helper('tax')->discountTax($store) && !Mage::helper('tax')->applyTaxAfterDiscount($store)) {
            if ($this->getDiscountPercent()) {
                $baseTaxAmount =  $this->getBaseTaxBeforeDiscount();
                $taxAmount = $this->getTaxBeforeDiscount();

                $baseDiscountDisposition = $baseTaxAmount/100*$this->getDiscountPercent();
                $discountDisposition = $taxAmount/100*$this->getDiscountPercent();

                $this->setDiscountAmount($this->getDiscountAmount()+$discountDisposition);
                $this->setBaseDiscountAmount($this->getBaseDiscountAmount()+$baseDiscountDisposition);
            }
        }

        return $this;
    }

    /**
     * Retrieve item price used for calculation
     *
     * @return unknown
     */
    public function getCalculationPrice()
    {
        $price = $this->getData('calculation_price');
        if (is_null($price)) {
            if ($this->hasCustomPrice()) {
                $price = $this->getCustomPrice();
            }
            else {
                $price = $this->getOriginalPrice();
            }
            $this->setData('calculation_price', $price);
        }
        return $price;
    }

    /**
     * Retrieve calculation price in base currency
     *
     * @return unknown
     */
    public function getBaseCalculationPrice()
    {
        if (!$this->hasBaseCalculationPrice()) {
            if ($price = (float) $this->getCustomPrice()) {
                $rate = $this->getStore()->convertPrice($price) / $price;
                $price = $price / $rate;
            }
            else {
                $price = $this->getPrice();
            }
            $this->setBaseCalculationPrice($price);
        }
        return $this->getData('base_calculation_price');
    }

    /**
     * Retrieve original price (retrieved from product) for item
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        $price = $this->getData('original_price');
        if (is_null($price)) {
            $price = $this->getStore()->convertPrice($this->getPrice());
            $this->setData('original_price', $price);
        }
        return $price;
    }

    /**
     * Get item tax amount
     *
     * @return decimal
     */
    public function getTaxAmount()
    {
        if ($this->getHasChildren() && $this->getProduct()->getPriceType() === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
            $amount = 0;
            foreach ($this->getChildren() as $child) {
                $amount+= $child->getTaxAmount();
            }
            return $amount;
        }
        else {
            return $this->_getData('tax_amount');
        }
    }

    /**
     * Get item price (item price always exclude price)
     *
     * @return decimal
     */
    public function getPrice()
    {
        if ($this->getHasChildren() && $this->getProduct()->getPriceType() === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
            $price = $this->_getData('price');
            /*
            foreach ($this->getChildren() as $child) {
                $price+= $child->getPrice()*$child->getQty();
            }
            */
            return $price;
        }
        else {
            return $this->_getData('price');
        }
    }

    public function setCustomPrice($value)
    {
        if (is_null($value)) {
            return $this->setData('custom_price', $value);
        }

        $excludingTax = $this->_calculatePrice($value, Mage::helper('tax')->applyTaxOnCustomPrice());
        $this->setData('original_custom_price', $value);
        return $this->setData('custom_price', $excludingTax);
    }

    public function setPrice($value)
    {
        $saveTaxes = true;
        if (Mage::helper('tax')->applyTaxOnCustomPrice() && $this->hasCustomPrice()) {
            $saveTaxes = false;
        }
        return $this->setData('price', $this->_calculatePrice($value, $saveTaxes));
    }

    protected function _calculatePrice($value, $saveTaxes = true)
    {
        $store = $this->getQuote()->getStore();

        if (Mage::helper('tax')->priceIncludesTax($store)) {
            $bAddress = $this->getQuote()->getBillingAddress();
            $sAddress = $this->getQuote()->getShippingAddress();

            $address = $this->getAddress();

            if ($address) {
                switch ($address->getAddressType()) {
                    case Mage_Sales_Model_Quote_Address::TYPE_BILLING:
                        $bAddress = $address;
                        break;
                    case Mage_Sales_Model_Quote_Address::TYPE_SHIPPING:
                        $sAddress = $address;
                        break;
                }
            }

            if ($this->getProduct()->getIsVirtual()) {
                $sAddress = $bAddress;
            }

            $priceExcludingTax = Mage::helper('tax')->getPrice($this->getProduct()->setTaxPercent(null), $value, false, $sAddress, $bAddress, $this->getQuote()->getCustomerTaxClassId(), $store);
            $priceIncludingTax = Mage::helper('tax')->getPrice($this->getProduct()->setTaxPercent(null), $value, true, $sAddress, $bAddress, $this->getQuote()->getCustomerTaxClassId(), $store);

            if ($saveTaxes) {
                $taxAmount = $priceIncludingTax - $priceExcludingTax;
                $this->setTaxPercent($this->getProduct()->getTaxPercent());

                $qty = $this->getQty();
                if ($this->getParentItem()) {
                    $qty = $qty*$this->getParentItem()->getQty();
                }
                $totalBaseTax = $taxAmount*$qty;
                $totalTax = $this->getStore()->convertPrice($totalBaseTax);
                $this->setTaxBeforeDiscount($totalTax);
                $this->setBaseTaxBeforeDiscount($totalBaseTax);

                $this->setTaxAmount($totalTax);
                $this->setBaseTaxAmount($totalBaseTax);
            }

            $value = $priceExcludingTax;
        }

        return $value;
    }

    /**
     * Clone quote item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_parentItem  = null;
        $this->_children    = array();
        return $this;
    }

    /**
     * Checking if there children calculated or parent item
     * when we have parent quote item and its children
     *
     * @return bool
     */
    public function isChildrenCalculated() {
        if ($this->getParentItem()) {
            $calculate = $this->getParentItem()->getProduct()->getPriceType();
        } else {
            $calculate = $this->getProduct()->getPriceType();
        }

        if ($calculate === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
            return true;
        }
        return false;
    }


    /**
     * Checking can we ship product separatelly (each child separately)
     * or each parent product item can be shipped only like one item
     *
     * @return bool
     */
    public function isShipSeparately() {
        if ($this->getParentItem()) {
            $shipmentType = $this->getParentItem()->getProduct()->getShipmentType();
        } else {
            $shipmentType = $this->getProduct()->getShipmentType();
        }

        if ($shipmentType === Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
            return true;
        }
        return false;
    }
}