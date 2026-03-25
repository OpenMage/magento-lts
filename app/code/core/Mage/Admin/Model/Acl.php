<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Acl model
 *
 * @package    Mage_Admin
 *
 * @property Mage_Admin_Model_Acl_Role_Registry $_roleRegistry
 *
 * @method Mage_Admin_Model_Resource_Acl _getResource()
 * @method Mage_Admin_Model_Resource_Acl getResource()
 */
class Mage_Admin_Model_Acl extends Zend_Acl
{
    /**
     * All the group roles are prepended by G
     */
    public const ROLE_TYPE_GROUP = 'G';

    /**
     * All the user roles are prepended by U
     */
    public const ROLE_TYPE_USER = 'U';

    /**
     * Permission level to deny access
     */
    public const RULE_PERM_DENY = 0;

    /**
     * Permission level to inheric access from parent role
     */
    public const RULE_PERM_INHERIT = 1;

    /**
     * Permission level to allow access
     */
    public const RULE_PERM_ALLOW = 2;

    /**
     * Get role registry object or create one
     *
     * @return Mage_Admin_Model_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if ($this->_roleRegistry === null) {
            $this->_roleRegistry = Mage::getModel('admin/acl_role_registry');
        }

        return $this->_roleRegistry;
    }

    /**
     * Add parent to role object
     *
     * @param  string|Zend_Acl_Role $role
     * @param  string|Zend_Acl_Role $parent
     * @return $this
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}
