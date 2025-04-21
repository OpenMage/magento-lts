<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert profile resource model
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Profile extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/profile', 'profile_id');
    }

    /**
     * Setting up created_at and updarted_at
     *
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->formatDate(time()));
        }
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Returns true if profile with name exists
     *
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function isProfileExists($name, $id = null)
    {
        $bind = ['name' => $name];
        $select = $this->_getReadAdapter()->select();
        $select
            ->from($this->getMainTable(), 'count(1)')
            ->where('name = :name');
        if ($id) {
            $select->where("{$this->getIdFieldName()} != :id");
            $bind['id'] = $id;
        }
        return $this->_getReadAdapter()->fetchOne($select, $bind) ? true : false;
    }
}
