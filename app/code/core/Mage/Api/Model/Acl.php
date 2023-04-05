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
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Acl model
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Acl extends Zend_Acl
{
    /**
     * All the group roles are prepended by G
     *
     */
    public const ROLE_TYPE_GROUP = 'G';

    /**
     * All the user roles are prepended by U
     *
     */
    public const ROLE_TYPE_USER = 'U';

    /**
     * User types for store access
     * G - Guest customer (anonymous)
     * C - Authenticated customer
     * A - Authenticated admin user
     *
     */
    public const USER_TYPE_GUEST    = 'G';
    public const USER_TYPE_CUSTOMER = 'C';
    public const USER_TYPE_ADMIN    = 'A';

    /**
     * Permission level to deny access
     *
     */
    public const RULE_PERM_DENY = 0;

    /**
     * Permission level to inheric access from parent role
     *
     */
    public const RULE_PERM_INHERIT = 1;

    /**
     * Permission level to allow access
     *
     */
    public const RULE_PERM_ALLOW = 2;

    /**
     * Get role registry object or create one
     *
     * @return Mage_Api_Model_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if ($this->_roleRegistry === null) {
            $this->_roleRegistry = Mage::getModel('api/acl_role_registry');
        }
        return $this->_roleRegistry;
    }

    /**
     * Add parent to role object
     *
     * @param Zend_Acl_Role_Interface|string $role
     * @param array|Zend_Acl_Role_Interface|string $parent
     * @return $this
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}
