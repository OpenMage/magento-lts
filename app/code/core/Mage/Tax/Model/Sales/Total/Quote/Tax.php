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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax totals calculation model
 */
class Mage_Tax_Model_Sales_Total_Quote_Tax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Tax module helper
     *
     * @var Mage_Tax_Helper_Data
     */
    protected $_helper;

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

    /**
     * Flag which is initialized when collect method is start.
     * Is used for checking if store tax and customer tax requests are similar
     *
     * @var bool
     */
    protected $_areTaxRequestsSimilar = false;

    /**
     * Array for the rounding deltas
     *
     * @var array
     */
    protected $_roundingDeltas = array();

    /**
     * Array for the base rounding deltas
     *
     * @var array
     */
    protected $_baseRoundingDeltas = array();

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Hidden taxes array
     *
     * @var array
     */
    protected $_hiddenTaxes = array();


    /**
     * Weee helper class
     *
     * @var Mage_Weee_Helper_Data
     */
    protected $_weeeHelper;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setCode('tax');
        $this->_helper = Mage::helper('tax');
        $this->_calculator = Mage::getSingleton('tax/calculation');
        $this->_config = Mage::getSingleton('tax/config');
        $this->_weeeHelper = Mage::helper('weee');
    }

    /**
     * Round the total amounts in address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Tax_Model_Sales_Total_Quote_Tax
     */
    protected function _roundTotals(Mage_Sales_Model_Quote_Address $address)
    {
        // initialize the delta to a small number to avoid non-deterministic behavior with rounding of 0.5
        $totalDelta = 0.000001;
        $baseTotalDelta = 0.000001;
        /*
         * The order of rounding is import here.
         * Tax is rounded first, to be consistent with unit based calculation.
         * Hidden tax and shipping_hidden_tax are rounded next, which are really part of tax.
         * Shipping is rounded before subtotal to minimize the chance that subtotal is
         * rounded differently because of the delta.
         * Here is an example: 19.2% tax rate, subtotal = 49.95, shipping = 9.99, discount = 20%
         * subtotalExclTax = 41.90436, tax = 7.7238, hidden_tax = 1.609128, shippingPriceExclTax = 8.38087
         * shipping_hidden_tax = 0.321826, discount = -11.988
         * The grand total is 47.952 ~= 47.95
         * The rounded values are:
         * tax = 7.72, hidden_tax = 1.61, shipping_hidden_tax = 0.32,
         * shipping = 8.39 (instead of 8.38 from simple rounding), subtotal = 41.9, discount = -11.99
         * The grand total calculated from the rounded value is 47.95
         * If we simply round each value and add them up, the result is 47.94, which is one penny off
         */
        $totalCodes = array('tax', 'hidden_tax', 'shipping_hidden_tax', 'shipping', 'subtotal', 'weee', 'discount');
        foreach ($totalCodes as $totalCode) {
            $exactAmount = $address->getTotalAmount($totalCode);
            $baseExactAmount = $address->getBaseTotalAmount($totalCode);
            if (!$exactAmount && !$baseExactAmount) {
                continue;
            }
            $roundedAmount = $this->_calculator->round($exactAmount + $totalDelta);
            $baseRoundedAmount = $this->_calculator->round($baseExactAmount + $baseTotalDelta);
            $address->setTotalAmount($totalCode, $roundedAmount);
            $address->setBaseTotalAmount($totalCode, $baseRoundedAmount);
            $totalDelta = $exactAmount + $totalDelta - $roundedAmount;
            $baseTotalDelta = $baseExactAmount + $baseTotalDelta - $baseRoundedAmount;
        }
        return $this;
    }

    /**
     * Collect tax totals for quote address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $this->_roundingDeltas = array();
        $this->_baseRoundingDeltas = array();
        $this->_hiddenTaxes = array();
        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);

        $this->_store = $address->getQuote()->getStore();
        $customer = $address->getQuote()->getCustomer();
        if ($customer) {
            $this->_calculator->setCustomer($customer);
        }

        if (!$address->getAppliedTaxesReset()) {
            $address->setAppliedTaxes(array());
        }

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }
        $request = $this->_calculator->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $address->getQuote()->getCustomerTaxClassId(),
            $this->_store
        );

        if ($this->_config->priceIncludesTax($this->_store)) {
            if ($this->_helper->isCrossBorderTradeEnabled($this->_store)) {
                $this->_areTaxRequestsSimilar = true;
            } else {
                $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests(
                    $this->_calculator->getRateOriginRequest($this->_store),
                    $request
                );
            }
        }

        switch ($this->_config->getAlgorithm($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_UNIT_BASE:
                $this->_unitBaseCalculation($address, $request);
                break;
            case Mage_Tax_Model_Calculation::CALC_ROW_BASE:
                $this->_rowBaseCalculation($address, $request);
                break;
            case Mage_Tax_Model_Calculation::CALC_TOTAL_BASE:
                $this->_totalBaseCalculation($address, $request);
                break;
            default:
                break;
        }

        $this->_addAmount($address->getExtraTaxAmount());
        $this->_addBaseAmount($address->getBaseExtraTaxAmount());
        $this->_calculateShippingTax($address, $request);

        $this->_processHiddenTaxes();

        //round total amounts in address
        $this->_roundTotals($address);
        return $this;
    }

    /**
     * Process hidden taxes for items and shippings (in accordance with hidden tax type)
     *
     * @return void
     */
    protected function _processHiddenTaxes()
    {
        $this->_getAddress()->setTotalAmount('hidden_tax', 0);
        $this->_getAddress()->setBaseTotalAmount('hidden_tax', 0);
        $this->_getAddress()->setTotalAmount('shipping_hidden_tax', 0);
        $this->_getAddress()->setBaseTotalAmount('shipping_hidden_tax', 0);
        foreach ($this->_hiddenTaxes as $taxInfoItem) {
            if (isset($taxInfoItem['item'])) {
                // Item hidden taxes
                $item = $taxInfoItem['item'];
                $rateKey = $taxInfoItem['rate_key'];
                $hiddenTax = $taxInfoItem['value'];
                $baseHiddenTax = $taxInfoItem['base_value'];
                $inclTax = $taxInfoItem['incl_tax'];
                $qty = $taxInfoItem['qty'];

                $hiddenTax = $this->_calculator->round($hiddenTax);
                $baseHiddenTax = $this->_calculator->round($baseHiddenTax);
                $item->setHiddenTaxAmount(max(0, $qty * $hiddenTax));
                $item->setBaseHiddenTaxAmount(max(0, $qty * $baseHiddenTax));
                $this->_getAddress()->addTotalAmount('hidden_tax', $item->getHiddenTaxAmount());
                $this->_getAddress()->addBaseTotalAmount('hidden_tax', $item->getBaseHiddenTaxAmount());
            } else {
                // Shipping hidden taxes
                $rateKey = $taxInfoItem['rate_key'];
                $hiddenTax = $taxInfoItem['value'];
                $baseHiddenTax = $taxInfoItem['base_value'];
                $inclTax = $taxInfoItem['incl_tax'];

                $hiddenTax = $this->_calculator->round($hiddenTax);
                $baseHiddenTax = $this->_calculator->round($baseHiddenTax);

                $this->_getAddress()->addTotalAmount('shipping_hidden_tax', $hiddenTax);
                $this->_getAddress()->addBaseTotalAmount('shipping_hidden_tax', $baseHiddenTax);

                $this->_getAddress()->setShippingHiddenTaxAmount(max(0, $hiddenTax));
                $this->_getAddress()->setBaseShippingHiddenTaxAmount(max(0, $baseHiddenTax));
            }
        }
    }

    /**
     * Check if price include tax should be used for calculations.
     * We are using price include tax just in case when catalog prices are including tax
     * and customer tax request is same as store tax request
     *
     * @param $store
     * @return bool
     */
    protected function _usePriceIncludeTax($store)
    {
        if ($this->_config->priceIncludesTax($store) || $this->_config->getNeedUsePriceExcludeTax()) {
            return $this->_areTaxRequestsSimilar;
        }
        return false;
    }

    /**
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param float $rate
     * @param array $appliedRates
     * @param string $taxId
     */
    protected function _calculateShippingTaxByRate(
        Mage_Sales_Model_Quote_Address $address, $rate, $appliedRates, $taxId = null)
    {
        $inclTax = $address->getIsShippingInclTax();
        $shipping = $address->getShippingTaxable();
        $baseShipping = $address->getBaseShippingTaxable();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;

        $hiddenTax = null;
        $baseHiddenTax = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $tax = $this->_calculator->calcTaxAmount($shipping, $rate, $inclTax, false);
                $baseTax = $this->_calculator->calcTaxAmount($baseShipping, $rate, $inclTax, false);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount = $address->getShippingDiscountAmount();
                $baseDiscountAmount = $address->getBaseShippingDiscountAmount();
                $tax = $this->_calculator->calcTaxAmount(
                    $shipping - $discountAmount,
                    $rate,
                    $inclTax,
                    false
                );
                $baseTax = $this->_calculator->calcTaxAmount(
                    $baseShipping - $baseDiscountAmount,
                    $rate,
                    $inclTax,
                    false
                );
                break;
        }

        if ($this->_config->getAlgorithm($this->_store) == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
            $tax = $this->_deltaRound($tax, $rateKey, $inclTax);
            $baseTax = $this->_deltaRound($baseTax, $rateKey, $inclTax, 'base');
            $this->_addAmount(max(0, $tax));
            $this->_addBaseAmount(max(0, $baseTax));
        } else {
            $tax = $this->_calculator->round($tax);
            $baseTax = $this->_calculator->round($baseTax);
            $this->_addAmount(max(0, $tax));
            $this->_addBaseAmount(max(0, $baseTax));
        }

        if ($inclTax && !empty($discountAmount)) {
            $taxBeforeDiscount = $this->_calculator->calcTaxAmount(
                $shipping,
                $rate,
                $inclTax,
                false
            );
            $baseTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                $baseShipping,
                $rate,
                $inclTax,
                false
            );
            if ($this->_config->getAlgorithm($this->_store) == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
                $taxBeforeDiscount =
                    $this->_deltaRound($taxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount');
                $baseTaxBeforeDiscount =
                    $this->_deltaRound($baseTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount_base');
            } else {
                $taxBeforeDiscount = $this->_calculator->round($taxBeforeDiscount);
                $baseTaxBeforeDiscount = $this->_calculator->round($baseTaxBeforeDiscount);
            }
            $hiddenTax = max(0, $taxBeforeDiscount - max(0, $tax));
            $baseHiddenTax = max(0, $baseTaxBeforeDiscount - max(0, $baseTax));
            $this->_hiddenTaxes[] = array(
                'rate_key' => $rateKey,
                'value' => $hiddenTax,
                'base_value' => $baseHiddenTax,
                'incl_tax' => $inclTax,
            );
        }

        $address->setShippingTaxAmount($address->getShippingTaxAmount() + max(0, $tax));
        $address->setBaseShippingTaxAmount($address->getBaseShippingTaxAmount() + max(0, $baseTax));
        $this->_saveAppliedTaxes($address, $appliedRates, $tax, $baseTax, $rate);
    }

    /**
     * Tax caclulation for shipping price
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        $rate = $this->_calculator->getRate($taxRateRequest);
        $inclTax = $address->getIsShippingInclTax();

        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);
        $address->setShippingHiddenTaxAmount(0);
        $address->setBaseShippingHiddenTaxAmount(0);
        $appliedRates = $this->_calculator->getAppliedRates($taxRateRequest);
        if ($inclTax) {
            $this->_calculateShippingTaxByRate($address, $rate, $appliedRates);
        } else {
            foreach ($appliedRates as $appliedRate) {
                $taxRate = $appliedRate['percent'];
                $taxId = $appliedRate['id'];
                $this->_calculateShippingTaxByRate($address, $taxRate, array($appliedRate), $taxId);
            }
        }
        return $this;
    }

    /**
     * Calculate address tax amount based on one unit price and tax amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _unitBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items = $this->_getAddressItems($address);
        $itemTaxGroups = array();
        $store = $address->getQuote()->getStore();
        $catalogPriceInclTax = $this->_config->priceIncludesTax($store);

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_unitBaseProcessItemTax(
                        $address, $child, $taxRateRequest, $itemTaxGroups, $catalogPriceInclTax);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_unitBaseProcessItemTax(
                    $address, $item, $taxRateRequest, $itemTaxGroups, $catalogPriceInclTax);
            }
        }
        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $taxRateRequest
     * @param array $itemTaxGroups
     * @param boolean $catalogPriceInclTax
     */
    protected function _unitBaseProcessItemTax(
        $address, $item, $taxRateRequest, &$itemTaxGroups, $catalogPriceInclTax
    )
    {
        $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $this->_calculator->getRate($taxRateRequest);

        $item->setTaxAmount(0);
        $item->setBaseTaxAmount(0);
        $item->setHiddenTaxAmount(0);
        $item->setBaseHiddenTaxAmount(0);
        $item->setTaxPercent($rate);
        $item->setDiscountTaxCompensation(0);
        $rowTotalInclTax = $item->getRowTotalInclTax();
        $recalculateRowTotalInclTax = false;
        if (!isset($rowTotalInclTax)) {
            $qty = $item->getTotalQty();
            $item->setRowTotalInclTax($this->_store->roundPrice($item->getTaxableAmount() * $qty));
            $item->setBaseRowTotalInclTax(
                $this->_store->roundPrice($item->getBaseTaxableAmount() * $qty));
            $recalculateRowTotalInclTax = true;
        }

        $appliedRates = $this->_calculator->getAppliedRates($taxRateRequest);
        $item->setTaxRates($appliedRates);
        if ($catalogPriceInclTax) {
            $this->_calcUnitTaxAmount($item, $rate);
            $this->_saveAppliedTaxes(
                $address, $appliedRates, $item->getTaxAmount(), $item->getBaseTaxAmount(), $rate);
        } else {
            //need to calculate each tax separately
            $taxGroups = array();
            foreach ($appliedRates as $appliedTax) {
                $taxId = $appliedTax['id'];
                $taxRate = $appliedTax['percent'];
                $this->_calcUnitTaxAmount($item, $taxRate, $taxGroups, $taxId, $recalculateRowTotalInclTax);
                $this->_saveAppliedTaxes(
                    $address, array($appliedTax), $taxGroups[$taxId]['tax'], $taxGroups[$taxId]['base_tax'], $taxRate);
            }
            //We need to calculate weeeAmountInclTax using multiple tax rate here
            //because the _calculateWeeeTax and _calculateRowWeeeTax only take one tax rate
            if ($this->_weeeHelper->isEnabled() && $this->_weeeHelper->isTaxable()) {
                $this->_calculateWeeeAmountInclTax($item, $appliedRates, false);
                $this->_calculateWeeeAmountInclTax($item, $appliedRates, true);
            }
        }
        if ($rate > 0) {
            $itemTaxGroups[$item->getId()] = $appliedRates;
        }
        $this->_addAmount($item->getTaxAmount());
        $this->_addBaseAmount($item->getBaseTaxAmount());
        return;
    }

    /**
     * Calculate unit tax anount based on unit price
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @param   array $taxGroups
     * @param   string $taxId
     * @param   boolean $recalculateRowTotalInclTax
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calcUnitTaxAmount(
        $item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false
    )
    {
        $qty = $item->getTotalQty();
        $inclTax = $item->getIsPriceInclTax();
        $price = $item->getTaxableAmount();
        $basePrice = $item->getBaseTaxableAmount();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $hiddenTax = null;
        $baseHiddenTax = null;
        $weeeTax = null;
        $baseWeeeTax = null;
        $unitTaxBeforeDiscount = null;
        $weeeTaxBeforeDiscount = null;
        $baseUnitTaxBeforeDiscount = null;
        $baseWeeeTaxBeforeDiscount = null;

        switch ($this->_config->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
                $baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate, false);
                    $unitTaxBeforeDiscount += $weeeTaxBeforeDiscount;
                    $baseWeeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate);
                    $baseUnitTaxBeforeDiscount += $baseWeeeTaxBeforeDiscount;
                }
                $unitTaxBeforeDiscount = $unitTax = $this->_calculator->round($unitTaxBeforeDiscount);
                $baseUnitTaxBeforeDiscount = $baseUnitTax = $this->_calculator->round($baseUnitTaxBeforeDiscount);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount = $item->getDiscountAmount() / $qty;
                $baseDiscountAmount = $item->getBaseDiscountAmount() / $qty;

                //We want to remove weee
                if ($isWeeeEnabled && $this->_weeeHelper->includeInSubtotal()) {
                    $discountAmount = $discountAmount - $item->getWeeeDiscount() / $qty;
                    $baseDiscountAmount = $baseDiscountAmount - $item->getBaseWeeeDiscount() / $qty;
                }

                $unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
                $unitTaxDiscount = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
                $unitTax = $this->_calculator->round(max($unitTaxBeforeDiscount - $unitTaxDiscount, 0));

                $baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);
                $baseUnitTaxDiscount = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
                $baseUnitTax = $this->_calculator->round(max($baseUnitTaxBeforeDiscount - $baseUnitTaxDiscount, 0));

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = $this->_calculateRowWeeeTax($item->getWeeeDiscount(), $item, $rate, false);
                    $weeeTax = $weeeTax / $qty;
                    $unitTax += $weeeTax;
                    $baseWeeeTax = $this->_calculateRowWeeeTax($item->getBaseWeeeDiscount(), $item, $rate);
                    $baseWeeeTax = $baseWeeeTax / $qty;
                    $baseUnitTax += $baseWeeeTax;
                }

                $unitTax = $this->_calculator->round($unitTax);
                $baseUnitTax = $this->_calculator->round($baseUnitTax);

                //Calculate the weee taxes before discount
                $weeeTaxBeforeDiscount = 0;
                $baseWeeeTaxBeforeDiscount = 0;

                if ($isWeeeTaxable) {
                    $weeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate, false);
                    $unitTaxBeforeDiscount += $weeeTaxBeforeDiscount;
                    $baseWeeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate);
                    $baseUnitTaxBeforeDiscount += $baseWeeeTaxBeforeDiscount;
                }

                $unitTaxBeforeDiscount = max(0, $this->_calculator->round($unitTaxBeforeDiscount));
                $baseUnitTaxBeforeDiscount = max(0, $this->_calculator->round($baseUnitTaxBeforeDiscount));

                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax = $unitTaxBeforeDiscount - $unitTax;
                    $baseHiddenTax = $baseUnitTaxBeforeDiscount - $baseUnitTax;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => $qty,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                } elseif ($discountAmount > $price) { // case with 100% discount on price incl. tax
                    $hiddenTax = $discountAmount - $price;
                    $baseHiddenTax = $baseDiscountAmount - $basePrice;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => $qty,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                // calculate discount compensation
                // We need the discount compensation when dont calculate the hidden taxes
                // (when product does not include taxes)
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
                    $item->setDiscountTaxCompensation($item->getDiscountTaxCompensation() +
                    $unitTaxBeforeDiscount * $qty - max(0, $unitTax) * $qty);
                }
                break;
        }

        $rowTax = $this->_store->roundPrice(max(0, $qty * $unitTax));
        $baseRowTax = $this->_store->roundPrice(max(0, $qty * $baseUnitTax));
        $item->setTaxAmount($item->getTaxAmount() + $rowTax);
        $item->setBaseTaxAmount($item->getBaseTaxAmount() + $baseRowTax);
        if (is_array($taxGroups)) {
            $taxGroups[$rateKey]['tax'] = max(0, $rowTax);
            $taxGroups[$rateKey]['base_tax'] = max(0, $baseRowTax);
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax) || $recalculateRowTotalInclTax) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($price * $qty);
                $item->setBaseRowTotalInclTax($basePrice * $qty);
            } else {
                $item->setRowTotalInclTax(
                    $item->getRowTotalInclTax() + ($unitTaxBeforeDiscount - $weeeTaxBeforeDiscount) * $qty);
                $item->setBaseRowTotalInclTax(
                    $item->getBaseRowTotalInclTax() +
                    ($baseUnitTaxBeforeDiscount - $baseWeeeTaxBeforeDiscount) * $qty);
            }
        }

        return $this;
    }

    /**
     * Calculate address total tax based on row total
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _rowBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items = $this->_getAddressItems($address);
        $itemTaxGroups = array();
        $store = $address->getQuote()->getStore();
        $catalogPriceInclTax = $this->_config->priceIncludesTax($store);

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_rowBaseProcessItemTax(
                        $address, $child, $taxRateRequest, $itemTaxGroups, $catalogPriceInclTax);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_rowBaseProcessItemTax(
                    $address, $item, $taxRateRequest, $itemTaxGroups, $catalogPriceInclTax);
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $taxRateRequest
     * @param array $itemTaxGroups
     * @param boolean $catalogPriceInclTax
     */
    protected function _rowBaseProcessItemTax($address, $item, $taxRateRequest, &$itemTaxGroups, $catalogPriceInclTax)
    {
        $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $this->_calculator->getRate($taxRateRequest);

        $item->setTaxAmount(0);
        $item->setBaseTaxAmount(0);
        $item->setHiddenTaxAmount(0);
        $item->setBaseHiddenTaxAmount(0);
        $item->setTaxPercent($rate);
        $item->setDiscountTaxCompensation(0);
        $rowTotalInclTax = $item->getRowTotalInclTax();
        $recalculateRowTotalInclTax = false;
        if (!isset($rowTotalInclTax)) {
            $item->setRowTotalInclTax($item->getTaxableAmount());
            $item->setBaseRowTotalInclTax($item->getBaseTaxableAmount());
            $recalculateRowTotalInclTax = true;
        }

        $appliedRates = $this->_calculator->getAppliedRates($taxRateRequest);
        $item->setTaxRates($appliedRates);
        if ($catalogPriceInclTax) {
            $this->_calcRowTaxAmount($item, $rate);
            $this->_saveAppliedTaxes(
                $address, $appliedRates, $item->getTaxAmount(), $item->getBaseTaxAmount(), $rate);
        } else {
            //need to calculate each tax separately
            $taxGroups = array();
            foreach ($appliedRates as $appliedTax) {
                $taxId = $appliedTax['id'];
                $taxRate = $appliedTax['percent'];
                $this->_calcRowTaxAmount($item, $taxRate, $taxGroups, $taxId, $recalculateRowTotalInclTax);
                $this->_saveAppliedTaxes(
                    $address, array($appliedTax), $taxGroups[$taxId]['tax'], $taxGroups[$taxId]['base_tax'], $taxRate);
            }
            //We need to calculate weeeAmountInclTax using multiple tax rate here
            //because the _calculateWeeeTax and _calculateRowWeeeTax only take one tax rate
            if ($this->_weeeHelper->isEnabled() && $this->_weeeHelper->isTaxable()) {
                $this->_calculateWeeeAmountInclTax($item, $appliedRates, false);
                $this->_calculateWeeeAmountInclTax($item, $appliedRates, true);
            }
        }
        if ($rate > 0) {
            $itemTaxGroups[$item->getId()] = $appliedRates;
        }
        $this->_addAmount($item->getTaxAmount());
        $this->_addBaseAmount($item->getBaseTaxAmount());
        return;
    }

    /**
     * Calculate item tax amount based on row total
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @param   array $taxGroups
     * @param   string $taxId
     * @param   boolean $recalculateRowTotalInclTax
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calcRowTaxAmount(
        $item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false
    )
    {
        $inclTax = $item->getIsPriceInclTax();
        $subtotal = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal = $baseTaxSubtotal = $item->getBaseTaxableAmount();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $hiddenTax = null;
        $baseHiddenTax = null;
        $weeeTax = null;
        $baseWeeeTax = null;
        $rowTaxBeforeDiscount = null;
        $baseRowTaxBeforeDiscount = null;
        $weeeRowTaxBeforeDiscount = null;
        $baseWeeeRowTaxBeforeDiscount = null;

        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                }
                $rowTaxBeforeDiscount = $rowTax = $this->_calculator->round($rowTaxBeforeDiscount);
                $baseRowTaxBeforeDiscount = $baseRowTax = $this->_calculator->round($baseRowTaxBeforeDiscount);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount = $item->getDiscountAmount();
                $baseDiscountAmount = $item->getBaseDiscountAmount();

                if ($isWeeeEnabled && $this->_weeeHelper->includeInSubtotal()) {
                    $discountAmount = $discountAmount - $item->getWeeeDiscount();
                    $baseDiscountAmount = $baseDiscountAmount - $item->getBaseWeeeDiscount();
                }

                $rowTax = $this->_calculator->calcTaxAmount(
                    max($subtotal - $discountAmount, 0),
                    $rate,
                    $inclTax
                );
                $baseRowTax = $this->_calculator->calcTaxAmount(
                    max($baseSubtotal - $baseDiscountAmount, 0),
                    $rate,
                    $inclTax
                );

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = $this->_calculateRowWeeeTax($item->getWeeeDiscount(), $item, $rate, false);
                    $rowTax += $weeeTax;
                    $baseWeeeTax = $this->_calculateRowWeeeTax($item->getBaseWeeeDiscount(), $item, $rate);
                    $baseRowTax += $baseWeeeTax;
                }

                $rowTax = $this->_calculator->round($rowTax);
                $baseRowTax = $this->_calculator->round($baseRowTax);

                //Calculate the Row Tax before discount
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $subtotal,
                    $rate,
                    $inclTax,
                    false
                );
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $baseSubtotal,
                    $rate,
                    $inclTax,
                    false
                );

                //Calculate the Weee taxes before discount
                $weeeRowTaxBeforeDiscount = 0;
                $baseWeeeRowTaxBeforeDiscount = 0;
                if ($isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                }

                $rowTaxBeforeDiscount = max(0, $this->_calculator->round($rowTaxBeforeDiscount));
                $baseRowTaxBeforeDiscount = max(0, $this->_calculator->round($baseRowTaxBeforeDiscount));

                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax = $rowTaxBeforeDiscount - $rowTax;
                    $baseHiddenTax = $baseRowTaxBeforeDiscount - $baseRowTax;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                } elseif ($discountAmount > $subtotal) { // case with 100% discount on price incl. tax
                    $hiddenTax = $discountAmount - $subtotal;
                    $baseHiddenTax = $baseDiscountAmount - $baseSubtotal;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                // calculate discount compensation
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
                    $item->setDiscountTaxCompensation($item->getDiscountTaxCompensation() +
                    $rowTaxBeforeDiscount - max(0, $rowTax));
                }
                break;
        }
        $item->setTaxAmount($item->getTaxAmount() + max(0, $rowTax));
        $item->setBaseTaxAmount($item->getBaseTaxAmount() + max(0, $baseRowTax));
        if (is_array($taxGroups)) {
            $taxGroups[$rateKey]['tax'] = max(0, $rowTax);
            $taxGroups[$rateKey]['base_tax'] = max(0, $baseRowTax);
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax) || $recalculateRowTotalInclTax) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal);
                $item->setBaseRowTotalInclTax($baseSubtotal);
            } else {
                $item->setRowTotalInclTax(
                    $item->getRowTotalInclTax() + $rowTaxBeforeDiscount - $weeeRowTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($item->getBaseRowTotalInclTax() +
                $baseRowTaxBeforeDiscount - $baseWeeeRowTaxBeforeDiscount);
            }
        }
        return $this;
    }

    /**
     * Calculate address total tax based on address subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _totalBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items = $this->_getAddressItems($address);
        $store = $address->getQuote()->getStore();
        $taxGroups = array();
        $itemTaxGroups = array();
        $catalogPriceInclTax = $this->_config->priceIncludesTax($store);

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_totalBaseProcessItemTax(
                        $child, $taxRateRequest, $taxGroups, $itemTaxGroups, $catalogPriceInclTax);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_totalBaseProcessItemTax(
                    $item, $taxRateRequest, $taxGroups, $itemTaxGroups, $catalogPriceInclTax);
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);

        foreach ($taxGroups as $taxId => $data) {
            if ($catalogPriceInclTax) {
                $rate = (float)$taxId;
            } else {
                $rate = $data['applied_rates'][0]['percent'];
            }

            $inclTax = $data['incl_tax'];

            $totalTax = array_sum($data['tax']);
            $baseTotalTax = array_sum($data['base_tax']);
            $this->_addAmount($totalTax);
            $this->_addBaseAmount($baseTotalTax);
            $totalTaxRounded = $this->_calculator->round($totalTax);
            $baseTotalTaxRounded = $this->_calculator->round($totalTaxRounded);
            $this->_saveAppliedTaxes($address, $data['applied_rates'], $totalTaxRounded, $baseTotalTaxRounded, $rate);
        }
        return $this;
    }

    /**
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Varien_Object $taxRateRequest
     * @param array $taxGroups
     * @param array $itemTaxGroups
     * @param boolean $catalogPriceInclTax
     */
    protected function _totalBaseProcessItemTax(
        $item, $taxRateRequest, &$taxGroups, &$itemTaxGroups, $catalogPriceInclTax
    )
    {
        $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
        $rate = $this->_calculator->getRate($taxRateRequest);

        $item->setTaxAmount(0);
        $item->setBaseTaxAmount(0);
        $item->setHiddenTaxAmount(0);
        $item->setBaseHiddenTaxAmount(0);
        $item->setTaxPercent($rate);
        $item->setDiscountTaxCompensation(0);
        $rowTotalInclTax = $item->getRowTotalInclTax();
        $recalculateRowTotalInclTax = false;
        if (!isset($rowTotalInclTax)) {
            $item->setRowTotalInclTax($item->getTaxableAmount());
            $item->setBaseRowTotalInclTax($item->getBaseTaxableAmount());
            $recalculateRowTotalInclTax = true;
        }

        $appliedRates = $this->_calculator->getAppliedRates($taxRateRequest);
        if ($catalogPriceInclTax) {
            $taxGroups[(string)$rate]['applied_rates'] = $appliedRates;
            $taxGroups[(string)$rate]['incl_tax'] = $item->getIsPriceInclTax();
            $this->_aggregateTaxPerRate($item, $rate, $taxGroups);
        } else {
            //need to calculate each tax separately
            foreach ($appliedRates as $appliedTax) {
                $taxId = $appliedTax['id'];
                $taxRate = $appliedTax['percent'];
                $taxGroups[$taxId]['applied_rates'] = array($appliedTax);
                $taxGroups[$taxId]['incl_tax'] = $item->getIsPriceInclTax();
                $this->_aggregateTaxPerRate($item, $taxRate, $taxGroups, $taxId, $recalculateRowTotalInclTax);
            }

            //We need to calculate weeeAmountInclTax using multiple tax rate here
            //because the _calculateWeeeTax and _calculateRowWeeeTax only take one tax rate
            if ($this->_weeeHelper->isEnabled() && $this->_weeeHelper->isTaxable()) {
                $this->_calculateWeeeAmountInclTax($item, $appliedRates, false);
                $this->_calculateWeeeAmountInclTax($item, $appliedRates, true);
            }
        }
        if ($rate > 0) {
            $itemTaxGroups[$item->getId()] = $appliedRates;
        }
        return;
    }

    /**
     * Aggregate row totals per tax rate in array
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @param   array $taxGroups
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _aggregateTaxPerRate(
        $item, $rate, &$taxGroups, $taxId = null, $recalculateRowTotalInclTax = false
    )
    {
        $inclTax = $item->getIsPriceInclTax();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;
        $taxSubtotal = $subtotal = $item->getTaxableAmount();
        $baseTaxSubtotal = $baseSubtotal = $item->getBaseTaxableAmount();

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        if (!isset($taxGroups[$rateKey]['totals'])) {
            $taxGroups[$rateKey]['totals'] = array();
            $taxGroups[$rateKey]['base_totals'] = array();
            $taxGroups[$rateKey]['weee_tax'] = array();
            $taxGroups[$rateKey]['base_weee_tax'] = array();
        }

        $hiddenTax = null;
        $baseHiddenTax = null;
        $weeeTax = null;
        $baseWeeeTax = null;
        $discount = 0;
        $rowTaxBeforeDiscount = 0;
        $baseRowTaxBeforeDiscount = 0;
        $weeeRowTaxBeforeDiscount = 0;
        $baseWeeeRowTaxBeforeDiscount = 0;


        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                    $taxGroups[$rateKey]['weee_tax'][] = $this->_deltaRound($weeeRowTaxBeforeDiscount,
                        $rateKey, $inclTax);
                    $taxGroups[$rateKey]['base_weee_tax'][] = $this->_deltaRound($baseWeeeRowTaxBeforeDiscount,
                        $rateKey, $inclTax);
                }
                $taxBeforeDiscountRounded = $rowTax = $this->_deltaRound($rowTaxBeforeDiscount, $rateKey, $inclTax);
                $baseTaxBeforeDiscountRounded = $baseRowTax = $this->_deltaRound($baseRowTaxBeforeDiscount,
                    $rateKey, $inclTax, 'base');
                $item->setTaxAmount($item->getTaxAmount() + max(0, $rowTax));
                $item->setBaseTaxAmount($item->getBaseTaxAmount() + max(0, $baseRowTax));
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                if ($this->_helper->applyTaxOnOriginalPrice($this->_store)) {
                    $discount = $item->getOriginalDiscountAmount();
                    $baseDiscount = $item->getBaseOriginalDiscountAmount();
                } else {
                    $discount = $item->getDiscountAmount();
                    $baseDiscount = $item->getBaseDiscountAmount();
                }

                //We remove weee discount from discount if weee is not taxed
                if ($isWeeeEnabled && $this->_weeeHelper->includeInSubtotal()) {
                    $discount = $discount - $item->getWeeeDiscount();
                    $baseDiscount = $baseDiscount - $item->getBaseWeeeDiscount();
                }
                $taxSubtotal = max($subtotal - $discount, 0);
                $baseTaxSubtotal = max($baseSubtotal - $baseDiscount, 0);

                $rowTax = $this->_calculator->calcTaxAmount($taxSubtotal, $rate, $inclTax, false);
                $baseRowTax = $this->_calculator->calcTaxAmount($baseTaxSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = $this->_calculateRowWeeeTax($item->getWeeeDiscount(), $item, $rate, false);
                    $rowTax += $weeeTax;
                    $baseWeeeTax = $this->_calculateRowWeeeTax($item->getBaseWeeeDiscount(), $item, $rate);
                    $baseRowTax += $baseWeeeTax;
                    $taxGroups[$rateKey]['weee_tax'][] = $weeeTax;
                    $taxGroups[$rateKey]['base_weee_tax'][] = $baseWeeeTax;
                }

                $rowTax = $this->_deltaRound($rowTax, $rateKey, $inclTax);
                $baseRowTax = $this->_deltaRound($baseRowTax, $rateKey, $inclTax, 'base');

                $item->setTaxAmount($item->getTaxAmount() + max(0, $rowTax));
                $item->setBaseTaxAmount($item->getBaseTaxAmount() + max(0, $baseRowTax));

                //Calculate the Row taxes before discount
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $subtotal,
                    $rate,
                    $inclTax,
                    false
                );
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $baseSubtotal,
                    $rate,
                    $inclTax,
                    false
                );


                if ($isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                }

                $taxBeforeDiscountRounded = max(
                    0,
                    $this->_deltaRound($rowTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount')
                );
                $baseTaxBeforeDiscountRounded = max(
                    0,
                    $this->_deltaRound($baseRowTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount_base')
                );

                if (!$item->getNoDiscount()) {
                    if ($item->getWeeeTaxApplied()) {
                        $item->setDiscountTaxCompensation($item->getDiscountTaxCompensation() +
                        $taxBeforeDiscountRounded - max(0, $rowTax));
                    }
                }

                if ($inclTax && $discount > 0) {
                    $roundedHiddenTax = $taxBeforeDiscountRounded - max(0, $rowTax);
                    $baseRoundedHiddenTax = $baseTaxBeforeDiscountRounded - max(0, $baseRowTax);
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $roundedHiddenTax,
                        'base_value' => $baseRoundedHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                break;
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax) || $recalculateRowTotalInclTax) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal);
                $item->setBaseRowTotalInclTax($baseSubtotal);
            } else {
                $item->setRowTotalInclTax(
                    $item->getRowTotalInclTax() + $taxBeforeDiscountRounded - $weeeRowTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax(
                    $item->getBaseRowTotalInclTax()
                    + $baseTaxBeforeDiscountRounded
                    - $baseWeeeRowTaxBeforeDiscount);
            }
        }

        $taxGroups[$rateKey]['totals'][] = max(0, $taxSubtotal);
        $taxGroups[$rateKey]['base_totals'][] = max(0, $baseTaxSubtotal);
        $taxGroups[$rateKey]['tax'][] = max(0, $rowTax);
        $taxGroups[$rateKey]['base_tax'][] = max(0, $baseRowTax);
        return $this;
    }

    /**
     * Calculates the weeeAmountInclTax for display purpose
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param array $appliedRates
     * @param bool $base
     */
    protected function _calculateWeeeAmountInclTax($item, $appliedRates, $base = true)
    {
        foreach ($this->_weeeHelper->getApplied($item) as $tax) {
            $weeeAmountInclTax = 0;
            $weeeAmountExclTax = 0;

            if ($base) {
                $weeeAmountInclTax = isset($tax['base_amount_incl_tax']) ? $tax['base_amount_incl_tax'] : 0;
                $weeeAmountExclTax = isset($tax['base_amount']) ? $tax['base_amount'] : 0;
                $weeeRowAmountInclTax = isset($tax['base_row_amount_incl_tax']) ? $tax['base_row_amount_incl_tax'] : 0;
                $weeeRowAmountExclTax = isset($tax['base_row_amount']) ? $tax['base_row_amount'] : 0;
            } else {
                $weeeAmountInclTax = isset($tax['amount_incl_tax']) ? $tax['amount_incl_tax'] : 0;
                $weeeAmountExclTax = isset($tax['amount']) ? $tax['amount'] : 0;
                $weeeRowAmountInclTax = isset($tax['row_amount_incl_tax']) ? $tax['row_amount_incl_tax'] : 0;
                $weeeRowAmountExclTax = isset($tax['row_amount']) ? $tax['row_amount'] : 0;
            }

            $weeeTax = array();
            $weeeRowTax = array();
            foreach ($appliedRates as $appliedRate) {
                $rate = $appliedRate['percent'];
                $weeeTax[] = $this->_getWeeeTax($rate, $item, 0, $weeeAmountInclTax, $weeeAmountExclTax);
                $weeeRowTax[] = $this->_getWeeeTax($rate, $item, 0, $weeeRowAmountInclTax, $weeeRowAmountExclTax);
            }

            //We want to update the tax calculated on Weee to the Item with out discount for display purpose
            $weeeAmountInclTax = array_sum($weeeTax) + $weeeAmountExclTax;
            $weeeRowAmountInclTax = array_sum($weeeRowTax) + $weeeRowAmountExclTax;
            $calculationMethod = $this->_config->getAlgorithm($this->_store);
            if ($calculationMethod == Mage_Tax_Model_Calculation::CALC_UNIT_BASE) {
                $weeeRowAmountInclTax = $this->_calculator->round($weeeAmountInclTax * $item->getQty());
            } else {
                $weeeAmountInclTax = $this->_calculator->round($weeeRowAmountInclTax / $item->getQty());
            }
            if ($base) {
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'base_amount_incl_tax', $weeeAmountInclTax);
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'base_row_amount_incl_tax', $weeeRowAmountInclTax);
            } else {
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'amount_incl_tax', $weeeAmountInclTax);
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'row_amount_incl_tax', $weeeRowAmountInclTax);
            }
        }
        return;
    }

    /**
     * Calculates the weee tax based on the customer tax rate and discount
     *
     * @param float $discountAmount
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param float $rate
     * @param bool $base
     * @return float
     */
    protected function _calculateWeeeTax($discountAmount, $item, $rate, $base = true)
    {
        $totalWeeeAmountInclTax = 0;
        $totalWeeeAmountExclTax = 0;

        foreach ($this->_weeeHelper->getApplied($item) as $tax) {
            $weeeAmountInclTax = 0;
            $weeeAmountExclTax = 0;

            if ($base) {
                $weeeAmountInclTax = isset($tax['base_amount_incl_tax']) ? $tax['base_amount_incl_tax'] : 0;
                $weeeAmountExclTax = isset($tax['base_amount']) ? $tax['base_amount'] : 0;
            } else {
                $weeeAmountInclTax = isset($tax['amount_incl_tax']) ? $tax['amount_incl_tax'] : 0;
                $weeeAmountExclTax = isset($tax['amount']) ? $tax['amount'] : 0;
            }

            $weeeTaxWithOutDiscount = $this->_getWeeeTax($rate, $item, 0, $weeeAmountInclTax, $weeeAmountExclTax);

            //We want to update the tax calculated on Weee to the Item with out discount for display purpose
            $weeeAmountInclTax = $weeeTaxWithOutDiscount + $weeeAmountExclTax;
            if ($base) {
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'base_amount_incl_tax', $weeeAmountInclTax);
            } else {
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'amount_incl_tax', $weeeAmountInclTax);
            }

            $totalWeeeAmountInclTax += $weeeAmountInclTax;
            $totalWeeeAmountExclTax += $weeeAmountExclTax;


        }
        return $this->_getWeeeTax($rate, $item, $discountAmount, $totalWeeeAmountInclTax, $totalWeeeAmountExclTax);
    }


    /**
     * Calculates and updates the wee tax based on the customer tax rate and discount for Row
     *
     * @param float $discountAmount
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param float $rate
     * @param bool $base
     * @return int
     */
    protected function _calculateRowWeeeTax($discountAmount, $item, $rate, $base = true)
    {
        //We want to update the weee tax for the unit too. discount amount set on the item is by row
        $discountAmountByUnit = $discountAmount / ($item->getTotalQty() ? $item->getTotalQty() : 1);
        $this->_calculateWeeeTax($discountAmountByUnit, $item, $rate, $base);


        $totalWeeeAmountInclTax = 0;
        $totalWeeeAmountExclTax = 0;

        foreach ($this->_weeeHelper->getApplied($item) as $tax) {
            $weeeAmountInclTax = 0;
            $weeeAmountExclTax = 0;

            if ($base) {
                $weeeAmountInclTax = isset($tax['base_row_amount_incl_tax']) ? $tax['base_row_amount_incl_tax'] : 0;
                $weeeAmountExclTax = isset($tax['base_row_amount']) ? $tax['base_row_amount'] : 0;
            } else {
                $weeeAmountInclTax = isset($tax['row_amount_incl_tax']) ? $tax['row_amount_incl_tax'] : 0;
                $weeeAmountExclTax = isset($tax['row_amount']) ? $tax['row_amount'] : 0;
            }

            $weeeTaxWithOutDiscount = $this->_getWeeeTax($rate, $item, 0, $weeeAmountInclTax, $weeeAmountExclTax);

            //We want to update the tax calculated on Weee to the Item without discount.
            //We do not show the discount to the user.
            $weeeAmountIncludingTax = $weeeTaxWithOutDiscount + $weeeAmountExclTax;
            if ($base) {
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'base_row_amount_incl_tax', $weeeAmountIncludingTax);
            } else {
                $this->_weeeHelper->setWeeeTaxesAppliedProperty($item, $tax['title'],
                    'row_amount_incl_tax', $weeeAmountIncludingTax);
            }
            $totalWeeeAmountInclTax += $weeeAmountInclTax;
            $totalWeeeAmountExclTax += $weeeAmountExclTax;
        }
        return $this->_getWeeeTax($rate, $item, $discountAmount, $totalWeeeAmountInclTax, $totalWeeeAmountExclTax);
    }


    /**
     * Calculate the Weee tax based on the discount and rate
     *
     * @param float $rate
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param float $discountAmount
     * @param float $weeeAmountIncludingTax
     * @param float $weeeAmountExclTax
     * @return mixed
     */
    private function _getWeeeTax($rate, $item, $discountAmount, $weeeAmountIncludingTax, $weeeAmountExclTax)
    {
        $isWeeeTaxAlreadyIncluded = $this->_weeeHelper->isTaxIncluded($this->_store);

        $sameRateAsStore = $this->_helper->isCrossBorderTradeEnabled($this->_store) ||
                ($rate == $this->_calculator->getStoreRateForItem($item));
        if ($sameRateAsStore && $isWeeeTaxAlreadyIncluded) {
            if (!$discountAmount || $discountAmount <= 0) {
                //We want to skip the re calculation and return the difference
                return max($weeeAmountIncludingTax - $weeeAmountExclTax, 0);
            } else {
                return $this->_calculator->calcTaxAmount($weeeAmountIncludingTax - $discountAmount, $rate, true, true);
            }
        }
        $discountAmount = !$discountAmount ? 0 : $discountAmount;

        ///Regular case where weee does not have the tax and we want to calculate the tax
        return $this->_calculator->calcTaxAmount($weeeAmountExclTax - $discountAmount, $rate, false, true);
    }

    /**
     * Round price based on previous rounding operation delta
     *
     * @param float $price
     * @param string $rate
     * @param bool $direction price including or excluding tax
     * @param string $type
     * @return float
     */
    protected function _deltaRound($price, $rate, $direction, $type = 'regular')
    {
        if ($price) {
            $rate = (string)$rate;
            $type = $type . $direction;
            // initialize the delta to a small number to avoid non-deterministic behavior with rounding of 0.5
            $delta = isset($this->_roundingDeltas[$type][$rate]) ? $this->_roundingDeltas[$type][$rate] : 0.000001;
            $price += $delta;
            $this->_roundingDeltas[$type][$rate] = $price - $this->_calculator->round($price);
            $price = $this->_calculator->round($price);
        }
        return $price;
    }

    /**
     * Recalculate parent item amounts base on children data
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $rowTaxAmount = 0;
        $baseRowTaxAmount = 0;
        foreach ($item->getChildren() as $child) {
            $rowTaxAmount += $child->getTaxAmount();
            $baseRowTaxAmount += $child->getBaseTaxAmount();
        }
        $item->setTaxAmount($rowTaxAmount);
        $item->setBaseTaxAmount($baseRowTaxAmount);
        return $this;
    }

    /**
     * Collect applied tax rates information on address level
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   array $applied
     * @param   float $amount
     * @param   float $baseAmount
     * @param   float $rate
     */
    protected function _saveAppliedTaxes(Mage_Sales_Model_Quote_Address $address,
                                         $applied, $amount, $baseAmount, $rate)
    {
        $previouslyAppliedTaxes = $address->getAppliedTaxes();
        $process = count($previouslyAppliedTaxes);

        foreach ($applied as $row) {
            if ($row['percent'] == 0) {
                continue;
            }
            if (!isset($previouslyAppliedTaxes[$row['id']])) {
                $row['process'] = $process;
                $row['amount'] = 0;
                $row['base_amount'] = 0;
                $previouslyAppliedTaxes[$row['id']] = $row;
            }

            if (!is_null($row['percent'])) {
                $row['percent'] = $row['percent'] ? $row['percent'] : 1;
                $rate = $rate ? $rate : 1;

                $appliedAmount = $amount / $rate * $row['percent'];
                $baseAppliedAmount = $baseAmount / $rate * $row['percent'];
            } else {
                $appliedAmount = 0;
                $baseAppliedAmount = 0;
                foreach ($row['rates'] as $rate) {
                    $appliedAmount += $rate['amount'];
                    $baseAppliedAmount += $rate['base_amount'];
                }
            }


            if ($appliedAmount || $previouslyAppliedTaxes[$row['id']]['amount']) {
                $previouslyAppliedTaxes[$row['id']]['amount'] += $appliedAmount;
                $previouslyAppliedTaxes[$row['id']]['base_amount'] += $baseAppliedAmount;
            } else {
                unset($previouslyAppliedTaxes[$row['id']]);
            }
        }
        $address->setAppliedTaxes($previouslyAppliedTaxes);
    }

    /**
     * Add tax totals information to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $applied = $address->getAppliedTaxes();
        $store = $address->getQuote()->getStore();
        $amount = $address->getTaxAmount();

        $items = $this->_getAddressItems($address);
        $discountTaxCompensation = 0;
        foreach ($items as $item) {
            $discountTaxCompensation += $item->getDiscountTaxCompensation();
        }
        $taxAmount = $amount + $discountTaxCompensation;
        /*
         * when weee discount is not included in extraTaxAmount, we need to add it to the total tax
         */
        if ($this->_weeeHelper->isEnabled()) {
            if (!$this->_weeeHelper->includeInSubtotal()) {
                $taxAmount += $address->getWeeeDiscount();
            }
        }

        $area = null;
        if ($this->_config->displayCartTaxWithGrandTotal($store) && $address->getGrandTotal()) {
            $area = 'taxes';
        }

        if (($amount != 0) || ($this->_config->displayCartZeroTax($store))) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('tax')->__('Tax'),
                'full_info' => $applied ? $applied : array(),
                'value' => $amount,
                'area' => $area
            ));
        }

        $store = $address->getQuote()->getStore();
        /**
         * Modify subtotal
         */
        if ($this->_config->displayCartSubtotalBoth($store) || $this->_config->displayCartSubtotalInclTax($store)) {
            if ($address->getSubtotalInclTax() > 0) {
                $subtotalInclTax = $address->getSubtotalInclTax();
            } else {
                $subtotalInclTax = $address->getSubtotal() + $taxAmount - $address->getShippingTaxAmount();
            }

            $address->addTotal(array(
                'code' => 'subtotal',
                'title' => Mage::helper('sales')->__('Subtotal'),
                'value' => $subtotalInclTax,
                'value_incl_tax' => $subtotalInclTax,
                'value_excl_tax' => $address->getSubtotal(),
            ));
        }

        return $this;
    }

    /**
     * Process model configuration array.
     * This method can be used for changing totals collect sort order
     *
     * @param   array $config
     * @param   store $store
     * @return  array
     */
    public function processConfigArray($config, $store)
    {
        $calculationSequence = $this->_helper->getCalculationSequence($store);
        switch ($calculationSequence) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $config['before'][] = 'discount';
                break;
            default:
                $config['after'][] = 'discount';
                break;
        }
        return $config;
    }

    /**
     * Get Tax label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('tax')->__('Tax');
    }
}
