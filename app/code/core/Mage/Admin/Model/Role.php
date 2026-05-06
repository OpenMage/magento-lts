<?php

declare(strict_types=1);

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
 * @method Mage_Admin_Model_Resource_Role            getResource()
 * @method Mage_Admin_Model_Resource_Role_Collection getResourceCollection()
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

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function getRoleName(): ?string
    {
        $value = $this->_getData('role_name');
        return $value !== null ? (string) $value : null;
    }

    public function getRoleType(): string
    {
        return (string) $this->_getData('role_type');
    }

    public function getSortOrder(): int
    {
        return (int) $this->_getData('sort_order');
    }

    public function getTreeLevel(): int
    {
        return (int) $this->_getData('tree_level');
    }

    public function getUserId(): int
    {
        return (int) $this->_getData('user_id');
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function setRoleName(?string $value): static
    {
        return $this->setData('role_name', $value);
    }

    public function setRoleType(string $value): static
    {
        return $this->setData('role_type', $value);
    }

    public function setSortOrder(int $value): static
    {
        return $this->setData('sort_order', $value);
    }

    public function setTreeLevel(int $value): static
    {
        return $this->setData('tree_level', $value);
    }

    public function setUserId(int $value): static
    {
        return $this->setData('user_id', $value);
    }

    public function getCreated(): string
    {
        return (string) $this->_getData('created');
    }

    public function getModified(): string
    {
        return (string) $this->_getData('modified');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function getPid(): int
    {
        return (int) $this->_getData('pid');
    }

    public function getRoleId(): int
    {
        return (int) $this->_getData('role_id');
    }

    public function setCreated(string $value): static
    {
        return $this->setData('created', $value);
    }

    public function setModified(string $value): static
    {
        return $this->setData('modified', $value);
    }
}
