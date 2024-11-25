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
