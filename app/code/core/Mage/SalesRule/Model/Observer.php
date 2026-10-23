<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Model Observer
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Observer
{
    /**
     * Sales Rule Validator
     *
     * @var Mage_SalesRule_Model_Validator
     */
    protected $_validator;

    /**
     * Quotes an order was already counted for in this request, see sales_order_afterPlace()
     *
     * @var array<int, true>
     */
    protected $_countedQuoteIds = [];

    /**
     * Get quote item validator/processor object
     *
     * @param  Varien_Event                   $event
     * @return Mage_SalesRule_Model_Validator
     */
    #[Deprecated]
    public function getValidator($event)
    {
        if (!$this->_validator) {
            $this->_validator = Mage::getModel('salesrule/validator')
                ->init($event->getWebsiteId(), $event->getCustomerGroupId(), $event->getCouponCode());
        }

        return $this->_validator;
    }

    /**
     * Process quote item (apply discount to item)
     *
     * @param  Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    #[Deprecated(message: 'process call moved to total model')]
    public function sales_quote_address_discount_item($observer)
    {
        $this->getValidator($observer->getEvent())
            ->process($observer->getEvent()->getItem());
    }

    /**
     * Registered callback: called after an order is placed
     *
     * Counts the use of every applied rule and of the coupon, and enforces the usage limits once more
     * against the latest committed counters. The check done while validating the quote is far too
     * early to be relied upon: concurrent orders all pass it with the same counters.
     *
     * The rows of the applied rules are locked first. That serializes concurrent redemptions of the
     * same rules, so every counter below is read with a locking read and written without racing,
     * including salesrule_rule_customer, which has no unique key to arbitrate concurrent first uses.
     * This runs as a commit callback of the order placement transaction where there is one, so the
     * lock is held until the order is committed and throwing here rolls the order back.
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     * @throws Throwable
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function sales_order_afterPlace($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$order || $order->getDiscountAmount() == 0) {
            return $this;
        }

        $customerId = $order->getCustomerId();

        // lookup rule ids
        $ruleIds = array_map(intval(...), explode(',', (string) $order->getAppliedRuleIds()));
        $ruleIds = array_filter(array_unique($ruleIds));

        $coupon = Mage::getModel('salesrule/coupon');
        if ($order->getCouponCode()) {
            $coupon->load($order->getCouponCode(), 'code');
        }

        /**
         * Multishipping checkout places one order per address from a single quote, which was validated
         * once. Only the first order of a quote is checked against the limits, the others are counted
         * as before, so that the coupon keeps applying to all orders of such a checkout.
         */
        $enforceLimits = !$this->_isQuoteCounted($order);

        /** @var Mage_SalesRule_Model_Resource_Rule $ruleResource */
        $ruleResource = Mage::getResourceModel('salesrule/rule');
        /** @var Mage_SalesRule_Model_Resource_Coupon $couponResource */
        $couponResource = Mage::getResourceModel('salesrule/coupon');

        $ruleResource->beginTransaction();
        try {
            $rulesTimesUsed = $ruleResource->lockRulesForUpdate(
                $coupon->getId() ? array_merge($ruleIds, [(int) $coupon->getRuleId()]) : $ruleIds,
            );

            foreach ($ruleIds as $ruleId) {
                if (!isset($rulesTimesUsed[$ruleId])) {
                    continue;
                }

                $rule = Mage::getModel('salesrule/rule');
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $rule->load($ruleId);
                if (!$rule->getId()) {
                    continue;
                }

                $this->_refreshPrimaryCouponTimesUsed($rule, $couponResource);
                $rule->setTimesUsed($rulesTimesUsed[$ruleId] + 1);
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $rule->save();

                if ($customerId) {
                    /** @var Mage_SalesRule_Model_Rule_Customer $ruleCustomer */
                    $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                    /** @var Mage_SalesRule_Model_Resource_Rule_Customer $ruleCustomerResource */
                    $ruleCustomerResource = $ruleCustomer->getResource();
                    $ruleCustomerResource->loadByCustomerRuleForUpdate($ruleCustomer, $customerId, $ruleId);

                    if ($ruleCustomer->getId()) {
                        if ($enforceLimits
                            && $rule->getUsesPerCustomer()
                            && $ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()
                        ) {
                            Mage::throwException(
                                Mage::helper('salesrule')->__('A discount applied to this order has already been used the maximum number of times by this customer.'),
                            );
                        }

                        $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() + 1);
                    } else {
                        $ruleCustomer
                            ->setCustomerId($customerId)
                            ->setRuleId($ruleId)
                            ->setTimesUsed(1);
                    }

                    // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                    $ruleCustomer->save();
                }
            }

            if ($coupon->getId()) {
                if (!$couponResource->incrementTimesUsed($coupon->getId(), $enforceLimits ? $coupon->getUsageLimit() : null)) {
                    Mage::throwException(
                        Mage::helper('salesrule')->__('Coupon code "%s" has already been used the maximum number of times.', $order->getCouponCode()),
                    );
                }

                if ($customerId) {
                    /** @var Mage_SalesRule_Model_Resource_Coupon_Usage $couponUsage */
                    $couponUsage = Mage::getResourceModel('salesrule/coupon_usage');
                    $usagePerCustomer = $enforceLimits ? $coupon->getUsagePerCustomer() : null;
                    if (!$couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId(), false, $usagePerCustomer)) {
                        Mage::throwException(
                            Mage::helper('salesrule')->__('Coupon code "%s" has already been used the maximum number of times by this customer.', $order->getCouponCode()),
                        );
                    }
                }

                // save the coupon with the counter the database computed, so that coupon save events keep firing
                $this->_saveCouponTimesUsed($coupon, $couponResource);
            }

            $ruleResource->commit();
        } catch (Throwable $throwable) {
            $ruleResource->rollBack();
            throw $throwable;
        }

        $this->_setQuoteCounted($order);

        return $this;
    }

    /**
     * Registered callback: called after an order payment is canceled
     *
     * Gives the uses of the coupon back, under the same rule lock as the redemption.
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     * @throws Mage_Core_Exception
     * @throws Throwable
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function sales_order_paymentCancel($observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order $order */
        $order = $event->getPayment()->getOrder();

        if (!$order->canCancel() || !($code = $order->getCouponCode())) {
            return;
        }

        $coupon = Mage::getModel('salesrule/coupon')->loadByCode($code);
        if (!$coupon->getId()) {
            return;
        }

        /** @var Mage_SalesRule_Model_Resource_Rule $ruleResource */
        $ruleResource = Mage::getResourceModel('salesrule/rule');
        /** @var Mage_SalesRule_Model_Resource_Coupon $couponResource */
        $couponResource = Mage::getResourceModel('salesrule/coupon');

        $ruleResource->beginTransaction();
        try {
            $rulesTimesUsed = $ruleResource->lockRulesForUpdate([(int) $coupon->getRuleId()]);

            // Decrement coupon times_used, and save it so that coupon save events keep firing
            $couponResource->decrementTimesUsed($coupon->getId());
            $this->_saveCouponTimesUsed($coupon, $couponResource);

            // Decrement times_used on rule
            $rule = Mage::getModel('salesrule/rule');
            $rule->load($coupon->getRuleId());
            if ($rule->getId() && isset($rulesTimesUsed[(int) $rule->getId()])) {
                $this->_refreshPrimaryCouponTimesUsed($rule, $couponResource);
                $rule->setTimesUsed(max(0, $rulesTimesUsed[(int) $rule->getId()] - 1));
                $rule->save();
            }

            if ($customerId = $order->getCustomerId()) {
                // Decrement coupon_usage times_used
                Mage::getResourceModel('salesrule/coupon_usage')->updateCustomerCouponTimesUsed($customerId, $coupon->getId(), true);

                // Decrement rule times_used
                /** @var Mage_SalesRule_Model_Rule_Customer $customerCoupon */
                $customerCoupon = Mage::getModel('salesrule/rule_customer');
                /** @var Mage_SalesRule_Model_Resource_Rule_Customer $ruleCustomerResource */
                $ruleCustomerResource = $customerCoupon->getResource();
                $ruleCustomerResource->loadByCustomerRuleForUpdate($customerCoupon, $customerId, $coupon->getRuleId());
                if ($customerCoupon->getId() && $customerCoupon->getTimesUsed() > 0) {
                    $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed() - 1);
                    $customerCoupon->save();
                }
            }

            $ruleResource->commit();
        } catch (Throwable $throwable) {
            $ruleResource->rollBack();
            throw $throwable;
        }
    }

    /**
     * Refresh the counter of the primary coupon of a rule before the rule is saved
     *
     * Saving a rule saves its primary coupon as well, with every column it was loaded with. The coupon
     * was loaded with a plain read, which inside a transaction can return an older snapshot than the
     * latest committed counter, so saving it as loaded would roll back concurrent redemptions.
     *
     * @return $this
     */
    protected function _refreshPrimaryCouponTimesUsed(Mage_SalesRule_Model_Rule $rule, Mage_SalesRule_Model_Resource_Coupon $couponResource)
    {
        $primaryCoupon = $rule->getPrimaryCoupon();
        if ($primaryCoupon->getId()) {
            $timesUsed = $couponResource->getTimesUsedForUpdate($primaryCoupon->getId());
            if ($timesUsed !== null) {
                $primaryCoupon->setTimesUsed($timesUsed);
            }
        }

        return $this;
    }

    /**
     * Save a coupon with the counter computed by the database
     *
     * The counter itself is updated with a single statement, the coupon is saved afterwards only so that
     * the salesrule_coupon_save_before and salesrule_coupon_save_after events keep firing on redemption
     * and cancellation as they did before. Writing the counter back is safe as the rule of the coupon is
     * locked, no other redemption can change it until the transaction ends.
     *
     * @return $this
     */
    protected function _saveCouponTimesUsed(Mage_SalesRule_Model_Coupon $coupon, Mage_SalesRule_Model_Resource_Coupon $couponResource)
    {
        $timesUsed = $couponResource->getTimesUsedForUpdate($coupon->getId());
        if ($timesUsed !== null) {
            $coupon->setTimesUsed($timesUsed);
            $coupon->save();
        }

        return $this;
    }

    /**
     * Whether an earlier order of the same quote was already counted in this request
     *
     * @return bool
     */
    protected function _isQuoteCounted(Mage_Sales_Model_Order $order)
    {
        $quoteId = (int) $order->getQuoteId();

        return $quoteId && isset($this->_countedQuoteIds[$quoteId]);
    }

    /**
     * Remember that an order of the quote was counted in this request
     *
     * @return $this
     */
    protected function _setQuoteCounted(Mage_Sales_Model_Order $order)
    {
        $quoteId = (int) $order->getQuoteId();
        if ($quoteId) {
            $this->_countedQuoteIds[$quoteId] = true;
        }

        return $this;
    }

    /**
     * Refresh sales coupons report statistics for last day
     *
     * @param  Mage_Cron_Model_Schedule $schedule
     * @return $this
     * @throws Zend_Date_Exception
     */
    public function aggregateSalesReportCouponsData($schedule)
    {
        Mage::app()->getLocale()->emulate(0);
        $currentDate = Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        Mage::getResourceModel('salesrule/report_rule')->aggregate($date);
        Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param  string    $attributeCode
     * @return $this
     * @throws Throwable
     */
    protected function _checkSalesRulesAvailability($attributeCode)
    {
        /** @var Mage_SalesRule_Model_Resource_Rule_Collection $collection */
        $collection = Mage::getResourceModel('salesrule/rule_collection')
            ->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            $rule->setIsActive(0);
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $this->_removeAttributeFromConditions($rule->getActions(), $attributeCode);
            // phpcs:ignore Ecg.Performance.Loop.ModelLSD
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            Mage::getSingleton('adminhtml/session')->addWarning(
                Mage::helper('salesrule')->__('%d Shopping Cart Price Rules based on "%s" attribute have been disabled.', $disabledRulesCount, $attributeCode),
            );
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Mage_Rule_Model_Condition_Combine $combine
     * @param string                            $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Mage_Rule_Model_Condition_Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }

            if ($condition instanceof Mage_SalesRule_Model_Rule_Condition_Product && $condition->getAttribute() == $attributeCode) {
                unset($conditions[$conditionId]);
            }
        }

        $combine->setConditions($conditions);
    }

    /**
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     *
     * @return $this
     * @throws Throwable
     */
    public function catalogAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->_checkSalesRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will seted to inactive and added notice to admin session
     *
     * @return $this
     * @throws Throwable
     */
    public function catalogAttributeDeleteAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkSalesRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * Append sales rule product attributes to select by quote item collection
     *
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function addProductAttributes(Varien_Event_Observer $observer)
    {
        /** @var Varien_Object $attributesTransfer */
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = Mage::getResourceModel('salesrule/rule')
            ->getActiveAttributes(
                Mage::app()->getWebsite()->getId(),
                Mage::getSingleton('customer/session')->getCustomer()->getGroupId(),
            );
        $result = [];
        foreach ($attributes as $attribute) {
            $result[$attribute['attribute_code']] = true;
        }

        $attributesTransfer->addData($result);
        return $this;
    }

    /**
     * Add coupon's rule name to order data
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function addSalesRuleNameToOrder($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();
        $couponCode = $order->getCouponCode();

        if (empty($couponCode)) {
            return $this;
        }

        /**
         * @var Mage_SalesRule_Model_Coupon $couponModel
         */
        $couponModel = Mage::getModel('salesrule/coupon');
        $couponModel->loadByCode($couponCode);

        $ruleId = $couponModel->getRuleId();

        if (empty($ruleId)) {
            return $this;
        }

        /**
         * @var Mage_SalesRule_Model_Rule $ruleModel
         */
        $ruleModel = Mage::getModel('salesrule/rule');
        $ruleModel->load($ruleId);

        $order->setCouponRuleName($ruleModel->getName());

        return $this;
    }
}
