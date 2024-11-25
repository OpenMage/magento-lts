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
 * Cms page mysql resource
 *
 * @category   Mage
 * @package    Mage_Cms
 */
class Mage_Cms_Model_Resource_Page extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store = null;

    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        if ($object instanceof Mage_Cms_Model_Page) {
            $isUsedInConfig = $this->getUsedInStoreConfigCollection($object);
            if ($isUsedInConfig->count()) {
                // prevent delete
                $object->setId(null);
                Mage::throwException(
                    Mage::helper('cms')->__(
                        'Cannot delete page, it is used in "%s".',
                        implode(', ', $isUsedInConfig->getColumnValues('path')),
                    ),
                );
            }
        }

        $condition = [
            'page_id = ?' => (int) $object->getId(),
        ];

        $this->_getWriteAdapter()->delete($this->getTable('cms/page_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param Mage_Cms_Model_Page $object
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /**
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (['custom_theme_from', 'custom_theme_to'] as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        if (!$object->getIsActive()) {
            $isUsedInConfig = $this->getUsedInStoreConfigCollection($object);
            if ($isUsedInConfig->count()) {
                $object->setIsActive(true);
                Mage::getSingleton('adminhtml/session')->addWarning(
                    Mage::helper('cms')->__(
                        'Cannot disable page, it is used in configuration "%s".',
                        implode(', ', $isUsedInConfig->getColumnValues('path')),
                    ),
                );
            }
        }

        if (!$this->getIsUniquePageToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('A page URL key for specified store already exists.'));
        }

        if (!$this->isValidPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key cannot consist only of numbers.'));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * @param Mage_Cms_Model_Page $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array) $object->getStores();
        if (empty($newStores)) {
            $newStores = (array) $object->getStoreId();
        }
        $table  = $this->getTable('cms/page_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                'page_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete,
            ];

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = [
                    'page_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId,
                ];
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        //Mark layout cache as invalidated
        Mage::app()->getCacheInstance()->invalidateType('layout');

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
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Cms_Model_Page $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [Mage_Core_Model_App::ADMIN_STORE_ID, (int) $object->getStoreId()];
            $select->join(
                ['cms_page_store' => $this->getTable('cms/page_store')],
                $this->getMainTable() . '.page_id = cms_page_store.page_id',
                [],
            )
                ->where('is_active = ?', 1)
                ->where('cms_page_store.store_id IN (?)', $storeIds)
                ->order('cms_page_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['cp' => $this->getMainTable()])
            ->join(
                ['cps' => $this->getTable('cms/page_store')],
                'cp.page_id = cps.page_id',
                [],
            )
            ->where('cp.identifier = ?', $identifier)
            ->where('cps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('cp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of page to selected store(s).
     *
     * @return bool
     */
    public function getIsUniquePageToStores(Mage_Core_Model_Abstract $object)
    {
        if (!$object->hasStores()) {
            $stores = [Mage_Core_Model_App::ADMIN_STORE_ID];
        } else {
            $stores = (array) $object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('cps.page_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether page identifier is numeric
     *
     * @return int|false
     */
    protected function isNumericPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether page identifier is valid
     *
     *
     * @return   int|false
     */
    protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    public function getUsedInStoreConfigCollection(Mage_Cms_Model_Page $page, ?array $paths = []): Mage_Core_Model_Resource_Db_Collection_Abstract
    {
        $storeIds   = (array) $page->getStoreId();
        $storeIds[] = Mage_Core_Model_App::ADMIN_STORE_ID;
        $config     = Mage::getResourceModel('core/config_data_collection')
            ->addFieldToFilter('value', $page->getIdentifier())
            ->addFieldToFilter('scope_id', ['in' => $storeIds]);

        if (!is_null($paths)) {
            $paths = Mage_Cms_Helper_Page::getUsedInStoreConfigPaths($paths);
            $config->addFieldToFilter('path', ['in' => $paths]);
        }

        return $config;
    }

    public function isUsedInStoreConfig(?Mage_Cms_Model_Page $page = null, ?array $paths = []): bool
    {
        return (bool) $this->getUsedInStoreConfigCollection($page, $paths)->count();
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [Mage_Core_Model_App::ADMIN_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cp.page_id')
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     *
     * @param string|int $identifier
     * @return string
     */
    public function getCmsPageTitleByIdentifier($identifier)
    {
        $stores = [Mage_Core_Model_App::ADMIN_STORE_ID];
        if ($this->_store) {
            $stores[] = (int) $this->getStore()->getId();
        }

        $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cp.title')
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param string|int $id
     * @return string
     */
    public function getCmsPageTitleById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'title')
            ->where('page_id = :page_id');

        $binds = [
            'page_id' => (int) $id,
        ];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageIdentifierById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'identifier')
            ->where('page_id = :page_id');

        $binds = [
            'page_id' => (int) $id,
        ];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param string $pageId
     * @return array
     */
    public function lookupStoreIds($pageId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('cms/page_store'), 'store_id')
            ->where('page_id = ?', (int) $pageId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }
}
