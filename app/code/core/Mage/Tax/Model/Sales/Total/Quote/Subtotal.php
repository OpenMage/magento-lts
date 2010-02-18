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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax subtotal totals calculation model.
 * Check configuration settings and calculate unit and shipping prices exclude price, subtotal and shipping exclude tax.
 * Check if price exclude tax should be calculated for discount calculation.
 */
class Mage_Tax_Model_Sales_Total_Quote_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Tax calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_calculator;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config;
    protected $_helper;

    protected $_subtotalInclTax     = 0;
    protected $_baseSubtotalInclTax = 0;

    /**
     * Flag which is initialized when collect method is start.
     * Is used for checking if store tax and customer tax requests are similar
     *
     * @var bool
     */
    protected $_areTaxRequestsSimilar = false;

    /**
     * Request which can be used for tax rate calculation
     *
     * @var Varien_Object
     */
    protected $_storeTaxRequest;
    protected $_addressTaxRequest;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setCode('tax_subtotal');
        $this->_helper      = Mage::helper('tax');
        $this->_calculator  = Mage::getSingleton('tax/calculation');
        $this->_config      = Mage::getSingleton('tax/config');
    }

    /**
     * Prepare subtotals calculations result before apply tax
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests(
            $this->_getStoreTaxRequest($address),
            $this->_getAddressTaxRequest($address)
        );

        $address->setSubtotalInclTax(0);
        $address->setBaseSubtotalInclTax(0);
        $this->_subtotalInclTax     = 0;
        $this->_baseSubtotalInclTax = 0;


        if (!$address->getTaxSubtotalIsProcessed() && $this->_needSubtractTax($address)) {
            $address->setTotalAmount('subtotal', 0);
            $address->setBaseTotalAmount('subtotal', 0);
            $items  = $address->getAllItems();

            foreach ($items as $item) {
                /**
                 * Child item's tax we calculate for parent - that why we skip them
                 */
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $this->_resetItemPriceInclTax($child);
                        $this->_recollectItem($address, $child);
                    }
                    $this->_recalculateParent($item);
                } else {
                    $this->_resetItemPriceInclTax($item);
                    $this->_recollectItem($address, $item);
                }
                $this->_addSubtotalAmount($address, $item);
            }
            if ($this->_areTaxRequestsSimilar) {
                $address->setSubtotalInclTax($this->_subtotalInclTax);
                $address->setBaseSubtotalInclTax($this->_baseSubtotalInclTax);
            }
            $this->_config->setNeedUsePriceExcludeTax(true);
        } elseif (!$address->getTaxSubtotalIsProcessed() && !$this->_needSubtractTax($address)) {
             foreach ($address->getAllItems() as $item) {
                 $this->_resetItemPriceInclTax($item);
             }
        }

        if (!$address->getTaxSubtotalIsProcessed() && $this->_needSubtractShippingTax($address)) {
            $this->_processShippingAmount($address);
            $this->_config->setNeedUseShippingExcludeTax(true);
        }
        $address->setTaxSubtotalIsProcessed(true);
        return $this;
    }

    /**
     * Unset item prices/totals with price include tax.
     * Operation is necessary for reset item state in case if configuration was changed
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _resetItemPriceInclTax(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setPriceInclTax(null);
        $item->setBasePriceInclTax(null);
        $item->setRowTotalInclTax(null);
        $item->setBaseRowTotalInclTax(null);
        return $this;
    }

    /**
     * Get request for fetching store tax rate
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Varien_Object
     */
    protected function _getStoreTaxRequest($address)
    {
        $this->_storeTaxRequest = $this->_calculator->getRateOriginRequest($address->getQuote()->getStore());
        return $this->_storeTaxRequest;
    }

    /**
     * Get request for fetching address tax rate
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Varien_Object
     */
    protected function _getAddressTaxRequest($address)
    {
        $this->_addressTaxRequest = $this->_calculator->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $address->getQuote()->getCustomerTaxClassId(),
            $address->getQuote()->getStore()
        );
        return $this->_addressTaxRequest;
    }

    /**
     * Calculate shipping price without store tax
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _processShippingAmount($address)
    {
        if ($this->_areTaxRequestsSimilar) {
            return $this;
        }
        $store = $address->getQuote()->getStore();
        $shippingTaxClass   = $this->_config->getShippingTaxClass($store);
        $shippingAmount     = $address->getShippingAmount();
        $baseShippingAmount = $address->getBaseShippingAmount();

        if ($shippingTaxClass) {
            $request = $this->_getStoreTaxRequest($address);
            $request->setProductClassId($shippingTaxClass);
            $rate = $this->_calculator->getRate($request);
            if ($rate) {
                $shippingTax    = $this->_calculator->calcTaxAmount($shippingAmount, $rate, true, false);
                $shippingBaseTax= $this->_calculator->calcTaxAmount($baseShippingAmount, $rate, true, false);
                $shippingAmount-= $shippingTax;
                $baseShippingAmount-=$shippingBaseTax;
                $address->setTotalAmount('shipping', $this->_calculator->roundUp($shippingAmount));
                $address->setBaseTotalAmount('shipping', $this->_calculator->roundUp($baseShippingAmount));
            }
        }
        return $this;
    }

    /**
     * Recollect item price and row total using after taxes subtract.
     * Declare item price including tax attributes
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _recollectItem($address, Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $store   = $address->getQuote()->getStore();
        $request = $this->_getStoreTaxRequest($address);
        $request->setProductClassId($item->getProduct()->getTaxClassId());
        $rate   = $this->_calculator->getRate($request);
        $qty    = $item->getTotalQty();

        $price          = $taxPrice         = $item->getCalculationPrice();
        $basePrice      = $baseTaxPrice     = $item->getBaseCalculationPrice();
        $subtotal       = $taxSubtotal      = $item->getRowTotal();
        $baseSubtotal   = $baseTaxSubtotal  = $item->getBaseRowTotal();

        if ($this->_config->discountTax($store)) {
            $item->setDiscountCalculationPrice($price);
            $item->setBaseDiscountCalculationPrice($basePrice);
        }

        /**
         * Use original price for tax calculation
         */
        if ($item->hasCustomPrice() && !$this->_helper->applyTaxOnCustomPrice($store)) {
            $taxPrice         = $item->getOriginalPrice();
            $baseTaxPrice     = $item->getBaseOriginalPrice();
            $taxSubtotal      = $taxPrice*$qty;
            $baseTaxSubtotal  = $baseTaxPrice*$qty;
        }

        if ($this->_areTaxRequestsSimilar) {
            $item->setRowTotalInclTax($subtotal);
            $item->setBaseRowTotalInclTax($baseSubtotal);
            $item->setPriceInclTax($price);
            $item->setBasePriceInclTax($basePrice);

            $item->setTaxCalcPrice($taxPrice);
            $item->setBaseTaxCalcPrice($baseTaxPrice);
            $item->setTaxCalcRowTotal($taxSubtotal);
            $item->setBaseTaxCalcRowTotal($baseTaxSubtotal);
        }

        $this->_subtotalInclTax     += $subtotal;
        $this->_baseSubtotalInclTax += $baseSubtotal;

        if ($this->_config->getAlgorithm($store) == Mage_Tax_Model_Calculation::CALC_UNIT_BASE) {
            $taxAmount      = $this->_calculator->calcTaxAmount($taxPrice, $rate, true);
            $baseTaxAmount  = $this->_calculator->calcTaxAmount($baseTaxPrice, $rate, true);
            $unitPrice      = $this->_calculator->round($price-$taxAmount);
            $baseUnitPrice  = $this->_calculator->round($basePrice-$baseTaxAmount);
            $subtotal       = $this->_calculator->round($unitPrice*$qty);
            $baseSubtotal   = $this->_calculator->round($baseUnitPrice*$qty);
        } else {
            $taxAmount      = $this->_calculator->calcTaxAmount($taxSubtotal, $rate, true, false);
            $baseTaxAmount  = $this->_calculator->calcTaxAmount($baseTaxSubtotal, $rate, true, false);
            $unitPrice      = ($subtotal-$taxAmount)/$qty;
            $baseUnitPrice  = ($baseSubtotal-$baseTaxAmount)/$qty;
            $subtotal       = $this->_calculator->round(($subtotal-$taxAmount));
            $baseSubtotal   = $this->_calculator->round(($baseSubtotal-$baseTaxAmount));
        }

        if ($item->hasCustomPrice()) {
            $item->setCustomPrice($unitPrice);
            $item->setBaseCustomPrice($baseUnitPrice);
        }
        $item->setPrice($baseUnitPrice);
        $item->setOriginalPrice($unitPrice);
        $item->setBasePrice($baseUnitPrice);
        $item->setRowTotal($subtotal);
        $item->setBaseRowTotal($baseSubtotal);
        return $this;
    }

    /**
     * Recalculate row information for item based on children calculation
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $price       = 0;
        $basePrice   = 0;
        $rowTotal    = 0;
        $baseRowTotal= 0;
        $priceInclTax       = 0;
        $basePriceInclTax   = 0;
        $rowTotalInclTax    = 0;
        $baseRowTotalInclTax= 0;
        foreach ($item->getChildren() as $child) {
            $price       += $child->getOriginalPrice();
            $basePrice   += $child->getBaseOriginalPrice();
            $rowTotal    += $child->getRowTotal();
            $baseRowTotal+= $child->getBaseRowTotal();
            $priceInclTax       += $child->getPriceInclTax();
            $basePriceInclTax   += $child->getBasePriceInclTax();
            $rowTotalInclTax    += $child->getRowTotalInclTax();
            $baseRowTotalInclTax+= $child->getBaseRowTotalInclTax();
        }
        $item->setOriginalPrice($price);
        $item->setPrice($basePrice);
        $item->setRowTotal($rowTotal);
        $item->setBaseRowTotal($baseRowTotal);

        if ($this->_areTaxRequestsSimilar) {
            $item->setPriceInclTax($priceInclTax);
            $item->setBasePriceInclTax($basePriceInclTax);
            $item->setRowTotalInclTax($rowTotalInclTax);
            $item->setBaseRowTotalInclTax($baseRowTotalInclTax);
        }
        return $this;
    }

    /**
     * Add row total item amount to subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _addSubtotalAmount(Mage_Sales_Model_Quote_Address $address, $item)
    {
        $address->setTotalAmount('subtotal', $address->getTotalAmount('subtotal')+$item->getRowTotal());
        $address->setBaseTotalAmount('subtotal', $address->getBaseTotalAmount('subtotal')+$item->getBaseRowTotal());
        return $this;
    }

    /**
     * Check if we need subtract store tax amount from item prices
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    protected function _needSubtractTax($address)
    {
        $store = $address->getQuote()->getStore();
        if ($this->_config->priceIncludesTax($store) || $this->_config->getNeedUsePriceExcludeTax()) {
            return true;
        }
        return false;
    }

    /**
     * Check if we need subtract store tax amount from shipping
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    protected function _needSubtractShippingTax($address)
    {
        $store = $address->getQuote()->getStore();
        if ($this->_config->shippingPriceIncludesTax($store) || $this->_config->getNeedUseShippingExcludeTax()) {
            return true;
        }
        return false;
    }
}
