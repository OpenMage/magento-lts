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
class Mage_SalesRule_Model_Observer_AddSalesRuleNameToOrder implements Mage_Core_Observer_Interface
{
    /**
     * Add coupon's rule name to order data
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getDataByKey('order');
        $couponCode = $order->getCouponCode();

        if (empty($couponCode)) {
            return $this;
        }

        /** @var Mage_SalesRule_Model_Coupon $couponModel */
        $couponModel = Mage::getModel('salesrule/coupon');
        $couponModel->loadByCode($couponCode);

        $ruleId = $couponModel->getRuleId();

        if (empty($ruleId)) {
            return $this;
        }

        /** @var Mage_SalesRule_Model_Rule $ruleModel */
        $ruleModel = Mage::getModel('salesrule/rule');
        $ruleModel->load($ruleId);

        $order->setCouponRuleName($ruleModel->getName());
        return $this;
    }
}
