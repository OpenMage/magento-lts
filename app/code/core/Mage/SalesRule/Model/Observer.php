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
     * with the counters themselves. The check done while validating the quote is far too early to be
     * relied upon: concurrent orders all pass it with the same counters.
     *
     * Orders without a discount amount are counted too: a rule that only grants free shipping gives
     * none, and its coupon has to be counted against its limits all the same.
     *
     * This runs inside the order placement transaction where there is one (onepage, admin and API
     * checkouts), so that throwing here rolls the order back. Only the counters that have a limit are
     * updated inside it, each with a single conditional statement that locks nothing but the existing
     * coupon or usage row it updates. Concurrent orders using the same limited coupon or the same
     * customer and limited rule wait for each other until the order is committed, nothing else does.
     * The other counters are not checked against anything, so they are updated after the order was
     * committed, each in a statement of its own. Concurrent orders using the same rule without
     * limits, typically a sitewide promotion, therefore never wait for each other.
     *
     * The counters are no longer written through the models: that read-modify-write undid concurrent
     * redemptions. The coupon is still saved, without its counter, so that its model save events keep
     * firing. The rule is not saved anymore: that would lock the rule row until the order is committed
     * and rewrite its websites, customer groups and product attributes on every order, so
     * salesrule_rule_save_* no longer fire for a redemption. salesrule_usage_update_after is dispatched
     * instead once all counters were updated.
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

        if (!$order || !$this->_hasAppliedRules($order)) {
            return $this;
        }

        $customerId = (int) $order->getCustomerId();

        /** @var Mage_SalesRule_Model_Resource_Rule $ruleResource */
        $ruleResource = Mage::getResourceModel('salesrule/rule');
        /** @var Mage_SalesRule_Model_Resource_Coupon $couponResource */
        $couponResource = Mage::getResourceModel('salesrule/coupon');
        /** @var Mage_SalesRule_Model_Resource_Coupon_Usage $couponUsageResource */
        $couponUsageResource = Mage::getResourceModel('salesrule/coupon_usage');
        /** @var Mage_SalesRule_Model_Resource_Rule_Customer $ruleCustomerResource */
        $ruleCustomerResource = Mage::getResourceModel('salesrule/rule_customer');

        // uses per customer of the applied rules that exist, in ascending rule id order
        $usesPerCustomer = $ruleResource->getUsesPerCustomer(explode(',', (string) $order->getAppliedRuleIds()));

        $coupon = Mage::getModel('salesrule/coupon');
        if ((string) $order->getCouponCode() !== '') {
            $coupon->load($order->getCouponCode(), 'code');
        }

        $couponId = (int) $coupon->getId();

        /**
         * Multishipping checkout places one order per address from a single quote, which was validated
         * once. Only the first order of a quote is checked against the limits, the others are counted
         * as before, so that the coupon keeps applying to all orders of such a checkout.
         */
        $enforceLimits = !$this->_isQuoteCounted($order);

        $limitCoupon = $enforceLimits && $couponId > 0 && $coupon->getUsageLimit() > 0;
        // the coupon row is locked anyway when its usage limit is checked, count the customer use along with it
        $countCouponUsageNow = $couponId > 0 && $customerId > 0 && ($limitCoupon || ($enforceLimits && $coupon->getUsagePerCustomer() > 0));
        $limitedRuleIds = [];
        if ($enforceLimits && $customerId > 0) {
            $limitedRuleIds = array_keys(array_filter($usesPerCustomer, static fn($limit) => $limit > 0));
        }

        $couponResource->beginTransaction();
        try {
            // the limited counters, always in the same order: coupon, coupon usage, rule usages by rule id
            if ($limitCoupon && !$couponResource->incrementTimesUsed($couponId, true)) {
                Mage::throwException(
                    Mage::helper('salesrule')->__('Coupon code "%s" has already been used the maximum number of times.', $order->getCouponCode()),
                );
            }

            if ($countCouponUsageNow) {
                $usagePerCustomer = $enforceLimits ? (int) $coupon->getUsagePerCustomer() : null;
                if (!$couponUsageResource->updateCustomerCouponTimesUsed($customerId, $couponId, false, $usagePerCustomer)) {
                    Mage::throwException(
                        Mage::helper('salesrule')->__('Coupon code "%s" has already been used the maximum number of times by this customer.', $order->getCouponCode()),
                    );
                }
            }

            foreach ($limitedRuleIds as $ruleId) {
                if (!$ruleCustomerResource->incrementTimesUsed($ruleId, $customerId, $usesPerCustomer[$ruleId])) {
                    Mage::throwException(
                        Mage::helper('salesrule')->__('A discount applied to this order has already been used the maximum number of times by this customer.'),
                    );
                }
            }

            // the coupon row is locked by this transaction already, the coupon is saved within it as before
            if ($limitCoupon) {
                $this->_saveCoupon($coupon, $couponResource);
            }

            // the counters without a limit are updated once the order is committed
            $couponResource->addCommitCallback(function () use (
                $order,
                $coupon,
                $customerId,
                $usesPerCustomer,
                $limitedRuleIds,
                $limitCoupon,
                $countCouponUsageNow,
                $ruleResource,
                $couponResource,
                $couponUsageResource,
                $ruleCustomerResource,
            ): void {
                $couponId = (int) $coupon->getId();
                if ($couponId > 0 && !$limitCoupon) {
                    $this->_updateCounter(fn() => $this->_updateCouponTimesUsed($coupon, $couponResource, 1));
                }

                if ($couponId > 0 && $customerId > 0 && !$countCouponUsageNow) {
                    $this->_updateCounter(static fn() => $couponUsageResource->updateCustomerCouponTimesUsed($customerId, $couponId));
                }

                if ($customerId > 0) {
                    foreach (array_diff(array_keys($usesPerCustomer), $limitedRuleIds) as $ruleId) {
                        $this->_updateCounter(static fn() => $ruleCustomerResource->incrementTimesUsed($ruleId, $customerId));
                    }
                }

                if ($usesPerCustomer !== []) {
                    $this->_updateCounter(static fn() => $ruleResource->updateTimesUsed(array_keys($usesPerCustomer), 1));
                }

                $this->_dispatchUsageUpdate($order, array_keys($usesPerCustomer), $couponId > 0 ? $coupon : null, $customerId, 1);
            });

            $couponResource->commit();
        } catch (Throwable $throwable) {
            $couponResource->rollBack();
            throw $throwable;
        }

        $this->_setQuoteCounted($order);

        return $this;
    }

    /**
     * Registered callback: called after an order payment is canceled
     *
     * Gives the uses of the coupon and of its rule back once the cancellation was committed, with
     * statements that never take a counter below 0.
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

        $code = (string) $order->getCouponCode();
        if (!$order->canCancel() || $code === '') {
            return;
        }

        $coupon = Mage::getModel('salesrule/coupon')->loadByCode($code);
        $couponId = (int) $coupon->getId();
        if ($couponId === 0) {
            return;
        }

        $ruleId = (int) $coupon->getRuleId();
        $customerId = (int) $order->getCustomerId();

        /** @var Mage_SalesRule_Model_Resource_Coupon $couponResource */
        $couponResource = Mage::getResourceModel('salesrule/coupon');

        // run right away without a transaction, after the commit within one, and not at all on rollback
        $couponResource->beginTransaction();
        try {
            $couponResource->addCommitCallback(function () use ($order, $coupon, $couponId, $ruleId, $customerId, $couponResource): void {
                $this->_updateCounter(fn() => $this->_updateCouponTimesUsed($coupon, $couponResource, -1));
                $this->_updateCounter(static fn() => Mage::getResourceModel('salesrule/rule')->updateTimesUsed([$ruleId], -1));

                if ($customerId > 0) {
                    $this->_updateCounter(static fn() => Mage::getResourceModel('salesrule/coupon_usage')
                        ->updateCustomerCouponTimesUsed($customerId, $couponId, true));
                    $this->_updateCounter(static fn() => Mage::getResourceModel('salesrule/rule_customer')
                        ->decrementTimesUsed($ruleId, $customerId));
                }

                $this->_dispatchUsageUpdate($order, [$ruleId], $coupon, $customerId, -1);
            });
            $couponResource->commit();
        } catch (Throwable $throwable) {
            $couponResource->rollBack();
            throw $throwable;
        }
    }

    /**
     * Run a usage counter update after the order transaction was committed
     *
     * The order is placed or canceled at this point, so a failure must not reach the customer, it is
     * logged instead. Single statement deadlocks and lock wait timeouts are retried.
     *
     * @param  callable $update
     * @return $this
     */
    protected function _updateCounter($update)
    {
        for ($attempt = 1; $attempt <= 3; ++$attempt) {
            try {
                $update();
                return $this;
            } catch (Throwable $throwable) {
                if ($attempt < 3 && $this->_isLockError($throwable)) {
                    continue;
                }

                Mage::logException($throwable);
                return $this;
            }
        }

        return $this;
    }

    /**
     * Whether a statement failed with a deadlock or a lock wait timeout
     */
    protected function _isLockError(Throwable $throwable): bool
    {
        $previous = $throwable->getPrevious();
        $errorCode = $previous instanceof PDOException ? (int) ($previous->errorInfo[1] ?? 0) : 0;

        // ER_LOCK_DEADLOCK, ER_LOCK_WAIT_TIMEOUT
        return in_array($errorCode, [1213, 1205], true);
    }

    /**
     * Update the counter of a coupon and save it, after the order transaction was committed
     *
     * Both happen in one short transaction, so the coupon row is locked once. A failing save is logged
     * and does not undo the counter update.
     *
     * @param  int       $delta 1 to count a use, -1 to give one back (never below 0)
     * @return $this
     * @throws Throwable
     */
    protected function _updateCouponTimesUsed(
        Mage_SalesRule_Model_Coupon $coupon,
        Mage_SalesRule_Model_Resource_Coupon $couponResource,
        $delta,
    ) {
        $couponResource->beginTransaction();
        try {
            if ($delta < 0) {
                $couponResource->decrementTimesUsed((int) $coupon->getId());
            } else {
                $couponResource->incrementTimesUsed((int) $coupon->getId());
            }

            try {
                $this->_saveCoupon($coupon, $couponResource);
            } catch (Throwable $throwable) {
                // InnoDB rolled the whole transaction back, retry it
                if ($this->_isLockError($throwable)) {
                    throw $throwable;
                }

                Mage::logException($throwable);
            }

            $couponResource->commit();
        } catch (Throwable $throwable) {
            $couponResource->rollBack();
            throw $throwable;
        }

        return $this;
    }

    /**
     * Save a coupon after its counter was updated, without writing the counter
     *
     * Saving the coupon keeps the model save events firing for a redemption or a cancellation as they
     * did before (the coupon model has no event prefix of its own, so these are model_save_before,
     * model_save_after, core_abstract_save_before and core_abstract_save_after). The model is given
     * the counter the database holds now, as original data too, so observers see the current value
     * while the save itself leaves the counter alone, see Mage_SalesRule_Model_Resource_Coupon::_prepareDataForSave().
     *
     * @return $this
     * @throws Throwable
     */
    protected function _saveCoupon(Mage_SalesRule_Model_Coupon $coupon, Mage_SalesRule_Model_Resource_Coupon $couponResource)
    {
        $timesUsed = $couponResource->getTimesUsed((int) $coupon->getId());
        if ($timesUsed !== null) {
            $coupon->setTimesUsed($timesUsed)->setOrigData('times_used', $timesUsed);
            $coupon->save();
        }

        return $this;
    }

    /**
     * Notify that the usage counters of an order were updated
     *
     * @param  int[]                            $ruleIds
     * @param  null|Mage_SalesRule_Model_Coupon $coupon
     * @param  int                              $customerId
     * @param  int                              $qty        1 for a placed order, -1 for a canceled one
     * @return $this
     */
    protected function _dispatchUsageUpdate(Mage_Sales_Model_Order $order, array $ruleIds, $coupon, $customerId, $qty)
    {
        try {
            Mage::dispatchEvent('salesrule_usage_update_after', [
                'order' => $order,
                'rule_ids' => $ruleIds,
                'coupon' => $coupon,
                'customer_id' => $customerId > 0 ? $customerId : null,
                'qty' => $qty,
            ]);
        } catch (Throwable $throwable) {
            Mage::logException($throwable);
        }

        return $this;
    }

    /**
     * Whether a rule or a coupon was applied to the order, whatever discount amount it gave
     */
    protected function _hasAppliedRules(Mage_Sales_Model_Order $order): bool
    {
        if ((string) $order->getCouponCode() !== '') {
            return true;
        }

        foreach (explode(',', (string) $order->getAppliedRuleIds()) as $ruleId) {
            if ((int) $ruleId > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether an earlier order of the same quote was already counted in this request
     *
     * @return bool
     */
    protected function _isQuoteCounted(Mage_Sales_Model_Order $order)
    {
        $quoteId = (int) $order->getQuoteId();

        return $quoteId > 0 && isset($this->_countedQuoteIds[$quoteId]);
    }

    /**
     * Remember that an order of the quote was counted in this request
     *
     * @return $this
     */
    protected function _setQuoteCounted(Mage_Sales_Model_Order $order)
    {
        $quoteId = (int) $order->getQuoteId();
        if ($quoteId > 0) {
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
