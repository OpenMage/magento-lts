<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Calculate items and address amounts including/excluding tax
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Sales_Total_Quote_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Tax calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_calculator = null;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config = null;

    /**
     * Tax helper instance
     *
     * @var Mage_Tax_Helper_Data|null
     */
    protected $_helper = null;

    /**
     * Default value for subtotal that include taxes
     *
     * @var float
     */
    protected $_subtotalInclTax = 0;

    /**
     * Default value for base subtotal that include taxes
     *
     * @var float
     */
    protected $_baseSubtotalInclTax = 0;

    /**
     * Default value for subtotal
     *
     * @var float
     */
    protected $_subtotal = 0;

    /**
     * Default value for base subtotal
     *
     * @var float
     */
    protected $_baseSubtotal = 0;

    /**
     * Flag which is initialized when collect method is started and catalog prices include tax.
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
    protected $_storeTaxRequest = null;

    /**
     *  Quote store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Rounding deltas for prices
     *
     * @var array
     */
    protected $_roundingDeltas = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setCode('tax_subtotal');
        $this->_helper = Mage::helper('tax');
        $this->_calculator = Mage::getSingleton('tax/calculation');
        $this->_config = Mage::getSingleton('tax/config');
    }

    /**
     * Calculate item price including/excluding tax, row total including/excluding tax
     * and subtotal including/excluding tax.
     * Determine discount price if needed
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_store = $address->getQuote()->getStore();
        $this->_address = $address;

        $this->_subtotalInclTax = 0;
        $this->_baseSubtotalInclTax = 0;
        $this->_subtotal = 0;
        $this->_baseSubtotal = 0;
        $this->_roundingDeltas = [];

        $address->setSubtotalInclTax(0);
        $address->setBaseSubtotalInclTax(0);
        $address->setTotalAmount('subtotal', 0);
        $address->setBaseTotalAmount('subtotal', 0);

        $items = $this->_getAddressItems($address);
        if (!$items) {
            return $this;
        }

        $addressRequest = $this->_getAddressTaxRequest($address);
        $storeRequest = $this->_getStoreTaxRequest($address);
        $this->_calculator->setCustomer($address->getQuote()->getCustomer());
        if ($this->_config->priceIncludesTax($this->_store)) {
            $classIds = [];
            foreach ($items as $item) {
                $classIds[] = $item->getProduct()->getTaxClassId();
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $classIds[] = $child->getProduct()->getTaxClassId();
                    }
                }
            }
            $classIds = array_unique($classIds);
            $storeRequest->setProductClassId($classIds);
            $addressRequest->setProductClassId($classIds);
            if ($this->_helper->isCrossBorderTradeEnabled($this->_store)) {
                $this->_areTaxRequestsSimilar = true;
            } else {
                $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests($storeRequest, $addressRequest);
            }
        }

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_processItem($child, $addressRequest);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_processItem($item, $addressRequest);
            }
            $this->_addSubtotalAmount($address, $item);
        }
        $address->setRoundingDeltas($this->_roundingDeltas);
        return $this;
    }

    /**
     * Calculate item price and row total with configured rounding level
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $taxRequest
     *
     * @return $this
     */
    protected function _processItem($item, $taxRequest)
    {
        switch ($this->_config->getAlgorithm($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_UNIT_BASE:
                $this->_unitBaseCalculation($item, $taxRequest);
                break;
            case Mage_Tax_Model_Calculation::CALC_ROW_BASE:
                $this->_rowBaseCalculation($item, $taxRequest);
                break;
            case Mage_Tax_Model_Calculation::CALC_TOTAL_BASE:
                $this->_totalBaseCalculation($item, $taxRequest);
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * Calculate item price and row total including/excluding tax based on unit price rounding level
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $request
     *
     * @return $this
     */
    protected function _unitBaseCalculation($item, $request)
    {
        $request->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $this->_calculator->getRate($request);
        $qty = $item->getTotalQty();

        $price = $taxPrice = $this->_calculator->round($item->getCalculationPriceOriginal());
        $basePrice = $baseTaxPrice = $this->_calculator->round($item->getBaseCalculationPriceOriginal());
        $subtotal = $taxSubtotal = $this->_calculator->round($item->getRowTotal());
        $baseSubtotal = $baseTaxSubtotal = $this->_calculator->round($item->getBaseRowTotal());

        // if we have a custom price, determine if tax should be based on the original price
        $taxOnOrigPrice = !$this->_helper->applyTaxOnCustomPrice($this->_store) && $item->hasCustomPrice();
        if ($taxOnOrigPrice) {
            $origPrice = $item->getOriginalPrice();
            $baseOrigPrice = $item->getBaseOriginalPrice();
        }

        $item->setTaxPercent($rate);
        if ($this->_config->priceIncludesTax($this->_store)) {
            if ($this->_sameRateAsStore($request)) {
                // determine which price to use when we calculate the tax
                if ($taxOnOrigPrice) {
                    $taxable        = $origPrice;
                    $baseTaxable    = $baseOrigPrice;
                } else {
                    $taxable        = $price;
                    $baseTaxable    = $basePrice;
                }
                $tax             = $this->_calculator->calcTaxAmount($taxable, $rate, true);
                $baseTax         = $this->_calculator->calcTaxAmount($baseTaxable, $rate, true);
                $taxPrice        = $price;
                $baseTaxPrice    = $basePrice;
                $taxSubtotal     = $subtotal;
                $baseTaxSubtotal = $baseSubtotal;
                $price = $price - $tax;
                $basePrice = $basePrice - $baseTax;
                $subtotal = $price * $qty;
                $baseSubtotal = $basePrice * $qty;
                $isPriceInclTax  = true;

                $item->setRowTax($tax * $qty);
                $item->setBaseRowTax($baseTax * $qty);
            } else {
                $storeRate       = $this->_calculator->getStoreRate($request, $this->_store);
                if ($taxOnOrigPrice) {
                    // the merchant already provided a customer's price that includes tax
                    $taxPrice     = $price;
                    $baseTaxPrice = $basePrice;
                    // determine which price to use when we calculate the tax
                    $taxable      = $this->_calculatePriceInclTax($origPrice, $storeRate, $rate);
                    $baseTaxable  = $this->_calculatePriceInclTax($baseOrigPrice, $storeRate, $rate);
                } else {
                    // determine the customer's price that includes tax
                    $taxPrice     = $this->_calculatePriceInclTax($price, $storeRate, $rate);
                    $baseTaxPrice = $this->_calculatePriceInclTax($basePrice, $storeRate, $rate);
                    // determine which price to use when we calculate the tax
                    $taxable      = $taxPrice;
                    $baseTaxable  = $baseTaxPrice;
                }
                // determine the customer's tax amount
                $tax             = $this->_calculator->calcTaxAmount($taxable, $rate, true, true);
                $baseTax         = $this->_calculator->calcTaxAmount($baseTaxable, $rate, true, true);
                // determine the customer's price without taxes
                $price = $taxPrice - $tax;
                $basePrice = $baseTaxPrice - $baseTax;
                // determine subtotal amounts
                $taxSubtotal = $taxPrice * $qty;
                $baseTaxSubtotal = $baseTaxPrice * $qty;
                $subtotal = $price * $qty;
                $baseSubtotal = $basePrice * $qty;
                $isPriceInclTax  = true;

                $item->setRowTax($tax * $qty);
                $item->setBaseRowTax($baseTax * $qty);
            }
        } else {
            // determine which price to use when we calculate the tax
            if ($taxOnOrigPrice) {
                $taxable = $origPrice;
                $baseTaxable = $baseOrigPrice;
            } else {
                $taxable = $price;
                $baseTaxable = $basePrice;
            }
            $appliedRates = $this->_calculator->getAppliedRates($request);
            $taxes = [];
            $baseTaxes = [];
            foreach ($appliedRates as $appliedRate) {
                $taxRate = $appliedRate['percent'];
                $taxes[] = $this->_calculator->calcTaxAmount($taxable, $taxRate, false);
                $baseTaxes[] = $this->_calculator->calcTaxAmount($baseTaxable, $taxRate, false);
            }
            $tax             = array_sum($taxes);
            $baseTax         = array_sum($baseTaxes);
            $taxPrice        = $price + $tax;
            $baseTaxPrice    = $basePrice + $baseTax;
            $taxSubtotal     = $taxPrice * $qty;
            $baseTaxSubtotal = $baseTaxPrice * $qty;
            $isPriceInclTax  = false;
        }

        if ($item->hasCustomPrice()) {
            /**
             * Initialize item original price before declaring custom price
             */
            $item->getOriginalPrice();
            $item->setCustomPrice($price);
            $item->setBaseCustomPrice($basePrice);
        }
        $item->setPrice($basePrice);
        $item->setBasePrice($basePrice);
        $item->setRowTotal($subtotal);
        $item->setBaseRowTotal($baseSubtotal);
        $item->setPriceInclTax($taxPrice);
        $item->setBasePriceInclTax($baseTaxPrice);
        $item->setRowTotalInclTax($taxSubtotal);
        $item->setBaseRowTotalInclTax($baseTaxSubtotal);
        $item->setTaxableAmount($taxable);
        $item->setBaseTaxableAmount($baseTaxable);
        $item->setIsPriceInclTax($isPriceInclTax);
        if ($this->_config->discountTax($this->_store)) {
            $item->setDiscountCalculationPrice($taxPrice);
            $item->setBaseDiscountCalculationPrice($baseTaxPrice);
        }
        return $this;
    }

    /**
     * Calculate item price and row total including/excluding tax based on row total price rounding level
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $request
     *
     * @return $this
     */
    protected function _rowBaseCalculation($item, $request)
    {
        $request->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $this->_calculator->getRate($request);
        $qty = $item->getTotalQty();

        $price = $taxPrice = $this->_calculator->round($item->getCalculationPriceOriginal());
        $basePrice = $baseTaxPrice = $this->_calculator->round($item->getBaseCalculationPriceOriginal());
        $subtotal = $taxSubtotal = $this->_calculator->round($item->getRowTotal());
        $baseSubtotal = $baseTaxSubtotal = $this->_calculator->round($item->getBaseRowTotal());

        // if we have a custom price, determine if tax should be based on the original price
        $taxOnOrigPrice = !$this->_helper->applyTaxOnCustomPrice($this->_store) && $item->hasCustomPrice();
        if ($taxOnOrigPrice) {
            $origSubtotal = $item->getOriginalPrice() * $qty;
            $baseOrigSubtotal = $item->getBaseOriginalPrice() * $qty;
        }

        $item->setTaxPercent($rate);
        if ($this->_config->priceIncludesTax($this->_store)) {
            if ($this->_sameRateAsStore($request)) {
                // determine which price to use when we calculate the tax
                if ($taxOnOrigPrice) {
                    $taxable        = $origSubtotal;
                    $baseTaxable    = $baseOrigSubtotal;
                } else {
                    $taxable        = $taxSubtotal;
                    $baseTaxable    = $baseTaxSubtotal;
                }
                $rowTax          = $this->_calculator->calcTaxAmount($taxable, $rate, true, true);
                $baseRowTax      = $this->_calculator->calcTaxAmount($baseTaxable, $rate, true, true);
                $taxPrice        = $price;
                $baseTaxPrice    = $basePrice;
                $taxSubtotal     = $subtotal;
                $baseTaxSubtotal = $baseSubtotal;
                $subtotal = $this->_calculator->round($subtotal - $rowTax);
                $baseSubtotal = $this->_calculator->round($baseSubtotal - $baseRowTax);
                $price = $this->_calculator->round($subtotal / $qty);
                $basePrice = $this->_calculator->round($baseSubtotal / $qty);
                $isPriceInclTax  = true;

                $item->setRowTax($rowTax);
                $item->setBaseRowTax($baseRowTax);
            } else {
                $storeRate       = $this->_calculator->getStoreRate($request, $this->_store);
                if ($taxOnOrigPrice) {
                    // the merchant already provided a customer's price that includes tax
                    $taxPrice     = $price;
                    $baseTaxPrice = $basePrice;
                    // determine which price to use when we calculate the tax
                    $taxable      = $this->_calculatePriceInclTax($item->getOriginalPrice(), $storeRate, $rate);
                    $baseTaxable  = $this->_calculatePriceInclTax($item->getBaseOriginalPrice(), $storeRate, $rate);
                } else {
                    // determine the customer's price that includes tax
                    $taxPrice     = $this->_calculatePriceInclTax($price, $storeRate, $rate);
                    $baseTaxPrice = $this->_calculatePriceInclTax($basePrice, $storeRate, $rate);
                    // determine which price to use when we calculate the tax
                    $taxable      = $taxPrice;
                    $baseTaxable  = $baseTaxPrice;
                }
                // determine the customer's tax amount
                $tax             = $this->_calculator->calcTaxAmount($taxable, $rate, true, true);
                $baseTax         = $this->_calculator->calcTaxAmount($baseTaxable, $rate, true, true);
                // determine the customer's price without taxes
                $price = $taxPrice - $tax;
                $basePrice = $baseTaxPrice - $baseTax;
                // determine subtotal amounts
                $taxable        *= $qty;
                $baseTaxable    *= $qty;
                $taxSubtotal     = $taxPrice * $qty;
                $baseTaxSubtotal = $baseTaxPrice * $qty;
                $rowTax          = $this->_calculator->calcTaxAmount($taxable, $rate, true, true);
                $baseRowTax      = $this->_calculator->calcTaxAmount($baseTaxable, $rate, true, true);
                $subtotal        = $taxSubtotal - $rowTax;
                $baseSubtotal    = $baseTaxSubtotal - $baseRowTax;
                $isPriceInclTax  = true;

                $item->setRowTax($rowTax);
                $item->setBaseRowTax($baseRowTax);
            }
        } else {
            // determine which price to use when we calculate the tax
            if ($taxOnOrigPrice) {
                $taxable = $origSubtotal;
                $baseTaxable = $baseOrigSubtotal;
            } else {
                $taxable = $subtotal;
                $baseTaxable = $baseSubtotal;
            }

            $appliedRates = $this->_calculator->getAppliedRates($request);
            $rowTaxes = [];
            $baseRowTaxes = [];
            foreach ($appliedRates as $appliedRate) {
                $taxRate = $appliedRate['percent'];
                $rowTaxes[] = $this->_calculator->calcTaxAmount($taxable, $taxRate, false, true);
                $baseRowTaxes[] = $this->_calculator->calcTaxAmount($baseTaxable, $taxRate, false, true);
            }
            $rowTax          = array_sum($rowTaxes);
            $baseRowTax      = array_sum($baseRowTaxes);
            $taxSubtotal     = $subtotal + $rowTax;
            $baseTaxSubtotal = $baseSubtotal + $baseRowTax;
            $taxPrice        = $this->_calculator->round($taxSubtotal / $qty);
            $baseTaxPrice    = $this->_calculator->round($baseTaxSubtotal / $qty);
            $isPriceInclTax  = false;
        }

        if ($item->hasCustomPrice()) {
            /**
             * Initialize item original price before declaring custom price
             */
            $item->getOriginalPrice();
            $item->setCustomPrice($price);
            $item->setBaseCustomPrice($basePrice);
        }
        $item->setPrice($basePrice);
        $item->setBasePrice($basePrice);
        $item->setRowTotal($subtotal);
        $item->setBaseRowTotal($baseSubtotal);
        $item->setPriceInclTax($taxPrice);
        $item->setBasePriceInclTax($baseTaxPrice);
        $item->setRowTotalInclTax($taxSubtotal);
        $item->setBaseRowTotalInclTax($baseTaxSubtotal);
        $item->setTaxableAmount($taxable);
        $item->setBaseTaxableAmount($baseTaxable);
        $item->setIsPriceInclTax($isPriceInclTax);
        if ($this->_config->discountTax($this->_store)) {
            $item->setDiscountCalculationPrice($taxSubtotal / $qty);
            $item->setBaseDiscountCalculationPrice($baseTaxSubtotal / $qty);
        } elseif ($isPriceInclTax) {
            $item->setDiscountCalculationPrice($subtotal / $qty);
            $item->setBaseDiscountCalculationPrice($baseSubtotal / $qty);
        }

        return $this;
    }

    /**
     * Calculate item price and row total including/excluding tax based on total price rounding level
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $request
     *
     * @return $this
     */
    protected function _totalBaseCalculation($item, $request)
    {
        $calc = $this->_calculator;
        $request->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $calc->getRate($request);
        $qty = $item->getTotalQty();

        $price = $taxPrice = $this->_calculator->round($item->getCalculationPriceOriginal());
        $basePrice = $baseTaxPrice = $this->_calculator->round($item->getBaseCalculationPriceOriginal());
        $subtotal = $taxSubtotal = $this->_calculator->round($item->getRowTotal());
        $baseSubtotal = $baseTaxSubtotal = $this->_calculator->round($item->getBaseRowTotal());

        // if we have a custom price, determine if tax should be based on the original price
        $taxOnOrigPrice = !$this->_helper->applyTaxOnCustomPrice($this->_store) && $item->hasCustomPrice();
        if ($taxOnOrigPrice) {
            $origSubtotal = $item->getOriginalPrice() * $qty;
            $baseOrigSubtotal = $item->getBaseOriginalPrice() * $qty;
        }

        $item->setTaxPercent($rate);
        if ($this->_config->priceIncludesTax($this->_store)) {
            if ($this->_sameRateAsStore($request)) {
                // determine which price to use when we calculate the tax
                if ($taxOnOrigPrice) {
                    $taxable = $origSubtotal;
                    $baseTaxable = $baseOrigSubtotal;
                } else {
                    $taxable = $subtotal;
                    $baseTaxable = $baseSubtotal;
                }
                $rowTaxExact     = $calc->calcTaxAmount($taxable, $rate, true, false);
                $rowTax          = $this->_deltaRound($rowTaxExact, $rate, true);
                $baseRowTaxExact = $calc->calcTaxAmount($baseTaxable, $rate, true, false);
                $baseRowTax      = $this->_deltaRound($baseRowTaxExact, $rate, true, 'base');

                $taxPrice        = $price;
                $baseTaxPrice    = $basePrice;
                $taxSubtotal = $subtotal;
                $baseTaxSubtotal = $baseSubtotal;

                $subtotal          = $subtotal - $rowTax;
                $baseSubtotal      = $baseSubtotal - $baseRowTax;

                $price = $calc->round($subtotal / $qty);
                $basePrice = $calc->round($baseSubtotal / $qty);

                $isPriceInclTax  = true;

                //Save the tax calculated
                $item->setRowTax($rowTax);
                $item->setBaseRowTax($baseRowTax);
            } else {
                $storeRate = $calc->getStoreRate($request, $this->_store);
                if ($taxOnOrigPrice) {
                    // the merchant already provided a customer's price that includes tax
                    $taxPrice     = $price;
                    $baseTaxPrice = $basePrice;
                    // determine which price to use when we calculate the tax
                    $taxable      = $this->_calculatePriceInclTax($item->getOriginalPrice(), $storeRate, $rate);
                    $baseTaxable  = $this->_calculatePriceInclTax($item->getBaseOriginalPrice(), $storeRate, $rate);
                } else {
                    // determine the customer's price that includes tax
                    $taxPrice     = $this->_calculatePriceInclTax($price, $storeRate, $rate);
                    $baseTaxPrice = $this->_calculatePriceInclTax($basePrice, $storeRate, $rate);
                    // determine which price to use when we calculate the tax
                    $taxable      = $taxPrice;
                    $baseTaxable  = $baseTaxPrice;
                }
                // determine the customer's tax amount based on the taxable price
                $tax             = $this->_calculator->calcTaxAmount($taxable, $rate, true, true);
                $baseTax         = $this->_calculator->calcTaxAmount($baseTaxable, $rate, true, true);
                // determine the customer's price without taxes
                $price = $taxPrice - $tax;
                $basePrice = $baseTaxPrice - $baseTax;
                // determine subtotal amounts
                $taxable        *= $qty;
                $baseTaxable    *= $qty;
                $taxSubtotal     = $taxPrice * $qty;
                $baseTaxSubtotal = $baseTaxPrice * $qty;
                $rowTax =
                    $this->_deltaRound($calc->calcTaxAmount($taxable, $rate, true, false), $rate, true);
                $baseRowTax =
                    $this->_deltaRound($calc->calcTaxAmount($baseTaxable, $rate, true, false), $rate, true, 'base');
                $subtotal = $taxSubtotal - $rowTax;
                $baseSubtotal = $baseTaxSubtotal - $baseRowTax;
                $isPriceInclTax  = true;

                $item->setRowTax($rowTax);
                $item->setBaseRowTax($baseRowTax);
            }
        } else {
            // determine which price to use when we calculate the tax
            if ($taxOnOrigPrice) {
                $taxable = $origSubtotal;
                $baseTaxable = $baseOrigSubtotal;
            } else {
                $taxable = $subtotal;
                $baseTaxable = $baseSubtotal;
            }
            $appliedRates = $this->_calculator->getAppliedRates($request);
            $rowTaxes = [];
            $baseRowTaxes = [];
            foreach ($appliedRates as $appliedRate) {
                $taxId = $appliedRate['id'];
                $taxRate = $appliedRate['percent'];
                $rowTaxes[] = $this->_deltaRound($calc->calcTaxAmount($taxable, $taxRate, false, false), $taxId, false);
                $baseRowTaxes[] = $this->_deltaRound(
                    $calc->calcTaxAmount($baseTaxable, $taxRate, false, false),
                    $taxId,
                    false,
                    'base'
                );
            }

            $taxSubtotal     = $subtotal + array_sum($rowTaxes);
            $baseTaxSubtotal = $baseSubtotal + array_sum($baseRowTaxes);

            $taxPrice        = $calc->round($taxSubtotal / $qty);
            $baseTaxPrice    = $calc->round($baseTaxSubtotal / $qty);

            $isPriceInclTax = false;
        }

        if ($item->hasCustomPrice()) {
            /**
             * Initialize item original price before declaring custom price
             */
            $item->getOriginalPrice();
            $item->setCustomPrice($price);
            $item->setBaseCustomPrice($basePrice);
        } else {
            $item->setConvertedPrice($price);
        }
        $item->setPrice($basePrice);
        $item->setBasePrice($basePrice);
        $item->setRowTotal($subtotal);
        $item->setBaseRowTotal($baseSubtotal);
        $item->setPriceInclTax($taxPrice);
        $item->setBasePriceInclTax($baseTaxPrice);
        $item->setRowTotalInclTax($taxSubtotal);
        $item->setBaseRowTotalInclTax($baseTaxSubtotal);
        $item->setTaxableAmount($taxable);
        $item->setBaseTaxableAmount($baseTaxable);
        $item->setIsPriceInclTax($isPriceInclTax);
        if ($this->_config->discountTax($this->_store)) {
            $item->setDiscountCalculationPrice($taxSubtotal / $qty);
            $item->setBaseDiscountCalculationPrice($baseTaxSubtotal / $qty);
        } elseif ($isPriceInclTax) {
            $item->setDiscountCalculationPrice($subtotal / $qty);
            $item->setBaseDiscountCalculationPrice($baseSubtotal / $qty);
        }
        return $this;
    }

    /**
     * Given a store price that includes tax at the store rate, this function will back out the store's tax, and add in
     * the customer's tax.  Returns this new price which is the customer's price including tax.
     *
     * @param float $storePriceInclTax
     * @param float $storeRate
     * @param float $customerRate
     *
     * @return float
     */
    protected function _calculatePriceInclTax($storePriceInclTax, $storeRate, $customerRate)
    {
        $storeTax = $this->_calculator->calcTaxAmount($storePriceInclTax, $storeRate, true, false);
        $priceExclTax = $storePriceInclTax - $storeTax;
        $customerTax = $this->_calculator->calcTaxAmount($priceExclTax, $customerRate, false, false);
        return $this->_calculator->round($priceExclTax + $customerTax);
    }

    /**
     * Checks whether request for an item has same rate as store one
     * Used only after collect() started, as far as uses optimized $_areTaxRequestsSimilar property
     * Used only in case of prices including tax
     *
     * @param Varien_Object $request
     *
     * @return bool
     */
    protected function _sameRateAsStore($request)
    {
        // Maybe we know that all requests for currently collected items have same rates
        if ($this->_areTaxRequestsSimilar) {
            return true;
        }

        // Check current request individually
        $rate = $this->_calculator->getRate($request);
        $storeRate = $this->_calculator->getStoreRate($request, $this->_store);
        return $rate == $storeRate;
    }

    /**
     * Round price based on previous rounding operation delta
     *
     * @param float $price
     * @param string $rate
     * @param bool $direction
     * @param string $type
     *
     * @return float
     */
    protected function _deltaRound($price, $rate, $direction, $type = 'regular')
    {
        if ($price) {
            $rate = (string)$rate;
            $type = $type . $direction;
            // initialize the delta to a small number to avoid non-deterministic behavior with rounding of 0.5
            $delta = $this->_roundingDeltas[$type][$rate] ?? 0.000001;
            $price += $delta;
            $this->_roundingDeltas[$type][$rate] = $price - $this->_calculator->round($price);
            $price = $this->_calculator->round($price);
        }
        return $price;
    }

    /**
     * Recalculate row information for item based on children calculation
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     *
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $rowTotal = 0;
        $baseRowTotal = 0;
        $rowTotalInclTax = 0;
        $baseRowTotalInclTax = 0;
        $rowTax = 0;
        $baseRowTax = 0;
        $store = $item->getStore();
        $qty = $item->getQty();

        foreach ($item->getChildren() as $child) {
            $rowTotal += $child->getRowTotal();
            $baseRowTotal += $child->getBaseRowTotal();
            $rowTotalInclTax += $child->getRowTotalInclTax();
            $baseRowTotalInclTax += $child->getBaseRowTotalInclTax();
            $rowTax += $child->getRowTax();
            $baseRowTax += $child->getBaseRowTax();
        }

        $item->setConvertedPrice($store->roundPrice($rowTotal) / $qty);
        $item->setPrice($store->roundPrice($baseRowTotal) / $qty);
        $item->setRowTotal($rowTotal);
        $item->setBaseRowTotal($baseRowTotal);
        $item->setPriceInclTax($store->roundPrice($rowTotalInclTax) / $qty);
        $item->setBasePriceInclTax($store->roundPrice($baseRowTotalInclTax) / $qty);
        $item->setRowTotalInclTax($rowTotalInclTax);
        $item->setBaseRowTotalInclTax($baseRowTotalInclTax);
        $item->setRowTax($rowTax);
        $item->setBaseRowTax($baseRowTax);
        return $this;
    }

    /**
     * Get request for fetching store tax rate
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     *
     * @return  Varien_Object
     */
    protected function _getStoreTaxRequest($address)
    {
        if (is_null($this->_storeTaxRequest)) {
            $this->_storeTaxRequest = $this->_calculator->getRateOriginRequest($address->getQuote()->getStore());
        }
        return $this->_storeTaxRequest;
    }

    /**
     * Get request for fetching address tax rate
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     *
     * @return  Varien_Object
     */
    protected function _getAddressTaxRequest($address)
    {
        return $this->_calculator->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $address->getQuote()->getCustomerTaxClassId(),
            $address->getQuote()->getStore()
        );
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
        if ($this->_config->priceIncludesTax($this->_store)) {
            $subTotal = $item->getRowTotalInclTax() - $item->getRowTax();
            $baseSubTotal = $item->getBaseRowTotalInclTax() - $item->getBaseRowTax();
            $address->setTotalAmount('subtotal', $address->getTotalAmount('subtotal') + $subTotal);
            $address->setBaseTotalAmount('subtotal', $address->getBaseTotalAmount('subtotal') + $baseSubTotal);
        } else {
            $address->setTotalAmount(
                'subtotal',
                $address->getTotalAmount('subtotal') + $item->getRowTotal()
            );
            $address->setBaseTotalAmount(
                'subtotal',
                $address->getBaseTotalAmount('subtotal') + $item->getBaseRowTotal()
            );
        }
        $address->setSubtotalInclTax($address->getSubtotalInclTax() + $item->getRowTotalInclTax());
        $address->setBaseSubtotalInclTax($address->getBaseSubtotalInclTax() + $item->getBaseRowTotalInclTax());
        return $this;
    }

    /**
     * Unset item prices/totals with price include tax.
     * Operation is necessary for reset item state in case if configuration was changed
     *
     * @deprecated after 1.4.1
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _resetItemPriceInclTax(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        return $this;
    }

    /**
     *
     * @deprecated after 1.4.0.1
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _processShippingAmount($address)
    {
        return $this;
    }

    /**
     * Recollect item price and row total using after taxes subtract.
     * Declare item price including tax attributes
     *
     * @deprecated after 1.4.1
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     *
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _recollectItem($address, Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $store = $address->getQuote()->getStore();
        $request = $this->_getStoreTaxRequest($address);
        $request->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $this->_calculator->getRate($request);
        $qty = $item->getTotalQty();

        $price = $taxPrice = $item->getCalculationPriceOriginal();
        $basePrice = $baseTaxPrice = $item->getBaseCalculationPriceOriginal();
        $subtotal = $taxSubtotal = $item->getRowTotal();
        $baseSubtotal = $baseTaxSubtotal = $item->getBaseRowTotal();

        if ($this->_config->discountTax($store)) {
            $item->setDiscountCalculationPrice($price);
            $item->setBaseDiscountCalculationPrice($basePrice);
        }

        /**
         * Use original price for tax calculation
         */
        if ($item->hasCustomPrice() && !$this->_helper->applyTaxOnCustomPrice($store)) {
            $taxPrice = $item->getOriginalPrice();
            $baseTaxPrice = $item->getBaseOriginalPrice();
            $taxSubtotal = $taxPrice * $qty;
            $baseTaxSubtotal = $baseTaxPrice * $qty;
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

        $this->_subtotalInclTax += $subtotal;
        $this->_baseSubtotalInclTax += $baseSubtotal;

        if ($this->_config->getAlgorithm($store) == Mage_Tax_Model_Calculation::CALC_UNIT_BASE) {
            $taxAmount = $this->_calculator->calcTaxAmount($taxPrice, $rate, true);
            $baseTaxAmount = $this->_calculator->calcTaxAmount($baseTaxPrice, $rate, true);
            $unitPrice = $this->_calculator->round($price - $taxAmount);
            $baseUnitPrice = $this->_calculator->round($basePrice - $baseTaxAmount);
            $subtotal = $this->_calculator->round($unitPrice * $qty);
            $baseSubtotal = $this->_calculator->round($baseUnitPrice * $qty);
        } else {
            $taxAmount = $this->_calculator->calcTaxAmount($taxSubtotal, $rate, true, false);
            $baseTaxAmount = $this->_calculator->calcTaxAmount($baseTaxSubtotal, $rate, true, false);
            $unitPrice = ($subtotal - $taxAmount) / $qty;
            $baseUnitPrice = ($baseSubtotal - $baseTaxAmount) / $qty;
            $subtotal = $this->_calculator->round(($subtotal - $taxAmount));
            $baseSubtotal = $this->_calculator->round(($baseSubtotal - $baseTaxAmount));
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
     * Check if we need subtract store tax amount from item prices
     *
     * @deprecated after 1.4.1
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
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
     * Subtract shipping tax
     *
     * @deprecated after 1.4.0.1
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
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
