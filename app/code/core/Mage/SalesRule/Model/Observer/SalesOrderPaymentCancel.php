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
class Mage_SalesRule_Model_Observer_SalesOrderPaymentCancel implements Mage_Core_Observer_Interface
{
    /**
     * Registered callback: called after an order payment is canceled
     *
     * @throws Throwable
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $observer->getEvent()->getDataByKey('payment');
        $order = $payment->getOrder();

        if ($order->canCancel()) {
            if ($code = $order->getCouponCode()) {
                // Decrement coupon times_used
                $coupon = Mage::getModel('salesrule/coupon')->loadByCode($code);

                if ($coupon->getId()) {
                    $coupon->setTimesUsed($coupon->getTimesUsed() - 1);
                    $coupon->save();

                    // Decrement times_used on rule
                    $rule = Mage::getModel('salesrule/rule');
                    $rule->load($coupon->getRuleId());
                    if ($rule->getId()) {
                        $rule->setTimesUsed($rule->getTimesUsed() - 1);
                        $rule->save();
                    }

                    if ($customerId = $order->getCustomerId()) {
                        // Decrement coupon_usage times_used
                        Mage::getResourceModel('salesrule/coupon_usage')->updateCustomerCouponTimesUsed($customerId, $coupon->getId(), true);

                        // Decrement rule times_used
                        $customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $coupon->getRuleId());
                        if ($customerCoupon->getId()) {
                            $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed() - 1);
                            $customerCoupon->save();
                        }
                    }
                }
            }
        }

        return $this;
    }
}
