<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Acl role registry
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Acl_Role_Registry extends Zend_Acl_Role_Registry
{
    /**
     * Add parent to the $role node
     *
     * @param  string|Zend_Acl_Role_Interface       $role
     * @param  array|string|Zend_Acl_Role_Interface $parents
     * @return $this
     */
    public function addParent($role, $parents)
    {
        try {
            if ($role instanceof Zend_Acl_Role_Interface) {
                $roleId = $role->getRoleId();
            } else {
                $roleId = $role;
                $role = $this->get($role);
            }
        } catch (Zend_Acl_Role_Registry_Exception $zendAclRoleRegistryException) {
            throw new Zend_Acl_Role_Registry_Exception("Child Role id '$roleId' does not exist", $zendAclRoleRegistryException->getCode(), $zendAclRoleRegistryException);
        }

        if (!is_array($parents)) {
            $parents = [$parents];
        }

        foreach ($parents as $parent) {
            try {
                if ($parent instanceof Zend_Acl_Role_Interface) {
                    $roleParentId = $parent->getRoleId();
                } else {
                    $roleParentId = $parent;
                }

                $roleParent = $this->get($roleParentId);
            } catch (Zend_Acl_Role_Registry_Exception $e) {
                throw new Zend_Acl_Role_Registry_Exception("Parent Role id '$roleParentId' does not exist", $e->getCode(), $e);
            }

            $this->_roles[$roleId]['parents'][$roleParentId] = $roleParent;
            $this->_roles[$roleParentId]['children'][$roleId] = $role;
        }

        return $this;
    }
}
