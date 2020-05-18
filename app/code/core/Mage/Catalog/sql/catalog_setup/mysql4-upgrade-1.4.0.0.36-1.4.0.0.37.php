<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_datetime'),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_PRODUCT_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_decimal'),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_PRODUCT_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_varchar'),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_PRODUCT_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_entity_tier_price'),
    'FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_PRODUCT_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_category_product_index'),
    'FK_CATALOG_CATEGORY_PRODUCT_INDEX_CATEGORY_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_category_product_index'),
    'FK_CATALOG_CATEGORY_PRODUCT_INDEX_PRODUCT_ENTITY'
);

$installer->getConnection()->addConstraint('FK_CATALOG_PROD_ENTITY_DATETIME_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_datetime'), 'entity_id',
    $installer->getTable('catalog_product_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_CATALOG_PROD_ENTITY_DECIMAL_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_decimal'), 'entity_id',
    $installer->getTable('catalog_product_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_CATALOG_PROD_ENTITY_VARCHAR_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_varchar'), 'entity_id',
    $installer->getTable('catalog_product_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_CATALOG_PROD_ENTITY_TIER_PRICE_PROD_ENTITY',
    $installer->getTable('catalog_product_entity_tier_price'), 'entity_id',
    $installer->getTable('catalog_product_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_CATALOG_CATEGORY_PROD_IDX_CATEGORY_ENTITY',
    $installer->getTable('catalog_category_product_index'), 'category_id',
    $installer->getTable('catalog_category_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_CATALOG_CATEGORY_PROD_IDX_PROD_ENTITY',
    $installer->getTable('catalog_category_product_index'), 'product_id',
    $installer->getTable('catalog_product_entity'), 'entity_id'
);

$installer->endSetup();
