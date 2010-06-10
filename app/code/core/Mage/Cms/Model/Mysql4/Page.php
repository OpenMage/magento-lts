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
 * Cms page mysql resource
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cms_Model_Mysql4_Page extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent datetime data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value.
         */
        foreach (array('custom_theme_from', 'custom_theme_to') as $dataKey) {
            if (!$object->getData($dataKey)) {
                $object->setData($dataKey, new Zend_Db_Expr('NULL'));
            }
        }

        if (!$this->getIsUniquePageToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('A page URL key for specified store already exists.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key cannot consist only of numbers.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('page_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('cms/page_store'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['page_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('cms/page_store'), $storeArray);
        }

        return parent::_afterSave($object);
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (!is_numeric($value)) {
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
            ->from($this->getTable('cms/page_store'))
            ->where('page_id = ?', $object->getId());

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

        $storeId = $object->getStoreId();
        if ($storeId) {
            $select->join(
                        array('cps' => $this->getTable('cms/page_store')),
                        $this->getMainTable().'.page_id = `cps`.page_id'
                    )
                    ->where('is_active=1 AND `cps`.store_id IN (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    /**
     * Check for unique of identifier of page to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePageToStores(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->join(array('cps' => $this->getTable('cms/page_store')), $this->getMainTable().'.page_id = `cps`.page_id')
                ->where($this->getMainTable().'.identifier = ?', $object->getData('identifier'));
        if ($object->getId()) {
            $select->where($this->getMainTable().'.page_id <> ?',$object->getId());
        }
        $stores = (array)$object->getData('stores');
        if (Mage::app()->isSingleStoreMode()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        }
        $select->where('`cps`.store_id IN (?)', $stores);

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether page identifier is numeric
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return      bool
     *  @date      Wed Mar 26 18:12:28 EET 2008
     */
    protected function isNumericPageIdentifier (Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'page_id')
            ->join(
                array('cps' => $this->getTable('cms/page_store')),
                'main_table.page_id = `cps`.page_id'
            )
            ->where('main_table.identifier=?', $identifier)
            ->where('main_table.is_active=1 AND `cps`.store_id IN (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
            ->order('store_id DESC');

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getCmsPageTitleByIdentifier($identifier)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $joinExpr = $this->_getReadAdapter()->quoteInto(
            'main_table.page_id = cps.page_id AND (cps.store_id = ' . Mage_Core_Model_App::ADMIN_STORE_ID . ' OR cps.store_id = ?)', $this->getStore()->getId()
        );
        $select->from(array('main_table' => $this->getMainTable()), 'title')
        ->joinLeft(array('cps' => $this->getTable('cms/page_store')), $joinExpr ,array())
            ->where('main_table.identifier = ?', $identifier)
            ->order('cps.store_id DESC');
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageTitleById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'title')
            ->where('main_table.page_id = ?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageIdentifierById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'identifier')
            ->where('main_table.page_id = ?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
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
            ->from($this->getTable('cms/page_store'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Mysql4_Page
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
