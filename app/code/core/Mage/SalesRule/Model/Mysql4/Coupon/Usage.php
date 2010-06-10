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


class Mage_SalesRule_Model_Mysql4_Coupon_Usage extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/coupon_usage', '');
    }

    /**
     * Increment times_used counter
     *
     * @param int Customer Id
     * @param int Coupon Id
     */
    public function updateCustomerCouponTimesUsed($customerId, $couponId)
    {
        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->getMainTable(),
            array(
                'coupon_id' => $couponId,
                'customer_id' => $customerId,
                'times_used' => 1
            ),
            array(
                'times_used' => new Zend_Db_Expr('times_used + 1')
            )
        );
    }

    /**
     * Load an object by customer_id & coupon_id
     *
     * @param  Varien_Object Object to load data to
     * @param  int Customer Id
     * @param  int Coupon Id
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function loadByCustomerCoupon(Varien_Object $object, $customerId, $couponId)
    {
        $read = $this->_getReadAdapter();
        if ($read && $couponId && $customerId) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.customer_id=?', $customerId)
                ->where($this->getMainTable() . '.coupon_id=?', $couponId);
            $data = $read->fetchRow($select);
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
