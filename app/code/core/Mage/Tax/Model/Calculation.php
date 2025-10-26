<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Calculation Model
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Calculation _getResource()
 * @method Mage_Tax_Model_Resource_Calculation getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Collection getCollection()
 *
 * @method $this setCalculationProcess(array $value)
 * @method array getCalculationProcess()
 * @method $this unsCalculationProcess()
 * @method $this unsEventModuleId()
 * @method string getRateId()
 * @method string getRateTitle()
 * @method bool hasRateValue()
 * @method string getRateValue()
 * @method $this setRateValue(string $value)
 * @method $this unsRateValue()
 */
class Mage_Tax_Model_Calculation extends Mage_Core_Model_Abstract
{
    /**
     * Identifier constant for Tax calculation before discount excluding TAX
     */
    public const CALC_TAX_BEFORE_DISCOUNT_ON_EXCL      = '0_0';

    /***/

    /**
     * Identifier constant for Tax calculation before discount including TAX
     */
    public const CALC_TAX_BEFORE_DISCOUNT_ON_INCL      = '0_1';

    /**
     * Identifier constant for Tax calculation after discount excluding TAX
     */
    public const CALC_TAX_AFTER_DISCOUNT_ON_EXCL       = '1_0';

    /**
     * Identifier constant for Tax calculation after discount including TAX
     */
    public const CALC_TAX_AFTER_DISCOUNT_ON_INCL       = '1_1';

    /**
     * Identifier constant for unit based calculation
     */
    protected $_rates                           = [];

    /**
     * Identifier constant for row based calculation
     */
    protected $_ctc                             = [];

    /**
     * Identifier constant for total based calculation
     */
    protected $_ptc                             = [];

    /**
     * CALC_UNIT_BASE
     */
    public const CALC_UNIT_BASE = 'UNIT_BASE_CALCULATION';

    /**
     * CALC_ROW_BASE
     */
    public const CALC_ROW_BASE = 'ROW_BASE_CALCULATION';

    /**
     * CALC_TOTAL_BASE
     */
    public const CALC_TOTAL_BASE = 'TOTAL_BASE_CALCULATION';

    /**
     * Cache to hold the rates
     *
     * @var array
     */
    protected $_rateCache = [];

    /**
     * Store the rate calculation process
     *
     * @var array
     */
    protected $_rateCalculationProcess = [];

    /**
     * Hold the customer
     *
     * @var Mage_Customer_Model_Customer|false
     */
    protected $_customer = null;

    /**
     * Customer group
     *
     * @var string
     */
    protected $_defaultCustomerTaxClass = null;

    /**
     * Tax helper
     *
     * @var Mage_Tax_Helper_Data
     */
    protected $_taxHelper;

    protected function _construct()
    {
        $this->_init('tax/calculation');
    }

    /**
     * Initialize tax helper
     */
    public function __construct(array $args = [])
    {
        parent::__construct();
        $this->_taxHelper = !empty($args['helper']) ? $args['helper'] : Mage::helper('tax');
    }

    /**
     * Specify customer object which can be used for rate calculation
     *
     * @return  Mage_Tax_Model_Calculation
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Get the customer default customer class
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getDefaultCustomerTaxClass($store = null)
    {
        if ($this->_defaultCustomerTaxClass === null) {
            $defaultCustomerGroup = Mage::helper('customer')->getDefaultCustomerGroupId($store);
            $this->_defaultCustomerTaxClass = Mage::getModel('customer/group')->getTaxClassId($defaultCustomerGroup);
        }

        return $this->_defaultCustomerTaxClass;
    }

    /**
     * Get customer object
     *
     * @return Mage_Customer_Model_Customer|false
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            $session = Mage::getSingleton('customer/session');
            if ($session->isLoggedIn()) {
                $this->_customer = $session->getCustomer();
            } elseif ($session->getCustomerId()) {
                $this->_customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
            } else {
                $this->_customer = false;
            }
        }

        return $this->_customer;
    }

    /**
     * Delete calculation settings by rule id
     *
     * @param   int $ruleId
     * @return  Mage_Tax_Model_Calculation
     */
    public function deleteByRuleId($ruleId)
    {
        $this->_getResource()->deleteByRuleId($ruleId);
        return $this;
    }

