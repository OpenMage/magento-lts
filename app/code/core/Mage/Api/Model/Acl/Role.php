<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * User acl role
 *
 * @category   Mage
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Resource_Role _getResource()
 * @method Mage_Api_Model_Resource_Role getResource()
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getTreeLevel()
 * @method $this setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method string getRoleType()
 * @method $this setRoleType(string $value)
 * @method int getUserId()
 * @method $this setUserId(int $value)
 * @method string getRoleName()
 * @method $this setRoleName(string $value)
 */
class Mage_Api_Model_Acl_Role extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('api/role');
    }
}
