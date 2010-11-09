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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog entity abstract model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes = array();
    /**
     * Redeclare attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return 'catalog/resource_eav_attribute';
    }

    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Check whether the attribute is Applicable to the object
     *
     * @param   Varien_Object $object
     * @param   Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return  boolean
     */
    protected function _isApplicableAttribute ($object, $attribute)
    {
        $applyTo = $attribute->getApplyTo();
        return count($applyTo) == 0 || in_array($object->getTypeId(), $applyTo);
    }

    /**
     * Retrieve select object for loading entity attributes values
     *
     * Join attribute store value
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        /**
         * This condition is applicable for all cases when we was work in not single
         * store mode, customize some value per specific store view and than back
         * to single store mode. We should load correct values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
        }
        else {
            $storeId = $object->getStoreId();
        }

        $setId  = $object->getAttributeSetId();
        $storeIds = array($this->getDefaultStoreId());
        if ($storeId != $this->getDefaultStoreId()) {
            $storeIds[] = $storeId;
        }
        $select = $this->_getReadAdapter()->select()
            ->from(array('attr_table' => $table))
            ->where('attr_table.'.$this->getEntityIdField().'=?', $object->getId())
            ->where('attr_table.store_id IN (?)', $storeIds);
        if ($setId) {
            $select->join(
                array('set_table' => $this->getTable('eav/entity_attribute')),
                'attr_table.attribute_id=set_table.attribute_id AND set_table.attribute_set_id=' . intval($setId),
                array()
            );
        }
        return $select;
    }

    /**
     * Prepare select object for loading entity attributes values
     *
     * @param  array $selects
     * @return Zend_Db_Select
     */
    protected function _prepareLoadSelect(array $selects)
    {
        $select = parent::_prepareLoadSelect($selects);
        $select->order('store_id');
        return $select;
    }

    /**
     * Initialize attribute value for object
     *
     * @param   Mage_Catalog_Model_Abstract $object
     * @param   array $valueRow
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _setAttribteValue($object, $valueRow)
    {
        $attribute = $this->getAttribute($valueRow['attribute_id']);
        if ($attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $isDefaultStore = $valueRow['store_id'] == $this->getDefaultStoreId();
            if (isset($this->_attributes[$valueRow['attribute_id']])) {
                if ($isDefaultStore) {
                    $object->setAttributeDefaultValue($attributeCode, $valueRow['value']);
                }
                else {
                    $object->setAttributeDefaultValue($attributeCode, $this->_attributes[$valueRow['attribute_id']]['value']);
                }
            }
            else {
                $this->_attributes[$valueRow['attribute_id']] = $valueRow;
            }

            $value   = $valueRow['value'];
            $valueId = $valueRow['value_id'];

            $object->setData($attributeCode, $value);
            if (!$isDefaultStore) {
                $object->setExistsStoreValueFlag($attributeCode);
            }
            $attribute->getBackend()->setValueId($valueId);
        }
        return $this;
    }

    /**
     * Insert or Update attribute data
     *
     * @param Mage_Catalog_Model_Abstract $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    protected function _saveAttributeValue($object, $attribute, $value)
    {
        $write   = $this->_getWriteAdapter();
        $storeId = Mage::app()->getStore($object->getStoreId())->getId();
        $table   = $attribute->getBackend()->getTable();

        /**
         * If we work in single store mode all values should be saved just
         * for default store id
         * In this case we clear all not default values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $storeId = $this->getDefaultStoreId();
            $write->delete($table, join(' AND ', array(
                $write->quoteInto('attribute_id=?', $attribute->getAttributeId()),
                $write->quoteInto('entity_id=?', $object->getEntityId()),
                $write->quoteInto('store_id<>?', $storeId)
            )));
        }

        $bind = array(
            'entity_type_id'    => $attribute->getEntityTypeId(),
            'attribute_id'      => $attribute->getAttributeId(),
            'store_id'          => $storeId,
            'entity_id'         => $object->getEntityId(),
            'value'             => $this->_prepareValueForSave($value, $attribute)
        );

        if ($attribute->isScopeStore()) {
            /**
             * Update attribute value for store
             */
            $this->_attributeValuesToSave[$table][] = $bind;
        } else if ($attribute->isScopeWebsite() && $storeId != $this->getDefaultStoreId()) {
            /**
             * Update attribute value for website
             */
            $storeIds = Mage::app()->getStore($storeId)->getWebsite()->getStoreIds(true);
            foreach ($storeIds as $storeId) {
                $bind['store_id'] = $storeId;
                $this->_attributeValuesToSave[$table][] = $bind;
            }
        } else {
            /**
             * Update global attribute value
             */
            $bind['store_id'] = $this->getDefaultStoreId();
            $this->_attributeValuesToSave[$table][] = $bind;
        }

        return $this;
    }

    /**
     * Insert entity attribute value
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        /**
         * save required attributes in global scope every time if store id different from default
         */
        $storeId = Mage::app()->getStore($object->getStoreId())->getId();
        if ($attribute->getIsRequired() && $this->getDefaultStoreId() != $storeId) {
            $bind = array(
                'entity_type_id'    => $attribute->getEntityTypeId(),
                'attribute_id'      => $attribute->getAttributeId(),
                'store_id'          => $this->getDefaultStoreId(),
                'entity_id'         => $object->getEntityId(),
                'value'             => $this->_prepareValueForSave($value, $attribute)
            );
            $this->_getWriteAdapter()->insertOnDuplicate($attribute->getBackend()->getTable(), $bind, array('value'));
        }
        return $this->_saveAttributeValue($object, $attribute, $value);

