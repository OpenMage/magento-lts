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
    #[Override]
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

            $this->_assertAdminUserIsActive();

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
     * Refuse API access to an admin user that has been deactivated or deleted
     *
     * An OAuth access token stays valid after its admin user is deactivated, so the account
     * state is checked on every request, as the admin login does.
     *
     * @throws Mage_Api2_Exception
     */
    protected function _assertAdminUserIsActive(): void
    {
        $user = $this->_loadAdminUser((int) $this->getUserId());
        if ((int) $user->getId() === 0 || (int) $user->getIsActive() !== 1) {
            throw new Mage_Api2_Exception(
                'Admin user is not active',
                Mage_Api2_Model_Server::HTTP_UNAUTHORIZED,
            );
        }
    }

    protected function _loadAdminUser(int $userId): Mage_Admin_Model_User
    {
        /** @var Mage_Admin_Model_User $user */
        $user = Mage::getModel('admin/user');
        $user->load($userId);

        return $user;
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
     * @param  int       $role
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
