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
     * Increment or decrement the times_used counter of a customer with atomic statements
     *
     * The counter is read, checked and written by the database itself, so that concurrent
     * order placements cannot all pass the per customer usage limit check with the same stale
     * value. The primary key (coupon_id, customer_id) arbitrates concurrent first uses through
     * INSERT ... ON DUPLICATE KEY UPDATE, which only takes a record lock on an existing row.
     * A locking read or an UPDATE of a row that does not exist yet is avoided on purpose: it
     * would lock the gap between existing rows, which unrelated first uses insert into, and
     * make concurrent orders of different customers and coupons deadlock.
     *
     * @param  int      $customerId
     * @param  int      $couponId
     * @param  bool     $decrement  Decrement instead of increment times_used, never below 0
     * @param  null|int $usageLimit Do not increment once the counter reached this limit
     * @return bool     Whether the counter was updated
     */
    public function updateCustomerCouponTimesUsed($customerId, $couponId, $decrement = false, $usageLimit = null)
    {
        $write = $this->_getWriteAdapter();
        $key = [
            'coupon_id' => (int) $couponId,
            'customer_id' => (int) $customerId,
        ];
        $where = [
            'coupon_id = ?' => $key['coupon_id'],
            'customer_id = ?' => $key['customer_id'],
        ];

        if ($decrement) {
            $where['times_used > ?'] = 0;

            return $write->update(
                $this->getMainTable(),
                ['times_used' => new Zend_Db_Expr('times_used - 1')],
                $where,
            ) > 0;
        }

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
        $where['times_used < ?'] = (int) $usageLimit;

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
}
