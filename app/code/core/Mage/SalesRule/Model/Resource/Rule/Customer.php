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
     * Load the rule usage record for a customer with a locking read
     *
     * Use inside a transaction, after the rule was locked, to get the latest committed counter rather
     * than the possibly older snapshot a plain read inside a transaction returns. The rule lock is also
     * what keeps two concurrent first uses from inserting two records, the table has no unique key.
     *
     * @param  Mage_SalesRule_Model_Rule_Customer $ruleCustomer
     * @param  int                                $customerId
     * @param  int                                $ruleId
     * @return $this
     */
    public function loadByCustomerRuleForUpdate($ruleCustomer, $customerId, $ruleId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('customer_id = ?', (int) $customerId)
            ->where('rule_id = ?', (int) $ruleId)
            ->forUpdate(true);

        $data = $adapter->fetchRow($select);
        $ruleCustomer->setData($data === false ? [] : $data);

        return $this;
    }
}
