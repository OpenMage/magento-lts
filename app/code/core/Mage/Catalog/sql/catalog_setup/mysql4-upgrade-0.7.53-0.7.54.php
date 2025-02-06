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
