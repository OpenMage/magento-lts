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
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule _getResource()
 * @method string getAllowedAttributes()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getCollection()
 * @method int getPermission()
 * @method string getPrivilege()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getResourceCollection()
 * @method string getResourceId()
 * @method int getRoleId()
 * @method $this setAllowedAttributes(string $allowedAttributes)
 * @method $this setPermission(int $permission)
 * @method $this setPrivilege(string $privilege)
 * @method $this setResourceId(string $resource)
 * @method $this setRoleId(int $roleId)
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
