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
    #[Override]
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
    #[Override]
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
    #[Override]
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
    #[Override]
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
    #[Override]
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
        if (!is_array($rolesIds) || $rolesIds === []) {
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
                $role = $rid > 0 ? Mage::getModel('admin/role')->load($rid) : new Varien_Object(['tree_level' => 0]);

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
                        "(ar.role_id = {$table}.parent_id and ar.role_type = 'G')",
                        ['role_id'],
                    )
                    ->where("{$table}.user_id = :user_id");

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

        // reload acl on next user http request
        $this->saveReloadAclFlag($user, 1);

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
     * Ask every session of the user to reload its ACL on its next request
     *
     * A non-zero $flag moves the user to a new ACL version (see bumpAclVersion()). A zero $flag clears
     * the reload request that code predating the ACL version leaves in `reload_acl_flag`.
     *
     * @param  Mage_Admin_Model_User     $object
     * @param  int                       $flag
     * @return $this
     * @throws Zend_Cache_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function saveReloadAclFlag($object, $flag)
    {
        if ((int) $object->getId() > 0) {
            if ((int) $flag !== 0) {
                $this->bumpAclVersion([(int) $object->getId()]);
                $this->cleanMenuCache();
            } else {
                $this->_getWriteAdapter()->update(
                    $this->getMainTable(),
                    ['reload_acl_flag' => 0],
                    ['user_id = ?' => (int) $object->getId()],
                );
            }
        }

        return $this;
    }

    /**
     * Move the given users to a new ACL version, so all their sessions rebuild the ACL on the next request
     *
     * Each session remembers the `acl_version` it built its ACL with (see Mage_Admin_Model_Session::refreshAcl()).
     * The update is a single statement, so concurrent bumps cannot lose each other, and it can run in the
     * transaction that changes the permissions. Clean the admin menu cache once that transaction is committed.
     *
     * @param  array<int|string>         $userIds
     * @return int                       number of updated users
     * @throws Zend_Db_Adapter_Exception
     */
    public function bumpAclVersion(array $userIds): int
    {
        $userIds = array_values(array_filter(array_map(intval(...), $userIds), static fn(int $id): bool => $id > 0));
        if ($userIds === []) {
            return 0;
        }

        $adapter = $this->_getWriteAdapter();
        return $adapter->update(
            $this->getMainTable(),
            ['acl_version' => new Zend_Db_Expr($adapter->quoteIdentifier('acl_version') . ' + 1')],
            ['user_id IN (?)' => $userIds],
        );
    }

    /**
     * Turn a reload request left in `reload_acl_flag` into a new ACL version, for every session of the user
     *
     * Code that predates the ACL version asks for a reload by writing a non-zero `reload_acl_flag`.
     *
     * @return bool                      whether this call took the request
     * @throws Zend_Db_Adapter_Exception
     */
    public function takeReloadAclFlag(Mage_Admin_Model_User $user): bool
    {
        if ($user->getId() <= 0) {
            return false;
        }

        $adapter = $this->_getWriteAdapter();
        return $adapter->update(
            $this->getMainTable(),
            [
                'acl_version' => new Zend_Db_Expr($adapter->quoteIdentifier('acl_version') . ' + 1'),
                'reload_acl_flag' => 0,
            ],
            ['user_id = ?' => (int) $user->getId(), 'reload_acl_flag <> ?' => 0],
        ) > 0;
    }

    /**
     * Read the user's current ACL version from the database
     *
     * @throws Mage_Core_Exception
     */
    public function getAclVersion(Mage_Admin_Model_User $user): int
    {
        if ($user->getId() <= 0) {
            return 0;
        }

        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['acl_version'])
            ->where('user_id = ?', (int) $user->getId());

        return (int) $adapter->fetchOne($select);
    }

    /**
     * Clean the cached admin menu, which depends on the ACL
     *
     * @throws Zend_Cache_Exception
     */
    public function cleanMenuCache(): void
    {
        Mage::app()->getCache()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS],
        );
    }

    /**
     * Leave the ACL version and an unchanged reload flag alone when a loaded user is saved
     *
     * Only bumpAclVersion() changes the ACL version. Writing back a value loaded earlier in the request
     * would undo a concurrent bump or reload request.
     *
     * @return array<string, mixed>
     */
    #[Override]
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $data = parent::_prepareDataForSave($object);
        if ($object->getId() > 0) {
            unset($data['acl_version']);
            if (array_key_exists('reload_acl_flag', $data) && !$object->dataHasChangedFor('reload_acl_flag')) {
                unset($data['reload_acl_flag']);
            }
        }

        return $data;
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
