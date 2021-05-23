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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Product Index Abstract Resource Model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Reports_Model_Resource_Product_Index_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Fields List for update in forsedSave
     *
     * @var array
     */
    protected $_fieldsForUpdate    = array('store_id', 'added_at');

    /**
     * Update Customer from visitor (Customer logged in)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function updateCustomerFromVisitor(Mage_Reports_Model_Product_Index_Abstract $object)
    {
        /**
         * Do nothing if customer not logged in
         */
        if (!$object->getCustomerId() || !$object->getVisitorId()) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('visitor_id = ?', $object->getVisitorId());

        $rowSet = $select->query()->fetchAll();
        foreach ($rowSet as $row) {
            /* We need to determine if there are rows with known
               customer for current product.
             */

            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('customer_id = ?', $object->getCustomerId())
                ->where('product_id = ?', $row['product_id']);
            $idx = $adapter->fetchRow($select);

            if ($idx) {
            /* If we are here it means that we have two rows: one with known customer, but second just visitor is set
             * One row should be updated with customer_id, second should be deleted
             *
             */
                $adapter->delete($this->getMainTable(), array('index_id = ?' => $row['index_id']));
                $where = array('index_id = ?' => $idx['index_id']);
                $data  = array(
                    'visitor_id'    => $object->getVisitorId(),
                    'store_id'      => $object->getStoreId(),
                    'added_at'      => Varien_Date::now(),
                );
            } else {
                $where = array('index_id = ?' => $row['index_id']);
                $data  = array(
                    'customer_id'   => $object->getCustomerId(),
                    'store_id'      => $object->getStoreId(),
                    'added_at'      => Varien_Date::now()
                );
            }

            $adapter->update($this->getMainTable(), $data, $where);
        }
        return $this;
    }

    /**
     * Purge visitor data by customer (logout)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function purgeVisitorByCustomer(Mage_Reports_Model_Product_Index_Abstract $object)
    {
        /**
         * Do nothing if customer not logged in
         */
        if (!$object->getCustomerId()) {
            return $this;
        }

        $bind   = array('visitor_id'      => null);
        $where  = array('customer_id = ?' => (int)$object->getCustomerId());
        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);

        return $this;
    }

    /**
     * Save Product Index data (forced save)
     *
     * @param Mage_Core_Model_Abstract|Mage_Reports_Model_Product_Index_Abstract  $object
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     * @throws Mage_Core_Exception
     */
    public function save(Mage_Core_Model_Abstract  $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_serializeFields($object);
        $this->_beforeSave($object);
        $this->_checkUnique($object);

        $data = $this->_prepareDataForSave($object);
        unset($data[$this->getIdFieldName()]);

        $matchFields = array('product_id', 'store_id');

        Mage::getResourceHelper('reports')->mergeVisitorProductIndex(
            $this->getMainTable(),
            $data,
            $matchFields
        );


        $this->unserializeFields($object);
        $this->_afterSave($object);

        return $this;
    }


    /**
     * Clean index (visitor)
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function clean()
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
                ->joinLeft(
                    array('visitor_table' => $this->getTable('log/visitor')),
                    'main_table.visitor_id = visitor_table.visitor_id',
                    array()
                )
                ->where('main_table.visitor_id > ?', 0)
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

    /**
     * Add information about product ids to visitor/customer
     *
     *
     * @param Mage_Reports_Model_Product_Index_Abstract|Varien_Object $object
     * @param array $productIds
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function registerIds(Varien_Object $object, $productIds)
    {
        $row = array(
            'visitor_id'    => $object->getVisitorId(),
            'customer_id'   => $object->getCustomerId(),
            'store_id'      => $object->getStoreId(),
        );
        $addedAt    = Varien_Date::toTimestamp(true);
        $data = array();
        foreach ($productIds as $productId) {
            $productId = (int) $productId;
            if ($productId) {
                $row['product_id'] = $productId;
                $row['added_at']   = Varien_Date::formatDate($addedAt);
                $data[] = $row;
            }
            $addedAt -= ($addedAt > 0) ? 1 : 0;
        }

        $matchFields = array('product_id', 'store_id');
        foreach ($data as $row) {
            Mage::getResourceHelper('reports')->mergeVisitorProductIndex(
                $this->getMainTable(),
                $row,
                $matchFields
            );
        }
        return $this;
    }
}
