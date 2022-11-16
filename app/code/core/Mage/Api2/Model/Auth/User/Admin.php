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
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 User Admin Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
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
                throw new Exception('Admin role not found');
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
            throw new Exception('Admin role has been already set');
        }
        $this->_role = $role;

        return $this;
    }
}
