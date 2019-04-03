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
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute resource model (Using Forms)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    abstract protected function _getEavWebsiteTable();

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids are stored
     *
     * @return string|null
     */
    abstract protected function _getFormAttributeTable();

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $validateRules = $object->getData('validate_rules');
        if (is_array($validateRules)) {
            $object->setData('validate_rules', serialize($validateRules));
        }
        return parent::_beforeSave($object);
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
        $select     = parent::_getLoadSelect($field, $value, $object);
        $websiteId  = (int)$object->getWebsite()->getId();
        if ($websiteId) {
            $adapter    = $this->_getReadAdapter();
            $columns    = array();
            $scopeTable = $this->_getEavWebsiteTable();
            $describe   = $adapter->describeTable($scopeTable);
            unset($describe['attribute_id']);
            foreach (array_keys($describe) as $columnName) {
                $columns['scope_' . $columnName] = $columnName;
            }
            $conditionSql = $adapter->quoteInto(
                $this->getMainTable() . '.attribute_id = scope_table.attribute_id AND scope_table.website_id =?',
                $websiteId);
            $select->joinLeft(
                array('scope_table' => $scopeTable),
                $conditionSql,
                $columns
            );
        }

        return $select;
    }

    /**
     * Save attribute/form relations after attribute save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Attribute
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $forms      = $object->getData('used_in_forms');
        $adapter    = $this->_getWriteAdapter();
        if (is_array($forms)) {
            $where = array('attribute_id=?' => $object->getId());
            $adapter->delete($this->_getFormAttributeTable(), $where);

            $data = array();
            foreach ($forms as $formCode) {
                $data[] = array(
                    'form_code'     => $formCode,
                    'attribute_id'  => (int)$object->getId()
                );
            }

            if ($data) {
                $adapter->insertMultiple($this->_getFormAttributeTable(), $data);
            }
        }

        // update sort order
        if (!$object->isObjectNew() && $object->dataHasChangedFor('sort_order')) {
            $data  = array('sort_order' => $object->getSortOrder());
            $where = array('attribute_id=?' => (int)$object->getId());
            $adapter->update($this->getTable('eav/entity_attribute'), $data, $where);
        }

        // save scope attributes
        $websiteId = (int)$object->getWebsite()->getId();
        if ($websiteId) {
            $table      = $this->_getEavWebsiteTable();
            $describe   = $this->_getReadAdapter()->describeTable($table);
            $data       = array();
            if (!$object->getScopeWebsiteId() || $object->getScopeWebsiteId() != $websiteId) {
                $data = $this->getScopeValues($object);
            }

            $data['attribute_id']   = (int)$object->getId();
            $data['website_id']     = (int)$websiteId;
            unset($describe['attribute_id']);
            unset($describe['website_id']);

            $updateColumns = array();
            foreach (array_keys($describe) as $columnName) {
                $data[$columnName] = $object->getData('scope_' . $columnName);
                $updateColumns[]   = $columnName;
            }

            $adapter->insertOnDuplicate($table, $data, $updateColumns);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return scope values for attribute and website
     *
     * @param Mage_Eav_Model_Attribute $object
     * @return array
     */
    public function getScopeValues(Mage_Eav_Model_Attribute $object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(
            'attribute_id' => (int)$object->getId(),
            'website_id'   => (int)$object->getWebsite()->getId()
        );
        $select = $adapter->select()
            ->from($this->_getEavWebsiteTable())
            ->where('attribute_id = :attribute_id')
            ->where('website_id = :website_id')
            ->limit(1);
        $result = $adapter->fetchRow($select, $bind);

        if (!$result) {
            $result = array();
        }

        return $result;
    }

    /**
     * Return forms in which the attribute
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    public function getUsedInForms(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('attribute_id' => (int)$object->getId());
        $select  = $adapter->select()
            ->from($this->_getFormAttributeTable(), 'form_code')
            ->where('attribute_id = :attribute_id');

        return $adapter->fetchCol($select, $bind);
    }
}
