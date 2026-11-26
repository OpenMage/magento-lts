<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin roles resource model
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Roles extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Users table
     *
     * @var string
     */
    protected $_usersTable;

    /**
     * Rule table
     *
     * @var string
     */
    protected $_ruleTable;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');

        $this->_usersTable = $this->getTable('admin/user');
        $this->_ruleTable  = $this->getTable('admin/rule');
    }

    /**
     * Process role before saving
     *
     * @param  Mage_Admin_Model_Roles $object
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);

        if ($object->getId() == '') {
            if ($object->getIdFieldName()) {
                $object->unsetData($object->getIdFieldName());
            } else {
                $object->unsetData('id');
            }
        }

        if ($object->getPid() > 0) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), ['tree_level'])
                ->where("{$this->getIdFieldName()} = :pid");

            $binds = [
                'pid' => (int) $object->getPid(),
            ];

            $treeLevel = $this->_getReadAdapter()->fetchOne($select, $binds);
        } else {
            $treeLevel = 0;
        }

        $object->setTreeLevel($treeLevel + 1);
        $object->setRoleName($object->getName());

        return $this;
    }

    /**
     * Process role after saving
     *
     * @param  Mage_Admin_Model_Roles    $object
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Cache_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    #[Override]
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        if ($this->_updateRoleUsersAcl($object)) {
            $this->_getUserResource()->cleanMenuCache();
        }

        return $this;
    }

    /**
     * Process role after deleting
     *
     * @param  Mage_Admin_Model_Roles $object
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);

        // Read the members while their rows still exist, so their open sessions drop the deleted role
        $users = $this->getRoleUsers($object);

        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable(), ['parent_id = ?' => (int) $object->getId()]);
        $adapter->delete($this->_ruleTable, ['role_id = ?' => (int) $object->getId()]);

        if ($this->_getUserResource()->bumpAclVersion($users) > 0) {
            $this->_getUserResource()->cleanMenuCache();
        }

        return $this;
    }

    /**
     * Get role users
     *
     * @return string[]
     * @throws Mage_Core_Exception
     */
    public function getRoleUsers(Mage_Admin_Model_Roles $role)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable(), ['user_id'])
            ->where('parent_id = ?', $role->getId())
            ->where('role_type = ?', Mage_Admin_Model_Acl::ROLE_TYPE_USER)
            ->where('user_id > 0');
        return $adapter->fetchCol($select);
    }

    /**
     * Update role users
     *
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    private function _updateRoleUsersAcl(Mage_Admin_Model_Roles $role): bool
    {
        return $this->_getUserResource()->bumpAclVersion($this->getRoleUsers($role)) > 0;
    }

    protected function _getUserResource(): Mage_Admin_Model_Resource_User
    {
        /** @var Mage_Admin_Model_Resource_User $resource */
        $resource = Mage::getResourceSingleton('admin/user');
        return $resource;
    }
}
