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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Acl role registry
 * 
 * @category   Mage
 * @package    Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Acl_Role_Registry extends Zend_Acl_Role_Registry 
{
    /**
     * Add parent to the $role node
     *
     * @param Zend_Acl_Role_Interface|string $role
     * @param array|Zend_Acl_Role_Interface|string $parents
     * @return Mage_Auth_Model_Acl_Role_Registry
     */
    function addParent($role, $parents)
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
            $parents = array($parents);
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
