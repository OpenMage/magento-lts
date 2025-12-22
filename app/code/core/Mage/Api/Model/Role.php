<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Role item model
 *
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Resource_Role            _getResource()
 * @method Mage_Api_Model_Resource_Role_Collection getCollection()
 * @method int                                     getParentId()
 * @method Mage_Api_Model_Resource_Role            getResource()
 * @method Mage_Api_Model_Resource_Role_Collection getResourceCollection()
 * @method string                                  getRoleName()
 * @method string                                  getRoleType()
 * @method int                                     getSortOrder()
 * @method int                                     getTreeLevel()
 * @method int                                     getUserId()
 * @method $this                                   setCreated(string $value)
 * @method $this                                   setModified(string $value)
 * @method $this                                   setParentId(int $value)
 * @method $this                                   setRoleName(string $value)
 * @method $this                                   setRoleType(string $value)
 * @method $this                                   setSortOrder(int $value)
 * @method $this                                   setTreeLevel(int $value)
 * @method $this                                   setUserId(int $value)
 */
class Mage_Api_Model_Role extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('api/role');
    }
}
