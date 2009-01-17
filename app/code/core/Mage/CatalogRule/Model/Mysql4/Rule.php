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
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogRule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogrule/rule', 'rule_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $startDate = $object->getFromDate();
        if ($startDate=='') {
            //$startDate = Mage::app()->getLocale()->date();
            $startDate = Mage::getModel('core/date')->gmtDate('Y-m-d');
        }
        $object->setFromDate($this->formatDate($startDate));
        $object->setToDate($this->formatDate($object->getToDate()));
        parent::_beforeSave($object);
    }

    public function updateRuleProductData(Mage_CatalogRule_Model_Rule $rule)
    {
        $ruleId = $rule->getId();

        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('catalogrule/rule_product'), $write->quoteInto('rule_id=?', $ruleId));

        if (!$rule->getIsActive()) {
            return $this;
        }

        $productIds = $rule->getMatchingProductIds();
        $websiteIds = explode(',', $rule->getWebsiteIds());
        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? $toTime+86400 : 0;

        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();
        $header = 'replace into '.$this->getTable('catalogrule/rule_product').' (rule_id, from_time, to_time, website_id, customer_group_id, product_id, action_operator, action_amount, action_stop, sort_order) values ';
        try {
            $write->beginTransaction();

            foreach ($productIds as $productId) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "('$ruleId', '$fromTime', '$toTime', '$websiteId', '$customerGroupId', '$productId', '$actionOperator', '$actionAmount', '$actionStop', '$sortOrder')";
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

    public function getRuleProductIds($ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('catalogrule/rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId);
        return $read->fetchCol($select);
    }

    public function removeCatalogPricesForDateRange($fromDate, $toDate, $productId=null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $cond = $write->quoteInto('rule_date between ?', $this->formatDate($fromDate));
        $cond = $write->quoteInto($cond.' and ?', $this->formatDate($toDate));
        $conds[] = $cond;
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        return $this;
    }

    public function getRuleProductsForDateRange($fromDate, $toDate, $productId=null)
    {
        $read = $this->_getReadAdapter();
        if (is_null($toDate)) {
            $toDate = $fromDate;
        }
        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product'))
            ->where($read->quoteInto('from_time=0 or from_time<=?', strtotime($toDate))
            ." or ".$read->quoteInto('to_time=0 or to_time>=?', strtotime($fromDate)))
            ->order(array('website_id', 'customer_group_id', 'product_id', 'sort_order'));
        // crucial for logic sort order: website_id, customer_group_id, product_id, sort_order
        // had 'from_time', 'to_time' in the beginning before
        if (!is_null($productId)) {
            $select->where('product_id=?', $productId);
        }
//echo (string)$select; exit;
        if (!$ruleProducts = $read->fetchAll($select)) {
            return false;
        }
        $productIds = array();
        foreach ($ruleProducts as $p) {
            $productIds[] = $p['product_id'];
        }

        $priceAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'price');

        $select = $read->select()
            ->from($priceAttr->getBackend()->getTable(), array('entity_id', 'value'))
            ->where('attribute_id=?', $priceAttr->getAttributeId())
            ->where('entity_id in (?)', $productIds);

        $prices = $read->fetchAssoc($select);
        foreach ($ruleProducts as &$p) {
            if (isset($prices[$p['product_id']]['value'])) {
                $p['price'] = $prices[$p['product_id']]['value'];
            }
        }

        return $ruleProducts;
    }

    public function applyAllRulesForDateRange($fromDate, $toDate=null, $productId=null)
    {
        $product = null;
        if (is_null($toDate)) {
            $toDate = $fromDate;
        }

        if ($productId instanceof Mage_Catalog_Model_Product) {
            $product = $productId;
            $productId = $productId->getId();
        }

        $this->removeCatalogPricesForDateRange($fromDate, $toDate, $productId);

        $productIdTags = array('catalogrule_product_price'=>true);
        $ruleProducts = $this->getRuleProductsForDateRange($fromDate, $toDate, $productId);
        if (empty($ruleProducts)) {
            Mage::app()->cleanCache(array_keys($productIdTags));
        }
        else {
            $prices = array();
            $stop = array();
            $fromTime = strtotime($fromDate);
            $toTime = strtotime($toDate);

            $rulePrice = null;
            $rows = array();

            $write = $this->_getWriteAdapter();
            $header = 'replace into '.$this->getTable('catalogrule/rule_product_price').' (rule_date, website_id, customer_group_id, product_id, rule_price, latest_start_date, earliest_end_date) values ';

            try {
                $write->beginTransaction();

                $ruleProductCount = count($ruleProducts);
                for ($time=$fromTime; $time<=$toTime; $time+=86400) {
                    $rulePrice = null;

                    for ($i=0, $l=count($ruleProducts); $i<$l; $i++) {
                        $r = $ruleProducts[$i];

                        if (!(($r['from_time']==0 || $r['from_time']<=$time) && ($r['to_time']==0 || $r['to_time']>=$time))) {
                            continue;
                        }

                        if (isset($r['price'])) {
                            if (is_null($rulePrice)) {
                                $rulePrice = $r['price'];
                                $latestFromTime = $r['from_time'];
                                $earliestToTime = $r['to_time'];
                            }

                            $amount = $r['action_amount'];
                            switch ($r['action_operator']) {
                                case 'to_fixed':
                                    $rulePrice = $amount;
                                    break;

                                case 'to_percent':
                                    $rulePrice = $rulePrice*$amount/100;
                                    break;

                                case 'by_fixed':
                                    $rulePrice -= $amount;
                                    break;

                                case 'by_percent':
                                    $rulePrice = $rulePrice*(1-$amount/100);
                                    break;
                            }

                            $latestFromTime = max($latestFromTime, $r['from_time']);
                            $earliestToTime = min($earliestToTime, $r['to_time']);
                            $rulePrice = max($rulePrice, 0);
                        }

                        if ($r['action_stop']) {
                            while (($i+1 < $l) && isset($ruleProducts[$i+1]) && !$this->_isDifferent($ruleProducts[$i+1], $r)) {
                                $i++;
                            }
                        }

                        if (($i+1 == $l) || !isset($ruleProducts[$i+1]) ||  $this->_isDifferent($ruleProducts[$i+1], $r)) {
                            if (!is_null($rulePrice)) {
                                $rows[] = "('{$this->formatDate($time)}', '{$r['website_id']}', '{$r['customer_group_id']}', '{$r['product_id']}', '$rulePrice', '{$this->formatDate($latestFromTime)}', '{$this->formatDate($earliestToTime)}')";
                            }
                            if ($i+1==$l || count($rows)===100) {
                                if (!empty($rows)) {
                                    $sql = $header.join(',', $rows);
                                    $write->query($sql);
                                }
                                $rows = array();
                            }
                            $rulePrice = null;
                        }
                        $productIdTags['catalog_product_'.$r['product_id']] = true;
                    }
                }
                Mage::app()->cleanCache(array_keys($productIdTags));

                $write->commit();

            } catch (Exception $e) {

                $write->rollback();
                throw $e;

            }
        }

        Mage::dispatchEvent('catalogrule_after_apply', array('product'=>$product));
        return $this;
    }

    protected function _isDifferent($first, $second)
    {
        return
            $first['product_id']!=$second['product_id'] ||
            $first['website_id']!=$second['website_id'] ||
            $first['customer_group_id']!=$second['customer_group_id'];
    }

    public function applyRulesCollectProductPrices($args)
    {

    }

    public function getRulePrice($date, $wId, $gId, $pId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product_price'), 'rule_price')
            ->where('rule_date=?', $this->formatDate($date))
            ->where('website_id=?', $wId)
            ->where('customer_group_id=?', $gId)
            ->where('product_id=?', $pId);
        return $read->fetchOne($select);
    }

    public function getRulesForProduct($date, $wId, $pId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product_price'), '*')
            ->where('rule_date=?', $this->formatDate($date))
            ->where('website_id=?', $wId)
            ->where('product_id=?', $pId);
        return $read->fetchAll($select);
    }

    public function applyToProduct($rule, $product, $websiteIds)
    {
        if (!$rule->getIsActive()) {
            return $this;
        }
        if (!$rule->getConditions()->validate($product)) {
            return $this;
        }

        $ruleId = $rule->getId();
        $productId = $product->getId();

        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('catalogrule/rule_product'), array(
            $write->quoteInto('rule_id=?', $ruleId),
            $write->quoteInto('product_id=?', $productId),
        ));

        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? $toTime+86400 : 0;

        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();
        $header = 'replace into '.$this->getTable('catalogrule/rule_product').' (rule_id, from_time, to_time, website_id, customer_group_id, product_id, action_operator, action_amount, action_stop, sort_order) values ';
        try {
            $write->beginTransaction();

            foreach ($websiteIds as $websiteId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = "('$ruleId', '$fromTime', '$toTime', '$websiteId', '$customerGroupId', '$productId', '$actionOperator', '$actionAmount', '$actionStop', '$sortOrder')";
                    if (sizeof($rows)==100) {
                        $sql = $header.join(',', $rows);
                        $write->query($sql);
                        $rows = array();
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
        $this->applyAllRulesForDateRange(
            $this->formatDate(mktime(0,0,0)),
            $this->formatDate(mktime(0,0,0,date('m'),date('d')+1)),
            $product
        );
        return $this;
    }
}
