<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Resource model for admin ACL
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Resource_Acl extends Mage_Core_Model_Resource_Db_Abstract
{
    public const ACL_ALL_RULES = 'all';

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }

    /**
     * Load ACL for the user
     *
     * @return Mage_Admin_Model_Acl
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

        $this->loadRoles($acl, $rolesArr);

        $select = $adapter->select()
            ->from(['r' => $ruleTable])
            ->joinLeft(
                ['a' => $assertTable],
                'a.assert_id = r.assert_id',
                ['assert_type', 'assert_data']
            );

        $rulesArr = $adapter->fetchAll($select);

        $this->loadRules($acl, $rulesArr);

        return $acl;
    }

    /**
     * Load roles
     *
     * @param Mage_Admin_Model_Acl $acl
     * @param array $rolesArr
     * @return $this
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
     * @param Mage_Admin_Model_Acl $acl
     * @param array $rulesArr
     * @return $this
     */
    public function loadRules(Mage_Admin_Model_Acl $acl, array $rulesArr)
    {
        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'] . $rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = !empty($rule['privileges']) ? explode(',', $rule['privileges']) : null;

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
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }
}
