<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 User Guest Class
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Auth_User_Guest extends Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * User type
     */
    public const USER_TYPE = 'guest';

    /**
     * Retrieve user human-readable label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('api2')->__('Guest');
    }

    /**
     * Retrieve user type
     *
     * @return string
     */
    public function getType()
    {
        return self::USER_TYPE;
    }

    /**
     * Retrieve user role
     *
     * @return int
     */
    public function getRole()
    {
        if (!$this->_role) {
            /** @var Mage_Api2_Model_Acl_Global_Role $role */
            $role = Mage::getModel('api2/acl_global_role')->load(Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID);
            if (!$role->getId()) {
                throw new Exception('Guest role not found');
            }

            $this->_role = Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID;
        }

        return $this->_role;
    }
}
