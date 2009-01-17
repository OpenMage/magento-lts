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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote mysql4 resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Quote extends Mage_Sales_Model_Mysql4_Abstract
{
    /**
     * Initialize table nad PK name
     */
    protected function _construct()
    {
        $this->_init('sales/quote', 'entity_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
           $select = parent::_getLoadSelect($field, $value, $object);
        if ($storeIds = $object->getSharedStoreIds()) {
            $select->where('store_id IN (?)', $storeIds);
        }
        else {
            /**
             * For empty result
             */
            $select->where('store_id<0');
        }
        return $select;
    }

    /**
     * Load quote data by customer identifier
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $this->_getLoadSelect('customer_id', $customerId, $quote)
                ->where('is_active=1')
                ->order('updated_at desc')
                ->limit(1);

            $data = $read->fetchRow($select);

            if ($data) {
                $quote->setData($data);
            }
        }

        $this->_afterLoad($quote);
        return $this;
    }

    public function getReservedOrderId($quote)
    {
        return Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($quote->getStoreId());
    }

    public function isOrderIncrementIdUsed($orderIncrementId) {
        if ($this->_getReadAdapter()) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('sales/order'), 'entity_id')
                ->where('increment_id = ?', $orderIncrementId);
            $entity_id = $this->_getReadAdapter()->fetchOne($select);
            if ($entity_id > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

}