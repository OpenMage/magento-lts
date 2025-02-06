<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Acl role registry
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Acl_Role_Registry extends Zend_Acl_Role_Registry
{
    /**
     * Add parent to the $role node
     *
     * @param Zend_Acl_Role_Interface|string $role
     * @param array|Zend_Acl_Role_Interface|string $parents
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
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            throw new Zend_Acl_Role_Registry_Exception("Child Role id '$roleId' does not exist");
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
                throw new Zend_Acl_Role_Registry_Exception("Parent Role id '$roleParentId' does not exist");
            }
            $this->_roles[$roleId]['parents'][$roleParentId] = $roleParent;
            $this->_roles[$roleParentId]['children'][$roleId] = $role;
        }
        return $this;
    }
}
