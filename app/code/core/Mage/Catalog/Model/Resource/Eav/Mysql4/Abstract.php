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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        } else {
            $storeId = $object->getStoreId();
        }

        $joinCondition = 'main.attribute_id=default.attribute_id AND '
            . $this->_read->quoteInto('main.store_id=? AND ', $storeId)
            . $this->_read->quoteInto('main.'.$this->getEntityIdField() . '=?', $object->getId());

        $select = $this->_read->select()
            ->from(array('default' => $table))
            ->joinLeft(array('main' => $table), $joinCondition, array(
                'store_value_id'=>'value_id',
                'store_value'=>'value'
            ))
            ->where('default.'.$this->getEntityIdField() . '=?', $object->getId())
            ->where('default.store_id=?', $this->getDefaultStoreId());

        return $select;
    }

    /**
     * Initialize attribute value for object
     *
     * @param   Varien_Object $object
     * @param   array $valueRow
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _setAttribteValue($object, $valueRow)
    {
        parent::_setAttribteValue($object, $valueRow);
        if ($attribute = $this->getAttribute($valueRow['attribute_id'])) {
            $attributeCode = $attribute->getAttributeCode();
            if (isset($valueRow['store_value'])) {
                $object->setAttributeDefaultValue($attributeCode, $valueRow['value']);
                $object->setData($attributeCode, $valueRow['store_value']);
                $attribute->getBackend()->setValueId($valueRow['store_value_id']);
            }
        }
        return $this;
    }

    /**
     * Insert entity attribute value
     *
     * Insert attribute value we do only for default store
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $row = array(
            $entityIdField  => $object->getId(),
            'entity_type_id'=> $object->getEntityTypeId(),
            'attribute_id'  => $attribute->getId(),
            'value'         => $this->_prepareValueForSave($value, $attribute),
            'store_id'      => $this->getDefaultStoreId()
        );
        $this->_getWriteAdapter()->insert($attribute->getBackend()->getTable(), $row);
        if ($object->getStoreId() != $this->getDefaultStoreId()) {
            $this->_updateAttribute($object, $attribute, $this->_getWriteAdapter()->lastInsertId(), $value);
        }
        return $this;
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
        /**
         * If we work in single store mode all values should be saved just
         * for default store id
         * In this case we clear all not default values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $this->_getWriteAdapter()->delete(
                $attribute->getBackend()->getTable(),
                $this->_getWriteAdapter()->quoteInto('attribute_id=?', $attribute->getId()) .
                $this->_getWriteAdapter()->quoteInto(' AND entity_id=?', $object->getId()) .
                $this->_getWriteAdapter()->quoteInto(' AND store_id!=?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            );
        }

        /**
         * Update attribute value for store
         */
        if ($attribute->isScopeStore()) {
            $this->_updateAttributeForStore($object, $attribute, $value, $object->getStoreId());
        }

        /**
         * Update attribute value for website
         */
        elseif ($attribute->isScopeWebsite()) {
            if ($object->getStoreId() == 0) {
                $this->_updateAttributeForStore($object, $attribute, $value, $object->getStoreId());
            } else {
                if (is_array($object->getWebsiteStoreIds())) {
                    foreach ($object->getWebsiteStoreIds() as $storeId) {
                        $this->_updateAttributeForStore($object, $attribute, $value, $storeId);
                    }
                }
            }
        }
        else {
            $this->_getWriteAdapter()->update($attribute->getBackend()->getTable(),
                array('value' => $this->_prepareValueForSave($value, $attribute)),
                'value_id='.(int)$valueId
            );
        }
        return $this;
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
                $websiteAttributes = $itemData['attribute_id'];
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

            $select = $this->_read->select()
                ->from($table)
                ->where($this->getEntityIdField()."=?", $object->getId());

            $where = $this->_read->quoteInto("store_id=?", $storeId);

            $globalAttributeIds = array();
            foreach ($attributes as $attrCode=>$attr) {
                if ($attr->getIsGlobal()) {
                    $globalAttributeIds[] = $attr->getId();
                }
            }
            if (!empty($globalAttributeIds)) {
                $where .= ' or '.$this->_read->quoteInto('attribute_id in (?)', $globalAttributeIds);
            }
            $select->where($where);

            $values = $this->_read->fetchAll($select);

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

}
