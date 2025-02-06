<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
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
