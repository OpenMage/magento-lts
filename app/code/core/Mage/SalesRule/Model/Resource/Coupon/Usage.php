<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Model Resource Coupon_Usage
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Coupon_Usage extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('salesrule/coupon_usage', '');
    }

    /**
     * Increment times_used counter with a single conditional statement
     *
     * The counter is read, checked and written by the database itself, so that concurrent
     * order placements cannot all pass the per customer usage limit check with the same
     * stale value. The composite primary key of the table lets only one of two concurrent
     * first uses insert a row, the loser falls back to the conditional update.
     *
     * @param  int      $customerId
     * @param  int      $couponId
     * @param  bool     $decrement  Decrement instead of increment times_used
     * @param  null|int $usageLimit Do not increment once the counter reached this limit
     * @return bool     Whether the counter was updated
     */
    public function updateCustomerCouponTimesUsed($customerId, $couponId, $decrement = false, $usageLimit = null)
    {
        $write = $this->_getWriteAdapter();
        $where = [
            'coupon_id = ?' => (int) $couponId,
            'customer_id = ?' => (int) $customerId,
        ];

        if ($decrement) {
            $where['times_used > ?'] = 0;

            return $write->update(
                $this->getMainTable(),
                ['times_used' => new Zend_Db_Expr('times_used - 1')],
                $where,
            ) > 0;
        }

        if ($usageLimit) {
            $where['times_used < ?'] = (int) $usageLimit;
        }

        if ($write->update($this->getMainTable(), ['times_used' => new Zend_Db_Expr('times_used + 1')], $where) > 0) {
            return true;
        }

        /**
         * Nothing was updated, so either there is no row for this customer yet or the limit
         * was reached, let the primary key decide which of the two it is
         */
        try {
            $write->insert(
                $this->getMainTable(),
                [
                    'coupon_id' => (int) $couponId,
                    'customer_id' => (int) $customerId,
                    'times_used' => 1,
                ],
            );

            return true;
        } catch (Zend_Db_Statement_Exception $zendDbStatementException) {
            // a concurrent request inserted the row first, retry the conditional update
            if (!$this->_isDuplicateKeyError($zendDbStatementException)) {
                throw $zendDbStatementException;
            }
        }

        return $write->update(
            $this->getMainTable(),
            ['times_used' => new Zend_Db_Expr('times_used + 1')],
            $where,
        ) > 0;
    }

    /**
     * Load an object by customer_id & coupon_id
     *
     * @param  int   $customerId
     * @param  int   $couponId
     * @return $this
     */
    public function loadByCustomerCoupon(Varien_Object $object, $customerId, $couponId)
    {
        $read = $this->_getReadAdapter();
        if ($read && $couponId && $customerId) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->where('customer_id =:customer_id')
                ->where('coupon_id = :coupon_id');
            $data = $read->fetchRow($select, [':coupon_id' => $couponId, ':customer_id' => $customerId]);
            if ($data) {
                $object->setData($data);
            }
        }

        if ($object instanceof Mage_Core_Model_Abstract) {
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * Whether the exception is a duplicate key violation, anything else must not be swallowed
     */
    protected function _isDuplicateKeyError(Zend_Db_Statement_Exception $exception): bool
    {
        $previous = $exception->getPrevious();
        if ($previous instanceof PDOException && isset($previous->errorInfo[1])) {
            return (int) $previous->errorInfo[1] === 1062;
        }

        return str_contains($exception->getMessage(), 'Duplicate entry');
    }
}
