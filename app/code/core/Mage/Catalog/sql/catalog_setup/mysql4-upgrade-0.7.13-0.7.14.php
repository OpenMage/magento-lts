<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
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
