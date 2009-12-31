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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Product Index Abstract Resource Model
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Reports_Model_Mysql4_Product_Index_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Fields List for update in forsedSave
     *
     * @var array
     */
    protected $_fieldsForUpdate = array('store_id', 'added_at');

    /**
     * Update Customer from visitor (Customer loggin)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Mysql4_Product_Index_Abstract
     */
    public function updateCustomerFromVisitor(Mage_Reports_Model_Product_Index_Abstract $object)
    {
        /**
         * Do nothing if customer not logged in
         */
        if (!$object->getCustomerId()) {
            return $this;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where('visitor_id=?', $object->getVisitorId());
        $rowSet = $select->query()->fetchAll();
        foreach ($rowSet as $row) {
            $select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->where('customer_id=?', $object->getCustomerId())
                ->where('product_id=?', $row['product_id']);
            $idx = $this->_getWriteAdapter()->fetchRow($select);

            if ($idx) {
                $this->_getWriteAdapter()->delete($this->getMainTable(),
                    $this->_getWriteAdapter()->quoteInto('index_id=?', $row['index_id'])
                );
                $this->_getWriteAdapter()->update($this->getMainTable(), array(
                    'visitor_id'    => $object->getVisitorId(),
                    'store_id'      => $object->getStoreId(),
                    'added_at'      => now(),
                    ), $this->_getWriteAdapter()->quoteInto('index_id=?', $idx['index_id']));
            }
            else {
                $this->_getWriteAdapter()->update($this->getMainTable(), array(
                    'customer_id'   => $object->getCustomerId(),
                    'store_id'      => $object->getStoreId(),
                    'added_at'      => now()
                    ), $this->_getWriteAdapter()->quoteInto('index_id=?', $row['index_id']));
            }
        }

        return $this;
    }

    /**
     * Purge visitor data by customer (logout)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Mysql4_Product_Index_Abstract
     */
    public function purgeVisitorByCustomer(Mage_Reports_Model_Product_Index_Abstract $object)
    {
        if (!$object->getCustomerId()) {
            return $this;
        }

        $where  = $this->_getWriteAdapter()->quoteInto('customer_id=?', $object->getCustomerId());
        $bind   = array(
            'visitor_id' => null,
        );

        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);

        return $this;
    }

    /**
     * Save Product Index data (forsed save)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Mysql4_Product_Index_Abstract
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        return $this->forsedSave($object);
    }

    /**
     * Clean index (visitor)
     *
     * @return Mage_Reports_Model_Mysql4_Product_Index_Abstract
     */
    public function clean()
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
                ->joinLeft(
                    array('visitor_table' => $this->getTable('log/visitor')),
                    'main_table.visitor_id = visitor_table.visitor_id',
                    array())
                ->where('main_table.visitor_id > 0')
                ->where('visitor_table.visitor_id IS NULL')
                ->limit(100);
            $indexIds = $this->_getReadAdapter()->fetchCol($select);

            if (!$indexIds) {
                break;
            }

            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . ' IN(?)', $indexIds)
            );
        }
        return $this;
    }
}
