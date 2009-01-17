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
 * @category   Mage
 * @package    Mage_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Api_Model_Mysql4_Roles extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_usersTable;
    protected $_ruleTable;

    protected function _construct() {
        $this->_init('api/role', 'role_id');

        $this->_usersTable = $this->getTable('api/user');
        $this->_ruleTable = $this->getTable('api/rule');
    }

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
            $row = $this->load($role->getPid());
        } else {
            $row = array('tree_level' => 0);
        }
        $role->setTreeLevel($row['tree_level'] + 1);
        $role->setRoleName($role->getName());
        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $role)
    {
        $this->_updateRoleUsersAcl($role);
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG);
        return $this;
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $role)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), "parent_id={$role->getId()}");
        $this->_getWriteAdapter()->delete($this->_ruleTable, "role_id={$role->getId()}");
        return $this;
    }

    public function getRoleUsers(Mage_Api_Model_Roles $role)
    {
        $read 	= $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), array('user_id'))->where("(parent_id = '{$role->getId()}' AND role_type = 'U') AND user_id > 0");
        return $read->fetchCol($select);
    }

    private function _updateRoleUsersAcl(Mage_Api_Model_Roles $role)
    {
        $write  = $this->_getWriteAdapter();
        $users  = $this->getRoleUsers($role);
        $rowsCount = 0;
        if ( sizeof($users) > 0 ) {
            $inStatement = implode(", ", $users);
            $rowsCount = $write->update($this->_usersTable, array('reload_acl_flag' => 1), "user_id IN({$inStatement})");
        }
        if ($rowsCount > 0) {
            return true;
        } else {
            return false;
        }
    }
}
