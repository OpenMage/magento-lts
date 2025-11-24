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
 * @method Mage_Sales_Model_Resource_Quote_Address_Item _getResource()
 * @method string getAdditionalData()
 * @method null|string getAppliedRuleIds()
 * @method float getBaseCost()
 * @method float getBaseDiscountAmount()
 * @method float getBaseDiscountTaxCompensation()
 * @method float getBaseHiddenTaxAmount()
 * @method float getBasePrice()
 * @method float getBasePriceInclTax()
 * @method float getBaseRowTotal()
 * @method float getBaseRowTotalInclTax()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item_Collection getCollection()
 * @method string getCreatedAt()
 * @method int getCustomerAddressId()
 * @method string getDescription()
 * @method float getDiscountAmount()
 * @method float getDiscountPercent()
 * @method int getFreeShipping()
 * @method int getGiftMessageId()
 * @method float getHiddenTaxAmount()
 * @method string getImage()
 * @method int getIsQtyDecimal()
 * @method string getName()
 * @method int getNoDiscount()
 * @method int getParentItemId()
 * @method int getParentProductId()
 * @method float getPriceInclTax()
 * @method int getProductId()
 * @method int getQuoteAddressId()
 * @method Mage_Sales_Model_Quote_Item getQuoteItem()
 * @method int getQuoteItemId()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item_Collection getResourceCollection()
 * @method float getRowTotal()
 * @method float getRowTotalInclTax()
 * @method float getRowTotalWithDiscount()
 * @method float getRowWeight()
 * @method string getSku()
 * @method int getSuperProductId()
 * @method float getTaxPercent()
 * @method string getUpdatedAt()
 * @method float getWeight()
 * @method bool hasQty()
 * @method $this setAdditionalData(string $value)
 * @method $this setAppliedRuleIds(string $value)
 * @method $this setBaseCost(float $value)
 * @method $this setBaseDiscountAmount(float $value)
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method $this setBasePrice(float $value)
 * @method $this setBasePriceInclTax(float $value)
 * @method $this setBaseRowTotal(float $value)
 * @method $this setBaseRowTotalInclTax(float $value)
 * @method $this setBaseTaxAmount(float $value)
 * @method $this setCost(float $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setCustomerAddressId(int $value)
 * @method $this setDescription(string $value)
 * @method $this setDiscountAmount(float $value)
 * @method $this setDiscountPercent(float $value)
 * @method $this setFreeShipping(int $value)
 * @method $this setGiftMessageId(int $value)
 * @method $this setHiddenTaxAmount(float $value)
 * @method $this setImage(string $value)
 * @method $this setIsQtyDecimal(int $value)
 * @method $this setName(string $value)
 * @method $this setNoDiscount(int $value)
 * @method $this setParentItemId(int $value)
 * @method $this setParentProductId(int $value)
 * @method $this setPriceInclTax(float $value)
 * @method $this setProductId(int $value)
 * @method $this setProductType(string $value)
 * @method $this setQty(float $value)
 * @method $this setQuoteAddressId(int $value)
 * @method $this setQuoteItem(Mage_Sales_Model_Quote_Item $value)
 * @method $this setQuoteItemId(int $value)
 * @method $this setQuoteItemImported(bool $value)
 * @method $this setRowTotal(float $value)
 * @method $this setRowTotalInclTax(float $value)
 * @method $this setRowTotalWithDiscount(float $value)
 * @method $this setRowWeight(float $value)
 * @method $this setShippingAmount(float $value)
 * @method $this setSku(string $value)
 * @method $this setSuperProductId(int $value)
 * @method $this setTaxAmount(float $value)
 * @method $this setTaxPercent(float $value)
 * @method $this setUpdatedAt(string $value)
 * @method $this setWeight(float $value)
 */
class Mage_Sales_Model_Quote_Address_Item extends Mage_Sales_Model_Quote_Item_Abstract
{
    /**
     * Quote address model object
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;

    protected $_quote;

    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
    }

    /**
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getAddress()) {
            $this->setQuoteAddressId($this->getAddress()->getId());
        }

        return $this;
    }

    /**
     * Declare address model
     *
     * @return  $this
     */
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        $this->_quote   = $address->getQuote();
        return $this;
    }

    /**
     * Retrieve address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Import item to quote
     *
     * @return $this
     */
    public function importQuoteItem(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        $this->_quote = $quoteItem->getQuote();
        $this->setQuoteItem($quoteItem)
            ->setQuoteItemId($quoteItem->getId())
            ->setProductId($quoteItem->getProductId())
            ->setProduct($quoteItem->getProduct())
            ->setSku($quoteItem->getSku())
            ->setName($quoteItem->getName())
            ->setDescription($quoteItem->getDescription())
            ->setWeight($quoteItem->getWeight())
            ->setPrice($quoteItem->getPrice())
            ->setIsQtyDecimal($quoteItem->getIsQtyDecimal())
            ->setCost($quoteItem->getCost());

        if (!$this->hasQty()) {
            $this->setQty($quoteItem->getQty());
        }

        $this->setQuoteItemImported(true);
        return $this;
    }

    /**
     * @param string $code
     * @return null|Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
     */
    public function getOptionBycode($code)
    {
        if ($this->getQuoteItem()) {
            return $this->getQuoteItem()->getOptionByCode($code);
        }

        return null;
    }
}
