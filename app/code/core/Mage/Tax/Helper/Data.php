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
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 */
class Mage_Tax_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PRICE_CONVERSION_PLUS = 1;
    const PRICE_CONVERSION_MINUS = 2;


    protected $_displayTaxColumn;
    protected $_taxData;
    protected $_priceIncludesTax;
    protected $_shippingPriceIncludesTax;
    protected $_applyTaxAfterDiscount;
    protected $_priceDisplayType;
    protected $_shippingPriceDisplayType;

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

    public function priceIncludesTax($store=null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_priceIncludesTax[$storeId])) {
            $this->_priceIncludesTax[$storeId] =
                (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
        }
        return $this->_priceIncludesTax[$storeId];
    }

    public function applyTaxAfterDiscount($store=null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_applyTaxAfterDiscount[$storeId])) {
            $this->_applyTaxAfterDiscount[$storeId] =
                (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $store);
        }
        return $this->_applyTaxAfterDiscount[$storeId];
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

/*
    public function updateProductTax($product)
    {
        $store = Mage::app()->getStore($product->getStoreId());
        $taxRatio = $this->getCatalogTaxRate($product->getTaxClassId(), null, $store);
        if (false===$taxRatio) {
            return false;
        }
        $taxRatio /= 100;
        $product->setPriceAfterTax($store->roundPrice($product->getPrice()*(1+$taxRatio)));
        $product->setFinalPriceAfterTax($store->roundPrice($product->getFinalPrice()*(1+$taxRatio)));
        $product->setShowTaxInCatalog(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHOW_IN_CATALOG, $store));

        return $taxRatio;
    }
*/
    public function getPriceDisplayType($store = null)
    {

        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_priceDisplayType[$storeId])) {
            $this->_priceDisplayType[$storeId] =
                (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, $store);
        }
        return $this->_priceDisplayType[$storeId];
    }

    public function needPriceConversion($store = null)
    {
        if ($this->priceIncludesTax($store)) {
            switch ($this->getPriceDisplayType($store)) {
                case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_MINUS;

                case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                    return false;
            }
        } else {
            switch ($this->getPriceDisplayType($store)) {
                case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                    return self::PRICE_CONVERSION_PLUS;

                case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    return false;
            }
        }
        return false;
    }

    public function displayFullSummary($store = null)
    {
        return ((int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY, $store) == 1);
    }

    public function displayCartPriceInclTax($store = null)
    {
        return $this->displayTaxColumn($store) == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayCartPriceExclTax($store = null)
    {
        return $this->displayTaxColumn($store) == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displayCartBothPrices($store = null)
    {
        return $this->displayTaxColumn($store) == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    public function displayTaxColumn($store = null)
    {
        if (is_null($this->_displayTaxColumn)) {
            $this->_displayTaxColumn = (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DISPLAY_TAX_COLUMN, $store);
        }
        return $this->_displayTaxColumn;
    }

    public function getPriceFormat($store = null)
    {
        return Zend_Json::encode(Mage::app()->getLocale()->getJsPriceFormat());
    }

    public function getTaxRatesByProductClass()
    {
        $result = array();
        $calc = Mage::getSingleton('tax/calculation');
        $rates = $calc->getRatesForAllProductTaxClasses($calc->getRateRequest());

        foreach ($rates as $class=>$rate) {
            $result["value_{$class}"] = $rate;
        }

        return Zend_Json::encode($result);
    }

    public function getPrice($product, $price, $includingTax = null, $shippingAddress = null, $billingAddress = null, $ctc = null, $store = null, $priceIncludesTax = null)
    {
        if (is_null($priceIncludesTax)) {
            $priceIncludesTax = $this->priceIncludesTax();
        }
        $store = Mage::app()->getStore($store);
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
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
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
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                    $price = $this->_calculatePrice($price, $percent, true);
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

    public function displayPriceIncludingTax()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayPriceExcludingTax()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displayBothPrices()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    protected function _calculatePrice($price, $percent, $type)
    {
        $store = Mage::app()->getStore();
        if ($type) {
            return $price * (1+($percent/100));
        } else {
            return $price - ($price/(100+$percent)*$percent);
        }
    }

    public function getIncExcTaxLabel($flag)
    {
        $text = $this->getIncExcText($flag);
        return $text ? ' <span class="tax-flag">('.$text.')</span>' : '';
    }

    public function shippingPriceIncludesTax($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_shippingPriceIncludesTax[$storeId])) {
            $this->_shippingPriceIncludesTax[$storeId] =
                (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX, $store);
        }
        return $this->_shippingPriceIncludesTax[$storeId];
    }

    public function getShippingPriceDisplayType($store = null)
    {

        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_shippingPriceDisplayType[$storeId])) {
            $this->_shippingPriceDisplayType[$storeId] =
                (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DISPLAY_SHIPPING, $store);
        }
        return $this->_shippingPriceDisplayType[$storeId];
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
        return (int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
    }

    public function getShippingPrice($price, $includingTax = null, $shippingAddress = null, $ctc = null, $store = null){
        $pseudoProduct = new Varien_Object();
        $pseudoProduct->setTaxClassId($this->getShippingTaxClass($store));

        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }

        return $this->getPrice($pseudoProduct, $price, $includingTax, $shippingAddress, $billingAddress, $ctc, $store, $this->shippingPriceIncludesTax($store));
    }

    public function getPriceTaxSql($priceField, $taxClassField)
    {
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

    public function discountTax($store=null)
    {
        return ((int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DISCOUNT_TAX, $store) == 1);
    }

    public function getTaxBasedOn($store = null)
    {
        return Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);
    }

    public function applyTaxOnCustomPrice($store = null) {
        return ((int) Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store) == 0);
    }

    public function applyTaxOnOriginalPrice($store = null) {
        return ((int) Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store) == 1);
    }
}
