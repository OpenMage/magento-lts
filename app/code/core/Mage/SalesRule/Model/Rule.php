<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Shopping Cart Rule data model
 *
 * @package    Mage_SalesRule
 *
 * @method Mage_SalesRule_Model_Resource_Rule            _getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Collection getCollection()
 * @method string                                        getCouponCode()
 * @method string                                        getDescription()
 * @method float                                         getDiscountQty()
 * @method string                                        getFromDate()
 * @method string                                        getName()
 * @method string                                        getProductIds()
 * @method Mage_SalesRule_Model_Resource_Rule            getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Collection getResourceCollection()
 * @method string                                        getSimpleAction()
 * @method string                                        getToDate()
 * @method bool                                          hasStoreLabels()
 * @method $this                                         setCouponCode(string $value)
 * @method $this                                         setDescription(string $value)
 * @method $this                                         setDiscountQty(float $value)
 * @method $this                                         setFromDate(string $value)
 * @method $this                                         setName(string $value)
 * @method $this                                         setProductIds(string $value)
 * @method $this                                         setSimpleAction(string $value)
 * @method $this                                         setStoreLabels(array $value)
 * @method $this                                         setToDate(string $value)
 */
class Mage_SalesRule_Model_Rule extends Mage_Rule_Model_Abstract
{
    /**
     * Free Shipping option "For matching items only"
     */
    public const FREE_SHIPPING_ITEM    = 1;

    /**
     * Free Shipping option "For shipment with matching items"
     */
    public const FREE_SHIPPING_ADDRESS = 2;

    /**
     * Coupon types
     */
    public const COUPON_TYPE_NO_COUPON = 1;

    public const COUPON_TYPE_SPECIFIC  = 2;

    public const COUPON_TYPE_AUTO      = 3;

    /**
     * Rule type actions
     */
    public const TO_PERCENT_ACTION = 'to_percent';

    public const BY_PERCENT_ACTION = 'by_percent';

    public const TO_FIXED_ACTION   = 'to_fixed';

    public const BY_FIXED_ACTION   = 'by_fixed';

    public const CART_FIXED_ACTION = 'cart_fixed';

    public const BUY_X_GET_Y_ACTION = 'buy_x_get_y';

    /**
     * Store coupon code generator instance
     *
     * @var Mage_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    protected static $_couponCodeGenerator;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Contain sores labels
     *
     * @deprecated after 1.6.2.0
     *
     * @var array
     */
    protected $_labels = [];

    /**
     * Rule's primary coupon
     *
     * @var Mage_SalesRule_Model_Coupon
     */
    protected $_primaryCoupon;

    /**
     * Rule's subordinate coupons
     *
     * @var array of Mage_SalesRule_Model_Coupon
     */
    protected $_coupons;

    /**
     * Coupon types cache for lazy getter
     *
     * @var array
     */
    protected $_couponTypes;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Returns code mass generator instance for auto generated specific coupons
     *
     * @return Mage_Core_Model_Abstract|Mage_SalesRule_Model_Coupon_Massgenerator
     */
    public static function getCouponMassGenerator()
    {
        return Mage::getSingleton('salesrule/coupon_massgenerator');
    }

    /**
     * Set coupon code and uses per coupon
     *
     * @inheritDoc
     */
    #[Override]
    protected function _afterLoad()
    {
        $this->setCouponCode($this->getPrimaryCoupon()->getCode());
        if ($this->getUsesPerCoupon() !== null && !$this->getUseAutoGeneration()) {
            $this->setUsesPerCoupon($this->getPrimaryCoupon()->getUsageLimit());
        }

        return parent::_afterLoad();
    }

