<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin Rules Model
 *
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_Rules            _getResource()
 * @method int                                        getAssertId()
 * @method string                                     getPermission()
 * @method string                                     getPrivileges()
 * @method Mage_Admin_Model_Resource_Rules            getResource()
 * @method Mage_Admin_Model_Resource_Rules_Collection getResourceCollection()
 * @method string                                     getResourceId()
 * @method array                                      getResources()
 * @method int                                        getRoleId()
 * @method string                                     getRoleType()
 * @method $this                                      setAssertId(int $value)
 * @method $this                                      setPermission(string $value)
 * @method $this                                      setPrivileges(string $value)
 * @method $this                                      setResourceId(string $value)
 * @method $this                                      setResources(array $value)
 * @method $this                                      setRoleId(int $value)
 * @method $this                                      setRoleType(string $value)
 */
class Mage_Admin_Model_Rules extends Mage_Core_Model_Abstract
{
    /**
     * Allowed permission code
     */
    public const RULE_PERMISSION_ALLOWED = 'allow';

    /**
     * Denied permission code
     */
    public const RULE_PERMISSION_DENIED = 'deny';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/rules');
    }

    /**
     * Update rules
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Initialize and retrieve permissions collection
     * @return Object
     */
    public function getCollection()
    {
        return Mage::getResourceModel('admin/permissions_collection');
    }

    /**
     * Save rules relations to the database
     * @return $this
     */
    public function saveRel()
    {
        $this->getResource()->saveRel($this);
        return $this;
    }

    /**
     * Check if the current rule is allowed
     * @return bool
     */
    public function isAllowed()
    {
        return $this->getPermission() == self::RULE_PERMISSION_ALLOWED;
    }

    /**
     * Check if the current rule is denied
     */
    public function isDenied()
    {
        return $this->getPermission() == self::RULE_PERMISSION_DENIED;
    }
}
