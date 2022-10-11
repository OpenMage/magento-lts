<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin roles resource model
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');

        $this->_usersTable = $this->getTable('admin/user');
        $this->_ruleTable = $this->getTable('admin/rule');
    }

    /**
     * Process role before saving
     *
     * @param Mage_Core_Model_Abstract|Mage_Admin_Model_Roles $role
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $role)
    {
        if ($role->getId() == '') {
            if ($role->getIdFieldName()) {
                $role->unsetData($role->getIdFieldName());
            } else {
                $role->unsetData('id');
            }
        }

        if ($role->getPid() > 0) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), ['tree_level'])
                ->where("{$this->getIdFieldName()} = :pid");

            $binds = [
                'pid' => (int) $role->getPid(),
            ];

            $treeLevel = $this->_getReadAdapter()->fetchOne($select, $binds);
        } else {
            $treeLevel = 0;
        }
        $role->setTreeLevel($treeLevel + 1);
        $role->setRoleName($role->getName());
        return $this;
    }

    /**
     * Process role after saving
     *
     * @param Mage_Core_Model_Abstract $role
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $role)
    {
        $this->_updateRoleUsersAcl($role);
        Mage::app()->getCache()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS]
        );
        return $this;
    }

    /**
     * Process role after deleting
     *
     * @param Mage_Core_Model_Abstract $role
     * @return $this
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $role)
    {
        $adapter = $this->_getWriteAdapter();

        $adapter->delete(
            $this->getMainTable(),
            ['parent_id = ?' => (int) $role->getId()]
        );

        $adapter->delete(
            $this->_ruleTable,
            ['role_id = ?' => (int) $role->getId()]
        );

        return $this;
    }

    /**
     * Get role users
     *
     * @param Mage_Admin_Model_Roles $role
     * @return array
     */
    public function getRoleUsers(Mage_Admin_Model_Roles $role)
    {
        $read = $this->_getReadAdapter();

        $binds = [
            'role_id'   => $role->getId(),
            'role_type' => 'U'
        ];

        $select = $read->select()
            ->from($this->getMainTable(), ['user_id'])
            ->where('parent_id = :role_id')
            ->where('role_type = :role_type')
            ->where('user_id > 0');

        return $read->fetchCol($select, $binds);
    }

    /**
     * Update role users ACL
     *
     * @param Mage_Admin_Model_Roles $role
     * @return bool
     */
    private function _updateRoleUsersAcl(Mage_Admin_Model_Roles $role)
    {
        $write  = $this->_getWriteAdapter();
        $users  = $this->getRoleUsers($role);
        $rowsCount = 0;

        if (count($users)) {
            $bind  = ['reload_acl_flag' => 1];
            $where = ['user_id IN(?)' => $users];
            $rowsCount = $write->update($this->_usersTable, $bind, $where);
        }

        return $rowsCount > 0;
    }
}
