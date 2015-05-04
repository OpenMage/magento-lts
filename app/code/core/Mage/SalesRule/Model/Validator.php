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
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * SalesRule Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Validator extends Mage_Core_Model_Abstract
{
    /**
     * Rule source collection
     *
     * @var Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    protected $_rules;

    /**
     * Rounding deltas
     *
     * @var array
     */
    protected $_roundingDeltas = array();

    /**
     * Base rounding deltas
     *
     * @var array
     */
    protected $_baseRoundingDeltas = array();

    /**
     * Quote address
     *
     * @var null|Mage_Sales_Model_Quote_Address
     */
    protected $_address = null;

    /**
     * Defines if method Mage_SalesRule_Model_Validator::process() was already called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @deprecated since 1.4.2.0
     * @var bool
     */
    protected $_isFirstTimeProcessRun = false;

    /**
     * Defines if method Mage_SalesRule_Model_Validator::reset() wasn't called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @var bool
     */
    protected $_isFirstTimeResetRun = true;

    /**
     * Information about item totals for rules.
     * @var array
     */
    protected $_rulesItemTotals = array();

    /**
     * Store information about addresses which cart fixed rule applied for
     *
     * @var array
     */
    protected $_cartFixedRuleUsedForAddress = array();

    /**
     * Defines if rule with stop further rules is already applied
     *
     * @var bool
     */
    protected $_stopFurtherRules = false;

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param   int $websiteId
     * @param   int $customerGroupId
     * @param   string $couponCode
     * @return  Mage_SalesRule_Model_Validator
     */
    public function init($websiteId, $customerGroupId, $couponCode)
    {
        $this->setWebsiteId($websiteId)
            ->setCustomerGroupId($customerGroupId)
            ->setCouponCode($couponCode);

        $key = $websiteId . '_' . $customerGroupId . '_' . $couponCode;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = Mage::getResourceModel('salesrule/rule_collection')
                ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
                ->load();
        }
        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @return Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    protected function _getRules()
    {
        $key = $this->getWebsiteId() . '_' . $this->getCustomerGroupId() . '_' . $this->getCouponCode();
        return $this->_rules[$key];
    }

    /**
     * Get address object which can be used for discount calculation
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddress(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $item->getAddress();
        } elseif ($this->_address) {
            $address = $this->_address;
        } elseif ($item->getQuote()->getItemVirtualQty() > 0) {
            $address = $item->getQuote()->getBillingAddress();
        } else {
            $address = $item->getQuote()->getShippingAddress();
        }
        return $address;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param   Mage_SalesRule_Model_Rule $rule
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    protected function _canProcessRule($rule, $address)
    {
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        /**
         * check per coupon usage limit
         */
        if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON) {
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $coupon = Mage::getModel('salesrule/coupon');
                $coupon->load($couponCode, 'code');
                if ($coupon->getId()) {
                    // check entire usage limit
                    if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                    // check per customer usage limit
                    $customerId = $address->getQuote()->getCustomerId();
                    if ($customerId && $coupon->getUsagePerCustomer()) {
                        $couponUsage = new Varien_Object();
                        Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon(
                            $couponUsage, $customerId, $coupon->getId());
                        if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            $rule->setIsValidForAddress($address, false);
                            return false;
                        }
                    }
                }
            }
        }

        /**
         * check per rule usage limit
         */
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            $customerId     = $address->getQuote()->getCustomerId();
            $ruleCustomer   = Mage::getModel('salesrule/rule_customer');
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    $rule->setIsValidForAddress($address, false);
                    return false;
                }
            }
        }
        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForAddress($address, true);
        return true;
    }

    /**
     * Quote item free shipping ability check
     * This process not affect information about applied rules, coupon code etc.
     * This information will be added during discount amounts processing
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_SalesRule_Model_Validator
     */
    public function processFreeShipping(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $address = $this->_getAddress($item);
        $item->setFreeShipping(false);

        foreach ($this->_getRules() as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            switch ($rule->getSimpleFreeShipping()) {
                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM:
                    $item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
                    break;

                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS:
                    $address->setFreeShipping(true);
                    break;
            }
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }
        return $this;
    }

    /**
     * Reset quote and address applied rules
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_SalesRule_Model_Validator
     */
    public function reset(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->_isFirstTimeResetRun) {
            $address->setAppliedRuleIds('');
            $address->getQuote()->setAppliedRuleIds('');
            $this->_isFirstTimeResetRun = false;
        }
        $this->_address = $address;

        return $this;
    }

    /**
     * Quote item discount calculation process
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_SalesRule_Model_Validator
     */
    public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $quote      = $item->getQuote();
        $address    = $this->_getAddress($item);

        $itemPrice              = $this->_getItemPrice($item);
        $baseItemPrice          = $this->_getItemBasePrice($item);
        $itemOriginalPrice      = $this->_getItemOriginalPrice($item);
        $baseItemOriginalPrice  = $this->_getItemBaseOriginalPrice($item);

        if ($itemPrice < 0) {
            return $this;
        }

        $appliedRuleIds = array();
        $this->_stopFurtherRules = false;
        foreach ($this->_getRules() as $rule) {

            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $qty = $this->_getItemQty($item, $rule);
            $rulePercent = min(100, $rule->getDiscountAmount());

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            //discount for original price
            $originalDiscountAmount = 0;
            $baseOriginalDiscountAmount = 0;

            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100-$rule->getDiscountAmount());
                //no break;
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $_rulePct = $rulePercent/100;
                    $discountAmount    = ($qty * $itemPrice - $item->getDiscountAmount()) * $_rulePct;
                    $baseDiscountAmount = ($qty * $baseItemPrice - $item->getBaseDiscountAmount()) * $_rulePct;
                    //get discount for original price
                    $originalDiscountAmount    = ($qty * $itemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;
                    $baseOriginalDiscountAmount =
                        ($qty * $baseItemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;

                    if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
                        $discountPercent = min(100, $item->getDiscountPercent()+$rulePercent);
                        $item->setDiscountPercent($discountPercent);
                    }
                    break;
                case Mage_SalesRule_Model_Rule::TO_FIXED_ACTION:
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $qty * ($itemPrice-$quoteAmount);
                    $baseDiscountAmount = $qty * ($baseItemPrice-$rule->getDiscountAmount());
                    //get discount for original price
                    $originalDiscountAmount    = $qty * ($itemOriginalPrice-$quoteAmount);
                    $baseOriginalDiscountAmount = $qty * ($baseItemOriginalPrice-$rule->getDiscountAmount());
                    break;

                case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $quoteAmount        = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount     = $qty * $quoteAmount;
                    $baseDiscountAmount = $qty * $rule->getDiscountAmount();
                    break;

                case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:
                    if (empty($this->_rulesItemTotals[$rule->getId()])) {
                        Mage::throwException(Mage::helper('salesrule')->__('Item totals are not set for rule.'));
                    }

                    /**
                     * prevent applying whole cart discount for every shipping order, but only for first order
                     */
                    if ($quote->getIsMultiShipping()) {
                        $usedForAddressId = $this->getCartFixedRuleUsedForAddress($rule->getId());
                        if ($usedForAddressId && $usedForAddressId != $address->getId()) {
                            break;
                        } else {
                            $this->setCartFixedRuleUsedForAddress($rule->getId(), $address->getId());
                        }
                    }
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }

                    if ($cartRules[$rule->getId()] > 0) {
                        if ($this->_rulesItemTotals[$rule->getId()]['items_count'] <= 1) {
                            $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                            $baseDiscountAmount = min($baseItemPrice * $qty, $cartRules[$rule->getId()]);
                        } else {
                            $discountRate = $baseItemPrice * $qty /
                                $this->_rulesItemTotals[$rule->getId()]['base_items_price'];
                            $maximumItemDiscount = $rule->getDiscountAmount() * $discountRate;
                            $quoteAmount = $quote->getStore()->convertPrice($maximumItemDiscount);

                            $baseDiscountAmount = min($baseItemPrice * $qty, $maximumItemDiscount);
                            $this->_rulesItemTotals[$rule->getId()]['items_count']--;
                        }

                        $discountAmount = min($itemPrice * $qty, $quoteAmount);
                        $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                        $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);

                        //get discount for original price
                        $originalDiscountAmount = min($itemOriginalPrice * $qty, $quoteAmount);
                        $baseOriginalDiscountAmount = $quote->getStore()->roundPrice($baseItemOriginalPrice);

                        $cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }
                    $address->setCartFixedRules($cartRules);

                    break;

                case Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION:
                    $x = $rule->getDiscountStep();
                    $y = $rule->getDiscountAmount();
                    if (!$x || $y > $x) {
                        break;
                    }
                    $buyAndDiscountQty = $x + $y;

                    $fullRuleQtyPeriod = floor($qty / $buyAndDiscountQty);
                    $freeQty  = $qty - $fullRuleQtyPeriod * $buyAndDiscountQty;

                    $discountQty = $fullRuleQtyPeriod * $y;
                    if ($freeQty > $x) {
                        $discountQty += $freeQty - $x;
                    }

                    $discountAmount    = $discountQty * $itemPrice;
                    $baseDiscountAmount = $discountQty * $baseItemPrice;
                    //get discount for original price
                    $originalDiscountAmount    = $discountQty * $itemOriginalPrice;
                    $baseOriginalDiscountAmount = $discountQty * $baseItemOriginalPrice;
                    break;
            }

            $result = new Varien_Object(array(
                'discount_amount'      => $discountAmount,
                'base_discount_amount' => $baseDiscountAmount,
            ));
            Mage::dispatchEvent('salesrule_validator_process', array(
                'rule'    => $rule,
                'item'    => $item,
                'address' => $address,
                'quote'   => $quote,
                'qty'     => $qty,
                'result'  => $result,
            ));

            $discountAmount = $result->getDiscountAmount();
            $baseDiscountAmount = $result->getBaseDiscountAmount();

            $percentKey = $item->getDiscountPercent();
            /**
             * Process "delta" rounding
             */
            if ($percentKey) {
                $delta      = isset($this->_roundingDeltas[$percentKey]) ? $this->_roundingDeltas[$percentKey] : 0;
                $baseDelta  = isset($this->_baseRoundingDeltas[$percentKey])
                    ? $this->_baseRoundingDeltas[$percentKey]
                    : 0;
                $discountAmount += $delta;
                $baseDiscountAmount += $baseDelta;

                $this->_roundingDeltas[$percentKey]     = $discountAmount -
                    $quote->getStore()->roundPrice($discountAmount);
                $this->_baseRoundingDeltas[$percentKey] = $baseDiscountAmount -
                    $quote->getStore()->roundPrice($baseDiscountAmount);
                $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            } else {
                $discountAmount     = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            }

            /**
             * We can't use row total here because row total not include tax
             * Discount can be applied on price included tax
             */

            $itemDiscountAmount = $item->getDiscountAmount();
            $itemBaseDiscountAmount = $item->getBaseDiscountAmount();

            $discountAmount     = min($itemDiscountAmount + $discountAmount, $itemPrice * $qty);
            $baseDiscountAmount = min($itemBaseDiscountAmount + $baseDiscountAmount, $baseItemPrice * $qty);

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);

            $item->setOriginalDiscountAmount($originalDiscountAmount);
            $item->setBaseOriginalDiscountAmount($baseOriginalDiscountAmount);

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            $this->_maintainAddressCouponCode($address, $rule);
            $this->_addDiscountDescription($address, $rule);

            if ($rule->getStopRulesProcessing()) {
                $this->_stopFurtherRules = true;
                break;
            }
        }

        $item->setAppliedRuleIds(join(',',$appliedRuleIds));
        $address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }

    /**
     * Apply discount amount to FPT
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param array $items
     * @return Mage_SalesRule_Model_Validator
     */
    public function processWeeeAmount(Mage_Sales_Model_Quote_Address $address, $items)
    {
        $quote = $address->getQuote();
        $store = $quote->getStore();

        if (!$this->_getHelper('weee')->isEnabled() || !$this->_getHelper('weee')->isDiscounted()) {
            return $this;
        }

        /**
         *   for calculating weee tax discount
         */
        $config = $this->_getSingleton('tax/config');
        $calculator = $this->_getSingleton('tax/calculation');
        $request = $calculator->getRateRequest(
            $address,
            $quote->getBillingAddress(),
            $quote->getCustomerTaxClassId(),
            $store
        );

        $applyTaxAfterDiscount = $config->applyTaxAfterDiscount();
        $discountTax = $config->discountTax();
        $includeInSubtotal = $this->_getHelper('weee')->includeInSubtotal();

        foreach ($this->_getRules() as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            $rulePercent = min(100, $rule->getDiscountAmount());
            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100 - $rule->getDiscountAmount());
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    foreach ($items as $item) {

                        $weeeTaxAppliedAmounts = $this->_getHelper('weee')->getApplied($item);

                        //Total weee discount for the item
                        $totalWeeeDiscount = 0;
                        $totalBaseWeeeDiscount = 0;

                        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {

                            /* we get the discount by row since we dont need to display the individual amounts */
                            $weeeTaxAppliedRowAmount = $weeeTaxAppliedAmount['row_amount'];
                            $baseWeeeTaxAppliedRowAmount = $weeeTaxAppliedAmount['base_row_amount'];
                            $request->setProductClassId($item->getProduct()->getTaxClassId());
                            $rate = $calculator->getRate($request);

                            /*
                             * calculate weee discount
                             */
                            $weeeDiscount = 0;
                            $baseWeeeDiscount = 0;

                            if ($this->_getHelper('weee')->isTaxable()) {
                                if ($applyTaxAfterDiscount) {
                                    if ($discountTax) {
                                        $weeeTax = $weeeTaxAppliedRowAmount * $rate / 100;
                                        $baseWeeeTax = $baseWeeeTaxAppliedRowAmount * $rate / 100;
                                        $weeeDiscount = ($weeeTaxAppliedRowAmount + $weeeTax) * $rulePercent / 100;
                                        $baseWeeeDiscount = ($baseWeeeTaxAppliedRowAmount + $baseWeeeTax)
                                            * $rulePercent / 100;
                                    } else {
                                        $weeeDiscount = $weeeTaxAppliedRowAmount * $rulePercent / 100;
                                        $baseWeeeDiscount = $baseWeeeTaxAppliedRowAmount * $rulePercent / 100;
                                    }
                                } else {
                                    if ($discountTax) {
                                        $weeeTax = $weeeTaxAppliedRowAmount * $rate / 100;
                                        $baseWeeeTax = $baseWeeeTaxAppliedRowAmount * $rate / 100;
                                        $weeeDiscount = ($weeeTaxAppliedRowAmount + $weeeTax) * $rulePercent / 100;
                                        $baseWeeeDiscount = ($baseWeeeTaxAppliedRowAmount + $baseWeeeTax)
                                            * $rulePercent / 100;
                                    } else {
                                        $weeeDiscount = $weeeTaxAppliedRowAmount * $rulePercent / 100;
                                        $baseWeeeDiscount = $baseWeeeTaxAppliedRowAmount * $rulePercent / 100;
                                    }
                                }
                            } else {
                                // weee is not taxable
                                $weeeDiscount = $weeeTaxAppliedRowAmount * $rulePercent / 100;
                                $baseWeeeDiscount = $baseWeeeTaxAppliedRowAmount * $rulePercent / 100;
                            }

                            if (!$includeInSubtotal) {
                                $this->_getHelper('weee')->setWeeeTaxesAppliedProperty(
                                    $item, $weeeTaxAppliedAmount['title'], 'weee_discount', $weeeDiscount);
                                $this->_getHelper('weee')->setWeeeTaxesAppliedProperty(
                                    $item, $weeeTaxAppliedAmount['title'], 'base_weee_discount', $baseWeeeDiscount);
                            }

                            //Record the total weee discount
                            $totalBaseWeeeDiscount += $baseWeeeDiscount;
                            $totalWeeeDiscount += $weeeDiscount;
                        }

                        if (!$totalBaseWeeeDiscount && !$totalWeeeDiscount) {
                            //skip further processing if there is no weee discount associated with the item
                            continue;
                        }

                        $discountPercentage = $item->getDiscountPercent();

                        $totalWeeeDiscount = $this->_roundWithDeltas($discountPercentage,
                            $totalWeeeDiscount, $quote->getStore());
                        $totalBaseWeeeDiscount = $this->_roundWithDeltasForBase($discountPercentage,
                            $totalBaseWeeeDiscount, $quote->getStore());

                        $item->setWeeeDiscount($totalWeeeDiscount);
                        $item->setBaseWeeeDiscount($totalBaseWeeeDiscount);

                        //Set the total discount replicated on all weee attributes.
                        //we need to do this as the mage_sales_order_item does not store the weee discount
                        //We need to store this as we want to keep the rounded amounts
                        if (!$includeInSubtotal) {
                            $this->_getHelper('weee')->setWeeeTaxesAppliedProperty(
                                $item, null, 'total_base_weee_discount', $totalBaseWeeeDiscount);
                            $this->_getHelper('weee')->setWeeeTaxesAppliedProperty(
                                $item, null, 'total_weee_discount', $totalWeeeDiscount);
                        }

                        if ($includeInSubtotal) {
                            $item->setDiscountAmount($item->getDiscountAmount() + $totalWeeeDiscount);
                            $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $totalBaseWeeeDiscount);
                            $address->addTotalAmount('discount', -$totalWeeeDiscount);
                            $address->addBaseTotalAmount('discount', -$totalBaseWeeeDiscount);
                        } else {
                            if ($applyTaxAfterDiscount) {
                                $address->setExtraTaxAmount($address->getExtraTaxAmount() - $totalWeeeDiscount);
                                $address->setBaseExtraTaxAmount(
                                    $address->getBaseExtraTaxAmount() - $totalBaseWeeeDiscount);
                                $address->setWeeeDiscount($address->getWeeeDiscount() + $totalWeeeDiscount);
                                $address->setBaseWeeeDiscount($address->getBaseWeeeDiscount() + $totalBaseWeeeDiscount);
                            } else {
                                //tax has already been calculated, we need to remove weeeDiscount from total tax
                                $address->setExtraTaxAmount($address->getExtraTaxAmount() - $totalWeeeDiscount);
                                $address->setBaseExtraTaxAmount(
                                    $address->getBaseExtraTaxAmount() - $totalBaseWeeeDiscount);
                                $address->addTotalAmount('tax', -$totalWeeeDiscount);
                                $address->addBaseTotalAmount('tax', -$totalBaseWeeeDiscount);
                                $address->setWeeeDiscount($address->getWeeeDiscount() + $totalWeeeDiscount);
                                $address->setBaseWeeeDiscount($address->getBaseWeeeDiscount() + $totalBaseWeeeDiscount);
                            }
                        }

                        break;
                    }
            }
        }
        return $this;
    }

    /**
     * Round the amount with deltas collected
     *
     * @param string $key
     * @param float $amount
     * @param Mage_Core_Model_Store $store
     * @return float
     */
    protected function _roundWithDeltas($key, $amount, $store)
    {
        $delta = isset($this->_roundingDeltas[$key]) ?
            $this->_roundingDeltas[$key] : 0;
        $this->_roundingDeltas[$key] = $store->roundPrice($amount + $delta)
            - $amount;
        return $store->roundPrice($amount + $delta);
    }

    /**
     * Round the amount with deltas collected
     *
     * @param string $key
     * @param float $amount
     * @param Mage_Core_Model_Store $store
     * @return float
     */
    protected function _roundWithDeltasForBase($key, $amount, $store)
    {
        $delta = isset($this->_baseRoundingDeltas[$key]) ?
            $this->_roundingDeltas[$key] : 0;
        $this->_baseRoundingDeltas[$key] = $store->roundPrice($amount + $delta)
            - $amount;
        return $store->roundPrice($amount + $delta);
    }

    /**
     * Apply discounts to shipping amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Validator
     */
    public function processShippingAmount(Mage_Sales_Model_Quote_Address $address)
    {
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $shippingAmount     = $address->getShippingAmount();
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $quote              = $address->getQuote();
        $appliedRuleIds = array();
        foreach ($this->_getRules() as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$rule->getApplyToShipping() || !$this->_canProcessRule($rule, $address)) {
                continue;
            }

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            $rulePercent = min(100, $rule->getDiscountAmount());
            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100-$rule->getDiscountAmount());
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    $discountAmount    = ($shippingAmount - $address->getShippingDiscountAmount()) * $rulePercent/100;
                    $baseDiscountAmount = ($baseShippingAmount -
                        $address->getBaseShippingDiscountAmount()) * $rulePercent/100;
                    $discountPercent = min(100, $address->getShippingDiscountPercent()+$rulePercent);
                    $address->setShippingDiscountPercent($discountPercent);
                    break;
                case Mage_SalesRule_Model_Rule::TO_FIXED_ACTION:
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $shippingAmount-$quoteAmount;
                    $baseDiscountAmount = $baseShippingAmount-$rule->getDiscountAmount();
                    break;
                case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                    $quoteAmount        = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount     = $quoteAmount;
                    $baseDiscountAmount = $rule->getDiscountAmount();
                    break;
                case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if ($cartRules[$rule->getId()] > 0) {
                        $quoteAmount        = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                        $discountAmount     = min(
                            $shippingAmount-$address->getShippingDiscountAmount(),
                            $quoteAmount
                        );
                        $baseDiscountAmount = min(
                            $baseShippingAmount-$address->getBaseShippingDiscountAmount(),
                            $cartRules[$rule->getId()]
                        );
                        $cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }

                    $address->setCartFixedRules($cartRules);
                    break;
            }

            $discountAmount     = min($address->getShippingDiscountAmount()+$discountAmount, $shippingAmount);
            $baseDiscountAmount = min(
                $address->getBaseShippingDiscountAmount()+$baseDiscountAmount,
                $baseShippingAmount
            );
            $address->setShippingDiscountAmount($discountAmount);
            $address->setBaseShippingDiscountAmount($baseDiscountAmount);
            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            $this->_maintainAddressCouponCode($address, $rule);
            $this->_addDiscountDescription($address, $rule);
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        $address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }

    /**
     * Merge two sets of ids
     *
     * @param array|string $a1
     * @param array|string $a2
     * @param bool $asString
     * @return array
     */
    public function mergeIds($a1, $a2, $asString = true)
    {
        if (!is_array($a1)) {
            $a1 = empty($a1) ? array() : explode(',', $a1);
        }
        if (!is_array($a2)) {
            $a2 = empty($a2) ? array() : explode(',', $a2);
        }
        $a = array_unique(array_merge($a1, $a2));
        if ($asString) {
            $a = implode(',', $a);
        }
        return $a;
    }

    /**
     * Set information about usage cart fixed rule by quote address
     *
     * @param int $ruleId
     * @param int $itemId
     * @return void
     */
    public function setCartFixedRuleUsedForAddress($ruleId, $itemId)
    {
        $this->_cartFixedRuleUsedForAddress[$ruleId] = $itemId;
    }

    /**
     * Retrieve information about usage cart fixed rule by quote address
     *
     * @param int $ruleId
     * @return int|null
     */
    public function getCartFixedRuleUsedForAddress($ruleId)
    {
        if (isset($this->_cartFixedRuleUsedForAddress[$ruleId])) {
            return $this->_cartFixedRuleUsedForAddress[$ruleId];
        }
        return null;
    }

    /**
     * Calculate quote totals for each rule and save results
     *
     * @param mixed $items
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_SalesRule_Model_Validator
     */
    public function initTotals($items, Mage_Sales_Model_Quote_Address $address)
    {
        $address->setCartFixedRules(array());

        if (!$items) {
            return $this;
        }

        foreach ($this->_getRules() as $rule) {
            if (Mage_SalesRule_Model_Rule::CART_FIXED_ACTION == $rule->getSimpleAction()
                && $this->_canProcessRule($rule, $address)) {

                $ruleTotalItemsPrice = 0;
                $ruleTotalBaseItemsPrice = 0;
                $validItemsCount = 0;

                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if (!$rule->getActions()->validate($item)) {
                        continue;
                    }
                    $qty = $this->_getItemQty($item, $rule);
                    $ruleTotalItemsPrice += $this->_getItemPrice($item) * $qty;
                    $ruleTotalBaseItemsPrice += $this->_getItemBasePrice($item) * $qty;
                    $validItemsCount++;
                }

                $this->_rulesItemTotals[$rule->getId()] = array(
                    'items_price' => $ruleTotalItemsPrice,
                    'base_items_price' => $ruleTotalBaseItemsPrice,
                    'items_count' => $validItemsCount,
                );
            }
        }
        $this->_stopFurtherRules = false;
        return $this;
    }

    /**
     * Set coupon code to address if $rule contains validated coupon
     *
     * @param  Mage_Sales_Model_Quote_Address $address
     * @param  Mage_SalesRule_Model_Rule $rule
     *
     * @return Mage_SalesRule_Model_Validator
     */
    protected function _maintainAddressCouponCode($address, $rule)
    {
        /*
        Rule is a part of rules collection, which includes only rules with 'No Coupon' type or with validated coupon.
        As a result, if rule uses coupon code(s) ('Specific' or 'Auto' Coupon Type), it always contains validated
        coupon
        */
        if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON) {
            $address->setCouponCode($this->getCouponCode());
        }

        return $this;
    }

    /**
     * Add rule discount description label to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_SalesRule_Model_Rule $rule
     * @return  Mage_SalesRule_Model_Validator
     */
    protected function _addDiscountDescription($address, $rule)
    {
        $description = $address->getDiscountDescriptionArray();
        $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStore());
        $label = '';
        if ($ruleLabel) {
            $label = $ruleLabel;
        } else if (strlen($address->getCouponCode())) {
            $label = $address->getCouponCode();
        }

        if (strlen($label)) {
            $description[$rule->getId()] = $label;
        }

        $address->setDiscountDescriptionArray($description);

        return $this;
    }

    /**
     * Return item price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        $calcPrice = $item->getCalculationPrice();
        return ($price !== null) ? $price : $calcPrice;
    }

    /**
     * Return item original price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemOriginalPrice($item)
    {
        return Mage::helper('tax')->getPrice($item, $item->getOriginalPrice(), true);
    }

    /**
     * Return item base price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }

    /**
     * Return item base original price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBaseOriginalPrice($item)
    {
        return Mage::helper('tax')->getPrice($item, $item->getBaseOriginalPrice(), true);
    }

    /**
     * Return discount item qty
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Mage_SalesRule_Model_Rule $rule
     * @return int
     */
    protected function _getItemQty($item, $rule)
    {
        $qty = $item->getTotalQty();
        return $rule->getDiscountQty() ? min($qty, $rule->getDiscountQty()) : $qty;
    }

    /**
     * Convert address discount description array to string
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param string $separator
     * @return Mage_SalesRule_Model_Validator
     */
    public function prepareDescription($address, $separator = ', ')
    {
        $descriptionArray = $address->getDiscountDescriptionArray();
        /** @see Mage_SalesRule_Model_Validator::_getAddress */
        if (!$descriptionArray && $address->getQuote()->getItemVirtualQty() > 0) {
            $descriptionArray = $address->getQuote()->getBillingAddress()->getDiscountDescriptionArray();
        }

        $description = $descriptionArray && is_array($descriptionArray)
            ? implode($separator, array_unique($descriptionArray))
            :  '';

        $address->setDiscountDescription($description);
        return $this;
    }

    /**
     * wrap Mage::getSingleton
     *
     * @param string $name
     * @return mixed
     */
    protected  function _getSingleton($name) {
        return Mage::getSingleton($name);
    }

    /**
     * wrap Mage::helper
     *
     * @param string $name
     * @return Mage_Weee_Helper_Data
     */
    protected function _getHelper($name) {
        return Mage::helper($name);
    }

    /**
     * Return items list sorted by possibility to apply prioritized rules
     *
     * @param array $items
     * @return array $items
     */
    public function sortItemsByPriority($items)
    {
        $itemsSorted = array();
        foreach ($this->_getRules() as $rule) {
            foreach ($items as $itemKey => $itemValue) {
                if ($rule->getActions()->validate($itemValue)) {
                    unset($items[$itemKey]);
                    array_push($itemsSorted, $itemValue);
                }
            }
        }
        if (!empty($itemsSorted)) {
            $items = array_merge($itemsSorted, $items);
        }
        return $items;
    }
}
