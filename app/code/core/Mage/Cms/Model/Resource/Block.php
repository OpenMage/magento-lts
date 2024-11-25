<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CMS block model
 *
 * @category   Mage
 * @package    Mage_Cms
 */
class Mage_Cms_Model_Resource_Block extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('cms/block', 'block_id');
    }

    /**
     * @inheritDoc
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = [
            'block_id = ?'     => (int) $object->getId(),
        ];

        $this->_getWriteAdapter()->delete($this->getTable('cms/block_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations before object save
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueBlockToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('A block identifier with the same properties already exists in the selected store.'));
        }

        if (!$object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('cms/block_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                'block_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            ];

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = [
                    'block_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                ];
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * @inheritDoc
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Cms_Model_Block $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [
                (int) $object->getStoreId(),
                Mage_Core_Model_App::ADMIN_STORE_ID,
            ];

            $select->join(
                ['cbs' => $this->getTable('cms/block_store')],
                $this->getMainTable() . '.block_id = cbs.block_id',
                ['store_id']
            )->where('is_active = ?', 1)
            ->where('cbs.store_id in (?) ', $stores)
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @return bool
     */
    public function getIsUniqueBlockToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode()) {
            $stores = [Mage_Core_Model_App::ADMIN_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getReadAdapter()->select()
            ->from(['cb' => $this->getMainTable()])
            ->join(
                ['cbs' => $this->getTable('cms/block_store')],
                'cb.block_id = cbs.block_id',
                []
            )->where('cb.identifier = ?', $object->getData('identifier'))
            ->where('cbs.store_id IN (?)', $stores);

        if ($object->getId()) {
            $select->where('cb.block_id <> ?', $object->getId());
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('cms/block_store'), 'store_id')
            ->where('block_id = :block_id');

        $binds = [
            ':block_id' => (int) $id
        ];

        return $adapter->fetchCol($select, $binds);
    }
}
