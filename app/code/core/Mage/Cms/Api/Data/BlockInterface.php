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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

interface Mage_Cms_Api_Data_BlockInterface
{
    public const DATA_ID                = 'block_id';

    public const DATA_CONTENT           = 'content';

    public const DATA_CREATION_TIME     = 'creation_time';

    public const DATA_IDENTIFIER        = 'identifier';

    public const DATA_IS_ACTIVE         = 'is_active';

    public const DATA_STORE_ID          = 'store_id';

    public const DATA_TITLE             = 'title';

    public const DATA_UPDATE_TIME       = 'update_time';

    public function getBlockId(): ?int;

    public function setBlockId(?int $blockId);

    public function getTitle(): string;

    public function setTitle(string $title);

    public function getIdentifier(): string;

    public function setIdentifier(string $identifier);

    public function getContent(): ?string;

    public function setContent(?string $content);

    public function getCreationTime(): ?string;

    public function setCreationTime(?string $time);

    public function getUpdateTime(): ?string;

    public function setUpdateTime(?string $time);

    public function getIsActive(): int;

    public function setIsActive(int $value);

    public function getStoreId(): ?int;

    public function setStoreId(int $storeId);
}
