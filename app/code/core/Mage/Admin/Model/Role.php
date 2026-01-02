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
 * @method Mage_Admin_Model_Resource_Role            _getResource()
 * @method Mage_Admin_Model_Resource_Role_Collection getCollection()
 * @method string                                    getName()
 * @method int                                       getParentId()
 * @method int                                       getPid()
 * @method Mage_Admin_Model_Resource_Role            getResource()
 * @method Mage_Admin_Model_Resource_Role_Collection getResourceCollection()
 * @method int                                       getRoleId()
 * @method string                                    getRoleName()
 * @method string                                    getRoleType()
 * @method int                                       getSortOrder()
 * @method int                                       getTreeLevel()
 * @method int                                       getUserId()
 * @method $this                                     setCreated(string $value)
 * @method $this                                     setModified(string $value)
 * @method $this                                     setParentId(int $value)
 * @method $this                                     setRoleName(string $value)
 * @method $this                                     setRoleType(string $value)
 * @method $this                                     setSortOrder(int $value)
 * @method $this                                     setTreeLevel(int $value)
 * @method $this                                     setUserId(int $value)
 */
class Mage_Admin_Model_Role extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('admin/role');
    }
}
