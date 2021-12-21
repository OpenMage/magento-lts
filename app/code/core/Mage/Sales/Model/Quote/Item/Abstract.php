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
 * Quote item abstract model
 *
 * Price attributes:
 *  - price - initial item price, declared during product association
 *  - original_price - product price before any calculations
 *  - calculation_price - prices for item totals calculation
 *  - custom_price - new price that can be declared by user and recalculated during calculation process
 *  - original_custom_price - original defined value of custom price without any convertion
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Sales_Model_Quote_Address getAddress()
 * @method $this setAddress(Mage_Sales_Model_Quote_Address $value)
 *
 * @method $this setAppliedRuleIds(string $value)
 *
 * @method bool hasBaseCalculationPrice()
 * @method $this setBaseCalculationPrice(float $value)
 * @method $this setBaseCustomPrice(float $value)
 * @method $this setBaseDiscountAmount(float $value)
 * @method $this setBaseDiscountCalculationPrice(float $value)
 * @method $this setBaseExtraRowTaxableAmount(float $value)
 * @method $this setBaseExtraTaxableAmount(float $value)
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method $this setBaseOriginalPrice(float $value)
 * @method $this setBasePriceInclTax(float $value)
 * @method $this unsBasePriceInclTax()

 * @method $this setBaseRowTotal(float $value)
 * @method $this setBaseRowTotalInclTax(float $value)
 * @method $this unsBaseRowTotalInclTax()
 * @method $this setBaseRowTotalWithDiscount(float $value)

 * @method $this setBaseTaxableAmount(float $value)
 * @method $this setBaseTaxAmount(float $value)
 * @method $this setBaseTaxBeforeDiscount(float $value)
 * @method $this setBaseTaxCalcPrice(float $value)
 * @method $this setBaseTaxCalcRowTotal(float $value)
 * @method $this setBasePrice(float $value)
 * @method $this setBaseRowTax(float $value)
 * @method $this setBaseShippingAmount(float $value)

 * @method $this setBaseWeeeTaxAppliedAmount(float $value)
 * @method $this setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method int getBaseWeeeTaxDisposition()
 * @method $this setBaseWeeeTaxDisposition(int $value)
 * @method int getBaseWeeeTaxRowDisposition()
 * @method $this setBaseWeeeTaxRowDisposition(int $value)
 *
 * @method $this setCalculationPrice(float $value)
 * @method bool hasCustomPrice()
 *
 * @method $this setDiscountAmount(float $value)
 * @method $this setDiscountCalculationPrice(float $value)
 * @method $this setDiscountPercent(float $value)
 * @method $this setDiscountTaxCompensation(float $value)
 *
 * @method $this setExtraRowTaxableAmount(float $value)
 * @method $this setExtraTaxableAmount(float $value)
 *
 * @method int getFreeShipping()
 * @method $this setFreeShipping(int $value)
 *
 * @method bool getHasChildren()
 * @method $this setHasChildren(bool $value)
 * @method $this setHasError(bool $value)
 * @method bool getHasConfigurationUnavailableError()
 * @method $this unsHasConfigurationUnavailableError()
 * @method $this setHiddenTaxAmount(float $value)
 *
 * @method bool getIsPriceInclTax()
 * @method $this setIsPriceInclTax(bool $value)
 *
 * @method string getName()
 * @method bool getNoDiscount()
 * @method array getNominalTotalDetails()
 *
 * @method $this unsMessage()
 *
 * @method bool hasOriginalCustomPrice()

 * @method $this setOriginalDiscountAmount(float $value)
 *
 * @method int getParentItemId()
 * @method $this setParentItemId(int $value)
 * @method $this setPriceInclTax(float $value)
 * @method $this unsPriceInclTax()
 * @method int getProductId()
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 * @method array getProductOrderOptions()
 * @method string getProductType()
 *
 * @method $this setQty(float $value)
 *
 * @method $this setRowTax(int $rowTax)
 * @method $this setRowTotal(float $value)
 * @method $this setRowTotalExcTax(float $value)
 * @method $this setRowTotalInclTax(float $value)
 * @method $this unsRowTotalInclTax()
 * @method $this setRowTotalWithDiscount(float $value)
 *
 * @method int getStoreId()
 *
 * @method $this setTaxableAmount(float $value)
 * @method $this setTaxCalcPrice(float $value)
 * @method $this setTaxCalcRowTotal(float $value)
 * @method $this setTaxRates(array $value)
 * @method $this setTaxAmount(float $value)
 * @method $this setTaxBeforeDiscount(float $value)
 * @method $this setTaxPercent(float $value)
 *
 * @method string getWeeeTaxApplied()
 * @method $this setWeeeTaxApplied(string $value)
 * @method $this setWeeeTaxAppliedAmount(float $value)
 * @method $this setWeeeTaxAppliedRowAmount(float $value)
 * @method int getWeeeTaxDisposition()
 * @method $this setWeeeTaxDisposition(int $value)
 * @method int getWeeeTaxRowDisposition()
 * @method $this setWeeeTaxRowDisposition(int $value)
 */
