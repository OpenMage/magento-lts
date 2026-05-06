<?php

declare(strict_types=1);

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
 * @method Mage_Admin_Model_Resource_Rules            getResource()
 * @method Mage_Admin_Model_Resource_Rules_Collection getResourceCollection()
 * @method array                                      getResources()
 * @method $this                                      setResources(array $value)
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

    public function getAssertId(): int
    {
        return (int) $this->_getData('assert_id');
    }

    public function getPermission(): string
    {
        return (string) $this->_getData('permission');
    }

    public function getPrivileges(): string
    {
        return (string) $this->_getData('privileges');
    }

    public function getResourceId(): ?string
    {
        $value = $this->_getData('resource_id');
        return $value !== null ? (string) $value : null;
    }

    public function getRoleId(): int
    {
        return (int) $this->_getData('role_id');
    }

    public function getRoleType(): string
    {
        return (string) $this->_getData('role_type');
    }

    public function setAssertId(int $value): static
    {
        return $this->setData('assert_id', $value);
    }

    public function setPermission(string $value): static
    {
        return $this->setData('permission', $value);
    }

    public function setPrivileges(string $value): static
    {
        return $this->setData('privileges', $value);
    }

    public function setResourceId(?string $value): static
    {
        return $this->setData('resource_id', $value);
    }

    public function setRoleId(int $value): static
    {
        return $this->setData('role_id', $value);
    }

    public function setRoleType(string $value): static
    {
        return $this->setData('role_type', $value);
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
    #[Override]
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
