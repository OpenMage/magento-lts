<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 User Admin Class
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Auth_User_Admin extends Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * User type
     */
    public const USER_TYPE = 'admin';

    /**
     * Retrieve user human-readable label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('api2')->__('Admin');
    }

    /**
     * Retrieve user role
     *
     * @return int
     * @throws Exception
     */
    public function getRole()
    {
        if (!$this->_role) {
            if (!$this->getUserId()) {
                throw new Exception('Admin identifier is not set');
            }

            /** @var Mage_Api2_Model_Resource_Acl_Global_Role_Collection $collection */
            $collection = Mage::getModel('api2/acl_global_role')->getCollection();
            $collection->addFilterByAdminId($this->getUserId());

            /** @var Mage_Api2_Model_Acl_Global_Role $role */
            $role = $collection->getFirstItem();
            if (!$role->getId()) {
                throw new Exception('Admin role for user ID ' . $this->getUserId() . ' not found');
            }

            $this->setRole($role->getId());
        }

        return $this->_role;
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
     * Set user role
     *
     * @param int $role
     * @return $this
     * @throws Exception
     */
    public function setRole($role)
    {
        if ($this->_role) {
            throw new Exception('Admin role has been already set to ' . $this->_role . ' for user ID ' . $this->getUserId());
        }

        $this->_role = $role;

        return $this;
    }
}
