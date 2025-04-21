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

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_datetime'),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_PRODUCT_ENTITY',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_decimal'),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_PRODUCT_ENTITY',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_varchar'),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_PRODUCT_ENTITY',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_tier_price'),
    'FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_PRODUCT_ENTITY',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_category_product_index'),
    'FK_CATALOG_CATEGORY_PRODUCT_INDEX_CATEGORY_ENTITY',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_category_product_index'),
    'FK_CATALOG_CATEGORY_PRODUCT_INDEX_PRODUCT_ENTITY',
);

$installer->getConnection()->addConstraint(
    'FK_CATALOG_PROD_ENTITY_DATETIME_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_datetime'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_PROD_ENTITY_DECIMAL_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_decimal'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_PROD_ENTITY_VARCHAR_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_varchar'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_PROD_ENTITY_TIER_PRICE_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_tier_price'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_CATEGORY_PROD_IDX_CATEGORY_ENTITY',
    $installer->getTable('catalog_category_product_index'),
    'category_id',
    $installer->getTable('catalog_category_entity'),
    'entity_id',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_CATEGORY_PROD_IDX_PROD_ENTITY',
    $installer->getTable('catalog_category_product_index'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);

$installer->endSetup();
