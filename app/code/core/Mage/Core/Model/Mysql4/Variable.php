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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom variable resource model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Variable extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('core/variable', 'variable_id');
    }

    /**
     * Load variable by code
     *
     * @param Mage_Core_Model_Variable $object
     * @param string $code
     * @return Mage_Core_Model_Mysql4_Variable
     */
    public function loadByCode(Mage_Core_Model_Variable $object, $code)
    {
        if ($result = $this->getVariableByCode($code, true, $object->getStoreId())) {
            $object->setData($result);
        }
        return $this;
    }

    /**
     * Retrieve variable data by code
     *
     * @param string $code
     * @param boolean $withValue
     * @param integer $storeId
     * @return array
     */
    public function getVariableByCode($code, $withValue = false, $storeId = 0)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable().'.code = ?', $code);
        if ($withValue) {
            $this->_addValueToSelect($select, $storeId);
        }
        return $this->_getReadAdapter()->fetchRow($select);
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Variable
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        if ($object->getUseDefaultValue()) {
            /*
             * remove store value
             */
            $this->_getWriteAdapter()->delete(
                $this->getTable('core/variable_value'), array(
                    'variable_id = ?' => $object->getId(),
                    'store_id = ?' => $object->getStoreId()
            ));
        } else {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getTable('core/variable_value'), array(
                    'variable_id' => $object->getId(),
                    'store_id'    => $object->getStoreId(),
                    'plain_value' => $object->getPlainValue(),
                    'html_value'  => $object->getHtmlValue()
                ), array('plain_value', 'html_value'));
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $this->_addValueToSelect($select, $object->getStoreId());
        return $select;
    }

    /**
     * Add variable store and default value to select
     *
     * @param Zend_Db_Select $select
     * @param integer $storeId
     * @return Mage_Core_Model_Mysql4_Variable
     */
    protected function _addValueToSelect(Zend_Db_Select $select, $storeId = 0)
    {
        $select->joinLeft(
                array('default' => $this->getTable('core/variable_value')),
                'default.variable_id = '.$this->getMainTable().'.variable_id AND default.store_id = 0',
                array())
            ->joinLeft(
                array('store' => $this->getTable('core/variable_value')),
                'store.variable_id = default.variable_id AND store.store_id = ' . $storeId,
                array())
            ->columns(array('plain_value' => new Zend_Db_Expr('IFNULL(store.plain_value, default.plain_value)'),
                'html_value' => new Zend_Db_Expr('IFNULL(store.html_value, default.html_value)'),
                'store_plain_value' => 'store.plain_value', 'store_html_value' => 'store.html_value'));
        return $this;
    }
}
