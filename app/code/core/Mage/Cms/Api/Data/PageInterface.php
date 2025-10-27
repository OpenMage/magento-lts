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

interface Mage_Cms_Api_Data_PageInterface
{
    public const DATA_ID                            = 'page_id';

    public const DATA_CONTENT                       = 'content';

    public const DATA_CONTENT_HEADING               = 'content_heading';

    public const DATA_CREATION_TIME                 = 'creation_time';

    public const DATA_CUSTOM_LAYOUT_UPDATE_XML      = 'custom_layout_update_xml';

    public const DATA_CUSTOM_ROOT_TEMPLATE          = 'custom_root_template';

    public const DATA_CUSTOM_THEME                  = 'custom_theme';

    public const DATA_CUSTOM_THEME_FROM             = 'custom_theme_from';

    public const DATA_CUSTOM_THEME_TO               = 'custom_theme_to';

    public const DATA_IDENTIFIER                    = 'identifier';

    public const DATA_IS_ACTIVE                     = 'is_active';

    public const DATA_LAYOUT_UPDATE_XML             = 'layout_update_xml';

    public const DATA_META_DESCRIPTION              = 'meta_description';

    public const DATA_META_KEYWORDS                 = 'meta_keywords';

    public const DATA_ROOT_TEMPLATE                 = 'root_template';

    public const DATA_SORT_ORDER                    = 'sort_order';

    public const DATA_STORE_ID                      = 'store_id';

    public const DATA_TITLE                         = 'title';

    public const DATA_UPDATE_TIME                   = 'update_time';

    public function getPageId(): ?int;

    public function setPageId(?int $pageId);

    public function getTitle(): ?string;

    public function setTitle(?string $title);

    public function getRootTemplate(): ?string;

    public function setRootTemplate(?string $template);

    public function getMetaKeywords(): ?string;

    public function setMetaKeywords(?string $keywords);

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $description);

    public function getIdentifier(): ?string;

    public function setIdentifier(?string $identifier);

    public function getContentHeading(): ?string;

    public function setContentHeading(?string $content);

    public function getContent(): ?string;

    public function setContent(?string $content);

    public function getCreationTime(): ?string;

    public function setCreationTime(?string $value);

    public function getUpdateTime(): ?string;

    public function setUpdateTime(?string $time);

    public function getIsActive(): int;

    public function setIsActive(int $value);

    public function getSortOrder(): int;

    public function setSortOrder(int $position);

    public function getLayoutUpdateXml(): ?string;

    public function setLayoutUpdateXml(?string $xml);

    public function getCustomTheme(): ?string;

    public function setCustomTheme(?string $from);

    public function getCustomRootTemplate(): ?string;

    public function setCustomRootTemplate(?string $template);

    public function getCustomLayoutUpdateXml(): ?string;

    public function setCustomLayoutUpdateXml(?string $xml);

    public function getCustomThemeTo(): ?string;

    public function setCustomThemeTo(?string $to);

    public function getCustomThemeFrom(): ?string;

    public function setCustomThemeFrom(?string $from);

    public function getStoreId(): ?int;

    public function setStoreId(int $storeId);
}
