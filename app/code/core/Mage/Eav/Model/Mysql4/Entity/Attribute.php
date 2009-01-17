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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected static $_entityAttributes = null;

    protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');
        $this->_uniqueFields = array(
            array('field' => array('attribute_code','entity_type_id'),
                'title' => Mage::helper('eav')->__('Attribute with the same code')
        ));
    }

    protected function _loadTypeAttributes($entityTypeId)
    {
        if (!isset(self::$_entityAttributes[$entityTypeId])) {
            $select = $this->_getReadAdapter()->select()->from($this->getMainTable())
                ->where('entity_type_id=?', $entityTypeId);
            $data = $this->_getReadAdapter()->fetchAll($select);
            foreach ($data as $row) {
                self::$_entityAttributes[$entityTypeId][$row['attribute_code']] = $row;
            }
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $entityTypeId
     * @param string $code
     * @return boolean
     */
    public function loadByCode(Mage_Core_Model_Abstract $object, $entityTypeId, $code)
    {
        $select = $this->_getLoadSelect('attribute_code', $code, $object)
            ->where('entity_type_id=?', $entityTypeId);
        $data = $this->_getReadAdapter()->fetchRow($select);

        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
            return true;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return int
     */
    private function _getMaxSortOrder(Mage_Core_Model_Abstract $object)
    {
        if( intval($object->getAttributeGroupId()) > 0 ) {
            $read = $this->_getReadAdapter();
            $select = $read->select()
                ->from($this->getTable('entity_attribute'), new Zend_Db_Expr("MAX(`sort_order`)"))
                ->where("{$this->getTable('entity_attribute')}.attribute_set_id = ?", $object->getAttributeSetId())
                ->where("{$this->getTable('entity_attribute')}.attribute_group_id = ?", $object->getAttributeGroupId());
            $maxOrder = $read->fetchOne($select);
            return $maxOrder;
        }

        return 0;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    public function deleteEntity(Mage_Core_Model_Abstract $object)
    {
        $write = $this->_getWriteAdapter();
        $condition = $write->quoteInto($this->getTable('entity_attribute').'.entity_attribute_id = ?', $object->getEntityAttributeId());
        /**
         * Delete attribute values
         */
        $select = $write->select()
            ->from($this->getTable('entity_attribute'))
            ->where($condition);
        $data = $write->fetchRow($select);
        if (!empty($data)) {
            /**
             * @todo !!!! need fix retrieving attribute entity, this realization is temprary
             */
            $attribute = Mage::getModel('eav/entity_attribute')
                ->load($data['attribute_id'])
                ->setEntity(Mage::getSingleton('catalog/product')->getResource());

            if ($this->isUsedBySuperProducts($attribute, $data['attribute_set_id'])) {
                Mage::throwException(Mage::helper('eav')->__("Attribute '%s' used in configurable products.", $attribute->getAttributeCode()));
            }

            if ($backendTable = $attribute->getBackend()->getTable()) {
                $clearCondition = array(
                    $write->quoteInto('entity_type_id=?',$attribute->getEntityTypeId()),
                    $write->quoteInto('attribute_id=?',$attribute->getId()),
                    $write->quoteInto('entity_id IN (
                        SELECT entity_id FROM '.$attribute->getEntity()->getEntityTable().' WHERE attribute_set_id=?)',
                        $data['attribute_set_id'])
                );
                $write->delete($backendTable, $clearCondition);
            }
        }

        $write->delete($this->getTable('entity_attribute'), $condition);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $frontendLabel = $object->getFrontendLabel();
        if (is_array($frontendLabel)) {
            if (!isset($frontendLabel[0]) || is_null($frontendLabel[0]) || $frontendLabel[0]=='') {
                Mage::throwException(Mage::helper('eav')->__('Frontend label is not defined'));
            }
            $object->setFrontendLabel($frontendLabel[0]);

            if ($object->getData('modulePrefix')) {
                $str = $object->getData('modulePrefix') . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $frontendLabel[0];
            }
            else {
                $str = $frontendLabel[0];
            }
            Mage::getModel('core/translate_string')
                ->setString($str)
                ->setTranslate($frontendLabel[0])
                ->setStoreTranslations($frontendLabel)
                ->save();
        }
        $applyTo = $object->getApplyTo();

        if (is_array($applyTo)) {
            $object->setApplyTo(implode(',', $applyTo));
        }

        /**
         * @todo need use default source model of entity type !!!
         */
        if (!$object->getId()) {
            if ($object->getFrontendInput()=='select') {
                $object->setSourceModel('eav/entity_attribute_source_table');
            }
        }

        return parent::_beforeSave($object);
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->saveInSetIncluding($object)
            ->_saveOption($object);
        return parent::_afterSave($object);
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    public function saveInSetIncluding(Mage_Core_Model_Abstract $object)
    {
        $attrId = $object->getId();
        $setId  = (int) $object->getAttributeSetId();
        $groupId= (int) $object->getAttributeGroupId();

        if ($setId && $groupId && $object->getEntityTypeId()) {
            $write = $this->_getWriteAdapter();
            $table = $this->getTable('entity_attribute');


            $data = array(
                'entity_type_id' => $object->getEntityTypeId(),
                'attribute_set_id' => $setId,
                'attribute_group_id' => $groupId,
                'attribute_id' => $attrId,
                'sort_order' => (($object->getSortOrder()) ? $object->getSortOrder() : $this->_getMaxSortOrder($object) + 1),
            );

            $condition = "$table.attribute_id = '$attrId'
                AND $table.attribute_set_id = '$setId'";
            $write->delete($table, $condition);
            $write->insert($table, $data);

        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (is_array($option)) {
            $write = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('attribute_option');
            $optionValueTable   = $this->getTable('attribute_option_value');
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();

            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $condition = $write->quoteInto('option_id=?', $intOptionId);
                            $write->delete($optionTable, $condition);
                        }

                        continue;
                    }

                    if (!$intOptionId) {
                        $data = array(
                           'attribute_id'  => $object->getId(),
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        $write->insert($optionTable, $data);
                        $intOptionId = $write->lastInsertId();
                    }
                    else {
                        $data = array(
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        $write->update($optionTable, $data, $write->quoteInto('option_id=?', $intOptionId));
                    }

                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
                        } else if ($object->getFrontendInput() == 'select') {
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }


                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined'));
                    }

                    $write->delete($optionValueTable, $write->quoteInto('option_id=?', $intOptionId));
                    foreach ($stores as $store) {
                        if (isset($values[$store->getId()]) && (!empty($values[$store->getId()]) || $values[$store->getId()] == "0")) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                            );
                            $write->insert($optionValueTable, $data);
                        }
                    }
                }

                $attribute = Mage::getModel('eav/entity_attribute')->load($object->getId());
                $attribute->setDefaultValue(implode(',', $attributeDefaultValue));
                $attribute->save();
            }
        }
        return $this;
    }

    public function isUsedBySuperProducts(Mage_Core_Model_Abstract $object, $attributeSet=null)
    {
        $read = $this->_getReadAdapter();
        $attrTable = $this->getTable('catalog/product_super_attribute');
        $productTable = $this->getTable('catalog/product');
        $select = $read->select()
            ->from(array('_main_table' => $attrTable), 'COUNT(*)')
            ->join(array('_entity'=> $productTable), '_main_table.product_id = _entity.entity_id')
            ->where("_main_table.attribute_id = ?", $object->getAttributeId())
            ->group('_main_table.attribute_id')
            ->limit(1);

        if (!is_null($attributeSet)) {
            $select->where('_entity.attribute_set_id = ?', $attributeSet);
        }
        $valueCount = $read->fetchOne($select);
        return $valueCount;
    }

    /**
     * Return attribute id
     *
     * @param string $entityType
     * @param string $code
     * @return int
     */
    public function getIdByCode($entityType, $code)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('a'=>$this->getTable('eav/attribute')), array('a.attribute_id'))
            ->join(array('t'=>$this->getTable('eav/entity_type')), 'a.entity_type_id = t.entity_type_id', array())
            ->where('t.entity_type_code = ?', $entityType)
            ->where('a.attribute_code = ?', $code);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}
