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

    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');

        $this->_usersTable = $this->getTable('admin/user');
        $this->_ruleTable  = $this->getTable('admin/rule');
    }

    /**
     * Process role before saving
     *
     * @param Mage_Admin_Model_Roles $object
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
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
     * @param Mage_Admin_Model_Roles $object
     * @return $this
     * @throws Zend_Cache_Exception
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_updateRoleUsersAcl($object);
        Mage::app()->getCache()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS],
        );
        return $this;
    }

    /**
     * Process role after deleting
     *
     * @param Mage_Admin_Model_Roles $object
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable(), ['parent_id = ?' => (int) $object->getId()]);
        $adapter->delete($this->_ruleTable, ['role_id = ?' => (int) $object->getId()]);
        return $this;
    }

    /**
     * Get role users
     *
     * @return array
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
     * @return bool
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    private function _updateRoleUsersAcl(Mage_Admin_Model_Roles $role)
    {
        $users = $this->getRoleUsers($role);
        $rowsCount = 0;

        if (count($users)) {
            $rowsCount = $this->_getWriteAdapter()->update(
                $this->_usersTable,
                ['reload_acl_flag' => 1],
                ['user_id IN (?)' => $users],
            );
        }

        return $rowsCount > 0;
    }
}
