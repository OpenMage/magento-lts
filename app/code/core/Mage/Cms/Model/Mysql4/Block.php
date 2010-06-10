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
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CMS block model
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cms_Model_Mysql4_Block extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('cms/block', 'block_id');
    }

    /**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueBlockToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('A block identifier with the same properties already exists in the selected store.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('block_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('cms/block_store'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['block_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('cms/block_store'), $storeArray);
        }

        return parent::_afterSave($object);
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {

        if (!intval($value) && is_string($value)) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cms/block_store'))
            ->where('block_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }

        /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(array('cbs' => $this->getTable('cms/block_store')), $this->getMainTable().'.block_id = cbs.block_id')
                    ->where('is_active=1 AND cbs.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueBlockToStores(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->join(array('cbs' => $this->getTable('cms/block_store')), $this->getMainTable().'.block_id = `cbs`.block_id')
                ->where($this->getMainTable().'.identifier = ?', $object->getData('identifier'));
        if ($object->getId()) {
            $select->where($this->getMainTable().'.block_id <> ?',$object->getId());
        }
        $select->where('`cbs`.store_id IN (?)', (array)$object->getData('stores'));

        if ($this->_getWriteAdapter()->fetchRow($select)) {
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
        return $this->_getReadAdapter()->fetchCol($this->_getReadAdapter()->select()
            ->from($this->getTable('cms/block_store'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }
}
