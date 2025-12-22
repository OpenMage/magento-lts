<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Resource model for admin ACL
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Acl extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('api/role', 'role_id');
    }

    /**
     * Load ACL for the user
     *
     * @return Mage_Api_Model_Acl
     */
    public function loadAcl()
    {
        $acl = Mage::getModel('api/acl');
        $adapter = $this->_getReadAdapter();

        Mage::getSingleton('api/config')->loadAclResources($acl);

        $rolesArr = $adapter->fetchAll(
            $adapter->select()
                ->from($this->getTable('api/role'))
                ->order(['tree_level', 'role_type']),
        );
        $this->loadRoles($acl, $rolesArr);

        $rulesArr =  $adapter->fetchAll(
            $adapter->select()
                ->from(['r' => $this->getTable('api/rule')])
                ->joinLeft(
                    ['a' => $this->getTable('api/assert')],
                    'a.assert_id=r.assert_id',
                    ['assert_type', 'assert_data'],
                ),
        );
        $this->loadRules($acl, $rulesArr);
        return $acl;
    }

    /**
     * Load roles
     *
     * @param  array[] $rolesArr
     * @return $this
     */
    public function loadRoles(Mage_Api_Model_Acl $acl, array $rolesArr)
    {
        foreach ($rolesArr as $role) {
            $parent = $role['parent_id'] > 0 ? Mage_Api_Model_Acl::ROLE_TYPE_GROUP . $role['parent_id'] : null;
            switch ($role['role_type']) {
                case Mage_Api_Model_Acl::ROLE_TYPE_GROUP:
                    $roleId = $role['role_type'] . $role['role_id'];
                    $acl->addRole(Mage::getModel('api/acl_role_group', $roleId), $parent);
                    break;

                case Mage_Api_Model_Acl::ROLE_TYPE_USER:
                    $roleId = $role['role_type'] . $role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(Mage::getModel('api/acl_role_user', $roleId), $parent);
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }

                    break;
            }
        }

        return $this;
    }

    /**
     * Load rules
     *
     * @return $this
     */
    public function loadRules(Mage_Api_Model_Acl $acl, array $rulesArr)
    {
        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'] . $rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = empty($rule['api_privileges']) ? null : explode(',', $rule['api_privileges']);

            $assert = null;
            if ($rule['assert_id'] != 0) {
                $assertClass = Mage::getSingleton('api/config')->getAclAssert($rule['assert_type'])->getClassName();
                $assert = new $assertClass(unserialize($rule['assert_data'], ['allowed_classes' => false]));
            }

            try {
                if ($rule['api_permission'] == 'allow') {
                    $acl->allow($role, $resource, $privileges, $assert);
                } elseif ($rule['api_permission'] == 'deny') {
                    $acl->deny($role, $resource, $privileges, $assert);
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }
}
