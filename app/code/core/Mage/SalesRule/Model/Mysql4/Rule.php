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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/rule', 'rule_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getFromDate()) {
            $object->setFromDate(Mage::app()->getLocale()->date());
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }

        if (!$object->getDiscountQty()) {
            $object->setDiscountQty(new Zend_Db_Expr('NULL'));
        }

        parent::_beforeSave($object);
    }

    public function getCustomerUses($rule, $customerId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('rule_customer'), array('cnt'=>'count(*)'))
            ->where('rule_id=?', $rule->getRuleId())
            ->where('customer_id=?', $customerId);
        return $read->fetchOne($select);
    }

    /**
     * Save rule labels for different store views
     *
     * @param   int $ruleId
     * @param   array $labels
     * @return  Mage_SalesRule_Model_Mysql4_Rule
     */
    public function saveStoreLabels($ruleId, $labels)
    {
        $delete = array();
        $save = array();
        $table = $this->getTable('salesrule/label');
        $adapter = $this->_getWriteAdapter();

        foreach ($labels as $storeId => $label) {
            if (Mage::helper('core/string')->strlen($label)) {
                $data = array('rule_id' => $ruleId, 'store_id' => $storeId, 'label' => $label);
                $adapter->insertOnDuplicate($table, $data, array('label'));
            } else {
                $delete[] = $storeId;
            }
        }

        if (!empty($delete)) {
            $adapter->delete($table,
                $adapter->quoteInto('rule_id=? AND ', $ruleId) . $adapter->quoteInto('store_id IN (?)', $delete)
            );
        }
        return $this;
    }

    /**
     * Get all existing rule labels
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getStoreLabels($ruleId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('salesrule/label'), array('store_id', 'label'))
            ->where('rule_id=?', $ruleId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Get rule label by specific store id
     *
     * @param   int $ruleId
     * @param   int $storeId
     * @return  string
     */
    public function getStoreLabel($ruleId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('salesrule/label'), 'label')
            ->where('rule_id=?', $ruleId)
            ->where('store_id IN(?)', array($storeId, 0))
            ->order('store_id DESC');
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Aggregate Coupons data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Mysql4_Rule
     */
    public function aggregate($from = null, $to = null)
    {
        if (!is_null($from)) {
            $from = $this->formatDate($from);
        }
        if (!is_null($to)) {
            $from = $this->formatDate($to);
        }

        $this->_aggregateByOrderCreatedAt($from, $to);
        $this->_aggregateByOrderUpdatedAt($from, $to);

        $reportsFlagModel = Mage::getModel('reports/flag');
        $reportsFlagModel->setReportFlagCode(Mage_Reports_Model_Flag::REPORT_COUPNS_FLAG_CODE);
        $reportsFlagModel->loadSelf();
        $reportsFlagModel->save();

        return $this;
    }

    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('salesrule/coupon_aggregated');
            $writeAdapter = $this->_getWriteAdapter();

            $writeAdapter->beginTransaction();

            if (is_null($from) && is_null($to)) {
                $writeAdapter->query("TRUNCATE TABLE {$tableName}");
            } else {
                $where = (!is_null($from)) ? "so.updated_at >= '{$from}'" : '';
                if (!is_null($to)) {
                    $where .= (!empty($where)) ? " AND so.updated_at <= '{$to}'" : "so.updated_at <= '{$to}'";
                }

                $subQuery = $writeAdapter->select();
                $subQuery->from(array('so' => $this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $columns = array(
                'period'            => "DATE(created_at)",
                'store_id'          => 'store_id',
                'order_status'      => 'status',
                'coupon_code'       => 'coupon_code',
                'coupon_uses'       => 'COUNT(`entity_id`)',
                'subtotal_amount'   => 'SUM(`base_subtotal` * `base_to_global_rate`)',
                'discount_amount'   => 'SUM(`base_discount_amount` * `base_to_global_rate`)',
                'total_amount'      => 'SUM((`base_subtotal` - `base_discount_amount`) * `base_to_global_rate`)'
            );

            $select = $writeAdapter->select()->from($this->getTable('sales/order'), $columns);

            if (!is_null($from) || !is_null($to)) {
                $select->where("DATE(created_at) IN(?)", new Zend_Db_Expr($subQuery));
            }

            $select->where("coupon_code <> ''");

            $select->group(array(
                "DATE(created_at)",
                'store_id',
                'status',
                'coupon_code'
            ));

            $select->having('coupon_uses > 0');

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'            => 'period',
                'store_id'          => new Zend_Db_Expr('0'),
                'order_status'      => 'order_status',
                'coupon_code'       => 'coupon_code',
                'coupon_uses'       => 'SUM(`coupon_uses`)',
                'subtotal_amount'   => 'SUM(`subtotal_amount`)',
                'discount_amount'   => 'SUM(`discount_amount`)',
                'total_amount'      => 'SUM(`total_amount`)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status',
                    'coupon_code'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }

    protected function _aggregateByOrderUpdatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('salesrule/coupon_aggregated_order');
            $writeAdapter = $this->_getWriteAdapter();

            $writeAdapter->beginTransaction();

            if (is_null($from) && is_null($to)) {
                $writeAdapter->query("TRUNCATE TABLE {$tableName}");
            } else {
                $where = (!is_null($from)) ? "so.updated_at >= '{$from}'" : '';
                if (!is_null($to)) {
                    $where .= (!empty($where)) ? " AND so.updated_at <= '{$to}'" : "so.updated_at <= '{$to}'";
                }

                $subQuery = $writeAdapter->select();
                $subQuery->from(array('so' => $this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $columns = array(
                'period'            => "DATE(updated_at)",
                'store_id'          => 'store_id',
                'order_status'      => 'status',
                'coupon_code'       => 'coupon_code',
                'coupon_uses'       => 'COUNT(`entity_id`)',
                'subtotal_amount'   => 'SUM(`base_subtotal` * `base_to_global_rate`)',
                'discount_amount'   => 'SUM(`base_discount_amount` * `base_to_global_rate`)',
                'total_amount'      => 'SUM((`base_subtotal` - `base_discount_amount`) * `base_to_global_rate`)'
            );

            $select = $writeAdapter->select()->from($this->getTable('sales/order'), $columns);

            if (!is_null($from) || !is_null($to)) {
                $select->where("DATE(updated_at) IN(?)", new Zend_Db_Expr($subQuery));
            }

            $select->where("coupon_code <> ''");

            $select->group(array(
                "DATE(updated_at)",
                'store_id',
                'status',
                'coupon_code'
            ));

            $select->having('coupon_uses > 0');

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'            => 'period',
                'store_id'          => new Zend_Db_Expr('0'),
                'order_status'      => 'order_status',
                'coupon_code'       => 'coupon_code',
                'coupon_uses'       => 'SUM(`coupon_uses`)',
                'subtotal_amount'   => 'SUM(`subtotal_amount`)',
                'discount_amount'   => 'SUM(`discount_amount`)',
                'total_amount'      => 'SUM(`total_amount`)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status',
                    'coupon_code'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }
}
