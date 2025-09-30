<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav attribute set resource model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Set extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_set', 'attribute_set_id');
    }

    /**
     * Perform actions after object save
     *
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getGroups()) {
            foreach ($object->getGroups() as $group) {
                $group->setAttributeSetId($object->getId());
                if ($group->itemExists() && !$group->getId()) {
                    continue;
                }
                $group->save();
            }
        }
        if ($object->getRemoveGroups()) {
            foreach ($object->getRemoveGroups() as $group) {
                $group->delete();
            }
            Mage::getResourceModel('eav/entity_attribute_group')->updateDefaultGroup($object->getId());
        }
        if ($object->getRemoveAttributes()) {
            foreach ($object->getRemoveAttributes() as $attribute) {
                $attribute->deleteEntity();
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Validate attribute set name
     *
     * @param Mage_Eav_Model_Entity_Attribute_Set $object
     * @param string $attributeSetName
     * @return bool
     */
    public function validate($object, $attributeSetName)
    {
        $adapter = $this->_getReadAdapter();
        $bind = [
            'attribute_set_name' => trim($attributeSetName),
            'entity_type_id'     => $object->getEntityTypeId(),
        ];
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('attribute_set_name = :attribute_set_name')
            ->where('entity_type_id = :entity_type_id');

        if ($object->getId()) {
            $bind['attribute_set_id'] = $object->getId();
            $select->where('attribute_set_id != :attribute_set_id');
        }

        return !$adapter->fetchOne($select, $bind) ? true : false;
    }

    /**
     * Retrieve Set info by attributes
     *
     * @param int|null $setId
     * @return array
     */
    public function getSetInfo(array $attributeIds = [], $setId = null)
    {
        $adapter = $this->_getReadAdapter();
        $setInfo = [];
        $attributeToSetInfo = [];

        $select = $adapter->select()
            ->from(
                ['entity' => $this->getTable('eav/entity_attribute')],
                ['attribute_id', 'attribute_set_id', 'attribute_group_id', 'sort_order'],
            )
            ->joinLeft(
                ['attribute_group' => $this->getTable('eav/attribute_group')],
                'entity.attribute_group_id = attribute_group.attribute_group_id',
                ['group_sort_order' => 'sort_order'],
            );
        if (count($attributeIds) > 0) {
            $select->where('entity.attribute_id IN (?)', $attributeIds);
        }
        $bind = [];
        if (is_numeric($setId)) {
            $bind[':attribute_set_id'] = $setId;
            $select->where('entity.attribute_set_id = :attribute_set_id');
        }
        $result = $adapter->fetchAll($select, $bind);

        foreach ($result as $row) {
            $data = [
                'group_id' => $row['attribute_group_id'],
                'group_sort' => $row['group_sort_order'],
                'sort' => $row['sort_order'],
            ];
            $attributeToSetInfo[$row['attribute_id']][$row['attribute_set_id']] = $data;
        }

        if (count($attributeIds)) {
            foreach ($attributeIds as $atttibuteId) {
                $setInfo[$atttibuteId] = $attributeToSetInfo[$atttibuteId] ?? [];
            }
        } else {
            return $attributeToSetInfo;
        }

        return $setInfo;
    }

    /**
     * Retrurn default attribute group id for attribute set id
     *
     * @param int $setId
     * @return string
     */
    public function getDefaultGroupId($setId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = [
            'attribute_set_id' => (int) $setId,
        ];
        $select = $adapter->select()
            ->from($this->getTable('eav/attribute_group'), 'attribute_group_id')
            ->where('attribute_set_id = :attribute_set_id')
            ->where('default_id = 1')
            ->limit(1);
        return $adapter->fetchOne($select, $bind);
    }
}
