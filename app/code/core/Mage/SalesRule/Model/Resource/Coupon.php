<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Resource Coupon
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Coupon extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('salesrule/coupon', 'coupon_id');
        $this->addUniqueField([
            'field' => 'code',
            'title' => Mage::helper('salesrule')->__('Coupon with the same code'),
        ]);
    }

    /**
     * Perform actions before object save
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    #[Override]
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getExpirationDate()) {
            $object->setExpirationDate(null);
        } elseif ($object->getExpirationDate() instanceof Zend_Date) {
            $object->setExpirationDate($object->getExpirationDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        // maintain single primary coupon per rule
        $object->setIsPrimary($object->getIsPrimary() ? 1 : null);

        return parent::_beforeSave($object);
    }

    /**
     * Load primary coupon (is_primary = 1) for specified rule
     *
     * @param  int|Mage_SalesRule_Model_Rule $rule
     * @return bool
     */
    public function loadPrimaryByRule(Mage_SalesRule_Model_Coupon $object, $rule)
    {
        $read = $this->_getReadAdapter();

        $ruleId = $rule instanceof Mage_SalesRule_Model_Rule ? $rule->getId() : (int) $rule;

        $select = $read->select()->from($this->getMainTable())
            ->where('rule_id = :rule_id')
            ->where('is_primary = :is_primary');

        $data = $read->fetchRow($select, [':rule_id' => $ruleId, ':is_primary' => 1]);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);
        return true;
    }

    /**
     * Increment times_used counter with a single conditional statement
     *
     * The counter is read, checked and written by the database itself, so that concurrent
     * order placements cannot all pass the usage limit check with the same stale value and
     * then redeem the coupon beyond its limit.
     *
     * @param  int      $couponId
     * @param  null|int $usageLimit Do not increment once the counter reached this limit
     * @return bool     Whether the counter was incremented
     */
    public function incrementTimesUsed($couponId, $usageLimit = null)
    {
        $where = ['coupon_id = ?' => (int) $couponId];
        if ($usageLimit) {
            $where['times_used < ?'] = (int) $usageLimit;
        }

        return $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            ['times_used' => new Zend_Db_Expr('times_used + 1')],
            $where,
        ) > 0;
    }

    /**
     * Decrement times_used counter with a single conditional statement
     *
     * @param  int  $couponId
     * @return bool Whether the counter was decremented
     */
    public function decrementTimesUsed($couponId)
    {
        return $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            ['times_used' => new Zend_Db_Expr('times_used - 1')],
            [
                'coupon_id = ?' => (int) $couponId,
                'times_used > ?' => 0,
            ],
        ) > 0;
    }

    /**
     * Read the times_used counter with a locking read
     *
     * Use inside a transaction, after the rules of the coupon were locked, to get the latest committed
     * value rather than the possibly older snapshot a plain read inside a transaction returns.
     *
     * @param  int      $couponId
     * @return null|int null if the coupon does not exist
     */
    public function getTimesUsedForUpdate($couponId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['times_used'])
            ->where('coupon_id = ?', (int) $couponId)
            ->forUpdate(true);

        $timesUsed = $adapter->fetchOne($select);

        return $timesUsed === false ? null : (int) $timesUsed;
    }

    /**
     * Check if code exists
     *
     * @param  string $code
     * @return bool
     */
    public function exists($code)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), 'code');
        $select->where('code = :code');
        return $read->fetchOne($select, ['code' => $code]) !== false;
    }

    /**
     * Update auto generated Specific Coupon if it's rule changed
     *
     * @return $this
     */
    public function updateSpecificCoupons(Mage_SalesRule_Model_Rule $rule)
    {
        if (!$rule || !$rule->getId() || !$rule->hasDataChanges()) {
            return $this;
        }

        $updateArray = [];
        if ($rule->dataHasChangedFor('uses_per_coupon')) {
            $updateArray['usage_limit'] = $rule->getUsesPerCoupon();
        }

        if ($rule->dataHasChangedFor('uses_per_customer')) {
            $updateArray['usage_per_customer'] = $rule->getUsesPerCustomer();
        }

        $ruleNewDate = new Zend_Date($rule->getToDate());
        $ruleOldDate = new Zend_Date($rule->getOrigData('to_date'));

        if ($ruleNewDate->compare($ruleOldDate)) {
            $updateArray['expiration_date'] = $rule->getToDate();
        }

        if (!empty($updateArray)) {
            $this->_getWriteAdapter()->update(
                $this->getTable('salesrule/coupon'),
                $updateArray,
                ['rule_id = ?' => $rule->getId()],
            );
        }

        return $this;
    }
}
