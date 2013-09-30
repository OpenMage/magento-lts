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
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Class constructor
     */
    public function __construct()
    {
        $this->setCode('tax');
        $this->_helper      = Mage::helper('tax');
        $this->_calculator  = Mage::getSingleton('tax/calculation');
        $this->_config      = Mage::getSingleton('tax/config');
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
        $this->_roundingDeltas      = array();
        $this->_baseRoundingDeltas  = array();
        $this->_hiddenTaxes         = array();
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
            $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests(
                $this->_calculator->getRateOriginRequest($this->_store),
                $request
            );
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
                $item           = $taxInfoItem['item'];
                $rateKey        = $taxInfoItem['rate_key'];
                $hiddenTax      = $taxInfoItem['value'];
                $baseHiddenTax  = $taxInfoItem['base_value'];
                $inclTax        = $taxInfoItem['incl_tax'];
                $qty            = $taxInfoItem['qty'];

                if ($this->_config->getAlgorithm($this->_store) == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
                    $this->_getAddress()->addTotalAmount('hidden_tax', max(0, $qty * $hiddenTax));
                    $this->_getAddress()->addBaseTotalAmount('hidden_tax', max(0, $qty * $baseHiddenTax));
                    $hiddenTax      = $this->_deltaRound($hiddenTax, $rateKey, $inclTax);
                    $baseHiddenTax  = $this->_deltaRound($baseHiddenTax, $rateKey, $inclTax, 'base');
                    $item->setHiddenTaxAmount(max(0, $qty * $hiddenTax));
                    $item->setBaseHiddenTaxAmount(max(0, $qty * $baseHiddenTax));
                } else {
                    $hiddenTax      = $this->_calculator->round($hiddenTax);
                    $baseHiddenTax  = $this->_calculator->round($baseHiddenTax);
                    $item->setHiddenTaxAmount(max(0, $qty * $hiddenTax));
                    $item->setBaseHiddenTaxAmount(max(0, $qty * $baseHiddenTax));
                    $this->_getAddress()->addTotalAmount('hidden_tax', $item->getHiddenTaxAmount());
                    $this->_getAddress()->addBaseTotalAmount('hidden_tax', $item->getBaseHiddenTaxAmount());
                }
            } else {
                // Shipping hidden taxes
                $rateKey        = $taxInfoItem['rate_key'];
                $hiddenTax      = $taxInfoItem['value'];
                $baseHiddenTax  = $taxInfoItem['base_value'];
                $inclTax        = $taxInfoItem['incl_tax'];

                if ($this->_config->getAlgorithm($this->_store) == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
                    $this->_getAddress()->addTotalAmount('shipping_hidden_tax', $hiddenTax);
                    $this->_getAddress()->addBaseTotalAmount('shipping_hidden_tax', $baseHiddenTax);

                    $hiddenTax      = $this->_deltaRound($hiddenTax, $rateKey, $inclTax);
                    $baseHiddenTax  = $this->_deltaRound($baseHiddenTax, $rateKey, $inclTax, 'base');

                    $this->_getAddress()->setShippingHiddenTaxAmount(max(0, $hiddenTax));
                    $this->_getAddress()->setBaseShippingHiddenTaxAmount(max(0, $baseHiddenTax));
                } else {
                    $hiddenTax      = $this->_deltaRound($hiddenTax, $rateKey, $inclTax);
                    $baseHiddenTax  = $this->_deltaRound($baseHiddenTax, $rateKey, $inclTax, 'base');

                    $this->_getAddress()->setShippingHiddenTaxAmount(max(0, $hiddenTax));
                    $this->_getAddress()->setBaseShippingHiddenTaxAmount(max(0, $baseHiddenTax));
                    $this->_getAddress()->addTotalAmount('shipping_hidden_tax', $hiddenTax);
                    $this->_getAddress()->addBaseTotalAmount('shipping_hidden_tax', $baseHiddenTax);
                }
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
     * Tax caclulation for shipping price
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        $rate           = $this->_calculator->getRate($taxRateRequest);
        $inclTax        = $address->getIsShippingInclTax();
        $shipping       = $address->getShippingTaxable();
        $baseShipping   = $address->getBaseShippingTaxable();
        $rateKey        = (string)$rate;

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $tax        = $this->_calculator->calcTaxAmount($shipping, $rate, $inclTax, false);
                $baseTax    = $this->_calculator->calcTaxAmount($baseShipping, $rate, $inclTax, false);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount     = $address->getShippingDiscountAmount();
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
            $this->_addAmount(max(0, $tax));
            $this->_addBaseAmount(max(0, $baseTax));
            $tax        = $this->_deltaRound($tax, $rate, $inclTax);
            $baseTax    = $this->_deltaRound($baseTax, $rate, $inclTax, 'base');
        } else {
            $tax        = $this->_calculator->round($tax);
            $baseTax    = $this->_calculator->round($baseTax);
            $this->_addAmount(max(0, $tax));
            $this->_addBaseAmount(max(0, $baseTax));
        }

        if ($inclTax && !empty($discountAmount)) {
            $hiddenTax      = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
            $baseHiddenTax  = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
            $this->_hiddenTaxes[] = array(
                'rate_key'   => $rateKey,
                'value'      => $hiddenTax,
                'base_value' => $baseHiddenTax,
                'incl_tax'   => $inclTax,
            );
        }

        $address->setShippingTaxAmount(max(0, $tax));
        $address->setBaseShippingTaxAmount(max(0, $baseTax));
        $applied = $this->_calculator->getAppliedRates($taxRateRequest);
        $this->_saveAppliedTaxes($address, $applied, $tax, $baseTax, $rate);

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
        $itemTaxGroups  = array();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcUnitTaxAmount($child, $rate);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }
                    $this->_saveAppliedTaxes(
                        $address,
                        $applied,
                        $child->getTaxAmount(),
                        $child->getBaseTaxAmount(),
                        $rate
                    );
                    $child->setTaxRates($applied);
                }
                $this->_recalculateParent($item);
            }
            else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcUnitTaxAmount($item, $rate);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }
                $this->_saveAppliedTaxes(
                    $address,
                    $applied,
                    $item->getTaxAmount(),
                    $item->getBaseTaxAmount(),
                    $rate
                );
                $item->setTaxRates($applied);
            }
        }
        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     * Calculate unit tax anount based on unit price
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate)
    {
        $qty        = $item->getTotalQty();
        $inclTax    = $item->getIsPriceInclTax();
        $price      = $item->getTaxableAmount();
        $basePrice  = $item->getBaseTaxableAmount();
        $rateKey    = (string)$rate;
        $item->setTaxPercent($rate);

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_config->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $unitTax        = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
                $baseUnitTax    = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $unitTax += $item->getWeeeTaxAppliedAmount() * $rate /100;
                    $baseUnitTax += $item->getBaseWeeeTaxAppliedAmount() * $rate /100;
                }
                $unitTax = $this->_calculator->round($unitTax);
                $baseUnitTax = $this->_calculator->round($baseUnitTax);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount     = $item->getDiscountAmount() / $qty;
                $baseDiscountAmount = $item->getBaseDiscountAmount() / $qty;

                if ($isWeeeEnabled) {
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
                    $weeeTax = ($item->getWeeeTaxAppliedRowAmount() - $item->getWeeeDiscount()) * $rate / 100;
                    $weeeTax = $weeeTax / $qty;
                    $unitTax += $weeeTax;
                    $baseWeeeTax =
                        ($item->getBaseWeeeTaxAppliedRowAmount() - $item->getBaseWeeeDiscount()) * $rate / 100;
                    $baseWeeeTax = $baseWeeeTax / $qty;
                    $baseUnitTax += $baseWeeeTax;
                }
                $unitTax = $this->_calculator->round($unitTax);
                $baseUnitTax = $this->_calculator->round($baseUnitTax);
                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax      = $this->_calculator->round($unitTaxBeforeDiscount) - $unitTax;
                    $baseHiddenTax  = $this->_calculator->round($baseUnitTaxBeforeDiscount) - $baseUnitTax;
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => $qty,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                } elseif ($discountAmount > $price) { // case with 100% discount on price incl. tax
                    $hiddenTax      = $discountAmount - $price;
                    $baseHiddenTax  = $baseDiscountAmount - $basePrice;
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => $qty,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                }
                // calculate discount compensation
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
                    $unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                        $price,
                        $rate,
                        $inclTax,
                        false
                    );
                    $baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                        $price,
                        $rate,
                        $inclTax,
                        false
                    );
                    if ($isWeeeTaxable) {
                        $unitTaxBeforeDiscount += $item->getWeeeTaxAppliedAmount() * $rate / 100;
                        $baseUnitTaxBeforeDiscount += $item->getBaseWeeeTaxAppliedAmount() * $rate / 100;
                    }
                    $unitTaxBeforeDiscount = max(0, $this->_calculator->round($unitTaxBeforeDiscount));
                    $baseUnitTaxBeforeDiscount = max(0, $this->_calculator->round($baseUnitTaxBeforeDiscount));
                    $item->setDiscountTaxCompensation($unitTaxBeforeDiscount * $qty - max(0, $unitTax) * $qty);
                    $item->setBaseDiscountTaxCompensation(
                        $baseUnitTaxBeforeDiscount * $qty - max(0, $baseUnitTax) * $qty
                    );
                }
                break;
        }

        $item->setTaxAmount($this->_store->roundPrice(max(0, $qty*$unitTax)));
        $item->setBaseTaxAmount($this->_store->roundPrice(max(0, $qty*$baseUnitTax)));

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax)) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $weeeTaxBeforeDiscount = 0;
                $baseWeeeTaxBeforeDiscount = 0;
                if ($isWeeeTaxable) {
                    $weeeTaxBeforeDiscount = $item->getWeeeTaxAppliedRowAmount() * $rate/100;
                    $baseWeeeTaxBeforeDiscount = $item->getBaseWeeeTaxAppliedRowAmount() * $rate/100;
                }
                $item->setRowTotalInclTax($price * $qty + $weeeTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($basePrice * $qty + $baseWeeeTaxBeforeDiscount);
            } else {
                $taxCompensation = $item->getDiscountTaxCompensation() ? $item->getDiscountTaxCompensation() : 0;
                $item->setRowTotalInclTax($price * $qty + $unitTax * $qty + $taxCompensation);
                $item->setBaseRowTotalInclTax(
                    $basePrice * $qty + $baseUnitTax * $qty + $item->getBaseDiscountTaxCompensation()
                );
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
        $itemTaxGroups  = array();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);

                    $this->_calcRowTaxAmount($child, $rate);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }
                    $this->_saveAppliedTaxes(
                        $address,
                        $applied,
                        $child->getTaxAmount(),
                        $child->getBaseTaxAmount(),
                        $rate
                    );
                    $child->setTaxRates($applied);
                }
                $this->_recalculateParent($item);
            }
            else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcRowTaxAmount($item, $rate);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }
                $this->_saveAppliedTaxes(
                    $address,
                    $applied,
                    $item->getTaxAmount(),
                    $item->getBaseTaxAmount(),
                    $rate
                );
                $item->setTaxRates($applied);
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     * Calculate item tax amount based on row total
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calcRowTaxAmount($item, $rate)
    {
        $inclTax        = $item->getIsPriceInclTax();
        $subtotal       = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal   = $baseTaxSubtotal = $item->getBaseTaxableAmount();
        $rateKey        = (string)$rate;
        $item->setTaxPercent($rate);

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTax = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTax = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $rowTax += $item->getWeeeTaxAppliedRowAmount() * $rate / 100;
                    $baseRowTax += $item->getBaseWeeeTaxAppliedRowAmount() * $rate / 100;
                }
                $rowTax = $this->_calculator->round($rowTax);
                $baseRowTax = $this->_calculator->round($baseRowTax);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount = $item->getDiscountAmount();
                $baseDiscountAmount = $item->getBaseDiscountAmount();

                if ($isWeeeEnabled) {
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
                    $weeeTax = ($item->getWeeeTaxAppliedRowAmount() - $item->getWeeeDiscount()) * $rate / 100;
                    $rowTax += $weeeTax;
                    $baseWeeeTax =
                        ($item->getBaseWeeeTaxAppliedRowAmount() - $item->getBaseWeeeDiscount()) * $rate / 100;
                    $baseRowTax += $baseWeeeTax;
                }

                $rowTax = $this->_calculator->round($rowTax);
                $baseRowTax = $this->_calculator->round($baseRowTax);

                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax      = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax) - $rowTax;
                    $baseHiddenTax  = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax) - $baseRowTax;
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => 1,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                } elseif ($discountAmount > $subtotal) { // case with 100% discount on price incl. tax
                    $hiddenTax      = $discountAmount - $subtotal;
                    $baseHiddenTax  = $baseDiscountAmount - $baseSubtotal;
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => 1,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                }
                // calculate discount compensation
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
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
                        $rowTaxBeforeDiscount += $item->getWeeeTaxAppliedRowAmount() * $rate / 100;
                        $baseRowTaxBeforeDiscount += $item->getBaseWeeeTaxAppliedRowAmount() * $rate / 100;
                    }
                    $rowTaxBeforeDiscount = max(0, $this->_calculator->round($rowTaxBeforeDiscount));
                    $baseRowTaxBeforeDiscount = max(0, $this->_calculator->round($baseRowTaxBeforeDiscount));
                    $item->setDiscountTaxCompensation($rowTaxBeforeDiscount - max(0, $rowTax));
                    $item->setBaseDiscountTaxCompensation($baseRowTaxBeforeDiscount - max(0, $baseRowTax));
                }

                break;
        }
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax)) {
            $weeeTaxBeforeDiscount = 0;
            $baseWeeeTaxBeforeDiscount = 0;
            if ($isWeeeTaxable) {
                $weeeTaxBeforeDiscount = $item->getWeeeTaxAppliedRowAmount() * $rate/100;
                $baseWeeeTaxBeforeDiscount = $item->getBaseWeeeTaxAppliedRowAmount() * $rate/100;
            }
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal + $weeeTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseWeeeTaxBeforeDiscount);
            } else {
                $taxCompensation = $item->getDiscountTaxCompensation() ? $item->getDiscountTaxCompensation() : 0;
                $item->setRowTotalInclTax($subtotal + $rowTax + $taxCompensation);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseRowTax + $item->getBaseDiscountTaxCompensation());
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
        $items          = $this->_getAddressItems($address);
        $store          = $address->getQuote()->getStore();
        $taxGroups      = array();
        $itemTaxGroups  = array();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $applied_rates = $this->_calculator->getAppliedRates($taxRateRequest);
                    $taxGroups[(string)$rate]['applied_rates'] = $applied_rates;
                    $taxGroups[(string)$rate]['incl_tax'] = $child->getIsPriceInclTax();
                    $this->_aggregateTaxPerRate($child, $rate, $taxGroups);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied_rates;
                    }
                }
                $this->_recalculateParent($item);
            } else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $applied_rates = $this->_calculator->getAppliedRates($taxRateRequest);
                $taxGroups[(string)$rate]['applied_rates'] = $applied_rates;
                $taxGroups[(string)$rate]['incl_tax'] = $item->getIsPriceInclTax();
                $this->_aggregateTaxPerRate($item, $rate, $taxGroups);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied_rates;
                }
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);

        foreach ($taxGroups as $rateKey => $data) {
            $rate = (float) $rateKey;
            $inclTax = $data['incl_tax'];

            $totalTax = $this->_calculator->calcTaxAmount(array_sum($data['totals']), $rate, $inclTax, false);
            $totalTax += array_sum($data['weee_tax']);
            $baseTotalTax = $this->_calculator->calcTaxAmount(array_sum($data['base_totals']), $rate, $inclTax, false);
            $baseTotalTax += array_sum($data['base_weee_tax']);
            $this->_addAmount($totalTax);
            $this->_addBaseAmount($baseTotalTax);
            $totalTaxRounded = $this->_calculator->round($totalTax);
            $baseTotalTaxRounded = $this->_calculator->round($totalTaxRounded);
            $this->_saveAppliedTaxes($address, $data['applied_rates'], $totalTaxRounded, $baseTotalTaxRounded, $rate);
        }
        return $this;
    }

    /**
     * Aggregate row totals per tax rate in array
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @param   array $taxGroups
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _aggregateTaxPerRate($item, $rate, &$taxGroups)
    {
        $inclTax         = $item->getIsPriceInclTax();
        $rateKey         = (string) $rate;
        $taxSubtotal     = $subtotal     = $item->getTaxableAmount();
        $baseTaxSubtotal = $baseSubtotal = $item->getBaseTaxableAmount();

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $item->setTaxPercent($rate);

        if (!isset($taxGroups[$rateKey]['totals'])) {
            $taxGroups[$rateKey]['totals'] = array();
            $taxGroups[$rateKey]['base_totals'] = array();
            $taxGroups[$rateKey]['weee_tax'] = array();
            $taxGroups[$rateKey]['base_weee_tax'] = array();
        }

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTax = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTax = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeTax = $item->getWeeeTaxAppliedRowAmount() * $rate / 100;
                    $baseWeeeTax = $item->getBaseWeeeTaxAppliedRowAmount() * $rate / 100;
                    $rowTax  += $weeeTax;
                    $baseRowTax += $baseWeeeTax;
                    $taxGroups[$rateKey]['weee_tax'][] = $this->_deltaRound($weeeTax, $rateKey, $inclTax);
                    $taxGroups[$rateKey]['base_weee_tax'][] = $this->_deltaRound($baseWeeeTax, $rateKey, $inclTax);
                }
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

                //weee discount should be removed when calculating hidden tax
                if ($isWeeeEnabled) {
                    $discount = $discount - $item->getWeeeDiscount();
                    $baseDiscount = $baseDiscount - $item->getBaseWeeeDiscount();
                }
                $taxSubtotal = max($subtotal - $discount, 0);
                $baseTaxSubtotal = max($baseSubtotal - $baseDiscount, 0);

                $rowTax = $this->_calculator->calcTaxAmount($taxSubtotal, $rate, $inclTax, false);
                $baseRowTax = $this->_calculator->calcTaxAmount($baseTaxSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = ($item->getWeeeTaxAppliedRowAmount() - $item->getWeeeDiscount()) * $rate / 100;
                    $rowTax += $weeeTax;
                    $baseWeeeTax =
                        ($item->getBaseWeeeTaxAppliedRowAmount() - $item->getBaseWeeeDiscount()) * $rate / 100;
                    $baseRowTax += $baseWeeeTax;
                    $taxGroups[$rateKey]['weee_tax'][] = $weeeTax;
                    $taxGroups[$rateKey]['base_weee_tax'][] = $baseWeeeTax;
                }
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
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
                        $rowTaxBeforeDiscount += $item->getWeeeTaxAppliedRowAmount() * $rate / 100;
                        $baseRowTaxBeforeDiscount += $item->getBaseWeeeTaxAppliedRowAmount() * $rate / 100;
                    }
                }

                if ($inclTax && $discount > 0) {
                    $hiddenTax = $this->_calculator->calcTaxAmount($discount, $rate, $inclTax, false);
                    $baseHiddenTax = $this->_calculator->calcTaxAmount($baseDiscount, $rate, $inclTax, false);
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                break;
        }

        $rowTax     = $this->_deltaRound($rowTax, $rateKey, $inclTax);
        $baseRowTax = $this->_deltaRound($baseRowTax, $rateKey, $inclTax, 'base');
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));

        if (isset($rowTaxBeforeDiscount) && isset($baseRowTaxBeforeDiscount)) {
            $taxBeforeDiscount = max(
                0,
                $this->_deltaRound($rowTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount')
            );
            $baseTaxBeforeDiscount = max(
                0,
                $this->_deltaRound($baseRowTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount_base')
            );

            $item->setDiscountTaxCompensation($taxBeforeDiscount - max(0, $rowTax));
            $item->setBaseDiscountTaxCompensation($baseTaxBeforeDiscount - max(0, $baseRowTax));
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax)) {
            $weeeTaxBeforeDiscount = 0;
            $baseWeeeTaxBeforeDiscount = 0;
            if ($isWeeeTaxable) {
                $weeeTaxBeforeDiscount = $item->getWeeeTaxAppliedRowAmount() * $rate/100;
                $baseWeeeTaxBeforeDiscount = $item->getBaseWeeeTaxAppliedRowAmount() * $rate/100;
            }
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal + $weeeTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseWeeeTaxBeforeDiscount);
            } else {
                $taxCompensation = $item->getDiscountTaxCompensation() ? $item->getDiscountTaxCompensation() : 0;
                $item->setRowTotalInclTax($subtotal + $rowTax + $taxCompensation);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseRowTax + $item->getBaseDiscountTaxCompensation());
            }
        }

        $taxGroups[$rateKey]['totals'][]        = max(0, $taxSubtotal);
        $taxGroups[$rateKey]['base_totals'][]   = max(0, $baseTaxSubtotal);

        return $this;
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
            $rate  = (string) $rate;
            $type  = $type . $direction;
            $delta = isset($this->_roundingDeltas[$type][$rate]) ? $this->_roundingDeltas[$type][$rate] : 0;
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
        $rowTaxAmount       = 0;
        $baseRowTaxAmount   = 0;
        foreach ($item->getChildren() as $child) {
            $rowTaxAmount       += $child->getTaxAmount();
            $baseRowTaxAmount   += $child->getBaseTaxAmount();
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
                $row['process']     = $process;
                $row['amount']      = 0;
                $row['base_amount'] = 0;
                $previouslyAppliedTaxes[$row['id']] = $row;
            }

            if (!is_null($row['percent'])) {
                $row['percent'] = $row['percent'] ? $row['percent'] : 1;
                $rate = $rate ? $rate : 1;

                $appliedAmount      = $amount/$rate*$row['percent'];
                $baseAppliedAmount  = $baseAmount/$rate*$row['percent'];
            } else {
                $appliedAmount      = 0;
                $baseAppliedAmount  = 0;
                foreach ($row['rates'] as $rate) {
                    $appliedAmount      += $rate['amount'];
                    $baseAppliedAmount  += $rate['base_amount'];
                }
            }


            if ($appliedAmount || $previouslyAppliedTaxes[$row['id']]['amount']) {
                $previouslyAppliedTaxes[$row['id']]['amount']       += $appliedAmount;
                $previouslyAppliedTaxes[$row['id']]['base_amount']  += $baseAppliedAmount;
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
        $applied    = $address->getAppliedTaxes();
        $store      = $address->getQuote()->getStore();
        $amount     = $address->getTaxAmount();

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
                $taxAmount  += $address->getWeeeDiscount();
            }
        }

        $area       = null;
        if ($this->_config->displayCartTaxWithGrandTotal($store) && $address->getGrandTotal()) {
            $area   = 'taxes';
        }

        if (($amount != 0) || ($this->_config->displayCartZeroTax($store))) {
            $address->addTotal(array(
                'code'      => $this->getCode(),
                'title'     => Mage::helper('tax')->__('Tax'),
                'full_info' => $applied ? $applied : array(),
                'value'     => $amount,
                'area'      => $area
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
                'code'      => 'subtotal',
                'title'     => Mage::helper('sales')->__('Subtotal'),
                'value'     => $subtotalInclTax,
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
