<?php

/**
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
