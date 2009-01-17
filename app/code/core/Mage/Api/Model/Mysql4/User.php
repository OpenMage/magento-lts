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
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ACL user resource
 *
 * @category   Mage
 * @package    Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Mysql4_User extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('api/user', 'user_id');
            $this->_uniqueFields = array(
                 array('field' => 'email', 'title' => Mage::helper('api')->__('Email')),
                 array('field' => 'username', 'title' => Mage::helper('api')->__('User Name')),
            );
    }

    /**
     * Authenticate user by $username and $password
     *
     * @param string $username
     * @param string $password
     * @return boolean|Object
     */
    public function recordLogin(Mage_Api_Model_User $user)
    {
        $data = array(
            'logdate' => now(),
            'lognum'  => $user->getLognum()+1,
            'sessid'  => $user->getSessid()
        );
        $condition = $this->_getWriteAdapter()->quoteInto('user_id=?', $user->getUserId());
        $this->_getWriteAdapter()->update($this->getTable('api/user'), $data, $condition);
        return $this;
    }

    public function loadByUsername($username)
    {
        $select = $this->_getReadAdapter()->select()->from($this->getTable('api/user'))
            ->where('username=:username');
        return $this->_getReadAdapter()->fetchRow($select, array('username'=>$username));
    }

    public function loadBySessId ($sessId)
    {
        $select = $this->_getReadAdapter()->select()->from($this->getTable('api/user'))
            ->where('sessid=:sessid');
        return $this->_getReadAdapter()->fetchRow($select, array('sessid'=>$sessId));
    }

    public function hasAssigned2Role($user)
    {
        if (is_numeric($user)) {
            $userId = $user;
        } else if ($user instanceof Mage_Core_Model_Abstract) {
            $userId = $user->getUserId();
        } else {
            return null;
        }

        if ( $userId > 0 ) {
            $dbh = $this->_getReadAdapter();
            $select = $dbh->select();
            $select->from($this->getTable('api/role'))
                ->where("parent_id > 0 AND user_id = {$userId}");
            return $dbh->fetchAll($select);
        } else {
            return null;
        }
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $user)
    {
        if (!$user->getId()) {
            $user->setCreated(now());
        }
        $user->setModified(now());
        return $this;
    }

    public function load(Mage_Core_Model_Abstract $user, $value, $field=null)
    {
        return parent::load($user, $value, $field);
    }

    public function delete(Mage_Core_Model_Abstract $user)
    {
        $dbh = $this->_getWriteAdapter();
        $uid = (int) $user->getId();
        $dbh->beginTransaction();
        try {
            $dbh->delete($this->getTable('api/user'), "user_id=$uid");
            $dbh->delete($this->getTable('api/role'), "user_id=$uid");
        } catch (Mage_Core_Exception $e) {
            throw $e;
            return false;
        } catch (Exception $e){
            $dbh->rollBack();
            return false;
        }
        $dbh->commit();
        return true;
    }

    public function _saveRelations(Mage_Core_Model_Abstract $user)
    {
        $rolesIds = $user->getRoleIds();

        if( !is_array($rolesIds) || count($rolesIds) == 0 ) {
            return $user;
        }

        $this->_getWriteAdapter()->beginTransaction();

        try {
            $this->_getWriteAdapter()->delete($this->getTable('api/role'), "user_id = {$user->getId()}");
            foreach ($rolesIds as $rid) {
                $rid = intval($rid);
                if ($rid > 0) {
                    //$row = $this->load($user, $rid);
                } else {
                    $row = array('tree_level' => 0);
                }
                $row = array('tree_level' => 0);

                $data = array(
                    'parent_id'     => $rid,
                    'tree_level'    => $row['tree_level'] + 1,
                    'sort_order'    => 0,
                    'role_type'     => 'U',
                    'user_id'       => $user->getId(),
                    'role_name'     => $user->getFirstname()
                );
                $this->_getWriteAdapter()->insert($this->getTable('api/role'), $data);
            }
            $this->_getWriteAdapter()->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_getWriteAdapter()->rollBack();
        }
    }

    public function _getRoles(Mage_Core_Model_Abstract $user)
    {
        if ( !$user->getId() ) {
            return array();
        }
        $table  = $this->getTable('api/role');
        $read   = $this->_getReadAdapter();
        $select = $read->select()->from($table, array())
                    ->joinLeft(array('ar' => $table), "(ar.role_id = `{$table}`.parent_id and ar.role_type = 'G')", array('role_id'))
                    ->where("`{$table}`.user_id = {$user->getId()}");

        return (($roles = $read->fetchCol($select)) ? $roles : array());
    }

    public function add(Mage_Core_Model_Abstract $user) {

        $dbh = $this->_getWriteAdapter();

        $aRoles = $this->hasAssigned2Role($user);
        if ( sizeof($aRoles) > 0 ) {
            foreach($aRoles as $idx => $data){
                $dbh->delete($this->getTable('api/role'), "role_id = {$data['role_id']}");
            }
        }

        if ($user->getId() > 0) {
            $role = Mage::getModel('api/role')->load($user->getRoleId());
        } else {
            $role = array('tree_level' => 0);
        }
        $dbh->insert($this->getTable('api/role'), array(
            'parent_id' => $user->getRoleId(),
            'tree_level'=> ($role->getTreeLevel() + 1),
            'sort_order'=> 0,
            'role_type' => 'U',
            'user_id'   => $user->getUserId(),
            'role_name' => $user->getFirstname()
        ));

        return $this;
    }

    public function deleteFromRole(Mage_Core_Model_Abstract $user) {
        if ( $user->getUserId() <= 0 ) {
            return $this;
        }
        if ( $user->getRoleId() <= 0 ) {
            return $this;
        }
        $dbh = $this->_getWriteAdapter();
        $condition = "`{$this->getTable('api/role')}`.user_id = ".$dbh->quote($user->getUserId())." AND `{$this->getTable('api/role')}`.parent_id = ".$dbh->quote($user->getRoleId());
        $dbh->delete($this->getTable('api/role'), $condition);
        return $this;
    }

    public function roleUserExists(Mage_Core_Model_Abstract $user)
    {
        if ( $user->getUserId() > 0 ) {
            $roleTable = $this->getTable('api/role');
            $dbh    = $this->_getReadAdapter();
            $select = $dbh->select()->from($roleTable)
                ->where("parent_id = {$user->getRoleId()} AND user_id = {$user->getUserId()}");
            return $dbh->fetchCol($select);
        } else {
            return array();
        }
    }

    public function userExists(Mage_Core_Model_Abstract $user)
    {
        $usersTable = $this->getTable('api/user');
        $select = $this->_getReadAdapter()->select();
        $select->from($usersTable);
        $select->where("({$usersTable}.username = '{$user->getUsername()}' OR {$usersTable}.email = '{$user->getEmail()}') AND {$usersTable}.user_id != '{$user->getId()}'");
        return $this->_getReadAdapter()->fetchRow($select);
    }
}
