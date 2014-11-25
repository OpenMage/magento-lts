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
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Model to calculate Weee amount
 */
class Mage_Weee_Model_Tax extends Mage_Core_Model_Abstract
{
    /**
     * Including FPT only
     */
    const DISPLAY_INCL              = 0;
    /**
     * Including FPT and FPT description
     */
    const DISPLAY_INCL_DESCR        = 1;
    /**
     * Excluding FPT, FPT description, final price
     */
    const DISPLAY_EXCL_DESCR_INCL   = 2;
    /**
     * Excluding FPT
     */
    const DISPLAY_EXCL              = 3;

    /**
     * All weee attributes
     *
     * @var array
     */
    protected $_allAttributes = null;

    /**
     * Cache product discounts
     *
     * @var array
     */
    protected $_productDiscounts = array();

    /**
     * Tax helper
     *
     * @var Mage_Tax_Helper_Data
     */
    protected $_taxHelper;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('weee/tax', 'weee/tax');
    }


    /**
     * Initialize tax helper
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct();
        $this->_taxHelper = !empty($args['helper']) ? $args['helper'] : Mage::helper('tax');
    }

    /**
     * Calculate weee amount for a product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Customer_Model_Address_Abstract $shipping
     * @param Mage_Customer_Model_Address_Abstract $billing
     * @param mixed $website
     * @param boolean $calculateTax
     * @param boolean $ignoreDiscount
     * @return float
     */
    public function getWeeeAmount(
        $product,
        $shipping = null,
        $billing = null,
        $website = null,
        $calculateTax = false,
        $ignoreDiscount = false)
    {
        $amount = 0;
        $attributes = $this->getProductWeeeAttributes(
            $product,
            $shipping,
            $billing,
            $website,
            $calculateTax,
            $ignoreDiscount
        );
        foreach ($attributes as $attribute) {
            $amount += $attribute->getAmount();
        }
        return $amount;
    }

    /**
     * Get a list of Weee attribute codes
     *
     * @param boolean $forceEnabled
     * @return array
     */
    public function getWeeeAttributeCodes($forceEnabled = false)
    {
        return $this->getWeeeTaxAttributeCodes($forceEnabled);
    }

    /**
     * Retrieve Weee tax attribute codes
     *
     * @param bool $forceEnabled
     * @return array
     */
    public function getWeeeTaxAttributeCodes($forceEnabled = false)
    {
        if (!$forceEnabled && !Mage::helper('weee')->isEnabled()) {
            return array();
        }

        if (is_null($this->_allAttributes)) {
            $this->_allAttributes = Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('weee');
        }
        return $this->_allAttributes;
    }

    /**
     * Get Weee amounts associated with a product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Customer_Model_Address_Abstract $shipping
     * @param Mage_Customer_Model_Address_Abstract $billing
     * @param mixed $website
     * @param boolean $calculateTax
     * @param boolean $ignoreDiscount
     * @return array|\Varien_Object
     */
    public function getProductWeeeAttributes(
        $product,
        $shipping = null,
        $billing = null,
        $website = null,
        $calculateTax = null,
        $ignoreDiscount = false)
    {
        $result = array();
        $allWeee = $this->getWeeeTaxAttributeCodes();
        if (!$allWeee) {
            return $result;
        }

        $websiteId = Mage::app()->getWebsite($website)->getId();
        $store = Mage::app()->getWebsite($website)->getDefaultGroup()->getDefaultStore();
        $customer = null;

        if ($shipping) {
            $customerTaxClass = $shipping->getQuote()->getCustomerTaxClassId();
            $customer = $shipping->getQuote()->getCustomer();
        } else {
            $customerTaxClass = null;
        }

        $calculator = Mage::getModel('tax/calculation');
        if ($customer) {
            $calculator->setCustomer($customer);
        }
        $rateRequest = $calculator->getRateRequest($shipping, $billing, $customerTaxClass, $store);

        $currentPercent = $product->getTaxPercent();

        if (!$currentPercent) {
            $currentPercent = Mage::getSingleton('tax/calculation')->getRate(
                $rateRequest->setProductClassId($product->getTaxClassId()));
        }

        $discountPercent = 0;

        if (!$ignoreDiscount && Mage::helper('weee')->isDiscounted($store)) {
            $discountPercent = $this->_getDiscountPercentForProduct($product);
        }

        $productAttributes = $product->getTypeInstance(true)->getSetAttributes($product);
        foreach ($productAttributes as $code => $attribute) {
            if (in_array($code, $allWeee)) {
                $attributeSelect = $this->getResource()->getReadConnection()->select();
                $attributeSelect
                    ->from($this->getResource()->getTable('weee/tax'), 'value')
                    ->where('attribute_id = ?', (int)$attribute->getId())
                    ->where('website_id IN(?)', array($websiteId, 0))
                    ->where('country = ?', $rateRequest->getCountryId())
                    ->where('state IN(?)', array($rateRequest->getRegionId(), '*'))
                    ->where('entity_id = ?', (int)$product->getId())
                    ->limit(1);

                $order = array('state ' . Varien_Db_Select::SQL_DESC, 'website_id ' . Varien_Db_Select::SQL_DESC);
                $attributeSelect->order($order);
                $value = $this->getResource()->getReadConnection()->fetchOne($attributeSelect);

                if ($value) {
                    if ($discountPercent) {
                        $value = Mage::app()->getStore()->roundPrice($value - ($value * $discountPercent / 100));
                    }

                    $taxAmount = 0;
                    $amount    = $value;
                    if ($calculateTax && Mage::helper('weee')->isTaxable($store)) {
                        if ($this->_taxHelper->isCrossBorderTradeEnabled($store)) {
                            $defaultPercent = $currentPercent;
                        } else {
                            $defaultRateRequest = $calculator->getDefaultRateRequest($store);
                            $defaultPercent = Mage::getModel('tax/calculation')
                                ->getRate($defaultRateRequest
                                ->setProductClassId($product->getTaxClassId()));
                        }

                        if (Mage::helper('weee')->isTaxIncluded($store)) {
                            $taxAmount = Mage::app()->getStore()
                                    ->roundPrice($value / (100 + $defaultPercent) * $currentPercent);
                            $amount =  $amount - $taxAmount;
                        } else {
                            $appliedRates = Mage::getModel('tax/calculation')->getAppliedRates($rateRequest);
                            if (count($appliedRates) > 1) {
                                $taxAmount = 0;
                                foreach ($appliedRates as $appliedRate) {
                                    $taxRate = $appliedRate['percent'];
                                    $taxAmount += Mage::app()->getStore()->roundPrice($value * $taxRate / 100);
                                }
                            } else {
                                $taxAmount = Mage::app()->getStore()->roundPrice($value * $currentPercent / 100);
                            }
                        }
                    }

                    $one = new Varien_Object();
                    $one->setName(Mage::helper('catalog')->__($attribute->getFrontend()->getLabel()))
                        ->setAmount($amount)
                        ->setTaxAmount($taxAmount)
                        ->setCode($attribute->getAttributeCode());

                    $result[] = $one;
                }
            }
        }
        return $result;
    }

    /**
     * Get discount percentage for a product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function _getDiscountPercentForProduct($product)
    {
        $website = Mage::app()->getStore()->getWebsiteId();
        $group = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $key = implode('-', array($website, $group, $product->getId()));
        if (!isset($this->_productDiscounts[$key])) {
            $this->_productDiscounts[$key] = (int) $this->getResource()
                ->getProductDiscountPercent($product->getId(), $website, $group);
        }
        $value = $this->_productDiscounts[$key];
        if ($value) {
            return 100 - min(100, max(0, $value));
        } else {
            return 0;
        }
    }

    /**
     * Update discounts for FPT amounts of all products
     *
     * @return Mage_Weee_Model_Tax
     */
    public function updateDiscountPercents()
    {
        $this->getResource()->updateDiscountPercents();
        return $this;
    }

    /**
     * Update discounts for FPT amounts base on products condiotion
     *
     * @param  mixed $products
     * @return Mage_Weee_Model_Tax
     */
    public function updateProductsDiscountPercent($products)
    {
        $this->getResource()->updateProductsDiscountPercent($products);
        return $this;
    }
}