//        $entityIdField = $attribute->getBackend()->getEntityIdField();
//        $row = array(
//            $entityIdField  => $object->getId(),
//            'entity_type_id'=> $object->getEntityTypeId(),
//            'attribute_id'  => $attribute->getId(),
//            'value'         => $this->_prepareValueForSave($value, $attribute),
//            'store_id'      => $this->getDefaultStoreId()
//        );
//
//        $fields = array();
//        $bind = array();
//        foreach ($row as $k => $v) {
//            $fields[] = $this->_getWriteAdapter()->quoteIdentifier($k);
//            $bind[':' . $k] = $v;
//        }
//
//        $sql = sprintf('INSERT IGNORE INTO %s (%s) VALUES(%s)',
//            $this->_getWriteAdapter()->quoteIdentifier($attribute->getBackend()->getTable()),
//            implode(',', $fields),
//            implode(',', array_keys($bind)));
//
//        $this->_getWriteAdapter()->query($sql, $bind);
//        if (!$lastId = $this->_getWriteAdapter()->lastInsertId()) {
//            $select = $this->_getReadAdapter()->select()
//                ->from($attribute->getBackend()->getTable(), 'value_id')
//                ->where($entityIdField . '=?', $row[$entityIdField])
//                ->where('entity_type_id=?', $row['entity_type_id'])
//                ->where('attribute_id=?', $row['attribute_id'])
//                ->where('store_id=?', $row['store_id']);
//            $lastId = $select->query()->fetchColumn();
//        }
//        if ($object->getStoreId() != $this->getDefaultStoreId()) {
//            $this->_updateAttribute($object, $attribute, $lastId, $value);
//        }
//        return $this;
    }

    /**
     * Update entity attribute value
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $valueId
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
        return $this->_saveAttributeValue($object, $attribute, $value);
//
//        /**
//         * If we work in single store mode all values should be saved just
//         * for default store id
//         * In this case we clear all not default values
//         */
//        if (Mage::app()->isSingleStoreMode()) {
//            $this->_getWriteAdapter()->delete(
//                $attribute->getBackend()->getTable(),
//                $this->_getWriteAdapter()->quoteInto('attribute_id=?', $attribute->getId()) .
//                $this->_getWriteAdapter()->quoteInto(' AND entity_id=?', $object->getId()) .
//                $this->_getWriteAdapter()->quoteInto(' AND store_id!=?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
//            );
//        }
//
//        /**
//         * Update attribute value for store
//         */
//        if ($attribute->isScopeStore()) {
//            $this->_updateAttributeForStore($object, $attribute, $value, $object->getStoreId());
//        }
//
//        /**
//         * Update attribute value for website
//         */
//        elseif ($attribute->isScopeWebsite()) {
//            if ($object->getStoreId() == 0) {
//                $this->_updateAttributeForStore($object, $attribute, $value, $object->getStoreId());
//            } else {
//                if (is_array($object->getWebsiteStoreIds())) {
//                    foreach ($object->getWebsiteStoreIds() as $storeId) {
//                        $this->_updateAttributeForStore($object, $attribute, $value, $storeId);
//                    }
//                }
//            }
//        }
//        else {
//            $this->_getWriteAdapter()->update($attribute->getBackend()->getTable(),
//                array('value' => $this->_prepareValueForSave($value, $attribute)),
//                'value_id='.(int)$valueId
//            );
//        }
//        return $this;
    }

    /**
     * Update attribute value for specific store
     *
     * @param   Mage_Catalog_Model_Abstract $object
     * @param   object $attribute
     * @param   mixed $value
     * @param   int $storeId
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    protected function _updateAttributeForStore($object, $attribute, $value, $storeId)
    {
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $select = $this->_getWriteAdapter()->select()
            ->from($attribute->getBackend()->getTable(), 'value_id')
            ->where('entity_type_id=?', $object->getEntityTypeId())
            ->where("$entityIdField=?",$object->getId())
            ->where('store_id=?', $storeId)
            ->where('attribute_id=?', $attribute->getId());
        /**
         * When value for store exist
         */
        if ($valueId = $this->_getWriteAdapter()->fetchOne($select)) {
            $this->_getWriteAdapter()->update($attribute->getBackend()->getTable(),
                array('value' => $this->_prepareValueForSave($value, $attribute)),
                'value_id='.$valueId
            );
        }
        else {
            $this->_getWriteAdapter()->insert($attribute->getBackend()->getTable(), array(
                $entityIdField  => $object->getId(),
                'entity_type_id'=> $object->getEntityTypeId(),
                'attribute_id'  => $attribute->getId(),
                'value'         => $this->_prepareValueForSave($value, $attribute),
                'store_id'      => $storeId
            ));
        }

        return $this;
    }

    /**
     * Delete entity attribute values
     *
     * @param   Varien_Object $object
     * @param   string $table
     * @param   array $info
     * @return  Varien_Object
     */
    protected function _deleteAttributes($object, $table, $info)
    {
        $entityIdField      = $this->getEntityIdField();
        $globalValues       = array();
        $websiteAttributes  = array();
        $storeAttributes    = array();

        /**
         * Separate attributes by scope
         */
        foreach ($info as $itemData) {
            $attribute = $this->getAttribute($itemData['attribute_id']);
            if ($attribute->isScopeStore()) {
                $storeAttributes[] = $itemData['attribute_id'];
            }
            elseif ($attribute->isScopeWebsite()) {
                $websiteAttributes[] = $itemData['attribute_id'];
            }
            else {
                $globalValues[] = $itemData['value_id'];
            }
        }

        /**
         * Delete global scope attributes
         */
        if (!empty($globalValues)) {
            $condition = $this->_getWriteAdapter()->quoteInto('value_id IN (?)', $globalValues);
            $this->_getWriteAdapter()->delete($table, $condition);
        }

        $condition = $this->_getWriteAdapter()->quoteInto("$entityIdField=?", $object->getId())
            . $this->_getWriteAdapter()->quoteInto(' AND entity_type_id=?', $object->getEntityTypeId());
        /**
         * Delete website scope attributes
         */
        if (!empty($websiteAttributes)) {
            $storeIds = $object->getWebsiteStoreIds();
            if (!empty($storeIds)) {
                $delCondition = $condition
                    . $this->_getWriteAdapter()->quoteInto(' AND attribute_id IN(?)', $websiteAttributes)
                    . $this->_getWriteAdapter()->quoteInto(' AND store_id IN(?)', $storeIds);
                $this->_getWriteAdapter()->delete($table, $delCondition);
            }
        }

        /**
         * Delete store scope attributes
         */
        if (!empty($storeAttributes)) {
            $delCondition = $condition
                . $this->_getWriteAdapter()->quoteInto(' AND attribute_id IN(?)', $storeAttributes)
                . $this->_getWriteAdapter()->quoteInto(' AND store_id =?', $object->getStoreId());
            $this->_getWriteAdapter()->delete($table, $delCondition);;
        }
        return $this;
    }

    /**
     * Retrieve Object instance with original data
     *
     * @param Varien_Object $object
     * @return Varien_Object
     */
    protected function _getOrigObject($object)
    {
        $className  = get_class($object);
        $origObject = new $className();
        $origObject->setData(array());
        $origObject->setStoreId($object->getStoreId());
        $this->load($origObject, $object->getData($this->getEntityIdField()));
        return $origObject;
    }

    protected function _collectOrigData($object)
    {
        $this->loadAllAttributes($object);

        if ($this->getUseDataSharing()) {
            $storeId = $object->getStoreId();
        } else {
            $storeId = $this->getStoreId();
        }

        $allStores = Mage::getConfig()->getStoresConfigByPath('system/store/id', array(), 'code');
//echo "<pre>".print_r($allStores ,1)."</pre>"; exit;
        $data = array();

        foreach ($this->getAttributesByTable() as $table=>$attributes) {
            $entityIdField = current($attributes)->getBackend()->getEntityIdField();

            $select = $this->_getReadAdapter()->select()
                ->from($table)
                ->where($this->getEntityIdField()."=?", $object->getId());

            $where = $this->_getReadAdapter()->quoteInto("store_id=?", $storeId);

            $globalAttributeIds = array();
            foreach ($attributes as $attrCode=>$attr) {
                if ($attr->getIsGlobal()) {
                    $globalAttributeIds[] = $attr->getId();
                }
            }
            if (!empty($globalAttributeIds)) {
                $where .= ' or '.$this->_getReadAdapter()->quoteInto('attribute_id in (?)', $globalAttributeIds);
            }
            $select->where($where);

            $values = $this->_getReadAdapter()->fetchAll($select);

            if (empty($values)) {
                continue;
            }
            foreach ($values as $row) {
                $data[$this->getAttribute($row['attribute_id'])->getName()][$row['store_id']] = $row;
            }
            foreach ($attributes as $attrCode=>$attr) {

            }
        }

        return $data;
    }

    /**
     * Check is attribute value empty
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value
     * @return bool
     */
    protected function _isAttributeValueEmpty(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $value)
    {
        return $value === false;
    }

    /**
     * Prepare value for save
     *
     * @param mixed $value
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return mixed
     */
    protected function _prepareValueForSave($value, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $type = $attribute->getBackendType();
        if (($type == 'int' || $type == 'decimal' || $type == 'datetime') && $value === '') {
            return null;
        }
        if ($type == 'decimal') {
            return Mage::app()->getLocale()->getNumber($value);
        }
        return $value;
    }

    /**
     * Retrieve attribute's raw value from DB.
     *
     * @param int $entityId
     * @param int|string|array $attribute atrribute's ids or codes
     * @param int|Mage_Core_Model_Store $store
     * @return bool|string|array
     */
    public function getAttributeRawValue($entityId, $attribute, $store)
    {
        if (!$entityId || empty($attribute)) {
            return false;
        }
        if (!is_array($attribute)) {
            $attribute = array($attribute);
        }
        $attributesData   = array();
        $staticAttributes = array();
        $typedAttributes  = array();
        $staticTable      = null;
        foreach ($attribute as $_attribute) {
            /* @var $attribute Mage_Catalog_Model_Entity_Attribute */
            $_attribute = $this->getAttribute($_attribute);
            if (!$_attribute) {
                continue;
            }
            $attributeCode = $_attribute->getAttributeCode();
            $attrTable     = $_attribute->getBackend()->getTable();
            $isStatic      = $_attribute->getBackend()->isStatic();
            if ($isStatic) {
                $staticAttributes[] = $attributeCode;
                $staticTable = $attrTable;
            }
            else {
                /**
                 * That structure needed to avoid farther sql joins for getting attribute's code by id
                 */
                $typedAttributes[$attrTable][$_attribute->getId()] = $attributeCode;
            }

        }
        /* @var $select Zend_Db_Select */
        $select = $this->_getReadAdapter()->select();
        /**
         * Collecting static attributes
         */
        if ($staticAttributes) {
            $select->from($staticTable, $staticAttributes)
                ->where($this->getEntityIdField() . ' = ?', $entityId);
            $attributesData = $this->_getReadAdapter()->fetchRow($select);
        }

        /**
         * Collecting typed attributes, performing separate SQL query for each attribute type table
         */
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }
        $store = (int)$store;
        if ($typedAttributes) {
            foreach ($typedAttributes as $table => $_attributes) {
                $select->reset()->from(array('default_value' => $table), array());
                $select->where('default_value.attribute_id IN (?)', array_keys($_attributes))
                    ->where('default_value.entity_type_id = ? ', $this->getTypeId())
                    ->where('default_value.entity_id = ? ', $entityId)
                    ->where('default_value.store_id = 0');

                $joinCondition = $this->_getReadAdapter()->quoteInto('store_value.attribute_id IN (?)', array_keys($_attributes));
                $joinCondition .= ' AND ' . $this->_getReadAdapter()->quoteInto('store_value.entity_type_id = ?', $this->getTypeId());
                $joinCondition .= ' AND ' . $this->_getReadAdapter()->quoteInto('store_value.entity_id = ?', $entityId);
                $joinCondition .= ' AND ' . $this->_getReadAdapter()->quoteInto('store_value.store_id = ?', $store);

                $select->joinLeft(array('store_value' => $table),
                    $joinCondition,
                    array('attr_value' => 'IFNULL(store_value.value, default_value.value)', 'default_value.attribute_id')
                );
                $result = $this->_getReadAdapter()->fetchAll($select);
                foreach ($result as $key => $_attribute) {
                    $attributeCode = $typedAttributes[$table][$_attribute['attribute_id']];
                    $attributesData[$attributeCode] = $_attribute['attr_value'];
                }
            }
        }
        if (sizeof($attributesData) == 1) {
            $_data = each($attributesData);
            $attributesData = $_data[1];
        }
        return $attributesData ? $attributesData : false;
    }

    /**
     * Reset firstly loaded attributes
     *
     * @param   Varien_Object $object
     * @param   integer $entityId
     * @param   array|null $attributes
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    public function load($object, $entityId, $attributes=array())
    {
        $this->_attributes = array();
        return parent::load($object, $entityId, $attributes);
    }
}