    /**
     * Get calculation rates by rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getRates($ruleId)
    {
        if (!isset($this->_rates[$ruleId])) {
            $this->_rates[$ruleId] = $this->_getResource()->getDistinct('tax_calculation_rate_id', $ruleId);
        }

        return $this->_rates[$ruleId];
    }

    /**
     * Get allowed customer tax classes by rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getCustomerTaxClasses($ruleId)
    {
        if (!isset($this->_ctc[$ruleId])) {
            $this->_ctc[$ruleId] = $this->_getResource()->getDistinct('customer_tax_class_id', $ruleId);
        }

        return $this->_ctc[$ruleId];
    }

    /**
     * Get allowed product tax classes by rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getProductTaxClasses($ruleId)
    {
        if (!isset($this->_ptc[$ruleId])) {
            $this->_ptc[$ruleId] = $this->getResource()->getDistinct('product_tax_class_id', $ruleId);
        }

        return $this->_ptc[$ruleId];
    }

    /**
     * Aggregate tax calculation data to array
     *
     * @return array
     */
    protected function _formCalculationProcess()
    {
        $title = $this->getRateTitle();
        $value = $this->getRateValue();
        $id = $this->getRateId();

        $rate = [
            'code' => $title, 'title' => $title, 'percent' => $value, 'position' => 1, 'priority' => 1];

        $process = [];
        $process['percent'] = $value;
        $process['id'] = "{$id}-{$value}";
        $process['rates'][] = $rate;

        return [$process];
    }

    /**
     * Get calculation tax rate by specific request
     *
     * @param   Varien_Object $request
     * @return  float
     */
    public function getRate($request)
    {
        if (!$request->getCountryId() || !$request->getCustomerClassId() || !$request->getProductClassId()) {
            return 0;
        }

        $cacheKey = $this->_getRequestCacheKey($request);
        if (!isset($this->_rateCache[$cacheKey])) {
            $this->unsRateValue();
            $this->unsCalculationProcess();
            $this->unsEventModuleId();
            Mage::dispatchEvent('tax_rate_data_fetch', [
                'request' => $request]);
            if (!$this->hasRateValue()) {
                $rateInfo = $this->_getResource()->getRateInfo($request);
                $this->setCalculationProcess($rateInfo['process']);
                $this->setRateValue($rateInfo['value']);
            } else {
                $this->setCalculationProcess($this->_formCalculationProcess());
            }

            $this->_rateCache[$cacheKey] = $this->getRateValue();
            $this->_rateCalculationProcess[$cacheKey] = $this->getCalculationProcess();
        }

        return $this->_rateCache[$cacheKey];
    }

    /**
     * Get cache key value for specific tax rate request
     *
     * @param Varien_Object $request
     * @return string
     */
    protected function _getRequestCacheKey($request)
    {
        $key = $request->getStore() ? $request->getStore()->getId() . '|' : '';
        return $key . ($request->getProductClassId() . '|' . $request->getCustomerClassId() . '|'
            . $request->getCountryId() . '|' . $request->getRegionId() . '|' . $request->getPostcode());
    }

    /**
     * Get tax rate based on store shipping origin address settings
     * This rate can be used for conversion store price including tax to
     * store price excluding tax
     *
     * @param Varien_Object $request
     * @param Mage_Core_Model_Store|null $store
     * @return float
     */
    public function getStoreRate($request, $store = null)
    {
        $storeRequest = $this->getRateOriginRequest($store)
            ->setProductClassId($request->getProductClassId());
        return $this->getRate($storeRequest);
    }

    /**
     * Get tax rate based on store shipping origin address settings
     * This rate can be used for conversion store price including tax to
     * store price excluding tax
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param null|Mage_Core_Model_Store $store
     * @return float
     */
    public function getStoreRateForItem($item, $store = null)
    {
        $storeRequest = $this->getRateOriginRequest($store)
            ->setProductClassId($item->getProduct()->getTaxClassId());
        return $this->getRate($storeRequest);
    }

