<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Environment helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_EnvironmentConfigLoader_Override
{
    public string $scope;

    public string $storeCode;

    public string $section;

    public string $group;

    public string $field;

    public function __construct(
        string $scope,
        string $section,
        string $group,
        string $field,
        string $storeCode = '',
    ) {
        $this->scope = $scope;
        $this->section = $section;
        $this->group = $group;
        $this->field = $field;
        $this->storeCode = $storeCode;
    }

    public function setScope(string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setStoreCode(string $storeCode): self
    {
        $this->storeCode = $storeCode;
        return $this;
    }

    public function getStoreCode(): string
    {
        return $this->storeCode;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;
        return $this;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function setGroup(string $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setField(string $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