abstract class Mage_Sales_Model_Quote_Item_Abstract extends Mage_Core_Model_Abstract implements Mage_Catalog_Model_Product_Configuration_Item_Interface
{
    /**
     * Parent item for sub items for bundle product, configurable product, etc.
     *
     * @var Mage_Sales_Model_Quote_Item_Abstract
     */
    protected $_parentItem  = null;

    /**
     * Children items in bundle product, configurable product, etc.
     *
     * @var array
     */
    protected $_children    = array();

    /**
     *
     * @var array
     */
    protected $_messages    = array();

    /**
     * Retrieve Quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    abstract public function getQuote();

    /**
     * Retrieve product model object associated with item
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if ($product === null && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId($this->getQuote()->getStoreId())
                ->load($this->getProductId());
            $this->setProduct($product);
        }

        /**
         * Reset product final price because it related to custom options
         */
        $product->setFinalPrice(null);
        if (is_array($this->_optionsByCode)) {
            $product->setCustomOptions($this->_optionsByCode);
        }
        return $product;
    }

    /**
     * Returns special download params (if needed) for custom option with type = 'file'
     * Needed to implement Mage_Catalog_Model_Product_Configuration_Item_Interface.
     * Return null, as quote item needs no additional configuration.
     *
     * @return null|Varien_Object
     */
    public function getFileDownloadParams()
    {
        return null;
    }

    /**
     * Specify parent item id before saving data
     *
     * @return  $this
     */
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
     * @return $this
     */
    public function setParentItem($parentItem)
    {
        if ($parentItem) {
            $this->_parentItem = $parentItem;
            // Prevent duplication of children in those are already set
            if (!in_array($this, $parentItem->getChildren(), true)) {
                $parentItem->addChild($this);
            }
        }
        return $this;
    }

    /**
     * Get parent item
     *
     * @return $this
     */
    public function getParentItem()
    {
        return $this->_parentItem;
    }

    /**
     * Get chil items
     *
     * @return $this[]
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Add child item
     *
     * @param  Mage_Sales_Model_Quote_Item_Abstract $child
     * @return $this
     */
    public function addChild($child)
    {
        $this->setHasChildren(true);
        $this->_children[] = $child;
        return $this;
    }

    /**
     * Adds message(s) for quote item. Duplicated messages are not added.
     *
     * @param  array|string $messages
     * @return $this
     */
    public function setMessage($messages)
    {
        $messagesExists = $this->getMessage(false);
        if (!is_array($messages)) {
            $messages = array($messages);
        }
        foreach ($messages as $message) {
            if (!in_array($message, $messagesExists)) {
                $this->addMessage($message);
            }
        }
        return $this;
    }

    /**
     * Add message of quote item to array of messages
     *
     * @param   string $message
     * @return  $this
     */
    public function addMessage($message)
    {
        $this->_messages[] = $message;
        return $this;
    }

    /**
     * Get messages array of quote item
     *
     * @param   bool $string flag for converting messages to string
     * @return  array|string
     */
    public function getMessage($string = true)
    {
        if ($string) {
            return implode("\n", $this->_messages);
        }
        return $this->_messages;
    }

    /**
     * Removes message by text
     *
     * @param string $text
     * @return $this
     */
    public function removeMessageByText($text)
    {
        foreach ($this->_messages as $key => $message) {
            if ($message == $text) {
                unset($this->_messages[$key]);
            }
        }
        return $this;
    }

    /**
     * Clears all messages
     *
     * @return $this
     */
    public function clearMessage()
    {
        $this->unsMessage(); // For older compatibility, when we kept message inside data array
        $this->_messages = array();
        return $this;
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
     * @return $this
     */
    public function checkData()
    {
        $this->setHasError(false);
        $this->clearMessage();

        $qty = $this->_getData('qty');

        try {
            $this->setQty($qty);
        } catch (Mage_Core_Exception $e) {
            $this->setHasError(true);
            $this->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->setHasError(true);
            $this->setMessage(Mage::helper('sales')->__('Item qty declaration error.'));
        }

        try {
            $this->getProduct()->getTypeInstance(true)->checkProductBuyState($this->getProduct());
        } catch (Mage_Core_Exception $e) {
            $this->setHasError(true)
                ->setMessage($e->getMessage());
            $this->getQuote()->setHasError(true)
                ->addMessage(Mage::helper('sales')->__('Some of the products below do not have all the required options.'));
        } catch (Exception $e) {
            $this->setHasError(true)
                ->setMessage(Mage::helper('sales')->__('Item options declaration error.'));
            $this->getQuote()->setHasError(true)
                ->addMessage(Mage::helper('sales')->__('Items options declaration error.'));
        }

        if ($this->getProduct()->getHasError()) {
            $this->setHasError(true)
                ->setMessage(Mage::helper('sales')->__('Some of the selected options are not currently available.'));
            $this->getQuote()->setHasError(true)
                ->addMessage($this->getProduct()->getMessage(), 'options');
        }

        if ($this->getHasConfigurationUnavailableError()) {
            $this->setHasError(true)
                ->setMessage(Mage::helper('sales')->__('Selected option(s) or their combination is not currently available.'));
            $this->getQuote()->setHasError(true)
                ->addMessage(Mage::helper('sales')->__('Some item options or their combination are not currently available.'), 'unavailable-configuration');
            $this->unsHasConfigurationUnavailableError();
        }

        return $this;
    }

    /**
     * Get original (not related with parent item) item quantity
     *
     * @return  int|float
     */
    public function getQty()
    {
        return $this->_getData('qty');
    }

    /**
     * Get total item quantity (include parent item relation)
     *
     * @return  int|float
     */
    public function getTotalQty()
    {
        if ($this->getParentItem()) {
            return $this->getQty()*$this->getParentItem()->getQty();
        }
        return $this->getQty();
    }

    /**
     * Calculate item row total price
     *
     * @return $this
     */
    public function calcRowTotal()
    {
        $qty        = $this->getTotalQty();
        // Round unit price before multiplying to prevent losing 1 cent on subtotal
        $total      = $this->getStore()->roundPrice($this->getCalculationPriceOriginal()) * $qty;
        $baseTotal  = $this->getStore()->roundPrice($this->getBaseCalculationPriceOriginal()) * $qty;

        $this->setRowTotal($this->getStore()->roundPrice($total));
        $this->setBaseRowTotal($this->getStore()->roundPrice($baseTotal));
        return $this;
    }

    /**
     * Get item price used for quote calculation process.
     * This method get custom price (if it is defined) or original product final price
     *
     * @return float
     */
    public function getCalculationPrice()
    {
        $price = $this->_getData('calculation_price');
        if (is_null($price)) {
            if ($this->hasCustomPrice()) {
                $price = $this->getCustomPrice();
            } else {
                $price = $this->getConvertedPrice();
            }
            $this->setData('calculation_price', $price);
        }
        return $price;
    }

    /**
     * Get item price used for quote calculation process.
     * This method get original custom price applied before tax calculation
     *
     * @return float
     */
    public function getCalculationPriceOriginal()
    {
        $price = $this->_getData('calculation_price');
        if (is_null($price)) {
            if ($this->hasOriginalCustomPrice()) {
                $price = $this->getOriginalCustomPrice();
            } else {
                $price = $this->getConvertedPrice();
            }
            $this->setData('calculation_price', $price);
        }
        return $price;
    }

    /**
     * Get calculation price used for quote calculation in base currency.
     *
     * @return float
     */
    public function getBaseCalculationPrice()
    {
        if (!$this->hasBaseCalculationPrice()) {
            if ($this->hasCustomPrice()) {
                $price = (float) $this->getCustomPrice();
                if ($price) {
                    $rate = $this->getStore()->convertPrice($price) / $price;
                    $price = $price / $rate;
                }
            } else {
                $price = $this->getPrice();
            }
            $this->setBaseCalculationPrice($price);
        }
        return $this->_getData('base_calculation_price');
    }

    /**
     * Get original calculation price used for quote calculation in base currency.
     *
     * @return float
     */
    public function getBaseCalculationPriceOriginal()
    {
        if (!$this->hasBaseCalculationPrice()) {
            if ($this->hasOriginalCustomPrice()) {
                $price = (float) $this->getOriginalCustomPrice();
                if ($price) {
                    $rate = $this->getStore()->convertPrice($price) / $price;
                    $price = $price / $rate;
                }
            } else {
                $price = $this->getPrice();
            }
            $this->setBaseCalculationPrice($price);
        }
        return $this->_getData('base_calculation_price');
    }

    /**
     * Get whether the item is nominal
     * TODO: fix for multishipping checkout
     *
     * @return bool
     */
    public function isNominal()
    {
        if (!$this->hasData('is_nominal')) {
            $this->setData('is_nominal', $this->getProduct() ? '1' == $this->getProduct()->getIsRecurring() : false);
        }
        return $this->_getData('is_nominal');
    }

    /**
     * Data getter for 'is_nominal'
     * Used for converting item to order item
     *
     * @return int
     */
    public function getIsNominal()
    {
        return (int)$this->isNominal();
    }

    /**
     * Get original price (retrieved from product) for item.
     * Original price value is in quote selected currency
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        $price = $this->_getData('original_price');
        if (is_null($price)) {
            $price = $this->getStore()->convertPrice($this->getBaseOriginalPrice());
            $this->setData('original_price', $price);
        }
        return $price;
    }

    /**
     * Set original price to item (calculation price will be refreshed too)
     *
     * @param   float $price
     * @return  $this
     */
    public function setOriginalPrice($price)
    {
        return $this->setData('original_price', $price);
    }

    /**
     * Get Original item price (got from product) in base website currency
     *
     * @return float
     */
    public function getBaseOriginalPrice()
    {
        return $this->_getData('base_original_price');
    }

    /**
     * Specify custom item price (used in case whe we have apply not product price to item)
     *
     * @param   float $value
     * @return  $this
     */
    public function setCustomPrice($value)
    {
        $this->setCalculationPrice($value);
        $this->setBaseCalculationPrice(null);
        return $this->setData('custom_price', $value);
    }

    /**
     * Get item price. Item price currency is website base currency.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_getData('price');
    }

    /**
     * Specify item price (base calculation price and converted price will be refreshed too)
     *
     * @param   float $value
     * @return  $this
     */
    public function setPrice($value)
    {
        $this->setBaseCalculationPrice(null);
        $this->setConvertedPrice(null);
        return $this->setData('price', $value);
    }

    /**
     * Get item price converted to quote currency
     * @return float
     */
    public function getConvertedPrice()
    {
        $price = $this->_getData('converted_price');
        if (is_null($price)) {
            $price = $this->getStore()->convertPrice($this->getPrice());
            $this->setData('converted_price', $price);
        }
        return $price;
    }

    /**
     * Set new value for converted price
     * @param float $value
     * @return $this
     */
    public function setConvertedPrice($value)
    {
        $this->setCalculationPrice(null);
        $this->setData('converted_price', $value);
        return $this;
    }

    /**
     * Clone quote item
     *
     * @return $this
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_parentItem  = null;
        $this->_children    = array();
        $this->_messages    = array();
        return $this;
    }

    /**
     * Checking if there children calculated or parent item
     * when we have parent quote item and its children
     *
     * @return bool
     */
    public function isChildrenCalculated()
    {
        if ($this->getParentItem()) {
            $calculate = $this->getParentItem()->getProduct()->getPriceType();
        } else {
            $calculate = $this->getProduct()->getPriceType();
        }

        if ((null !== $calculate) && (int)$calculate === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
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
    public function isShipSeparately()
    {
        if ($this->getParentItem()) {
            $shipmentType = $this->getParentItem()->getProduct()->getShipmentType();
        } else {
            $shipmentType = $this->getProduct()->getShipmentType();
        }

        if ((null !== $shipmentType) &&
            (int)$shipmentType === Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
            return true;
        }
        return false;
    }

    /**
     * Calculate item tax amount
     *
     * @deprecated logic moved to tax totals calculation model
     * @return  $this
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
                    $totalTax -= $this->getDiscountAmount() * ($this->getTaxPercent() / 100);
                    $totalBaseTax -= $this->getBaseDiscountAmount() * ($this->getTaxPercent() / 100);

                    $this->setBaseTaxAmount($store->roundPrice($totalBaseTax));
                    $this->setTaxAmount($store->roundPrice($totalTax));
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
     * Get item tax amount
     *
     * @deprecated
     * @return  float
     */
    public function getTaxAmount()
    {
        return $this->_getData('tax_amount');
    }


    /**
     * Get item base tax amount
     *
     * @deprecated
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return $this->_getData('base_tax_amount');
    }

    /**
     * Get item price (item price always exclude price)
     *
     * @param float $value
     * @param bool $saveTaxes
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     * @deprecated
     */
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

            $priceExcludingTax = Mage::helper('tax')->getPrice(
                $this->getProduct()->setTaxPercent(null),
                $value,
                false,
                $sAddress,
                $bAddress,
                $this->getQuote()->getCustomerTaxClassId(),
                $store
            );

            $priceIncludingTax = Mage::helper('tax')->getPrice(
                $this->getProduct()->setTaxPercent(null),
                $value,
                true,
                $sAddress,
                $bAddress,
                $this->getQuote()->getCustomerTaxClassId(),
                $store
            );

            if ($saveTaxes) {
                $qty = $this->getQty();
                if ($this->getParentItem()) {
                    $qty = $qty*$this->getParentItem()->getQty();
                }

                if (Mage::helper('tax')->displayCartPriceInclTax($store)) {
                    $rowTotal = $value*$qty;
                    $rowTotalExcTax = Mage::helper('tax')->getPrice(
                        $this->getProduct()->setTaxPercent(null),
                        $rowTotal,
                        false,
                        $sAddress,
                        $bAddress,
                        $this->getQuote()->getCustomerTaxClassId(),
                        $store
                    );
                    $rowTotalIncTax = Mage::helper('tax')->getPrice(
                        $this->getProduct()->setTaxPercent(null),
                        $rowTotal,
                        true,
                        $sAddress,
                        $bAddress,
                        $this->getQuote()->getCustomerTaxClassId(),
                        $store
                    );
                    $totalBaseTax = $rowTotalIncTax-$rowTotalExcTax;
                    $this->setRowTotalExcTax($rowTotalExcTax);
                } else {
                    $taxAmount = $priceIncludingTax - $priceExcludingTax;
                    $this->setTaxPercent($this->getProduct()->getTaxPercent());
                    $totalBaseTax = $taxAmount*$qty;
                }

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
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return (float) $this->_getData('base_discount_amount');
    }

    /**
     * @return float
     */
    public function getBaseDiscountCalculationPrice()
    {
        return (float) $this->_getData('base_discount_calculation_price');
    }

    /**
     * @return float
     */
    public function getBaseOriginalDiscountAmount()
    {
        return (float) $this->_getData('base_original_discount_amount');
    }

    /**
     * @return float
     */
    public function getBaseRowTax()
    {
        return (float) $this->_getData('base_row_tax');
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
    public function getBaseRowTotalWithDiscount()
    {
        return (float) $this->_getData('base_row_total_with_discount');
    }

    /**
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return (float) $this->_getData('base_shipping_amount');
    }

    /**
     * @return float
     */
    public function getBaseTaxableAmount()
    {
        return (float) $this->_getData('base_taxable_amount');
    }

    /**
     * @return float
     */
    public function getBaseTaxBeforeDiscount()
    {
        return (float) $this->_getData('base_tax_before_discount');
    }

    /**
     * @return float
     */
    public function getBaseWeeeDiscount()
    {
        return (float) $this->_getData('base_weee_discount');
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
    public function getCustomPrice()
    {
        return (float) $this->_getData('custom_price');
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
    public function getDiscountCalculationPrice()
    {
        return (float) $this->_getData('discount_calculation_price');
    }

    /**
     * @return float
     */
    public function getDiscountPercent()
    {
        return (float) $this->_getData('discount_percent');
    }

    /**
     * @return float
     */
    public function getDiscountTaxCompensation()
    {
        return (float) $this->_getData('discount_tax_compensation');
    }

    /**
     * @return float
     */
    public function getNominalRowTotal()
    {
        return (float) $this->_getData('nominal_row_total');
    }

    /**
     * @return float
     */
    public function getOriginalCustomPrice()
    {
        return (float) $this->_getData('original_custom_price');
    }

    /**
     * @return float
     */
    public function getOriginalDiscountAmount()
    {
        return (float) $this->_getData('original_discount_amount');
    }

    /**
     * @return float
     */
    public function getRowTax()
    {
        return (float) $this->_getData('row_tax');
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
    public function getRowTotalWithDiscount()
    {
        return (float) $this->_getData('row_total_with_discount');
    }

    /**
     * @return float
     */
    public function getRowWeight()
    {
        return (float) $this->_getData('row_weight');
    }

    /**
     * @return float
     */
    public function getTaxableAmount()
    {
        return (float) $this->_getData('taxable_amount');
    }

    /**
     * @return float
     */
    public function getTaxBeforeDiscount()
    {
        return (float) $this->_getData('tax_before_discount');
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return (float) $this->_getData('tax_percent');
    }

    /**
     * @return float
     */
    public function getWeeeDiscount()
    {
        return (float) $this->_getData('weee_discount');
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
     * @return $this
     */
    public function setBaseOriginalDiscountAmount($value)
    {
        return $this->setData('base_original_discount_amount', (float) $value);
    }
}
