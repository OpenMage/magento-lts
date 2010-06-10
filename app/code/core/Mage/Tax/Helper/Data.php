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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 */
class Mage_Tax_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PRICE_CONVERSION_PLUS = 1;
    const PRICE_CONVERSION_MINUS = 2;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config      = null;
    protected $_calculator  = null;
    protected $_displayTaxColumn;
    protected $_taxData;
    protected $_priceIncludesTax;
    protected $_shippingPriceIncludesTax;
    protected $_applyTaxAfterDiscount;
    protected $_priceDisplayType;
    protected $_shippingPriceDisplayType;

    /**
     * Postcode cut to this length when creating search templates
     *
     * @var integer
     */
    protected $_postCodeSubStringLength = 10;

    public function  __construct()
    {
        $this->_config = Mage::getSingleton('tax/config');
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
     * Get product price including store convertion rate
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   null|string $format
     * @return  float|string
     */
    public function getProductPrice($product, $format=null)
    {
        try {
            $value = $product->getPrice();
            $value = Mage::app()->getStore()->convertPrice($value, $format);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }
        return $value;
    }

    /**
     * Check if product prices inputed include tax
     *
     * @param   mix $store
     * @return  bool
     */
    public function priceIncludesTax($store=null)
    {
        return $this->_config->priceIncludesTax($store) || $this->_config->getNeedUseShippingExcludeTax();
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   mixed $store
     * @return  bool
     */
    public function applyTaxAfterDiscount($store=null)
    {
        return $this->_config->applyTaxAfterDiscount($store);
    }

    /**
     * Output
     *
     * @param boolean $includes
     */
    public function getIncExcText($flag, $store=null)
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
        return Mage::helper('core')->jsonEncode(Mage::app()->getLocale()->getJsPriceFormat());
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
     * @return string
     */
    public function getAllRatesByProductClass($store=null)
    {
        return $this->_getAllRatesByProductClass($store);
    }


    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     * @return string
     */
    protected function _getAllRatesByProductClass($store=null)
    {
        $result = array();
        $calc = Mage::getSingleton('tax/calculation');
        $rates = $calc->getRatesForAllProductTaxClasses($calc->getRateOriginRequest($store));

        foreach ($rates as $class=>$rate) {
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
     * @param   mixed $store
     * @param   bool $priceIncludesTax flag what price parameter contain tax
     * @return  float
     */
    public function getPrice($product, $price, $includingTax = null, $shippingAddress = null, $billingAddress = null, $ctc = null, $store = null, $priceIncludesTax = null)
    {
        if (!$price) {
            return $price;
        }
        $store = Mage::app()->getStore($store);
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
                $request = Mage::getSingleton('tax/calculation')->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
                $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
            $includingPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
        }

        if ($percent === false || is_null($percent)) {
            if ($priceIncludesTax && !$includingPercent) {
                return $price;
            }
        }

        $product->setTaxPercent($percent);

        if (!is_null($includingTax)) {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    /**
                     * Recalculate price include tax in case of different rates
                     */
                    if ($includingPercent != $percent) {
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        /**
                         * Using regular rounding. Ex:
                         * price incl tax   = 52.76
                         * store tax rate   = 19.6%
                         * customer tax rate= 19%
                         *
                         * price excl tax = 52.76 / 1.196 = 44.11371237 ~ 44.11
                         * tax = 44.11371237 * 0.19 = 8.381605351 ~ 8.38
                         * price incl tax = 52.49531773 ~ 52.50 != 52.49
                         *
                         * that why we need round prices excluding tax before applying tax
                         * this calculation is used for showing prices on catalog pages
                         */
                        if ($percent != 0) {
                            $price = $this->getCalculator()->round($price);
                            $price = $this->_calculatePrice($price, $percent, true);
                        }
                    }
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            }
        } else {
            if ($priceIncludesTax) {
                switch ($this->getPriceDisplayType($store)) {
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        break;

                    case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        $price = $this->_calculatePrice($price, $percent, true);
                        break;
                }
            } else {
                switch ($this->getPriceDisplayType($store)) {
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $price = $this->_calculatePrice($price, $percent, true);
                        break;

                    case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                        break;
                }
            }
        }
        return $store->roundPrice($price);
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
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Calculate price imcluding/excluding tax base on tax rate percent
     *
     * @param   float $price
     * @param   float $percent
     * @param   bool $type true - for calculate price including tax and false if price excluding tax
     * @return  float
     */
    protected function _calculatePrice($price, $percent, $type)
    {
        if ($type) {
            return $price * (1+($percent/100));
        } else {
            return $price/(1+$percent/100);
        }
    }

    public function getIncExcTaxLabel($flag)
    {
        $text = $this->getIncExcText($flag);
        return $text ? ' <span class="tax-flag">('.$text.')</span>' : '';
    }

    public function shippingPriceIncludesTax($store = null)
    {
        return $this->_config->shippingPriceIncludesTax($store);
    }

    public function getShippingPriceDisplayType($store = null)
    {
        return $this->_config->getShippingPriceDisplayType($store);
    }

    public function displayShippingPriceIncludingTax()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayShippingPriceExcludingTax()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displayShippingBothPrices()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    public function getShippingTaxClass($store)
    {
        return $this->_config->getShippingTaxClass($store);
    }

    /**
     * Get shipping price
     *
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

    public function getPriceTaxSql($priceField, $taxClassField)
    {
        if (!$this->priceIncludesTax() && $this->displayPriceExcludingTax()) {
            return '';
        }

        $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $defaultTaxes = Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request);

        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $currentTaxes = Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request);

        $defaultTaxString = $currentTaxString = '';

        $rateToVariable = array(
                            'defaultTaxString'=>'defaultTaxes',
                            'currentTaxString'=>'currentTaxes',
                            );
        foreach ($rateToVariable as $rateVariable=>$rateArray) {
            if ($$rateArray && is_array($$rateArray)) {
                $$rateVariable = '';
                foreach ($$rateArray as $classId=>$rate) {
                    if ($rate) {
                        $$rateVariable .= "WHEN '{$classId}' THEN '".($rate/100)."'";
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
                $result  = "-({$priceField}/(1+({$defaultTaxString}))*{$defaultTaxString})";
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

    public function joinTaxClass($select, $storeId, $priceTable='main_table')
    {
        $taxClassAttribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'tax_class_id');
        $select->joinLeft(
            array('tax_class_d'=>$taxClassAttribute->getBackend()->getTable()),
            "tax_class_d.entity_id = {$priceTable}.entity_id AND tax_class_d.attribute_id = '{$taxClassAttribute->getId()}'
            AND tax_class_d.store_id = 0",
             array());
        $select->joinLeft(
            array('tax_class_c'=>$taxClassAttribute->getBackend()->getTable()),
            "tax_class_c.entity_id = {$priceTable}.entity_id AND tax_class_c.attribute_id = '{$taxClassAttribute->getId()}'
            AND tax_class_c.store_id = '{$storeId}'",
            array());
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|int $store
     * @return  0|1
     */
    public function discountTax($store=null)
    {
        return $this->_config->discountTax($store);
    }

    /**
     * Get value of "Apply Tax On" custom/original price configuration settings
     *
     * @param $store
     * @return 0|1
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
        return ((int) Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 0);
    }

    /**
     * Check if tax should be applied just to original price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnOriginalPrice($store = null)
    {
        return ((int) Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 1);
    }

    /**
     * Get taxes/discounts calculation sequence.
     * This sequence depends on "Catalog price include tax", "Apply Tax After Discount"
     * and "Apply Discount On Prices Including Tax" configuration options.
     *
     * @param   null|int|string|Mage_Core_Model_Store $store
     * @return  string
     */
    public function getCalculationSequence($store=null)
    {
        return $this->_config->getCalculationSequence($store);
    }

    /**
     * Get tax caclulation algorithm code
     *
     * @param   null|int $store
     * @return  string
     */
    public function getCalculationAgorithm($store=null)
    {
        return $this->_config->getAlgorithm($store);
    }
}