    /**
     * Get request object for getting tax rate based on store shippig original address
     *
     * @param   null|string|bool|int|Mage_Core_Model_Store $store
     * @return  Varien_Object
     */
    public function getRateOriginRequest($store = null)
    {
        $request = new Varien_Object();
        $request->setCountryId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID, $store))
            ->setRegionId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID, $store))
            ->setPostcode(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE, $store))
            ->setCustomerClassId($this->getDefaultCustomerTaxClass($store))
            ->setStore($store);
        return $request;
    }

    /**
     * Return the default rate request. It can be either based on store address or customer address
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return Varien_Object
     */
    public function getDefaultRateRequest($store = null)
    {
        if ($this->_taxHelper->isCrossBorderTradeEnabled($store)) {
            //If cross border trade is enabled, we will use customer tax rate as store tax rate
            return $this->getRateRequest(null, null, null, $store);
        } else {
            return $this->getRateOriginRequest($store);
        }
    }

    /**
     * Get request object with information necessary for getting tax rate
     * Request object contain:
     *  country_id (->getCountryId())
     *  region_id (->getRegionId())
     *  postcode (->getPostcode())
     *  customer_class_id (->getCustomerClassId())
     *  store (->getStore())
     *
     * @param   null|false|Mage_Sales_Model_Quote_Address $shippingAddress
     * @param   null|false|Mage_Sales_Model_Quote_Address $billingAddress
     * @param   null|int $customerTaxClass
     * @param   null|string|bool|int|Mage_Core_Model_Store $store
     * @return  Varien_Object
     */
    public function getRateRequest(
        $shippingAddress = null,
        $billingAddress = null,
        $customerTaxClass = null,
        $store = null
    ) {
        if ($shippingAddress === false && $billingAddress === false && $customerTaxClass === false) {
            return $this->getRateOriginRequest($store);
        }

        $address = new Varien_Object();
        $customer = $this->getCustomer();
        $basedOn = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);

        if (($shippingAddress === false && $basedOn == 'shipping')
            || ($billingAddress === false && $basedOn == 'billing')) {
            $basedOn = 'default';
        } elseif ((($billingAddress === false || is_null($billingAddress) || !$billingAddress->getCountryId())
            && $basedOn == 'billing')
            || (($shippingAddress === false || is_null($shippingAddress) || !$shippingAddress->getCountryId())
                && $basedOn == 'shipping')) {
            if ($customer) {
                $defBilling = $customer->getDefaultBillingAddress();
                $defShipping = $customer->getDefaultShippingAddress();

                if ($basedOn == 'billing' && $defBilling && $defBilling->getCountryId()) {
                    $billingAddress = $defBilling;
                } elseif ($basedOn == 'shipping' && $defShipping && $defShipping->getCountryId()) {
                    $shippingAddress = $defShipping;
                } else {
                    $basedOn = 'default';
                }
            } else {
                $basedOn = 'default';
            }
        }

        switch ($basedOn) {
            case 'billing':
                $address = $billingAddress;
                break;
            case 'shipping':
                $address = $shippingAddress;
                break;
            case 'origin':
                $address = $this->getRateOriginRequest($store);
                break;
            case 'default':
                /** @var Mage_Sales_Model_Quote_Address|Varien_Object $address */
                $address
                    ->setCountryId(Mage::getStoreConfig(
                        Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                        $store,
                    ))
                    ->setRegionId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION, $store))
                    ->setPostcode(Mage::getStoreConfig(
                        Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE,
                        $store,
                    ));
                break;
        }

        if (is_null($customerTaxClass) && $customer) {
            $customerTaxClass = $customer->getTaxClassId();
        } elseif (($customerTaxClass === false) || !$customer) {
            $customerTaxClass = Mage::getModel('customer/group')
                    ->getTaxClassId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        $request = new Varien_Object();
        $request
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setStore($store)
            ->setCustomerClassId($customerTaxClass);
        return $request;
    }

    /**
     * Compare data and rates for two tax rate requests for same products (product tax class ids).
     * Returns true if requests are similar (i.e. equal taxes rates will be applied to them)
     *
     * Notice:
     * a) productClassId MUST be identical for both requests, because we intend to check selling SAME products to DIFFERENT locations
     * b) due to optimization productClassId can be array of ids, not only single id
     *
     * @param   Varien_Object $first
     * @param   Varien_Object $second
     * @return  bool
     */
    public function compareRequests($first, $second)
    {
        $country = $first->getCountryId() == $second->getCountryId();
        // "0" support for admin dropdown with --please select--
        $region  = (int) $first->getRegionId() == (int) $second->getRegionId();
        $postcode = $first-> getPostcode() == $second-> getPostcode();
        $taxClass = $first-> getCustomerClassId() == $second-> getCustomerClassId();

        if ($country && $region && $postcode && $taxClass) {
            return true;
        }

        /**
         * Compare available tax rates for both requests
         */
        $firstReqRates = $this->_getResource()->getRateIds($first);
        $secondReqRates = $this->_getResource()->getRateIds($second);
        if ($firstReqRates === $secondReqRates) {
            return true;
        }

        /**
         * If rates are not equal by ids then compare actual values
         * All product classes must have same rates to assume requests been similar
         */
        $productClassId1 = $first->getProductClassId(); // Save to set it back later
        $productClassId2 = $second->getProductClassId(); // Save to set it back later

        // Ids are equal for both requests, so take any of them to process
        $ids = is_array($productClassId1) ? $productClassId1 : [$productClassId1];
        $identical = true;
        foreach ($ids as $productClassId) {
            $first->setProductClassId($productClassId);
            $rate1 = $this->getRate($first);

            $second->setProductClassId($productClassId);
            $rate2 = $this->getRate($second);

            if ($rate1 != $rate2) {
                $identical = false;
                break;
            }
        }

        $first->setProductClassId($productClassId1);
        $second->setProductClassId($productClassId2);

        return $identical;
    }

    /**
     * Gets the tax rates by type
     *
     * @param Varien_Object $request
     * @param string $fieldName
     * @param string $type
     * @return array
     */
    protected function _getRates($request, $fieldName, $type)
    {
        $result = [];
        $classes = Mage::getModel('tax/class')->getCollection()
            ->addFieldToFilter('class_type', $type)
            ->load();
        /** @var Mage_Tax_Model_Class $class */
        foreach ($classes as $class) {
            $request->setData($fieldName, $class->getId());
            $result[$class->getId()] = $this->getRate($request);
        }

        return $result;
    }

    /**
     * Gets rates for all the product tax classes
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getRatesForAllProductTaxClasses($request)
    {
        return $this->_getRates($request, 'product_class_id', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
    }

    /**
     * Gets rates for all the customer tax classes
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getRatesForAllCustomerTaxClasses($request)
    {
        return $this->_getRates($request, 'customer_class_id', Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER);
    }

    /**
     * Get information about tax rates applied to request
     *
     * @param   Varien_Object $request
     * @return  array
     */
    public function getAppliedRates($request)
    {
        if (!$request->getCountryId() || !$request->getCustomerClassId() || !$request->getProductClassId()) {
            return [];
        }

        $cacheKey = $this->_getRequestCacheKey($request);
        if (!isset($this->_rateCalculationProcess[$cacheKey])) {
            $this->_rateCalculationProcess[$cacheKey] = $this->_getResource()->getCalculationProcess($request);
        }

        return $this->_rateCalculationProcess[$cacheKey];
    }

    /**
     * Get rate ids applicable for some address
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getApplicableRateIds($request)
    {
        return $this->_getResource()->getApplicableRateIds($request);
    }

    /**
     * Get the calculation process
     *
     * @param array $rates
     * @return array
     */
    public function reproduceProcess($rates)
    {
        return $this->getResource()->getCalculationProcess(null, $rates);
    }

    /**
     * Get rates by customer tax class
     *
     * @param int $customerTaxClass
     * @return array
     */
    public function getRatesByCustomerTaxClass($customerTaxClass)
    {
        return $this->getResource()->getRatesByCustomerTaxClass($customerTaxClass);
    }

    /**
     * Get rates by customer and product classes
     *
     * @param int $customerTaxClass
     * @param int $productTaxClass
     * @return array
     */
    public function getRatesByCustomerAndProductTaxClasses($customerTaxClass, $productTaxClass)
    {
        return $this->getResource()->getRatesByCustomerTaxClass($customerTaxClass, $productTaxClass);
    }

    /**
     * Calculate rated tax abount based on price and tax rate.
     * If you are using price including tax $priceIncludeTax should be true.
     *
     * @param   float $price
     * @param   float $taxRate
     * @param   bool $priceIncludeTax
     * @param   bool $round
     * @return  float
     */
    public function calcTaxAmount($price, $taxRate, $priceIncludeTax = false, $round = true)
    {
        $taxRate /= 100;

        if ($priceIncludeTax) {
            $amount = $price * (1 - 1 / (1 + $taxRate));
        } else {
            $amount = $price * $taxRate;
        }

        if ($round) {
            return $this->round($amount);
        }

        return $amount;
    }

    /**
     * Truncate number to specified precision
     *
     * @param   float $price
     * @param   int $precision
     * @return  float
     */
    public function truncate($price, $precision = 4)
    {
        $exp = 10 ** $precision;
        return floor($price * $exp) / $exp;
    }

    /**
     * Round tax amount
     *
     * @param   float $price
     * @return  float
     */
    public function round($price)
    {
        return Mage::app()->getStore()->roundPrice($price);
    }

    /**
     * Round price up
     *
     * @param   float $price
     * @return  float
     */
    public function roundUp($price)
    {
        return ceil($price * 100) / 100;
    }

    /**
     * Round price down
     *
     * @param   float $price
     * @return  float
     */
    public function roundDown($price)
    {
        return floor($price * 100) / 100;
    }
}