    /**
     * Save/delete coupon
     *
     * @return $this
     */
    #[Override]
    protected function _afterSave()
    {
        $couponCode = trim((string) $this->getCouponCode());
        if (strlen($couponCode)
            && $this->getCouponType() == self::COUPON_TYPE_SPECIFIC
            && !$this->getUseAutoGeneration()
        ) {
            $this->getPrimaryCoupon()
                ->setCode($couponCode)
                ->setUsageLimit($this->getUsesPerCoupon() ? $this->getUsesPerCoupon() : null)
                ->setUsagePerCustomer($this->getUsesPerCustomer() ? $this->getUsesPerCustomer() : null)
                ->setExpirationDate($this->getToDate())
                ->save();
        } else {
            $this->getPrimaryCoupon()->delete();
        }

        parent::_afterSave();
        return $this;
    }

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @return $this
     */
    #[Override]
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_combine');
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function getActionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_product_combine');
    }

    /**
     * Returns code generator instance for auto generated coupons
     *
     * @return Mage_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    public static function getCouponCodeGenerator()
    {
        if (!self::$_couponCodeGenerator) {
            return Mage::getSingleton('salesrule/coupon_codegenerator', ['length' => 16]);
        }

        return self::$_couponCodeGenerator;
    }

    /**
     * Set code generator instance for auto generated coupons
     */
    public static function setCouponCodeGenerator(Mage_SalesRule_Model_Coupon_CodegeneratorInterface $codeGenerator)
    {
        self::$_couponCodeGenerator = $codeGenerator;
    }

    /**
     * Retrieve rule's primary coupon
     *
     * @return Mage_SalesRule_Model_Coupon
     */
    public function getPrimaryCoupon()
    {
        if ($this->_primaryCoupon === null) {
            $this->_primaryCoupon = Mage::getModel('salesrule/coupon');
            $this->_primaryCoupon->loadPrimaryByRule($this->getId());
            $this->_primaryCoupon->setRule($this)->setIsPrimary(true);
        }

        return $this->_primaryCoupon;
    }

    /**
     * Get sales rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array) $customerGroupIds);
        }

        return $this->_getData('customer_group_ids');
    }

    /**
     * Get Rule label by specified store
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return bool|string
     */
    public function getStoreLabel($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        $labels = (array) $this->getStoreLabels();
        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        }

        if (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * Set if not yet and retrieve rule store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * Retrieve subordinate coupons
     *
     * @return array of Mage_SalesRule_Model_Coupon
     */
    public function getCoupons()
    {
        if ($this->_coupons === null) {
            $collection = Mage::getResourceModel('salesrule/coupon_collection');
            $collection->addRuleToFilter($this);
            $this->_coupons = $collection->getItems();
        }

        return $this->_coupons;
    }

    /**
     * Retrieve coupon types
     *
     * @return array
     */
    public function getCouponTypes()
    {
        if ($this->_couponTypes === null) {
            $this->_couponTypes = [
                self::COUPON_TYPE_NO_COUPON => Mage::helper('salesrule')->__('No Coupon'),
                self::COUPON_TYPE_SPECIFIC  => Mage::helper('salesrule')->__('Specific Coupon'),
            ];
            $transport = new Varien_Object([
                'coupon_types'                => $this->_couponTypes,
                'is_coupon_type_auto_visible' => false,
            ]);
            Mage::dispatchEvent('salesrule_rule_get_coupon_types', ['transport' => $transport]);
            $this->_couponTypes = $transport->getCouponTypes();
            if ($transport->getIsCouponTypeAutoVisible()) {
                $this->_couponTypes[self::COUPON_TYPE_AUTO] = Mage::helper('salesrule')->__('Auto');
            }
        }

        return $this->_couponTypes;
    }

    /**
     * Acquire coupon instance
     *
     * @param bool $saveNewlyCreated Whether or not to save newly created coupon
     * @param int  $saveAttemptCount Number of attempts to save newly created coupon
     *
     * @return null|Mage_SalesRule_Model_Coupon
     */
    public function acquireCoupon($saveNewlyCreated = true, $saveAttemptCount = 10)
    {
        if ($this->getCouponType() == self::COUPON_TYPE_NO_COUPON) {
            return null;
        }

        if ($this->getCouponType() == self::COUPON_TYPE_SPECIFIC) {
            return $this->getPrimaryCoupon();
        }

        $coupon = Mage::getModel('salesrule/coupon');
        $coupon->setRule($this)
            ->setIsPrimary(false)
            ->setUsageLimit($this->getUsesPerCoupon() ? $this->getUsesPerCoupon() : null)
            ->setUsagePerCustomer($this->getUsesPerCustomer() ? $this->getUsesPerCustomer() : null)
            ->setExpirationDate($this->getToDate());

        $couponCode = self::getCouponCodeGenerator()->generateCode();
        $coupon->setCode($couponCode);

        $check = false;
        if (!$saveNewlyCreated) {
            $check = true;
        } elseif ($this->getId()) {
            for ($attemptNum = 0; $attemptNum < $saveAttemptCount; $attemptNum++) {
                try {
                    $coupon->save();
                } catch (Exception $exception) {
                    if ($exception instanceof Mage_Core_Exception || $coupon->getId()) {
                        throw $exception;
                    }

                    $coupon->setCode(
                        $couponCode
                        . self::getCouponCodeGenerator()->getDelimiter()
                        . sprintf('%04u', random_int(0, 9999)),
                    );
                    continue;
                }

                $check = true;
                break;
            }
        }

        if (!$check) {
            Mage::throwException(Mage::helper('salesrule')->__("Can't acquire coupon."));
        }

        return $coupon;
    }

    /**
     * Check cached validation result for specific address
     *
     * @param  Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]);
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param  Mage_Sales_Model_Quote_Address $address
     * @param  bool                           $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param  Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return $this->_validatedAddresses[$addressId] ?? false;
    }

    /**
     * Return id for address
     *
     * @param  Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Mage_Sales_Model_Quote_Address) {
            return $address->getId();
        }

        return $address;
    }

    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @param string $serializedString
     *
     * @return array
     * @deprecated after 1.6.2.0 use Mage_SalesRule_Model_Resource_Rule::getProductAttributes() instead
     */
    protected function _getUsedAttributes($serializedString)
    {
        return $this->_getResource()->getProductAttributes($serializedString);
    }

    /**
     * @param string $format
     *
     * @return string
     * @deprecated after 1.6.2.0
     */
    #[Override]
    public function toString($format = '')
    {
        return '';
    }

    /**
     * Returns rule as an array for admin interface
     *
     * @param array $arrAttributes
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     * @deprecated after 1.6.2.0
     */
    #[Override]
    public function toArray(array $arrAttributes = [])
    {
        return parent::toArray($arrAttributes);
    }

    public function getApplyToShipping(): int
    {
        return (int) $this->_getData('apply_to_shipping');
    }

    public function setApplyToShipping(int $value): static
    {
        return $this->setData('apply_to_shipping', $value);
    }

    public function getCouponType(): int
    {
        return (int) $this->_getData('coupon_type');
    }

    public function setCouponType(int $value): static
    {
        return $this->setData('coupon_type', $value);
    }

    public function setDiscountAmount(float $value): static
    {
        return $this->setData('discount_amount', $value);
    }

    public function getDiscountStep(): int
    {
        return (int) $this->_getData('discount_step');
    }

    public function setDiscountStep(int $value): static
    {
        return $this->setData('discount_step', $value);
    }

    public function getIsActive(): int
    {
        return (int) $this->_getData('is_active');
    }

    public function setIsActive(int $value): static
    {
        return $this->setData('is_active', $value);
    }

    public function getIsAdvanced(): int
    {
        return (int) $this->_getData('is_advanced');
    }

    public function setIsAdvanced(int $value): static
    {
        return $this->setData('is_advanced', $value);
    }

    public function getIsRss(): int
    {
        return (int) $this->_getData('is_rss');
    }

    public function setIsRss(int $value): static
    {
        return $this->setData('is_rss', $value);
    }

    public function getRuleId(): int
    {
        return (int) $this->_getData('rule_id');
    }

    public function getSimpleFreeShipping(): int
    {
        return (int) $this->_getData('simple_free_shipping');
    }

    public function setSimpleFreeShipping(int $value): static
    {
        return $this->setData('simple_free_shipping', $value);
    }

    public function getSortOrder(): int
    {
        return (int) $this->_getData('sort_order');
    }

    public function setSortOrder(int $value): static
    {
        return $this->setData('sort_order', $value);
    }

    public function getStopRulesProcessing(): int
    {
        return (int) $this->_getData('stop_rules_processing');
    }

    public function setStopRulesProcessing(int $value): static
    {
        return $this->setData('stop_rules_processing', $value);
    }

    public function getTimesUsed(): int
    {
        return (int) $this->_getData('times_used');
    }

    public function setTimesUsed(int $value): static
    {
        return $this->setData('times_used', $value);
    }

    public function getUseAutoGeneration(): int
    {
        return (int) $this->_getData('use_auto_generation');
    }

    public function setUseAutoGeneration(int $value): static
    {
        return $this->setData('use_auto_generation', $value);
    }

    public function getUsesPerCoupon(): int
    {
        return (int) $this->_getData('uses_per_coupon');
    }

    public function setUsesPerCoupon(int $value): static
    {
        return $this->setData('uses_per_coupon', $value);
    }

    public function getUsesPerCustomer(): int
    {
        return (int) $this->_getData('uses_per_customer');
    }

    public function setUsesPerCustomer(int $value): static
    {
        return $this->setData('uses_per_customer', $value);
    }
}
