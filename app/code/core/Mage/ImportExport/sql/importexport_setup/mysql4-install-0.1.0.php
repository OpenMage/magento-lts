<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_ImportExport_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('importexport_importdata')}` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity` VARCHAR(50) NOT NULL,
    `behavior` SET('" . Mage_ImportExport_Model_Import::BEHAVIOR_APPEND . "','"
    . Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE . "','" .
    Mage_ImportExport_Model_Import::BEHAVIOR_DELETE . "') NOT NULL DEFAULT '" .
    Mage_ImportExport_Model_Import::BEHAVIOR_APPEND . "',
    `data` MEDIUMTEXT NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
", );

// add unique key for parent-child pairs which makes easier configurable products import
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_super_link'),
    'UNQ_product_id_parent_id',
    ['product_id', 'parent_id'],
    'unique',
);

// add unique key for product-attribute pairs
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_super_attribute'),
    'UNQ_product_id_attribute_id',
    ['product_id', 'attribute_id'],
    'unique',
);

// add unique key for product-value-website
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'UNQ_product_super_attribute_id_value_index_website_id',
    ['product_super_attribute_id', 'value_index', 'website_id'],
    'unique',
);

$installer->getConnection()->addConstraint(
    'FK_INT_PRODUCT_LINK',
    $installer->getTable('catalog/product_link_attribute_int'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id',
);

$installer->getConnection()->addConstraint(
    'FK_INT_PRODUCT_LINK_ATTRIBUTE',
    $installer->getTable('catalog/product_link_attribute_int'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id',
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_link_attribute_int'),
    'UNQ_product_link_attribute_id_link_id',
    ['product_link_attribute_id', 'link_id'],
    'unique',
);

$installer->endSetup();
