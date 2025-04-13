<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Model Observer
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Observer_SalesOrderAfterPlace implements Mage_Core_Observer_Interface
{
    /**
     * Registered callback: called after an order is placed
     *
     * @throws Throwable
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getDataByKey('order');

        if (!$order) {
            return;
        }

        // lookup rule ids
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);

        $ruleCustomer = null;
        $customerId = $order->getCustomerId();

        // use each rule (and apply to customer, if applicable)
        if ($order->getDiscountAmount() != 0) {
            foreach ($ruleIds as $ruleId) {
                if (!$ruleId) {
                    continue;
                }
                $rule = Mage::getModel('salesrule/rule');
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $rule->load($ruleId);
                if ($rule->getId()) {
                    $rule->setTimesUsed($rule->getTimesUsed() + 1);
                    // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                    $rule->save();

                    if ($customerId) {
                        $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                        $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                        if ($ruleCustomer->getId()) {
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
            }
            $coupon = Mage::getModel('salesrule/coupon');
            $coupon->load($order->getCouponCode(), 'code');
            if ($coupon->getId()) {
                $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
                $coupon->save();
                if ($customerId) {
                    $couponUsage = Mage::getResourceModel('salesrule/coupon_usage');
                    $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
                }
            }
        }
    }
}
