<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * ACL user resource
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_User extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/user', 'user_id');
    }

    /**
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            [
                'field' => 'email',
                'title' => Mage::helper('adminhtml')->__('Email'),
            ],
            [
                'field' => 'username',
                'title' => Mage::helper('adminhtml')->__('User Name'),
            ],
        ];
        return $this;
    }

    /**
     * Authenticate user by $username and $password
     *
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    public function recordLogin(Mage_Admin_Model_User $user)
    {
        $adapter = $this->_getWriteAdapter();

        $data = [
            'logdate' => Varien_Date::now(),
            'lognum'  => $user->getLognum() + 1,
        ];

        $condition = [
            'user_id = ?' => (int) $user->getUserId(),
        ];

        $adapter->update($this->getMainTable(), $data, $condition);

        return $this;
    }

    /**
     * Load data by specified username
     *
     * @param  string      $username
     * @return array|false
     */
    public function loadByUsername($username)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('username=:username');

        $binds = [
            'username' => $username,
        ];

        return $adapter->fetchRow($select, $binds);
    }

    /**
     * Check if user is assigned to any role
     *
     * @param  int|Mage_Admin_Model_User|Mage_Core_Model_Abstract|string $user
     * @return null|array
     */
    public function hasAssigned2Role($user)
    {
        if (is_numeric($user)) {
            $userId = $user;
        } elseif ($user instanceof Mage_Core_Model_Abstract) {
            $userId = $user->getUserId();
        } else {
            return null;
        }

        if ($userId > 0) {
            $adapter = $this->_getReadAdapter();

            $select = $adapter->select();
            $select->from($this->getTable('admin/role'))
                ->where('parent_id > :parent_id')
                ->where('user_id = :user_id');

            $binds = [
                'parent_id' => 0,
                'user_id' => $userId,
            ];

            return $adapter->fetchAll($select, $binds);
        }

        return null;
    }

    /**
     * Set created/modified values before user save
     *
     * @param Mage_Admin_Model_User $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreated($this->formatDate(true));
        }

        $object->setModified($this->formatDate(true));

        return parent::_beforeSave($object);
    }

    /**
     * Unserialize user extra data after user save
     *
     * @param  Mage_Admin_Model_User $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_unserializeExtraData($object);
        return $this;
    }

    /**
     * Unserialize user extra data after user load
     *
     * @param Mage_Admin_Model_User $object
     * @inheritDoc
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        return parent::_afterLoad($this->_unserializeExtraData($object));
    }

    /**
     * Delete user role record with user
     *
     * @param  Mage_Admin_Model_User $object
     * @return $this
     * @throws Exception
     * @throws Throwable
     */
    public function delete(Mage_Core_Model_Abstract $object)
    {
        $this->_beforeDelete($object);
        $adapter = $this->_getWriteAdapter();

        $uid = $object->getId();
        $adapter->beginTransaction();
        try {
            $conditions = [
                'user_id = ?' => $uid,
            ];

            $adapter->delete($this->getMainTable(), $conditions);
            $adapter->delete($this->getTable('admin/role'), $conditions);
            $adapter->commit();
        } catch (Throwable $throwable) {
            $adapter->rollBack();
            throw $throwable;
        }

        $this->_afterDelete($object);
        return $this;
    }

    /**
     * TODO: unify _saveRelations() and add() methods, they make same things
     *
     * @param  Mage_Admin_Model_User                                $user
     * @return $this|Mage_Admin_Model_User|Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function _saveRelations(Mage_Core_Model_Abstract $user)
    {
        $rolesIds = $user->getRoleIds();
        if (!is_array($rolesIds) || count($rolesIds) == 0) {
            return $user;
        }

        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $conditions = [
                'user_id = ?' => (int) $user->getId(),
            ];

            $adapter->delete($this->getTable('admin/role'), $conditions);
            foreach ($rolesIds as $rid) {
                $rid = (int) $rid;
                if ($rid > 0) {
                    $role = Mage::getModel('admin/role')->load($rid);
                } else {
                    $role = new Varien_Object(['tree_level' => 0]);
                }

                $data = new Varien_Object([
                    'parent_id'  => $rid,
                    'tree_level' => $role->getTreeLevel() + 1,
                    'sort_order' => 0,
                    'role_type'  => Mage_Admin_Model_Acl::ROLE_TYPE_USER,
                    'user_id'    => $user->getId(),
                    'role_name'  => $user->getFirstname(),
                ]);

                $insertData = $this->_prepareDataForTable($data, $this->getTable('admin/role'));
                $adapter->insert($this->getTable('admin/role'), $insertData);
            }

            if ($user->getId() > 0) {
                // reload acl on next user http request
                $this->saveReloadAclFlag($user, 1);
            }

            $adapter->commit();
        } catch (Mage_Core_Exception $mageCoreException) {
            $adapter->rollBack();
            throw $mageCoreException;
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Get user roles
     *
     * @param  Mage_Admin_Model_User $user
     * @return array
     */
    public function getRoles(Mage_Core_Model_Abstract $user)
    {
        if (!$user->getId()) {
            return [];
        }

        $table   = $this->getTable('admin/role');
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
                    ->from($table, [])
                    ->joinLeft(
                        ['ar' => $table],
                        "(ar.role_id = $table.parent_id and ar.role_type = 'G')",
                        ['role_id'],
                    )
                    ->where("$table.user_id = :user_id");

        $binds = [
            'user_id' => (int) $user->getId(),
        ];

        $roles = $adapter->fetchCol($select, $binds);

        if ($roles) {
            return $roles;
        }

        return [];
    }

    /**
     * Save user roles
     *
     * @param  Mage_Admin_Model_User     $user
     * @return $this
     * @throws Zend_Cache_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function add(Mage_Core_Model_Abstract $user)
    {
        $dbh = $this->_getWriteAdapter();
        $aRoles = $this->hasAssigned2Role($user);
        if ($aRoles && count($aRoles)) {
            foreach ($aRoles as $data) {
                $dbh->delete(
                    $this->getTable('admin/role'),
                    ['role_id = ?' => $data['role_id']],
                );
            }
        }

        if ($user->getId() > 0) {
            $role = Mage::getModel('admin/role')->load($user->getRoleId());
        } else {
            $role = new Varien_Object(['tree_level' => 0]);
        }

        $data = new Varien_Object([
            'parent_id'  => $user->getRoleId(),
            'tree_level' => $role->getTreeLevel() + 1,
            'sort_order' => 0,
            'role_type'  => Mage_Admin_Model_Acl::ROLE_TYPE_USER,
            'user_id'    => $user->getUserId(),
            'role_name'  => $user->getFirstname(),
        ]);

        $insertData = $this->_prepareDataForTable($data, $this->getTable('admin/role'));
        $dbh->insert($this->getTable('admin/role'), $insertData);

        if ($user->getId() > 0) {
            // reload acl on next user http request
            $this->saveReloadAclFlag($user, 1);
        }

        return $this;
    }

    /**
     * Delete user role
     *
     * @param  Mage_Admin_Model_User $user
     * @return $this
     */
    public function deleteFromRole(Mage_Core_Model_Abstract $user)
    {
        if ($user->getUserId() <= 0) {
            return $this;
        }

        if ($user->getRoleId() <= 0) {
            return $this;
        }

        $dbh = $this->_getWriteAdapter();

        $condition = [
            'user_id = ?'   => (int) $user->getId(),
            'parent_id = ?' => (int) $user->getRoleId(),
        ];

        $dbh->delete($this->getTable('admin/role'), $condition);
        return $this;
    }

    /**
     * Check if role user exists
     *
     * @param  Mage_Admin_Model_User $user
     * @return array
     */
    public function roleUserExists(Mage_Core_Model_Abstract $user)
    {
        if ($user->getUserId() > 0) {
            $roleTable = $this->getTable('admin/role');

            $dbh = $this->_getReadAdapter();

            $binds = [
                'parent_id' => $user->getRoleId(),
                'user_id'   => $user->getUserId(),
            ];

            $select = $dbh->select()->from($roleTable)
                ->where('parent_id = :parent_id')
                ->where('user_id = :user_id');

            return $dbh->fetchCol($select, $binds);
        }

        return [];
    }

    /**
     * Check if user exists
     *
     * @param  Mage_Admin_Model_User $user
     * @return array|false
     */
    public function userExists(Mage_Core_Model_Abstract $user)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select();

        $binds = [
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'user_id'  => (int) $user->getId(),
        ];

        $select->from($this->getMainTable())
            ->where('(username = :username OR email = :email)')
            ->where('user_id <> :user_id');

        return $adapter->fetchRow($select, $binds);
    }

    /**
     * Save user extra data
     *
     * @param  Mage_Admin_Model_User     $object
     * @param  string                    $data
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    public function saveExtra($object, $data)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                ['extra' => $data],
                ['user_id = ?' => (int) $object->getId()],
            );
        }

        return $this;
    }

    /**
     * Set reload ACL flag
     *
     * @param  Mage_Admin_Model_User     $object
     * @param  int                       $flag
     * @return $this
     * @throws Zend_Cache_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function saveReloadAclFlag($object, $flag)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                ['reload_acl_flag' => $flag],
                ['user_id = ?' => (int) $object->getId()],
            );
            if ($flag) {
                // refresh cache menu
                Mage::app()->getCache()->clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    [Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS],
                );
            }
        }

        return $this;
    }

    /**
     * Unserializes user extra data
     *
     * @param  Mage_Admin_Model_User    $user
     * @return Mage_Core_Model_Abstract
     */
    protected function _unserializeExtraData(Mage_Core_Model_Abstract $user)
    {
        try {
            $unsterilizedData = Mage::helper('core/unserializeArray')->unserialize($user->getExtra());
            $user->setExtra($unsterilizedData);
        } catch (Exception) {
            $user->setExtra(false);
        }

        return $user;
    }
}
