<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * ACL user resource
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_User extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('api/user', 'user_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            [
                'field' => 'email',
                'title' => Mage::helper('api')->__('Email'),
            ],
            [
                'field' => 'username',
                'title' => Mage::helper('api')->__('User Name'),
            ],
        ];
        return $this;
    }

    /**
     * Authenticate user by $username and $password
     *
     * @return $this
     */
    public function recordLogin(Mage_Api_Model_User $user)
    {
        $data = [
            'lognum'  => $user->getLognum() + 1,
        ];
        $condition = $this->_getReadAdapter()->quoteInto('user_id = ?', $user->getUserId());
        $this->_getWriteAdapter()->update($this->getTable('api/user'), $data, $condition);
        return $this;
    }

    /**
     * Record api user session
     *
     * @return $this
     */
    public function recordSession(Mage_Api_Model_User $user)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $select = $readAdapter->select()
            ->from($this->getTable('api/session'), 'user_id')
            ->where('user_id = ?', $user->getId())
            ->where('sessid = ?', $user->getSessid());
        $loginDate = Varien_Date::now();
        if ($readAdapter->fetchRow($select)) {
            $writeAdapter->update(
                $this->getTable('api/session'),
                ['logdate' => $loginDate],
                $readAdapter->quoteInto('user_id = ?', $user->getId()) . ' AND '
                . $readAdapter->quoteInto('sessid = ?', $user->getSessid()),
            );
        } else {
            $writeAdapter->insert(
                $this->getTable('api/session'),
                [
                    'user_id' => $user->getId(),
                    'logdate' => $loginDate,
                    'sessid' => $user->getSessid(),
                ],
            );
        }
        $user->setLogdate($loginDate);
        return $this;
    }

    /**
     * Clean old session
     *
     * @return $this
     */
    public function cleanOldSessions(?Mage_Api_Model_User $user)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $timeout        = Mage::getStoreConfig('api/config/session_timeout');
        $timeSubtract   = $readAdapter->getDateAddSql(
            'logdate',
            $timeout,
            Varien_Db_Adapter_Interface::INTERVAL_SECOND,
        );
        $where = [
            $readAdapter->quote(Varien_Date::now()) . ' > ' . $timeSubtract,
        ];
        if ($user) {
            $where['user_id = ?'] = $user->getId();
        }
        $writeAdapter->delete(
            $this->getTable('api/session'),
            $where,
        );
        return $this;
    }

    /**
     * Load data by username
     *
     * @param string $username
     * @return array
     */
    public function loadByUsername($username)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getTable('api/user'))
            ->where('username=:username');
        return $adapter->fetchRow($select, ['username' => $username]);
    }

    /**
     * load by session id
     *
     * @param string $sessId
     * @return array
     */
    public function loadBySessId($sessId)
    {
        $result = [];
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('api/session'))
            ->where('sessid = ?', $sessId);
        if ($apiSession = $adapter->fetchRow($select)) {
            $selectUser = $adapter->select()
                ->from($this->getTable('api/user'))
                ->where('user_id = ?', $apiSession['user_id']);
            if ($user = $adapter->fetchRow($selectUser)) {
                $result = array_merge($user, $apiSession);
            }
        }
        return $result;
    }

    /**
     * Clear by session
     *
     * @param string $sessid
     * @return $this
     */
    public function clearBySessId($sessid)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('api/session'),
            ['sessid = ?' => $sessid],
        );
        return $this;
    }

    /**
     * Retrieve api user role data if it was assigned to role
     *
     * @param int | Mage_Api_Model_User $user
     * @return null | array
     */
    public function hasAssigned2Role($user)
    {
        $userId = null;
        $result = null;
        if (is_numeric($user)) {
            $userId = $user;
        } elseif ($user instanceof Mage_Core_Model_Abstract) {
            $userId = $user->getUserId();
        }

        if ($userId) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select();
            $select->from($this->getTable('api/role'))
                ->where('parent_id > 0 AND user_id = ?', $userId);
            $result = $adapter->fetchAll($select);
        }
        return $result;
    }

    /**
     * Action before save
     *
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $user)
    {
        $now = Varien_Date::now();
        if (!$user->getId()) {
            $user->setCreated($now);
        }
        $user->setModified($now);
        return $this;
    }

    /**
     * Delete the object
     *
     * @return $this
     * @throws Exception
     */
    public function delete(Mage_Core_Model_Abstract $user)
    {
        $dbh = $this->_getWriteAdapter();
        $uid = (int) $user->getId();
        $dbh->beginTransaction();
        try {
            $dbh->delete($this->getTable('api/user'), ['user_id = ?' => $uid]);
            $dbh->delete($this->getTable('api/role'), ['user_id = ?' => $uid]);
            $dbh->commit();
        } catch (Throwable $e) {
            $dbh->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Save user roles
     *
     * @return $this|Mage_Core_Model_Abstract
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
            $adapter->delete(
                $this->getTable('api/role'),
                ['user_id = ?' => (int) $user->getId()],
            );
            foreach ($rolesIds as $rid) {
                $rid = (int) $rid;
                if ($rid > 0) {
                    //$row = $this->load($user, $rid);
                    $row = ['tree_level' => 0];
                } else {
                    $row = ['tree_level' => 0];
                }

                $data = [
                    'parent_id'  => $rid,
                    'tree_level' => $row['tree_level'] + 1,
                    'sort_order' => 0,
                    'role_type'  => Mage_Api_Model_Acl::ROLE_TYPE_USER,
                    'user_id'    => $user->getId(),
                    'role_name'  => $user->getFirstname(),
                ];
                $adapter->insert($this->getTable('api/role'), $data);
            }

            $adapter->commit();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollBack();
            throw $e;
        } catch (Exception $e) {
            $adapter->rollBack();
        }
        return $this;
    }

    /**
     * Retrieve roles data
     *
     * @return array
     */
    public function _getRoles(Mage_Core_Model_Abstract $user)
    {
        if (!$user->getId()) {
            return [];
        }
        $table   = $this->getTable('api/role');
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($table, [])
            ->joinLeft(
                ['ar' => $table],
                $adapter->quoteInto(
                    "ar.role_id = {$table}.parent_id AND ar.role_type = ?",
                    Mage_Api_Model_Acl::ROLE_TYPE_GROUP,
                ),
                ['role_id'],
            )
            ->where("{$table}.user_id = ?", $user->getId());

        return (($roles = $adapter->fetchCol($select)) ? $roles : []);
    }

    /**
     * Add Role
     *
     * @return $this
     */
    public function add(Mage_Core_Model_Abstract $user)
    {
        $adapter = $this->_getWriteAdapter();
        $aRoles  = $this->hasAssigned2Role($user);
        if (count($aRoles)) {
            foreach ($aRoles as $idx => $data) {
                $adapter->delete(
                    $this->getTable('api/role'),
                    ['role_id = ?' => $data['role_id']],
                );
            }
        }

        if ($user->getId() > 0) {
            $role = Mage::getModel('api/role')->load($user->getRoleId());
        } else {
            $role = new Varien_Object(['tree_level' => 0]);
        }
        $adapter->insert($this->getTable('api/role'), [
            'parent_id'  => $user->getRoleId(),
            'tree_level' => $role->getTreeLevel() + 1,
            'sort_order' => 0,
            'role_type'  => Mage_Api_Model_Acl::ROLE_TYPE_USER,
            'user_id'    => $user->getUserId(),
            'role_name'  => $user->getFirstname(),
        ]);

        return $this;
    }

    /**
     * Delete from role
     *
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

        $adapter   = $this->_getWriteAdapter();
        $table     = $this->getTable('api/role');

        $condition = [
            $table . '.user_id = ?'   => (int) $user->getUserId(),
            $table . '.parent_id = ?' => (int) $user->getRoleId(),
        ];

        $adapter->delete($table, $condition);
        return $this;
    }

    /**
     * Retrieve roles which exists for user
     *
     * @return array
     */
    public function roleUserExists(Mage_Core_Model_Abstract $user)
    {
        $result = [];
        if ($user->getUserId() > 0) {
            $adapter = $this->_getReadAdapter();
            $select  = $adapter->select()->from($this->getTable('api/role'))
                ->where('parent_id = ?', $user->getRoleId())
                ->where('user_id = ?', $user->getUserId());
            $result = $adapter->fetchCol($select);
        }
        return $result;
    }

    /**
     * Check if user not unique
     *
     * @return array
     */
    public function userExists(Mage_Core_Model_Abstract $user)
    {
        $usersTable = $this->getTable('api/user');
        $adapter    = $this->_getReadAdapter();
        $condition  = [
            $adapter->quoteInto("{$usersTable}.username = ?", $user->getUsername()),
            $adapter->quoteInto("{$usersTable}.email = ?", $user->getEmail()),
        ];
        $select = $adapter->select()
            ->from($usersTable)
            ->where(implode(' OR ', $condition))
            ->where($usersTable . '.user_id != ?', (int) $user->getId());
        return $adapter->fetchRow($select);
    }
}
