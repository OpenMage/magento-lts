<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Rule Customer Model Resource
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Rule_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('salesrule/rule_customer', 'rule_customer_id');
    }

    /**
     * Get rule usage record for a customer
     *
     * @param  Mage_SalesRule_Model_Rule_Customer $rule
     * @param  int                                $customerId
     * @param  int                                $ruleId
     * @return $this
     */
    public function loadByCustomerRule($rule, $customerId, $ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('rule_id = :rule_id');
        $data = $read->fetchRow($select, [':rule_id' => $ruleId, ':customer_id' => $customerId]);
        if ($data === false) {
            // set empty data, as an existing rule object might be used
            $data = [];
        }

        $rule->setData($data);
        return $this;
    }

    /**
     * Increment the times_used counter of a customer for a rule with atomic statements
     *
     * The primary key (rule_id, customer_id) arbitrates concurrent first uses through
     * INSERT ... ON DUPLICATE KEY UPDATE, which only takes a record lock on an existing row. See
     * Mage_SalesRule_Model_Resource_Coupon_Usage::updateCustomerCouponTimesUsed() for why a locking
     * read or an UPDATE of a row that may not exist yet is avoided.
     *
     * @param  int      $ruleId
     * @param  int      $customerId
     * @param  null|int $usageLimit Do not increment once the counter reached this limit
     * @return bool     Whether the counter was incremented
     */
    public function incrementTimesUsed($ruleId, $customerId, $usageLimit = null)
    {
        $write = $this->_getWriteAdapter();
        $key = [
            'rule_id' => (int) $ruleId,
            'customer_id' => (int) $customerId,
        ];

        if ((int) $usageLimit <= 0) {
            $write->insertOnDuplicate(
                $this->getMainTable(),
                $key + ['times_used' => 1],
                ['times_used' => new Zend_Db_Expr('times_used + 1')],
            );

            return true;
        }

        // make sure the row exists, then increment it only while it is below the limit
        $write->insertOnDuplicate(
            $this->getMainTable(),
            $key + ['times_used' => 0],
            ['times_used' => new Zend_Db_Expr('times_used')],
        );

        return $write->update(
            $this->getMainTable(),
            ['times_used' => new Zend_Db_Expr('times_used + 1')],
            [
                'rule_id = ?' => $key['rule_id'],
                'customer_id = ?' => $key['customer_id'],
                'times_used < ?' => (int) $usageLimit,
            ],
        ) > 0;
    }

    /**
     * Decrement the times_used counter of a customer for a rule, never below 0
     *
     * @param  int  $ruleId
     * @param  int  $customerId
     * @return bool Whether the counter was decremented
     */
    public function decrementTimesUsed($ruleId, $customerId)
    {
        return $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            ['times_used' => new Zend_Db_Expr('times_used - 1')],
            [
                'rule_id = ?' => (int) $ruleId,
                'customer_id = ?' => (int) $customerId,
                'times_used > ?' => 0,
            ],
        ) > 0;
    }
}
