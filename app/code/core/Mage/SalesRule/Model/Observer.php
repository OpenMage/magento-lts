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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Observer
{
    protected $_validator;

    /**
     * Get quote item validator/processor object
     *
     * @deprecated
     * @param   Varien_Event $event
     * @return  Mage_SalesRule_Model_Validator
     */
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
     * @deprecated process call movet to total model
     * @param Varien_Event_Observer $observer
     */
    public function sales_quote_address_discount_item($observer)
    {
        $this->getValidator($observer->getEvent())
            ->process($observer->getEvent()->getItem());
    }

    public function sales_order_afterPlace($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return $this;
        }

        // lookup rule ids
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);

        $ruleCustomer = null;
        $customerId = $order->getCustomerId();

        // use each rule (and apply to customer, if applicable)
        foreach ($ruleIds as $ruleId) {
            if (!$ruleId) {
                continue;
            }
            $rule = Mage::getModel('salesrule/rule');
            $rule->load($ruleId);
            if ($rule->getId()) {
                $rule->setTimesUsed($rule->getTimesUsed() + 1);
                $rule->save();

                if ($customerId) {
                    $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                    if ($ruleCustomer->getId()) {
                        $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed()+1);
                    }
                    else {
                        $ruleCustomer
                        ->setCustomerId($customerId)
                        ->setRuleId($ruleId)
                        ->setTimesUsed(1);
                    }
                    $ruleCustomer->save();
                }
            }
        }

        $coupon = Mage::getModel('salesrule/coupon');
        /** @var Mage_SalesRule_Model_Coupon */
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

    /**
     * Refresh sales coupons report statistics for last day
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Mage_Tax_Model_Observer
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
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will seted to inactive and added notice to admin session
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogRule_Model_Observer
     */
    public function catalogAttributeDeleteAfter(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        $attributeCode = $attribute->getAttributeCode();
        if ($attribute->getIsUsedForPromoRules()) {
            /* @var $collection Mage_CatalogRule_Model_Mysql4_Rule_Collection */
            $collection = Mage::getResourceModel('salesrule/rule_collection')
                ->addAttributeInConditionFilter($attributeCode);
            $hasRule = false;
            foreach ($collection as $rule) {
                /* @var $rule Mage_CatalogRule_Model_Rule */
                $rule->setIsActive(0);
                $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
                $this->_removeAttributeFromConditions($rule->getActions(), $attributeCode);
                $rule->save();
                $hasRule = true;
            }

            if ($hasRule) {
                Mage::getSingleton('adminhtml/session')->addWarning(
                    Mage::helper('salesrule')->__('Shopping Cart Price Rules based on deleted attribute "%s" has been disabled.', $attributeCode));
            }
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Mage_Rule_Model_Condition_Combine $combine
     * @param string $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Mage_Rule_Model_Condition_Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof Mage_SalesRule_Model_Rule_Condition_Product) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }
}

