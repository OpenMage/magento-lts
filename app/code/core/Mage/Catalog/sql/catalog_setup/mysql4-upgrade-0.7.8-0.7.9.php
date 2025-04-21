<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('catalog_product_entity_media_gallery_image')}`;
");
$installer->getConnection()->dropColumn($this->getTable('catalog_product_entity_media_gallery'), 'entity_type_id');
$installer->updateAttribute('catalog_product', 'image', 'frontend_input', 'media_image');
$installer->updateAttribute('catalog_product', 'small_image', 'frontend_input', 'media_image');
$installer->updateAttribute('catalog_product', 'thumbnail', 'frontend_input', 'media_image');

$installer->endSetup();
