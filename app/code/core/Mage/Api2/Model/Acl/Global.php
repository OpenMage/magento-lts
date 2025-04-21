<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API Global ACL model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Global
{
    /**
     * Check if the operation is allowed on resources of given type type for given user type/role
     *
     * @param string $resourceType
     * @param string $operation
     * @return bool
     * @throws Mage_Api2_Exception
     */
    public function isAllowed(Mage_Api2_Model_Auth_User_Abstract $apiUser, $resourceType, $operation)
    {
        // skip user without role, e.g. Customer
        if ($apiUser->getRole() === null) {
            return true;
        }
        /** @var Mage_Api2_Model_Acl $aclInstance */
        $aclInstance = Mage::getSingleton(
            'api2/acl',
            ['resource_type' => $resourceType, 'operation' => $operation],
        );

        if (!$aclInstance->hasRole($apiUser->getRole())) {
            throw new Mage_Api2_Exception('Role not found', Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
        }
        if (!$aclInstance->has($resourceType)) {
            throw new Mage_Api2_Exception('Resource not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $aclInstance->isAllowed($apiUser->getRole(), $resourceType, $operation);
    }
}
