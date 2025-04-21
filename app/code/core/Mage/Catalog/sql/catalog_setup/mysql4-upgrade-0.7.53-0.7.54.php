<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'image_label', [
    'type'              => 'varchar',
    'label'             => 'Image Label',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'searchable'        => true,
    'is_configurable'   => false,
]);

$installer->addAttribute('catalog_product', 'small_image_label', [
    'type'              => 'varchar',
    'label'             => 'Small Image Label',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'searchable'        => true,
    'is_configurable'   => false,
]);

$installer->addAttribute('catalog_product', 'thumbnail_label', [
    'type'              => 'varchar',
    'label'             => 'Thumbnail Label',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'searchable'        => true,
    'is_configurable'   => false,
]);

$installer->endSetup();
