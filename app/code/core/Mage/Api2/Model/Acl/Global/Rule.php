<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 Global ACL Rule model
 *
 * @package    Mage_Api2
 *
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getResourceCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule _getResource()
 * @method int getRoleId()
 * @method $this setRoleId(int $roleId)
 * @method string getResourceId()
 * @method $this setResourceId(string $resource)
 * @method int getPermission()
 * @method $this setPermission(int $permission)
 * @method string getPrivilege()
 * @method $this setPrivilege(string $privilege)
 * @method string getAllowedAttributes()
 * @method $this setAllowedAttributes(string $allowedAttributes)
 */
class Mage_Api2_Model_Acl_Global_Rule extends Mage_Core_Model_Abstract
{
    /**
     * Root resource ID "all"
     */
    public const RESOURCE_ALL = 'all';

    protected function _construct()
    {
        $this->_init('api2/acl_global_rule');
    }
}
