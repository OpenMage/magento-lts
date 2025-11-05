<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Resource Entity Attribute Group
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Group extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_group', 'attribute_group_id');
    }

    /**
     * Checks if attribute group exists
     *
     * @param Mage_Eav_Model_Entity_Attribute_Group $object
     * @return bool
     */
    public function itemExists($object)
    {
        $adapter   = $this->_getReadAdapter();
        $bind      = [
            'attribute_set_id'      => $object->getAttributeSetId(),
            'attribute_group_name'  => $object->getAttributeGroupName(),
        ];
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('attribute_set_id = :attribute_set_id')
            ->where('attribute_group_name = :attribute_group_name');

        return $adapter->fetchRow($select, $bind) > 0;
    }

    /**
     * Perform actions before object save
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getSortOrder()) {
            $object->setSortOrder($this->_getMaxSortOrder($object) + 1);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $attribute) {
                $attribute->setAttributeGroupId($object->getId());
                $attribute->save();
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve max sort order
     *
     * @param Mage_Core_Model_Abstract|Mage_Eav_Model_Entity_Attribute_Group $object
     * @return null|false|string
     */
    protected function _getMaxSortOrder($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = [':attribute_set_id' => $object->getAttributeSetId()];
        $select  = $adapter->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('MAX(sort_order)'))
            ->where('attribute_set_id = :attribute_set_id');

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Set any group default if old one was removed
     *
     * @param int $attributeSetId
     * @return $this
     */
    public function updateDefaultGroup($attributeSetId)
    {
        $adapter = $this->_getWriteAdapter();
        $bind    = [':attribute_set_id' => $attributeSetId];
        $select  = $adapter->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('attribute_set_id = :attribute_set_id')
            ->order('default_id ' . Varien_Data_Collection::SORT_ORDER_DESC)
            ->limit(1);

        $groupId = $adapter->fetchOne($select, $bind);

        if ($groupId) {
            $data  = ['default_id' => 1];
            $where = ['attribute_group_id =?' => $groupId];
            $adapter->update($this->getMainTable(), $data, $where);
        }

        return $this;
    }
}
