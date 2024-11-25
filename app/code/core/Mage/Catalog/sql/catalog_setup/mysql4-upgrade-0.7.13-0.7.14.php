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

$mediaAttributeId = (int) $installer->getAttributeId('catalog_product', 'media_gallery');

$imagesAttributesIds = implode(',', [
    (int) $installer->getAttributeId('catalog_product', 'small_image'),
    (int) $installer->getAttributeId('catalog_product', 'image'),
    (int) $installer->getAttributeId('catalog_product', 'thumbnail'),
]);

$installer->startSetup();
$installer->run("
INSERT INTO `{$installer->getTable('catalog_product_entity_media_gallery')}` (attribute_id, entity_id, value)
    SELECT $mediaAttributeId as attribute_id, entity_id, `value`
        FROM `{$installer->getTable('catalog_product_entity_gallery')}`
        GROUP BY `value`;

INSERT INTO `{$installer->getTable('catalog_product_entity_media_gallery')}` (attribute_id, entity_id, value)
    SELECT $mediaAttributeId as attribute_id, entity_id, `value`
        FROM `{$installer->getTable('catalog_product_entity_varchar')}`
        WHERE attribute_id IN($imagesAttributesIds) AND store_id = 0
        GROUP BY `value`;
");

$installer->endSetup();
