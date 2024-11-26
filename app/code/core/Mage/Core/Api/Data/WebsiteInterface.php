<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

interface Mage_Core_Api_Data_WebsiteInterface
{
    public const DATA_ID                = 'website_id';
    public const DATA_CODE              = 'code';
    public const DATA_NAME              = 'name';
    public const DATA_SORT_ORDER        = 'sort_order';
    public const DATA_DEFAULT_GROUP_ID  = 'default_group_id';
    public const DATA_IS_DEFAULT        = 'is_default';

    public function getWebsiteId(): ?int;
    public function setWebsiteId(?int $value);
    public function getCode();
    public function setCode(?string $code);
    public function getName(): ?string;
    public function setName(?string $name);
    public function getSortOrder(): int;
    public function setSortOrder(int $position);
    public function getDefaultGroupId();
    public function setDefaultGroupId(int $value);
    public function getIsDefault(): ?int;
    public function setIsDefault(?int $value);
}
