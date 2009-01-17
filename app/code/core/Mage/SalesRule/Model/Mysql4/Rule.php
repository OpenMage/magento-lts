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
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/rule', 'rule_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $startDate = $object->getFromDate();
        if ($startDate=='') {
            $startDate = Mage::getModel('core/date')->gmtDate();
        }
        $object->setFromDate($this->formatDate($startDate));
        $object->setToDate($this->formatDate($object->getToDate()));
        if (!$object->getDiscountQty()) {
            $object->setDiscountQty(new Zend_Db_Expr('NULL'));
        }
        parent::_beforeSave($object);
    }

    public function updateRuleProductData(Mage_SalesRule_Model_Rule $rule)
    {
//        foreach ($rule->getActions()->getActions() as $action) {
//            break;
//        }

        $ruleId = $rule->getId();

        $read = $this->_getReadAdapter();
        $write = $this->_getWriteAdapter();

        $write->delete($this->getTable('salesrule/rule_product'), $write->quoteInto('rule_id=?', $ruleId));

        if (!$rule->getIsActive()) {
            return $this;
        }

        if ($rule->getUsesPerCoupon()>0) {
            $usedPerCoupon = $read->fetchOne('select count(*) from '.$this->getTable('salesrule/rule_customer').' where rule_id=?', $ruleId);
            if ($usedPerCoupon>=$rule->getUsesPerCoupon()) {
                return $this;
            }
        }

        $productIds = explode(',', $rule->getProductIds());
        $websiteIds = explode(',', $rule->getWebsiteIds());
        $customerGroupIds = explode(',', $rule->getCustomerGroupIds());

        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        if ($toTime)
            $toTime += 86400;

        $couponCode = $rule->getCouponCode() ? "'".$rule->getCouponCode()."'" : 'NULL';
        $sortOrder = (int)$rule->getSortOrder();

        $rows = array();
        $header = 'replace into '.$this->getTable('salesrule/rule_product').' (rule_id, from_time, to_time, website_id, customer_group_id, product_id, coupon_code, sort_order) values ';
        try {
            $write->beginTransaction();

            foreach ($productIds as $productId) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "('$ruleId', '$fromTime', '$toTime', '$websiteId', '$customerGroupId', '$productId', $couponCode, '$sortOrder')";
                        if (sizeof($rows)==100) {
                            $sql = $header.join(',', $rows);
                            $write->query($sql);
                            $rows = array();
                        }
                    }
                }
            }
            if (!empty($rows)) {
                $sql = $header.join(',', $rows);
                $write->query($sql);
            }

            $write->commit();
        } catch (Exception $e) {

            $write->rollback();
            throw $e;

        }

        return $this;
    }

    public function getCustomerUses($rule, $customerId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('rule_customer'), array('cnt'=>'count(*)'))
            ->where('rule_id=?', $rule->getRuleId())
            ->where('customer_id=?', $customerId);
        return $read->fetchOne($select);
    }
}