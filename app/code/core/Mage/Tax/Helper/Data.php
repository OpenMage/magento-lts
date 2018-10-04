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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 */
class Mage_Tax_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Price conversion constant for positive
     */
    const PRICE_CONVERSION_PLUS = 1;

    /**
     * Price conversion constat for negative
     */
    const PRICE_CONVERSION_MINUS = 2;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config = null;

    /**
     * Tax calculator
     *
     * @var Mage_Tac_Model_Calculation
     */
    protected $_calculator = null;

    /**
     * Display tax column
     *
     * @var bool
     */
    protected $_displayTaxColumn;

    /**
     * Tax data
     *
     * @var mixed
     */
    protected $_taxData;

    /**
     * Price includes tax
     *
     * @var bool
     */
    protected $_priceIncludesTax;

    /**
     * Shipping price includes tax
     *
     * @var bool
     */
    protected $_shippingPriceIncludesTax;

    /**
     * Apply tax after discount
     *
     * @var bool
     */
    protected $_applyTaxAfterDiscount;

    /**
     * Price display type
     *
     * @var int
     */
    protected $_priceDisplayType;

    /**
     * Shipping price display type
     *
     * @var int
     */
    protected $_shippingPriceDisplayType;

    /**
     * Postcode cut to this length when creating search templates
     *
     * @var integer
     */
    protected $_postCodeSubStringLength = 10;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Initialize helper instance
     *
     * @param array $args
     */
    public function  __construct(array $args = array())
    {
        $this->_config = Mage::getSingleton('tax/config');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
    }

    /**
     * Return max postcode length to create search templates
     *
     * @return integer  $len
     */
    public function getPostCodeSubStringLength()
    {
        $len = (int)$this->_postCodeSubStringLength;
        if ($len <= 0) {
            $len = 10;
        }
        return $len;
    }

    /**
     * Get tax configuration object
     *
     * @return Mage_Tax_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Get tax calculation object
     *
     * @return  Mage_Tac_Model_Calculation
     */
    public function getCalculator()
    {
        if ($this->_calculator === null) {
            $this->_calculator = Mage::getSingleton('tax/calculation');
        }
        return $this->_calculator;
    }

    /**
     * Get product price including store conversion rate
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   null|string $format
     * @return  float|string
     */
    public function getProductPrice($product, $format = null)
    {
        try {
            $value = $product->getPrice();
            $value = $this->_app->getStore()->convertPrice($value, $format);
        } catch (Exception $e) {
            $value = $e->getMessage();
        }
        return $value;
    }

    /**
     * Check if product prices inputted include tax
     *
     * @param   mix $store
     * @return  bool
     */
    public function priceIncludesTax($store = null)
    {
        return $this->_config->priceIncludesTax($store) || $this->_config->getNeedUseShippingExcludeTax();
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   mixed $store
     * @return  bool
     */
    public function applyTaxAfterDiscount($store = null)
    {
        return $this->_config->applyTaxAfterDiscount($store);
    }

    /**
     * Output
     *
     * @param bool $flag
     * @param mixed $store
     * @return string
     */
    public function getIncExcText($flag, $store = null)
    {
        if ($flag) {
            $s = $this->__('Incl. Tax');
        } else {
            $s = $this->__('Excl. Tax');
        }
        return $s;
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return $this->_config->getPriceDisplayType($store);
    }

    /**
     * Check if necessary do product price conversion
     * If it necessary will be returned conversion type (minus or plus)
     *
     * @param   mixed $store
     * @return  false | int
     */
    public function needPriceConversion($store = null)
    {
        $res = false;
        if ($this->priceIncludesTax($store)) {
            switch ($this->getPriceDisplayType($store)) {
                case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_MINUS;
                case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                    $res = true;
            }
        } else {
            switch ($this->getPriceDisplayType($store)) {
                case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_PLUS;
                case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    $res = false;
            }
        }

        if ($res === false) {
            $res = $this->displayTaxColumn($store);
        }
        return $res;
    }

    /**
     * Check if need display full tax summary information in totals block
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayFullSummary($store = null)
    {
        return $this->_config->displayCartFullSummary($store);
    }

    /**
     * Check if need display zero tax in subtotal
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayZeroTax($store = null)
    {
        return $this->_config->displayCartZeroTax($store);
    }

    /**
     * Check if need display cart prices included tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartPriceInclTax($store = null)
    {
        return $this->_config->displayCartPricesInclTax($store);
    }

    /**
     * Check if need display cart prices excluding price
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartPriceExclTax($store = null)
    {
        return $this->_config->displayCartPricesExclTax($store);
    }

    /**
     * Check if need display cart prices excluding and including tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartBothPrices($store = null)
    {
        return $this->_config->displayCartPricesBoth($store);
    }

    /**
     * Check if need display order prices included tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesPriceInclTax($store = null)
    {
        return $this->_config->displaySalesPricesInclTax($store);
    }

    /**
     * Check if need display order prices excluding price
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesPriceExclTax($store = null)
    {
        return $this->_config->displaySalesPricesExclTax($store);
    }

    /**
     * Check if need display order prices excluding and including tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesBothPrices($store = null)
    {
        return $this->_config->displaySalesPricesBoth($store);
    }


    /**
     * Check if we need display price include and exclude tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        return $this->_config->displaySalesSubtotalBoth($store);
    }

    /**
     * Check if we need display price include tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        return $this->_config->displaySalesSubtotalInclTax($store);
    }

    /**
     * Check if we need display price exclude tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        return $this->_config->displaySalesSubtotalExclTax($store);
    }

    /**
     * Check if need display tax column in for shopping cart/order items
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayTaxColumn($store = null)
    {
        return $this->_config->displayCartPricesBoth();
    }

    /**
     * Get prices javascript format json
     *
     * @param   mixed $store
     * @return  string
     */
    public function getPriceFormat($store = null)
    {
        $this->_app->getLocale()->emulate($store);
        $priceFormat = $this->_app->getLocale()->getJsPriceFormat();
        $this->_app->getLocale()->revert();
        if ($store) {
            $priceFormat['pattern'] = $this->_app->getStore($store)->getCurrentCurrency()->getOutputFormat();
        }
        return Mage::helper('core')->jsonEncode($priceFormat);
    }

    /**
     * Get all tax rates JSON for all product tax classes
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     * @deprecated after 1.4 - please use getAllRatesByProductClass
     * @return string
     */
    public function getTaxRatesByProductClass()
    {
        return $this->_getAllRatesByProductClass();
    }

    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     *
     * @param mixed $store
     * @return string
     */
    public function getAllRatesByProductClass($store = null)
    {
        return $this->_getAllRatesByProductClass($store);
    }


    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     *
     * @param mixed $store
     * @return string
     */
    protected function _getAllRatesByProductClass($store = null)
    {
        $result = array();
        $calc = Mage::getSingleton('tax/calculation');
        $rates = $calc->getRatesForAllProductTaxClasses($calc->getDefaultRateRequest($store));

        foreach ($rates as $class => $rate) {
            $result["value_{$class}"] = $rate;
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Get product price with all tax settings processing
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   float $price inputed product price
     * @param   bool $includingTax return price include tax flag
     * @param   null|Mage_Customer_Model_Address $shippingAddress
     * @param   null|Mage_Customer_Model_Address $billingAddress
     * @param   null|int $ctc customer tax class
     * @param   null|Mage_Core_Model_Store $store
     * @param   bool $priceIncludesTax flag what price parameter contain tax
     * @return  float
     */
    public function getPrice($product, $price, $includingTax = null, $shippingAddress = null, $billingAddress = null,
                             $ctc = null, $store = null, $priceIncludesTax = null, $roundPrice = true)
    {
        if (!$price) {
            return $price;
        }
        $store = $this->_app->getStore($store);
        if (!$this->needPriceConversion($store)) {
            return $store->roundPrice($price);
        }
        if (is_null($priceIncludesTax)) {
            $priceIncludesTax = $this->priceIncludesTax($store);
        }

        $percent = $product->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $product->getTaxClassId();
        if (is_null($percent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('tax/calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
                $percent = Mage::getSingleton('tax/calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            if ($this->isCrossBorderTradeEnabled($store)) {
                $includingPercent = $percent;
            } else {
                $request = Mage::getSingleton('tax/calculation')->getRateOriginRequest($store);
                $includingPercent = Mage::getSingleton('tax/calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }

        if ($percent === false || is_null($percent)) {
            if ($priceIncludesTax && !$includingPercent) {
                return $price;
            }
        }

        $product->setTaxPercent($percent);
        if ($product->getAppliedRates() == null) {
            $request = Mage::getSingleton('tax/calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
            $request->setProductClassId($taxClassId);
            $appliedRates =  Mage::getSingleton('tax/calculation')->getAppliedRates($request);
            $product->setAppliedRates($appliedRates);
        }

        if (!is_null($includingTax)) {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    /**
                     * Recalculate price include tax in case of different rates.  Otherwise price remains the same.
                     */
                    if ($includingPercent != $percent) {
                        // determine the customer's price that includes tax
                        $price = $this->_calculatePriceInclTax($price, $includingPercent, $percent, $store);
                    }
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $appliedRates = $product->getAppliedRates();
                    if (count($appliedRates) > 1) {
                        $price = $this->_calculatePriceInclTaxWithMultipleRates($price, $appliedRates);
                    } else {
                        $price = $this->_calculatePrice($price, $percent, true);
                    }
                }
            }
        } else {
            if ($priceIncludesTax) {
                switch ($this->getPriceDisplayType($store)) {
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                        if ($includingPercent != $percent) {
                            // determine the customer's price that includes tax
                            $taxablePrice = $this->_calculatePriceInclTax($price, $includingPercent, $percent, $store);
                            // determine the customer's tax amount,
                            // round tax unless $roundPrice is set explicitly to false
                            $tax = $this->getCalculator()->calcTaxAmount($taxablePrice, $percent, true, $roundPrice);
                            // determine the customer's price without taxes
                            $price = $taxablePrice - $tax;
                        } else {
                            //round tax first unless $roundPrice is set to false explicitly
                            $price = $this->_calculatePrice($price, $includingPercent, false, $roundPrice);
                        }
                        break;

                    case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        $price = $this->_calculatePrice($price, $percent, true);
                        break;
                }
            } else {
                switch ($this->getPriceDisplayType($store)) {
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $appliedRates = $product->getAppliedRates();
                        if (count($appliedRates) > 1) {
                            $price = $this->_calculatePriceInclTaxWithMultipleRates($price, $appliedRates);
                        } else {
                            $price = $this->_calculatePrice($price, $percent, true);
                        }
                        break;

                    case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                        break;
                }
            }
        }
        if ($roundPrice) {
            return $store->roundPrice($price);
        } else {
            return $price;
        }
    }

    /**
     * Given a store price that includes tax at the store rate, this function will back out the store's tax, and add in
     * the customer's tax.  Returns this new price which is the customer's price including tax.
     *
     * @param float $storePriceInclTax
     * @param float $storePercent
     * @param float $customerPercent
     * @param Mage_Core_Model_Store $store
     * @return float
     */
    protected function _calculatePriceInclTax($storePriceInclTax, $storePercent, $customerPercent, $store)
    {
        $priceExclTax         = $this->_calculatePrice($storePriceInclTax, $storePercent, false, false);
        $customerTax          = $this->getCalculator()->calcTaxAmount($priceExclTax, $customerPercent, false, false);
        $customerPriceInclTax = $store->roundPrice($priceExclTax + $customerTax);
        return $customerPriceInclTax;
    }

    /**
     * Check if we have display in catalog prices including tax
     *
     * @return bool
     */
    public function displayPriceIncludingTax()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if we have display in catalog prices excluding tax
     *
     * @return bool
     */
    public function displayPriceExcludingTax()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if we have display in catalog prices including and excluding tax
     *
     * @param int $store
     * @return bool
     */
    public function displayBothPrices($store = null)
    {
        return $this->getPriceDisplayType($store) == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Calculate price imcluding/excluding tax base on tax rate percent
     *
     * @param   float $price
     * @param   float $percent
     * @param   bool $type true - to calculate the price including tax and false if calculating price to exclude tax
     * @param   bool $roundTaxFirst
     * @return  float
     */
    protected function _calculatePrice($price, $percent, $type, $roundTaxFirst = false)
    {
        $calculator = $this->getCalculator();
        if ($type) {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, false, $roundTaxFirst);
            return $price + $taxAmount;
        } else {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, true, $roundTaxFirst);
            return $price - $taxAmount;
        }
    }

    /**
     * Calculate price including tax when multiple taxes is applied and rounded
     * independently.
     *
     * @param foat $price
     * @param array $appliedRates
     * @return float
     */
    protected function _calculatePriceInclTaxWithMultipleRates($price, $appliedRates)
    {
        $calculator = $this->getCalculator();
        $tax = 0;
        foreach ($appliedRates as $appliedRate) {
            $taxRate = $appliedRate['percent'];
            $tax += $calculator->round($price * $taxRate / 100);
        }
        return $tax + $price;
    }

    /**
     * Returns the include / exclude tax label
     *
     * @param bool $flag
     * @return string
     */
    public function getIncExcTaxLabel($flag)
    {
        $text = $this->getIncExcText($flag);
        return $text ? ' <span class="tax-flag">(' . $text . ')</span>' : '';
    }

    /**
     * Check if shipping prices include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function shippingPriceIncludesTax($store = null)
    {
        return $this->_config->shippingPriceIncludesTax($store);
    }

    /**
     * Get shipping methods prices display type
     *
     * @param mixed $store
     * @return int
     */
    public function getShippingPriceDisplayType($store = null)
    {
        return $this->_config->getShippingPriceDisplayType($store);
    }

    /**
     * Returns whether the shipping price should display with taxes included
     *
     * @return bool
     */
    public function displayShippingPriceIncludingTax()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Returns whether the shipping price should display without taxes
     *
     * @return bool
     */
    public function displayShippingPriceExcludingTax()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Returns whether the shipping price should display both with and without taxes
     *
     * @return bool
     */
    public function displayShippingBothPrices()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param mixed $store
     * @return int
     */
    public function getShippingTaxClass($store)
    {
        return $this->_config->getShippingTaxClass($store);
    }

    /**
     * Get Shipping Price
     *
     * @param float $price
     * @param null|bool $includingTax
     * @param mixed $shippingAddress
     * @param mixed $ctc
     * @param mixed $store
     * @return float
     */
    public function getShippingPrice($price, $includingTax = null, $shippingAddress = null, $ctc = null, $store = null)
    {
        $pseudoProduct = new Varien_Object();
        $pseudoProduct->setTaxClassId($this->getShippingTaxClass($store));

        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }

        $price = $this->getPrice(
            $pseudoProduct,
            $price,
            $includingTax,
            $shippingAddress,
            $billingAddress,
            $ctc,
            $store,
            $this->shippingPriceIncludesTax($store)
        );
        return $price;
    }

    /**
     * Returns the SQL for the price tax
     *
     * @param string $priceField
     * @param string $taxClassField
     * @return string
     */
    public function getPriceTaxSql($priceField, $taxClassField)
    {
        if (!$this->priceIncludesTax() && $this->displayPriceExcludingTax()) {
            return '';
        }

        $request = Mage::getSingleton('tax/calculation')->getDefaultRateRequest();
        $defaultTaxes = Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request);

        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $currentTaxes = Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request);

        $defaultTaxString = $currentTaxString = '';

        $rateToVariable = array(
            'defaultTaxString' => 'defaultTaxes',
            'currentTaxString' => 'currentTaxes',
        );
        foreach ($rateToVariable as $rateVariable => $rateArray) {
            if ($$rateArray && is_array($$rateArray)) {
                $$rateVariable = '';
                foreach ($$rateArray as $classId => $rate) {
                    if ($rate) {
                        $$rateVariable .= sprintf("WHEN %d THEN %12.4f ", $classId, $rate / 100);
                    }
                }
                if ($$rateVariable) {
                    $$rateVariable = "CASE {$taxClassField} {$$rateVariable} ELSE 0 END";
                }
            }
        }

        $result = '';

        if ($this->priceIncludesTax()) {
            if ($defaultTaxString) {
                $result = "-({$priceField}/(1+({$defaultTaxString}))*{$defaultTaxString})";
            }
            if (!$this->displayPriceExcludingTax() && $currentTaxString) {
                $result .= "+(({$priceField}{$result})*{$currentTaxString})";
            }
        } else {
            if ($this->displayPriceIncludingTax()) {
                if ($currentTaxString) {
                    $result .= "+({$priceField}*{$currentTaxString})";
                }
            }
        }
        return $result;
    }

    /**
     * Join tax class
     * @param Varien_Db_Select $select
     * @param int $storeId
     * @param string $priceTable
     * @return Mage_Tax_Helper_Data
     */
    public function joinTaxClass($select, $storeId, $priceTable = 'main_table')
    {
        $taxClassAttribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id');
        $joinConditionD = implode(' AND ', array(
            "tax_class_d.entity_id = {$priceTable}.entity_id",
            $select->getAdapter()->quoteInto('tax_class_d.attribute_id = ?', (int)$taxClassAttribute->getId()),
            'tax_class_d.store_id = 0'
        ));
        $joinConditionC = implode(' AND ', array(
            "tax_class_c.entity_id = {$priceTable}.entity_id",
            $select->getAdapter()->quoteInto('tax_class_c.attribute_id = ?', (int)$taxClassAttribute->getId()),
            $select->getAdapter()->quoteInto('tax_class_c.store_id = ?', (int)$storeId)
        ));
        $select
            ->joinLeft(
                array('tax_class_d' => $taxClassAttribute->getBackend()->getTable()),
                $joinConditionD,
                array())
            ->joinLeft(
                array('tax_class_c' => $taxClassAttribute->getBackend()->getTable()),
                $joinConditionC,
                array());

        return $this;
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|int $store
     * @return  0|1
     */
    public function discountTax($store = null)
    {
        return $this->_config->discountTax($store);
    }

    /**
     * Get value of "Apply Tax On" custom/original price configuration settings.
     * Result is 0 or 1
     *
     * @param mixed $store
     * @return mixed
     */
    public function getTaxBasedOn($store = null)
    {
        return Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);
    }

    /**
     * Check if tax can be applied to custom price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnCustomPrice($store = null)
    {
        return ((int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 0);
    }

    /**
     * Check if tax should be applied just to original price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnOriginalPrice($store = null)
    {
        return ((int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 1);
    }

    /**
     * Get taxes/discounts calculation sequence.
     * This sequence depends on "Catalog price include tax", "Apply Tax After Discount"
     * and "Apply Discount On Prices Including Tax" configuration options.
     *
     * @param   null|int|string|Mage_Core_Model_Store $store
     * @return  string
     */
    public function getCalculationSequence($store = null)
    {
        return $this->_config->getCalculationSequence($store);
    }

    /**
     * Get tax calculation algorithm code
     *
     * @param   null|int $store
     * @return  string
     */
    public function getCalculationAgorithm($store = null)
    {
        return $this->_config->getAlgorithm($store);
    }

    /**
     * Get calculated taxes for each tax class
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount,
     *      'title'             => $title,
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Mage_Sales_Model_Order $source
     * @return array
     */
    public function getCalculatedTaxes($source)
    {
        if ($this->_getFromRegistry('current_invoice')) {
            $current = $this->_getFromRegistry('current_invoice');
        } elseif ($this->_getFromRegistry('current_creditmemo')) {
            $current = $this->_getFromRegistry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            if ($current == $source) {
                // use the actuals
                $rates = $this->_getTaxRateSubtotals($source);
                foreach ($rates['items'] as $rate) {
                    $taxClassId = $rate['tax_id'];
                    $taxClassAmount[$taxClassId]['tax_amount'] = $rate['amount'];
                    $taxClassAmount[$taxClassId]['base_tax_amount'] = $rate['base_amount'];
                    $taxClassAmount[$taxClassId]['title'] = $rate['title'];
                    $taxClassAmount[$taxClassId]['percent'] = $rate['percent'];
                }
            } else {
                // regenerate tax subtotals
                // Calculate taxes for shipping
                $shippingTaxAmount = $current->getShippingTaxAmount();
                if ($shippingTaxAmount) {
                    $shippingTax    = Mage::helper('tax')->getShippingTax($current);
                    $taxClassAmount = array_merge($taxClassAmount, $shippingTax);
                }

                foreach ($current->getItemsCollection() as $item) {
                    $taxCollection = Mage::getResourceModel('tax/sales_order_tax_item')
                        ->getTaxItemsByItemId(
                            $item->getOrderItemId() ? $item->getOrderItemId() : $item->getItemId()
                        );

                    foreach ($taxCollection as $tax) {
                        $taxClassId = $tax['tax_id'];
                        $percent = $tax['tax_percent'];

                        $price = $item->getRowTotal();
                        $basePrice = $item->getBaseRowTotal();
                        if ($this->applyTaxAfterDiscount($item->getStoreId())) {
                            $price = $price - $item->getDiscountAmount() + $item->getHiddenTaxAmount();
                            $basePrice = $basePrice - $item->getBaseDiscountAmount() + $item->getBaseHiddenTaxAmount();
                        }
                        $tax_amount = $price * $percent / 100;
                        $base_tax_amount = $basePrice * $percent / 100;

                        if (isset($taxClassAmount[$taxClassId])) {
                            $taxClassAmount[$taxClassId]['tax_amount'] += $tax_amount;
                            $taxClassAmount[$taxClassId]['base_tax_amount'] += $base_tax_amount;
                        } else {
                            $taxClassAmount[$taxClassId]['tax_amount'] = $tax_amount;
                            $taxClassAmount[$taxClassId]['base_tax_amount'] = $base_tax_amount;
                            $taxClassAmount[$taxClassId]['title'] = $tax['title'];
                            $taxClassAmount[$taxClassId]['percent'] = $tax['percent'];
                        }
                    }
                }
            }

            foreach ($taxClassAmount as $key => $tax) {
                if ($tax['tax_amount'] == 0 && $tax['base_tax_amount'] == 0) {
                    unset($taxClassAmount[$key]);
                }
            }

            $taxClassAmount = array_values($taxClassAmount);
        }

        return $taxClassAmount;
    }

    /**
     * Returns the array of tax rates for the order
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function _getTaxRateSubtotals($order)
    {
        return Mage::getModel('tax/sales_order_tax')->getCollection()->loadByOrder($order)->toArray();
    }

    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    protected function _getFromRegistry($key)
    {
        return Mage::registry($key);
    }

    /**
     * Get calculated Shipping & Handling Tax
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount
     *      'title'             => $title
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Mage_Sales_Model_Order $source
     * @return array
     */
    public function getShippingTax($source)
    {
        if (Mage::registry('current_invoice')) {
            $current = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $current = Mage::registry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            if ($current->getShippingTaxAmount() != 0 && $current->getBaseShippingTaxAmount() != 0) {
                $taxClassAmount[0]['tax_amount'] = $current->getShippingTaxAmount();
                $taxClassAmount[0]['base_tax_amount'] = $current->getBaseShippingTaxAmount();
                if ($current->getShippingHiddenTaxAmount() > 0) {
                    $taxClassAmount[0]['hidden_tax_amount'] = $current->getShippingHiddenTaxAmount();
                }
                $taxClassAmount[0]['title'] = $this->__('Shipping & Handling Tax');
                $taxClassAmount[0]['percent'] = NULL;
            }
        }

        return $taxClassAmount;
    }

    /**
     * Get all FPTs
     *
     * @return array
     */
    public function getAllWeee($source = null)
    {
        $allWeee = array();
        $store = $this->_app->getStore();

        if (Mage::registry('current_invoice')) {
            $source = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $source = Mage::registry('current_creditmemo');
        } elseif ($source == null) {
            $source = $this->_app->getOrder();
        }

        $helper = Mage::helper('weee');
        if (!$helper->includeInSubtotal($store)) {
            foreach ($source->getAllItems() as $item) {
                foreach ($helper->getApplied($item) as $tax) {
                    $weeeDiscount = isset($tax['weee_discount']) ? $tax['weee_discount'] : 0;
                    $title = $tax['title'];

                    $rowAmount = isset($tax['row_amount']) ? $tax['row_amount'] : 0;
                    $rowAmountInclTax = isset($tax['row_amount_incl_tax']) ? $tax['row_amount_incl_tax'] : 0;
                    $amountDisplayed = ($helper->isTaxIncluded()) ? $rowAmountInclTax : $rowAmount;

                    if (array_key_exists($title, $allWeee)) {
                        $allWeee[$title] = $allWeee[$title] + $amountDisplayed - $weeeDiscount;
                    } else {
                        $allWeee[$title] = $amountDisplayed - $weeeDiscount;
                    }
                }
            }
        }

        return $allWeee;
    }

    /**
     * Check if do not show notification about wrong display settings
     *
     * @return bool
     */
    public function isWrongDisplaySettingsIgnored()
    {
        return (bool)$this->_app->getStore()->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_PRICE_DISPLAY);
    }

    /**
     * Check if do not show notification about wrong discount settings
     *
     * @return bool
     */
    public function isWrongDiscountSettingsIgnored()
    {
        return (bool)$this->_app->getStore()->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_DISCOUNT);
    }

    /**
     * Check if warning about conflicting FPT configuration should be shown
     *
     * @return bool
     */
    public function isConflictingFptTaxConfigurationSettingsIgnored()
    {
        return (bool) $this->_app->getStore()
            ->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_FPT_CONFIGURATION);
    }

    /**
     * Return whether cross border trade is enabled or not
     *
     * @param   null|int $store
     * @return boolean
     */
    public function isCrossBorderTradeEnabled($store = null)
    {
        return (bool)$this->_config->crossBorderTradeEnabled($store);
    }
}
