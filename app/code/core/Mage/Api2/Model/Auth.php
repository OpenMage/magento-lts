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
 * API User authentication model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Auth
{
    /**
     * Use this type if no authentication adapter is applied
     */
    public const DEFAULT_USER_TYPE = 'guest';

    /**
     * Figure out API user type and create user model instance
     *
     * @throws Exception
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    public function authenticate(Mage_Api2_Model_Request $request)
    {
        /** @var Mage_Api2_Helper_Data $helper */
        $helper    = Mage::helper('api2/data');
        $userTypes = $helper->getUserTypes();

        if (!$userTypes) {
            throw new Exception('No allowed user types found');
        }
        /** @var Mage_Api2_Model_Auth_Adapter $authAdapter */
        $authAdapter   = Mage::getModel('api2/auth_adapter');
        $userParamsObj = $authAdapter->getUserParams($request);

        if (!isset($userTypes[$userParamsObj->type])) {
            throw new Mage_Api2_Exception(
                'Invalid user type or type is not allowed',
                Mage_Api2_Model_Server::HTTP_UNAUTHORIZED,
            );
        }
        /** @var Mage_Api2_Model_Auth_User_Abstract $userModel */
        $userModel = Mage::getModel($userTypes[$userParamsObj->type]);

        if (!$userModel instanceof Mage_Api2_Model_Auth_User_Abstract) {
            throw new Exception('User model must to extend Mage_Api2_Model_Auth_User_Abstract');
        }
        // check user type consistency
        if ($userModel->getType() != $userParamsObj->type) {
            throw new Exception('User model type does not match appropriate type in config');
        }
        $userModel->setUserId($userParamsObj->id);

        return $userModel;
    }
}
