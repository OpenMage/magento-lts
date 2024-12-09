<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 User Customer Class
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Auth_User_Customer extends Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * User type
     */
    public const USER_TYPE = 'customer';

    /**
     * Retrieve user human-readable label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('api2')->__('Customer');
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
            $role = Mage::getModel('api2/acl_global_role')->load(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID);
            if (!$role->getId()) {
                throw new Exception('Customer role not found');
            }

            $this->_role = Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID;
        }

        return $this->_role;
    }
}
