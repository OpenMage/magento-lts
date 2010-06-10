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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Mysql4_Store extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/store', 'store_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'code',
            'title' => Mage::helper('core')->__('Store with the same code')
        ));
        return $this;
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $model)
    {
        if(!preg_match('/^[a-z]+[a-z0-9_]*$/',$model->getCode())) {
            Mage::throwException(
                Mage::helper('core')->__('The store code may contain only letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'));
        }

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $this->_updateGroupDefaultStore($object->getGroupId(), $object->getId());
        $this->_changeGroup($object);

        return $this;
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $model)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('core/config_data'),
            $this->_getWriteAdapter()->quoteInto("scope = 'stores' AND scope_id = ?", $model->getStoreId())
        );
        return $this;
    }

    protected function _updateGroupDefaultStore($groupId, $store_id)
    {
        $write = $this->_getWriteAdapter();
        $cnt   = $write->fetchOne($write->select()
            ->from($this->getTable('core/store'), array('count'=>'COUNT(*)'))
            ->where($write->quoteInto('group_id=?', $groupId)),
            'count');
        if ($cnt == 1) {
            $write->update($this->getTable('core/store_group'),
                array('default_store_id' => $store_id),
                $write->quoteInto('group_id=?', $groupId)
            );
        }
        return $this;
    }

    protected function _changeGroup(Mage_Core_Model_Abstract $model) {
        if ($model->getOriginalGroupId() && $model->getGroupId() != $model->getOriginalGroupId()) {
            $write = $this->_getWriteAdapter();
            $storeId = $write->fetchOne($write->select()
                ->from($this->getTable('core/store_group'), 'default_store_id')
                ->where($write->quoteInto('group_id=?', $model->getOriginalGroupId())),
                'default_store_id'
            );
            if ($storeId == $model->getId()) {
                $write->update($this->getTable('core/store_group'),
                    array('default_store_id'=>0),
                    $write->quoteInto('group_id=?', $model->getOriginalGroupId()));
            }
        }
        return $this;
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($field.'=?', $value)
            ->order('sort_order ASC');

        return $select;
    }
}
