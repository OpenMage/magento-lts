<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin Role Model
 *
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_Role _getResource()
 * @method Mage_Admin_Model_Resource_Role getResource()
 * @method Mage_Admin_Model_Resource_Role_Collection getResourceCollection()
 *
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getTreeLevel()
 * @method $this setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method int getRoleId()
 * @method string getRoleType()
 * @method $this setRoleType(string $value)
 * @method int getUserId()
 * @method $this setUserId(int $value)
 * @method string getRoleName()
 * @method $this setRoleName(string $value)
 * @method int getPid()
 * @method string getName()
 * @method $this setCreated(string $value)
 * @method $this setModified(string $value)
 */
class Mage_Admin_Model_Role extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role');
    }
}
