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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * SalesRule Model Resource Coupon_Usage
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Coupon_Usage extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('salesrule/coupon_usage', '');
    }

    /**
     * Increment times_used counter
     *
     *
     * @param unknown_type $customerId
     * @param unknown_type $couponId
     */
    public function updateCustomerCouponTimesUsed($customerId, $couponId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), array('times_used'))
                ->where('coupon_id = :coupon_id')
                ->where('customer_id = :customer_id');

        $timesUsed = $read->fetchOne($select, array(':coupon_id' => $couponId, ':customer_id' => $customerId));

        if ($timesUsed > 0) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array(
                    'times_used' => $timesUsed + 1
                ),
                array(
                    'coupon_id = ?' => $couponId,
                    'customer_id = ?' => $customerId,
                )
            );
        } else {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'coupon_id' => $couponId,
                    'customer_id' => $customerId,
                    'times_used' => 1
                )
            );
        }
    }

    /**
     * Load an object by customer_id & coupon_id
     *
     *
     * @param Varien_Object $object
     * @param unknown_type $customerId
     * @param unknown_type $couponId
     * @return Mage_SalesRule_Model_Resource_Coupon_Usage
     */
    public function loadByCustomerCoupon(Varien_Object $object, $customerId, $couponId)
    {
        $read = $this->_getReadAdapter();
        if ($read && $couponId && $customerId) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->where('customer_id =:customet_id')
                ->where('coupon_id = :coupon_id');
            $data = $read->fetchRow($select, array(':coupon_id' => $couponId, ':customet_id' => $customerId));
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
