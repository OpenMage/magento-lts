<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Resource model for admin ACL
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Acl extends Mage_Core_Model_Resource_Db_Abstract
{
    public const ACL_ALL_RULES = 'all';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }

    /**
     * Load ACL for the user
     *
     * @return Mage_Admin_Model_Acl
     * @throws Zend_Acl_Exception
     */
    public function loadAcl()
    {
        $acl = Mage::getModel('admin/acl');

        Mage::getSingleton('admin/config')->loadAclResources($acl);

        $roleTable   = $this->getTable('admin/role');
        $ruleTable   = $this->getTable('admin/rule');
        $assertTable = $this->getTable('admin/assert');

        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($roleTable)
            ->order('tree_level');

        $rolesArr = $adapter->fetchAll($select);

        if (is_array($rolesArr)) {
            $this->loadRoles($acl, $rolesArr);
        }

        $select = $adapter->select()
            ->from(['r' => $ruleTable])
            ->joinLeft(
                ['a' => $assertTable],
                'a.assert_id = r.assert_id',
                ['assert_type', 'assert_data'],
            );

        /**
         * @var array<int, array<string, mixed>> $rulesArr
         */
        $rulesArr = $adapter->fetchAll($select);

        if (is_array($rulesArr)) {
            $this->loadRules($acl, $rulesArr);
            $this->denyUnlistedResources($acl, $rulesArr);
        }

        return $acl;
    }

    /**
     * Deny every registered resource that has no explicit rule for a group role.
     *
     * The role editor writes an explicit allow/deny row for every resource that exists
     * when the role is saved. A resource added to code afterwards has no row, and
     * Zend_Acl inherits the nearest ancestor's rule for it. A role allowed on a parent
     * therefore gains every new child until the role is saved again. Denying the
     * unlisted resources makes the runtime match what the role editor shows and saves.
     *
     * Roles with an "all" allow row keep full access. The role editor also writes an
     * "all" deny row for every other role, so only the permission decides. User roles
     * inherit from their group role through Zend_Acl role inheritance, so only group
     * roles need rules.
     *
     * @param array<int, array<string, mixed>> $rulesArr
     */
    public function denyUnlistedResources(Mage_Admin_Model_Acl $acl, array $rulesArr): void
    {
        $listed = [];
        $allowAll = [];
        foreach ($rulesArr as $rule) {
            if (($rule['role_type'] ?? null) !== Mage_Admin_Model_Acl::ROLE_TYPE_GROUP) {
                continue;
            }
            $roleId = $rule['role_type'] . $rule['role_id'];
            $resourceId = (string) $rule['resource_id'];
            $listed[$roleId][$resourceId] = true;
            if ($resourceId === self::ACL_ALL_RULES && $rule['permission'] === 'allow') {
                $allowAll[$roleId] = true;
            }
        }

        $resources = $acl->getResources();
        foreach ($acl->getRoles() as $roleId) {
            $roleId = (string) $roleId;
            if (!str_starts_with($roleId, Mage_Admin_Model_Acl::ROLE_TYPE_GROUP) || isset($allowAll[$roleId])) {
                continue;
            }
            $rules = $listed[$roleId] ?? [];
            foreach ($resources as $resourceId) {
                if (!isset($rules[$resourceId])) {
                    $acl->deny($roleId, $resourceId);
                }
            }
        }
    }

    /**
     * Load roles
     *
     * @return $this
     * @throws Zend_Acl_Exception
     */
    public function loadRoles(Mage_Admin_Model_Acl $acl, array $rolesArr)
    {
        foreach ($rolesArr as $role) {
            $parent = ($role['parent_id'] > 0) ? Mage_Admin_Model_Acl::ROLE_TYPE_GROUP . $role['parent_id'] : null;
            switch ($role['role_type']) {
                case Mage_Admin_Model_Acl::ROLE_TYPE_GROUP:
                    $roleId = $role['role_type'] . $role['role_id'];
                    $acl->addRole(Mage::getModel('admin/acl_role_group', $roleId), $parent);
                    break;

                case Mage_Admin_Model_Acl::ROLE_TYPE_USER:
                    $roleId = $role['role_type'] . $role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(Mage::getModel('admin/acl_role_user', $roleId), $parent);
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
    public function loadRules(Mage_Admin_Model_Acl $acl, array $rulesArr)
    {
        $orphanedResources = [];
        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'] . $rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = empty($rule['privileges']) ? null : explode(',', $rule['privileges']);

            $assert = null;
            if ($rule['assert_id'] != 0) {
                $assertClass = Mage::getSingleton('admin/config')->getAclAssert($rule['assert_type'])->getClassName();
                $assert = new $assertClass(unserialize($rule['assert_data'], ['allowed_classes' => false]));
            }

            try {
                if ($rule['permission'] == 'allow') {
                    if ($resource === self::ACL_ALL_RULES) {
                        $acl->allow($role, null, $privileges, $assert);
                    }

                    $acl->allow($role, $resource, $privileges, $assert);
                } elseif ($rule['permission'] == 'deny') {
                    $acl->deny($role, $resource, $privileges, $assert);
                }
            } catch (Zend_Acl_Exception $zendAclException) {
                if (!in_array($resource, $orphanedResources) && str_contains($zendAclException->getMessage(), "Resource '{$resource}' not found")) {
                    $orphanedResources[] = $resource;
                }
            } catch (Exception $exception) {
                if (Mage::getIsDeveloperMode()) {
                    Mage::logException($exception);
                }
            }
        }

        if ($orphanedResources !== [] && $acl->isAllowed(Mage::getSingleton('admin/session')->getUser()->getAclRole(), 'admin/system/acl/orphaned_resources')) {
            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('adminhtml')->__(
                    'The following role resources are no longer available in the system: %s. You can delete them by <a href="%s">clicking here</a>.',
                    implode(', ', $orphanedResources),
                    Mage::helper('adminhtml')::getUrl('adminhtml/permissions_orphanedResource'),
                ),
            );
        }

        return $this;
    }
}
