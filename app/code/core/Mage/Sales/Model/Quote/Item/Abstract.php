<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote item abstract model
 *
 * Price attributes:
 *  - price - initial item price, declared during product association
 *  - original_price - product price before any calculations
 *  - calculation_price - prices for item totals calculation
 *  - custom_price - new price that can be declared by user and recalculated during calculation process
 *  - original_custom_price - original defined value of custom price without any conversion
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Quote_Address getAddress()
 * @method float getBaseDiscountAmount()
 * @method float getBaseDiscountCalculationPrice()
 * @method float getBaseOriginalDiscountAmount()
 * @method float getBaseRowTax()
 * @method float getBaseRowTotal()
 * @method float getBaseRowTotalInclTax()
 * @method float getBaseRowTotalWithDiscount()
 * @method float getBaseShippingAmount()
 * @method float getBaseTaxableAmount()
 * @method float getBaseTaxBeforeDiscount()
 * @method float getBaseWeeeDiscount()
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method float getBaseWeeeTaxAppliedRowAmount()
 * @method int getBaseWeeeTaxDisposition()
 * @method int getBaseWeeeTaxRowDisposition()
 * @method float getCustomPrice()
 * @method float getDiscountAmount()
 * @method float getDiscountCalculationPrice()
 * @method float getDiscountPercent()
 * @method float getDiscountTaxCompensation()
 * @method int getFreeShipping()
 * @method bool getHasChildren()
 * @method bool getHasConfigurationUnavailableError()
 * @method bool getIsPriceInclTax()
 * @method string getName()
 * @method bool getNoDiscount()
 * @method float getNominalRowTotal()
 * @method array getNominalTotalDetails()
 * @method float getOriginalCustomPrice()
 * @method float getOriginalDiscountAmount()
 * @method int getParentItemId()
 * @method int getProductId()
 * @method array getProductOrderOptions()
 * @method string getProductType()
 * @method float getRowTax()
 * @method float getRowTotal()
 * @method float getRowTotalInclTax()
 * @method float getRowTotalWithDiscount()
 * @method float getRowWeight()
 * @method int getStoreId()
 * @method float getTaxableAmount()
 * @method float getTaxBeforeDiscount()
 * @method float getTaxPercent()
 * @method float getWeeeDiscount()
 * @method string getWeeeTaxApplied()
 * @method float getWeeeTaxAppliedAmount()
 * @method float getWeeeTaxAppliedRowAmount()
 * @method int getWeeeTaxDisposition()
 * @method int getWeeeTaxRowDisposition()
 * @method bool hasBaseCalculationPrice()
 * @method bool hasCustomPrice()
 * @method bool hasOriginalCustomPrice()
 * @method $this setAddress(Mage_Sales_Model_Quote_Address $value)
 * @method $this setAppliedRuleIds(string $value)
 * @method $this setBaseCalculationPrice(null|float $value)
 * @method $this setBaseCustomPrice(float $value)
 * @method $this setBaseDiscountAmount(float $value)
 * @method $this setBaseDiscountCalculationPrice(float $value)
 * @method $this setBaseExtraRowTaxableAmount(float $value)
 * @method $this setBaseExtraTaxableAmount(float $value)
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method float setBaseOriginalDiscountAmount(float $value)
 * @method $this setBaseOriginalPrice(float $value)
 * @method $this setBasePrice(float $value)
 * @method $this setBasePriceInclTax(float $value)
 * @method $this setBaseRowTax(float $value)
 * @method $this setBaseRowTotal(float $value)
 * @method $this setBaseRowTotalInclTax(float $value)
 * @method $this setBaseRowTotalWithDiscount(float $value)
 * @method $this setBaseShippingAmount(float $value)
 * @method $this setBaseTaxableAmount(float $value)
 * @method $this setBaseTaxAmount(float $value)
 * @method $this setBaseTaxBeforeDiscount(float $value)
 * @method $this setBaseTaxCalcPrice(float $value)
 * @method $this setBaseTaxCalcRowTotal(float $value)
 * @method $this setBaseWeeeTaxAppliedAmount(float $value)
 * @method $this setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method $this setBaseWeeeTaxDisposition(int $value)
 * @method $this setBaseWeeeTaxRowDisposition(int $value)
 * @method $this setCalculationPrice(null|float $value)
 * @method $this setDiscountAmount(float $value)
 * @method $this setDiscountCalculationPrice(float $value)
 * @method $this setDiscountPercent(float $value)
 * @method $this setDiscountTaxCompensation(float $value)
 * @method $this setExtraRowTaxableAmount(float $value)
 * @method $this setExtraTaxableAmount(float $value)
 * @method $this setFreeShipping(int $value)
 * @method $this setHasChildren(bool $value)
 * @method $this setHasError(bool $value)
 * @method $this setHiddenTaxAmount(float $value)
 * @method $this setIsPriceInclTax(bool $value)
 * @method $this setOriginalDiscountAmount(float $value)
 * @method $this setParentItemId(int $value)
 * @method $this setPriceInclTax(float $value)
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 * @method $this setQty(float $value)
 * @method $this setRowTax(int $rowTax)
 * @method $this setRowTotal(float $value)
 * @method $this setRowTotalExcTax(float $value)
 * @method $this setRowTotalInclTax(float $value)
 * @method $this setRowTotalWithDiscount(float $value)
 * @method $this setTaxableAmount(float $value)
 * @method $this setTaxAmount(float $value)
 * @method $this setTaxBeforeDiscount(float $value)
 * @method $this setTaxCalcPrice(float $value)
 * @method $this setTaxCalcRowTotal(float $value)
 * @method $this setTaxPercent(float $value)
 * @method $this setTaxRates(array $value)
 * @method $this setWeeeTaxApplied(string $value)
 * @method $this setWeeeTaxAppliedAmount(float $value)
 * @method $this setWeeeTaxAppliedRowAmount(float $value)
 * @method $this setWeeeTaxDisposition(int $value)
 * @method $this setWeeeTaxRowDisposition(int $value)
 * @method $this unsBasePriceInclTax()
 * @method $this unsBaseRowTotalInclTax()
 * @method $this unsHasConfigurationUnavailableError()
 * @method $this unsMessage()
 * @method $this unsPriceInclTax()
 * @method $this unsRowTotalInclTax()
 */
