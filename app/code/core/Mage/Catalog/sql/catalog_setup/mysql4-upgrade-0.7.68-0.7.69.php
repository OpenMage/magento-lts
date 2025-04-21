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

// fix for sample data 1.2.0
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_PRODUCT',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE',
);
$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE',
);
$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE',
);
$installer->getConnection()->addConstraint(
    'FK_SUPER_PRODUCT_ATTRIBUTE_LABEL',
    $installer->getTable('catalog/product_super_attribute_label'),
    'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'),
    'product_super_attribute_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_SUPER_PRODUCT_ATTRIBUTE_PRICING',
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'),
    'product_super_attribute_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_SUPER_PRODUCT_LINK_ENTITY',
    $installer->getTable('catalog/product_super_link'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_SUPER_PRODUCT_LINK_PARENT',
    $installer->getTable('catalog/product_super_link'),
    'parent_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE',
    $installer->getTable('catalog/product_website'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_WEBSITE_PRODUCT_PRODUCT',
    $installer->getTable('catalog/product_website'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);

$installer->endSetup();
