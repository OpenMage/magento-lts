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
 * @method Mage_Sales_Model_Resource_Quote_Address_Item _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item_Collection getCollection()
 *
 * @method string getAdditionalData()
 * @method $this setAdditionalData(string $value)
 * @method string getAppliedRuleIds()
 * @method $this setAppliedRuleIds(string $value)
 *

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
 * @method int getNoDiscount()
 * @method $this setNoDiscount(int $value)
 * @method int getGiftMessageId()
 * @method $this setGiftMessageId(int $value)
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
    public function getBaseDiscountTaxCompensation()
    {
        return (float) $this->_getData('base_discount_tax_compensation');
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
    public function getWeight()
    {
        return (float) $this->_getData('weight');
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
    public function getBaseRowTotal()
    {
        return (float) $this->_getData('base_row_total');
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
    public function getDiscountPercent()
    {
        return (float) $this->_getData('discount_percent');
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
    public function getPriceInclTax()
    {
        return (float) $this->_getData('price_incl_tax');
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
    public function getRowTotalInclTax()
    {
        return (float) $this->_getData('row_total_incl_tax');
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
    public function getHiddenTaxAmount()
    {
        return (float) $this->_getData('hidden_tax_amount');
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
    public function setWeight($value)
    {
        return $this->setData('weight', (float) $value);
    }

    /**
     * @return $this
     */
    public function setQty($value)
    {
        return $this->setData('qty', (float) $value);
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
    public function setTaxAmount($value)
    {
        return $this->setData('tax_amount', (float) $value);
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
    public function setBaseRowTotal($value)
    {
        return $this->setData('base_row_total', (float) $value);
    }

    /**
     * @return $this
     */
    public function setRowTotalWithDiscount($value)
    {
        return $this->setData('row_total_with_discount', (float) $value);
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
    public function setRowWeight($value)
    {
        return $this->setData('row_weight', (float) $value);
    }

    /**
     * @return $this
     */
    public function setDiscountPercent($value)
    {
        return $this->setData('discount_percent', (float) $value);
    }

    /**
     * @return $this
     */
    public function setTaxPercent($value)
    {
        return $this->setData('tax_percent', (float) $value);
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
    public function setBasePriceInclTax($value)
    {
        return $this->setData('base_price_incl_tax', (float) $value);
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
    public function setBaseRowTotalInclTax($value)
    {
        return $this->setData('base_row_total_incl_tax', (float) $value);
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
    public function setCost($value)
    {
        return $this->setData('cost', (float) $value);
    }

    /**
     * @return $this
     */
    public function setShippingAmount($value)
    {
        return $this->setData('shipping_amount', (float) $value);
    }
}