abstract class Mage_Sales_Model_Quote_Item_Abstract extends Mage_Core_Model_Abstract implements Mage_Catalog_Model_Product_Configuration_Item_Interface
{
    /**
     * Parent item for sub items for bundle product, configurable product, etc.
     *
     * @var null|Mage_Sales_Model_Quote_Item_Abstract
     */
    protected $_parentItem  = null;

    /**
     * Children items in bundle product, configurable product, etc.
     *
     * @var array
     */
    protected $_children    = [];

    /**
     * @var array
     */
    protected $_messages    = [];

    /**
     * @var array
     */
    protected $_optionsByCode;

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
     * @return null|Mage_Sales_Model_Quote_Item_Abstract
     */
    public function getParentItem()
    {
        return $this->_parentItem;
    }

    /**
     * Get child items
     *
     * @return Mage_Sales_Model_Quote_Item_Abstract[]
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
            $messages = [$messages];
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
        $this->_messages = [];
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
        } catch (Exception) {
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
     * @return  float|int
     */
    public function getQty()
    {
        return $this->_getData('qty');
    }

    /**
     * Get total item quantity (include parent item relation)
     *
     * @return  float|int
     */
    public function getTotalQty()
    {
        if ($this->getParentItem()) {
            return $this->getQty() * $this->getParentItem()->getQty();
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
                    $price /= $rate;
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
                    $price /= $rate;
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
            $this->setData('is_nominal', $this->getProduct() ? $this->getProduct()->getIsRecurring() == '1' : false);
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
        return (int) $this->isNominal();
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
     * Specify custom item price (used in case when we have applied not product price to item)
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
     * @param null|float $value
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
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_parentItem  = null;
        $this->_children    = [];
        $this->_messages    = [];
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

        if (($calculate !== null) && (int) $calculate === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
            return true;
        }

        return false;
    }

    /**
     * Checking can we ship product separately (each child separately)
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

        if (($shipmentType !== null)
            && (int) $shipmentType === Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
        ) {
            return true;
        }

        return false;
    }

    /**
     * Calculate item tax amount
     *
     * @return  $this
     * @deprecated logic moved to tax totals calculation model
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

            $taxPercent = $this->getTaxPercent() / 100;
            $this->setTaxAmount($store->roundPrice($rowTotal * $taxPercent));
            $this->setBaseTaxAmount($store->roundPrice($rowBaseTotal * $taxPercent));
            $rowTotal       = $this->getRowTotal();
            $rowBaseTotal   = $this->getBaseRowTotal();
            $this->setTaxBeforeDiscount($store->roundPrice($rowTotal * $taxPercent));
            $this->setBaseTaxBeforeDiscount($store->roundPrice($rowBaseTotal * $taxPercent));
        } elseif (Mage::helper('tax')->applyTaxAfterDiscount($store)) {
            $totalBaseTax = $this->getBaseTaxAmount();
            $totalTax = $this->getTaxAmount();
            if ($totalTax && $totalBaseTax) {
                $totalTax -= $this->getDiscountAmount() * ($this->getTaxPercent() / 100);
                $totalBaseTax -= $this->getBaseDiscountAmount() * ($this->getTaxPercent() / 100);

                $this->setBaseTaxAmount($store->roundPrice($totalBaseTax));
                $this->setTaxAmount($store->roundPrice($totalTax));
            }
        }

        if (Mage::helper('tax')->discountTax($store) && !Mage::helper('tax')->applyTaxAfterDiscount($store)) {
            if ($this->getDiscountPercent()) {
                $baseTaxAmount =  $this->getBaseTaxBeforeDiscount();
                $taxAmount = $this->getTaxBeforeDiscount();

                $baseDiscountDisposition = $baseTaxAmount / 100 * $this->getDiscountPercent();
                $discountDisposition = $taxAmount / 100 * $this->getDiscountPercent();

                $this->setDiscountAmount($this->getDiscountAmount() + $discountDisposition);
                $this->setBaseDiscountAmount($this->getBaseDiscountAmount() + $baseDiscountDisposition);
            }
        }

        return $this;
    }

    /**
     * Get item tax amount
     *
     * @return  float
     * @deprecated
     */
    public function getTaxAmount()
    {
        return $this->_getData('tax_amount');
    }

    /**
     * Get item base tax amount
     *
     * @return float
     * @deprecated
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
                $store,
            );

            $priceIncludingTax = Mage::helper('tax')->getPrice(
                $this->getProduct()->setTaxPercent(null),
                $value,
                true,
                $sAddress,
                $bAddress,
                $this->getQuote()->getCustomerTaxClassId(),
                $store,
            );

            if ($saveTaxes) {
                $qty = $this->getQty();
                if ($this->getParentItem()) {
                    $qty *= $this->getParentItem()->getQty();
                }

                if (Mage::helper('tax')->displayCartPriceInclTax($store)) {
                    $rowTotal = $value * $qty;
                    $rowTotalExcTax = Mage::helper('tax')->getPrice(
                        $this->getProduct()->setTaxPercent(null),
                        $rowTotal,
                        false,
                        $sAddress,
                        $bAddress,
                        $this->getQuote()->getCustomerTaxClassId(),
                        $store,
                    );
                    $rowTotalIncTax = Mage::helper('tax')->getPrice(
                        $this->getProduct()->setTaxPercent(null),
                        $rowTotal,
                        true,
                        $sAddress,
                        $bAddress,
                        $this->getQuote()->getCustomerTaxClassId(),
                        $store,
                    );
                    $totalBaseTax = $rowTotalIncTax - $rowTotalExcTax;
                    $this->setRowTotalExcTax($rowTotalExcTax);
                } else {
                    $taxAmount = $priceIncludingTax - $priceExcludingTax;
                    $this->setTaxPercent($this->getProduct()->getTaxPercent());
                    $totalBaseTax = $taxAmount * $qty;
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
}
