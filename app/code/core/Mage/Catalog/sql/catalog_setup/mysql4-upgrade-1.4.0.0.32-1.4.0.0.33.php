<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$categoryEntityTypeId = $installer->getEntityTypeId('catalog_category');
$productEntityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($categoryEntityTypeId, 'description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($categoryEntityTypeId, 'description', 'is_html_allowed_on_front', 1);
$installer->updateAttribute($productEntityTypeId, 'description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($productEntityTypeId, 'description', 'is_html_allowed_on_front', 1);
$installer->updateAttribute($productEntityTypeId, 'short_description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($productEntityTypeId, 'short_description', 'is_html_allowed_on_front', 1);

$installer->endSetup();
