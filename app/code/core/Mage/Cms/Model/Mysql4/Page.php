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
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    /**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniquePageToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('Page Identifier for specified store already exist.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('Page Identifier cannot consist only of numbers.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        if (!$object->getCustomThemeFrom()) {
            $object->setCustomThemeFrom(new Zend_Db_Expr('NULL'));
        }

        if (!$object->getCustomThemeTo()) {
            $object->setCustomThemeTo(new Zend_Db_Expr('NULL'));
        }
        return $this;
    }

    /**
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
        if (strcmp($value, (int)$value) !== 0) {
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

        if ($object->getStoreId()) {
            $select->join(array('cps' => $this->getTable('cms/page_store')), $this->getMainTable().'.page_id = `cps`.page_id')
                    ->where('is_active=1 AND `cps`.store_id in (0, ?) ', $object->getStoreId())
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
        $select->where('`cps`.store_id IN (?)', (array)$object->getData('stores'));

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether page identifier is numeric
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return	  bool
     *  @date	  Wed Mar 26 18:12:28 EET 2008
     */
    protected function isNumericPageIdentifier (Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }
}