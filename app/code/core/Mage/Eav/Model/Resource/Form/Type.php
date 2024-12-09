<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Form Type Resource Model
 *
 * @category   Mage
 * @package    Mage_Eav
 *
 * @method bool hasEntityTypes()
 */
class Mage_Eav_Model_Resource_Form_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/form_type', 'type_id');
        $this->addUniqueField([
            'field' => ['code', 'theme', 'store_id'],
            'title' => Mage::helper('eav')->__('Form Type with the same code')
        ]);
    }

    /**
     * Load an object
     *
     * @param Mage_Eav_Model_Form_Type $object
     * @inheritDoc
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_null($field) && !is_numeric($value)) {
            $field = 'code';
        }
        return parent::load($object, $value, $field);
    }

    /**
     * Retrieve form type entity types
     *
     * @param Mage_Eav_Model_Form_Type $object
     * @return array
     */
    public function getEntityTypes($object)
    {
        $objectId = $object->getId();
        if (!$objectId) {
            return [];
        }
        $adapter = $this->_getReadAdapter();
        $bind    = [':type_id' => $objectId];
        $select  = $adapter->select()
            ->from($this->getTable('eav/form_type_entity'), 'entity_type_id')
            ->where('type_id = :type_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Save entity types after save form type
     *
     * @see Mage_Core_Model_Resource_Db_Abstract::_afterSave()
     *
     * @param Mage_Eav_Model_Form_Type $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasEntityTypes()) {
            $new = $object->getEntityTypes();
            $old = $this->getEntityTypes($object);

            $insert = array_diff($new, $old);
            $delete = array_diff($old, $new);

            $adapter  = $this->_getWriteAdapter();

            if (!empty($insert)) {
                $data = [];
                foreach ($insert as $entityId) {
                    if (empty($entityId)) {
                        continue;
                    }
                    $data[] = [
                        'entity_type_id' => (int)$entityId,
                        'type_id'        => $object->getId()
                    ];
                }
                if ($data) {
                    $adapter->insertMultiple($this->getTable('eav/form_type_entity'), $data);
                }
            }

            if (!empty($delete)) {
                $where = [
                    'entity_type_id IN (?)' => $delete,
                    'type_id = ?'           => $object->getId()
                ];
                $adapter->delete($this->getTable('eav/form_type_entity'), $where);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve form type filtered by given attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|int $attribute
     * @return array
     */
    public function getFormTypesByAttribute($attribute)
    {
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $attribute = $attribute->getId();
        }
        if (!$attribute) {
            return [];
        }
        $bind   = [':attribute_id' => $attribute];
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('eav/form_element'))
            ->where('attribute_id = :attribute_id');

        return $this->_getReadAdapter()->fetchAll($select, $bind);
    }
}
