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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core Store Resource Model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Store extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary key
     *
     */
    protected function _construct()
    {
        $this->_init('core/store', 'store_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Resource_Store
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'code',
            'title' => Mage::helper('core')->__('Store with the same code')
        ));
        return $this;
    }

    /**
     * Check store code before save
     *
     * @param Mage_Core_Model_Abstract $model
     * @return Mage_Core_Model_Resource_Store
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $model)
    {
        if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $model->getCode())) {
            Mage::throwException(
                Mage::helper('core')->__('The store code may contain only letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'));
        }

        return $this;
    }

    /**
     * Update Store Group data after save store
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Store
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $this->_updateGroupDefaultStore($object->getGroupId(), $object->getId());
        $this->_changeGroup($object);

        return $this;
    }

    /**
     * Remove core configuration data after delete store
     *
     * @param Mage_Core_Model_Abstract $model
     * @return Mage_Core_Model_Resource_Store
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $model)
    {
        $where = array(
            'scope = ?'    => 'stores',
            'scope_id = ?' => $model->getStoreId()
        );

        $this->_getWriteAdapter()->delete(
            $this->getTable('core/config_data'),
            $where
        );
        return $this;
    }

    /**
     * Update Default store for Store Group
     *
     * @param int $groupId
     * @param int $storeId
     * @return Mage_Core_Model_Resource_Store
     */
    protected function _updateGroupDefaultStore($groupId, $storeId)
    {
        $adapter    = $this->_getWriteAdapter();

        $bindValues = array('group_id' => (int)$groupId);
        $select = $adapter->select()
            ->from($this->getMainTable(), array('count' => 'COUNT(*)'))
            ->where('group_id = :group_id');
        $count  = $adapter->fetchOne($select, $bindValues);

        if ($count == 1) {
            $bind  = array('default_store_id' => (int)$storeId);
            $where = array('group_id = ?' => (int)$groupId);
            $adapter->update($this->getTable('core/store_group'), $bind, $where);
        }

        return $this;
    }

    /**
     * Change store group for store
     *
     * @param Mage_Core_Model_Abstract $model
     * @return Mage_Core_Model_Resource_Store
     */
    protected function _changeGroup(Mage_Core_Model_Abstract $model)
    {
        if ($model->getOriginalGroupId() && $model->getGroupId() != $model->getOriginalGroupId()) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from($this->getTable('core/store_group'), 'default_store_id')
                ->where($adapter->quoteInto('group_id=?', $model->getOriginalGroupId()));
            $storeId = $adapter->fetchOne($select, 'default_store_id');

            if ($storeId == $model->getId()) {
                $bind = array('default_store_id' => Mage_Core_Model_App::ADMIN_STORE_ID);
                $where = array('group_id = ?' => $model->getOriginalGroupId());
                $this->_getWriteAdapter()->update($this->getTable('core/store_group'), $bind, $where);
            }
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->order('sort_order');
        return $select;
    }
}
