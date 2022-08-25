<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @method Mage_Sales_Model_Resource_Quote_Address_Item _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item_Collection getCollection()
 *
 * @method string getAdditionalData()
 * @method $this setAdditionalData(string $value)
 * @method string getAppliedRuleIds()
 * @method $this setAppliedRuleIds(string $value)
 *
 * @method float getBaseCost()
 * @method $this setBaseCost(float $value)
 * @method float getBaseDiscountAmount()
 * @method $this setBaseDiscountAmount(float $value)
 * @method float getBaseDiscountTaxCompensation()
 * @method float getBaseHiddenTaxAmount()
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method float getBasePrice()
 * @method $this setBasePrice(float $value)
 *
 * @method $this setCustomerAddressId(int $value)
 * @method int getParentItemId()
 * @method $this setParentItemId(int $value)
 * @method int getQuoteAddressId()
 * @method $this setQuoteAddressId(int $value)
 * @method int getQuoteItemId()
 * @method $this setQuoteItemId(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method float getWeight()
 * @method $this setWeight(float $value)
 * @method $this setQty(float $value)
 * @method float getDiscountAmount()
 * @method $this setDiscountAmount(float $value)
 * @method $this setTaxAmount(float $value)
 * @method float getRowTotal()
 * @method $this setRowTotal(float $value)
 * @method float getBaseRowTotal()
 * @method $this setBaseRowTotal(float $value)
 * @method float getRowTotalWithDiscount()
 * @method $this setRowTotalWithDiscount(float $value)
 * @method $this setBaseTaxAmount(float $value)
 * @method float getRowWeight()
 * @method $this setRowWeight(float $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getSuperProductId()
 * @method $this setSuperProductId(int $value)
 * @method int getParentProductId()
 * @method $this setParentProductId(int $value)
 * @method string getSku()
 * @method $this setSku(string $value)
 * @method string getImage()
 * @method $this setImage(string $value)
 * @method string getName()
 * @method $this setName(string $value)
 * @method string getDescription()
 * @method $this setDescription(string $value)
 * @method int getFreeShipping()
 * @method $this setFreeShipping(int $value)
 * @method int getIsQtyDecimal()
 * @method $this setIsQtyDecimal(int $value)
 * @method float getDiscountPercent()
 * @method $this setDiscountPercent(float $value)
 * @method int getNoDiscount()
 * @method $this setNoDiscount(int $value)
 * @method float getTaxPercent()
 * @method $this setTaxPercent(float $value)
 * @method float getPriceInclTax()
 * @method $this setPriceInclTax(float $value)
 * @method float getBasePriceInclTax()
 * @method $this setBasePriceInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method $this setRowTotalInclTax(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method $this setBaseRowTotalInclTax(float $value)
 * @method int getGiftMessageId()
 * @method $this setGiftMessageId(int $value)
 * @method float getHiddenTaxAmount()
 * @method $this setHiddenTaxAmount(float $value)
 * @method $this setCost(float $value)
 * @method $this setShippingAmount(float $value)
 * @method Mage_Sales_Model_Quote_Item getQuoteItem()
 * @method $this setQuoteItem(Mage_Sales_Model_Quote_Item $value)
 * @method bool hasQty()
 * @method $this setQuoteItemImported(bool $value)
 * @method $this setProductType(string $value)
 * @method int getCustomerAddressId()
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @param   Mage_Sales_Model_Quote_Address $address
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
     * @param Mage_Sales_Model_Quote_Item $quoteItem
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
     * @return Mage_Catalog_Model_Product_Configuration_Item_Option_Interface|null
     */
    public function getOptionBycode($code)
    {
        if ($this->getQuoteItem()) {
            return $this->getQuoteItem()->getOptionBycode($code);
        }
        return null;
    }
}
