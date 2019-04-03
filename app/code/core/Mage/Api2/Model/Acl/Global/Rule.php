<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 Global ACL Rule model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getResourceCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule _getResource()
 * @method int getRoleId()
 * @method Mage_Api2_Model_Acl_Global_Rule setRoleId() setRoleId(int $roleId)
 * @method string getResourceId()
 * @method Mage_Api2_Model_Acl_Global_Rule setResourceId() setResourceId(string $resource)
 * @method string getPrivilege()
 * @method int getPermission()
 * @method Mage_Api2_Model_Acl_Global_Rule setPermission() setPermission(int $permission)
 * @method string getPrivilege()
 * @method Mage_Api2_Model_Acl_Global_Rule setPrivilege() setPrivilege(string $privilege)
 * @method string getAllowedAttributes()
 * @method Mage_Api2_Model_Acl_Global_Rule setAllowedAttributes() setAllowedAttributes(string $allowedAttributes)
 */
class Mage_Api2_Model_Acl_Global_Rule extends Mage_Core_Model_Abstract
{
    /**
     * Root resource ID "all"
     */
    const RESOURCE_ALL = 'all';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('api2/acl_global_rule');
    }
}
