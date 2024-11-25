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

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/category_product'),
    'CATALOG_CATEGORY_PRODUCT_CATEGORY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/category_product'),
    'CATALOG_CATEGORY_PRODUCT_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/category_product_index'),
    'FK_CATALOG_CATEGORY_PROD_IDX_CATEGORY_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/category_product_index'),
    'FK_CATALOG_CATEGORY_PROD_IDX_PROD_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/category_product_index'),
    'FK_CATEGORY_PRODUCT_INDEX_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/compare_item'),
    'FK_CATALOG_COMPARE_ITEM_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/compare_item'),
    'FK_CATALOG_COMPARE_ITEM_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/compare_item'),
    'FK_CATALOG_COMPARE_ITEM_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/eav_attribute'),
    'FK_CATALOG_EAV_ATTRIBUTE_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_enabled_index'),
    'FK_CATALOG_PRODUCT_ENABLED_INDEX_PRODUCT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_enabled_index'),
    'FK_CATALOG_PRODUCT_ENABLED_INDEX_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product'),
    'FK_CATALOG_PRODUCT_ENTITY_ATTRIBUTE_SET_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product'),
    'FK_CATALOG_PRODUCT_ENTITY_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_media_gallery_value'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_VALUE_GALLERY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_media_gallery_value'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_VALUE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_GROUP',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'FK_CATALOG_PRODUCT_TIER_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'FK_CATALOG_PROD_ENTITY_TIER_PRICE_PROD_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_eav'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_eav'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_eav'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_DECIMAL_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_DECIMAL_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_DECIMAL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_price'),
    'FK_CATALOG_PRODUCT_INDEX_PRICE_CUSTOMER_GROUP',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_price'),
    'FK_CATALOG_PRODUCT_INDEX_PRICE_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_price'),
    'FK_CATALOG_PRODUCT_INDEX_PRICE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_tier_price'),
    'FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_tier_price'),
    'FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_tier_price'),
    'FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_index_website'),
    'FK_CATALOG_PRODUCT_INDEX_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link'),
    'FK_PRODUCT_LINK_LINKED_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link'),
    'FK_PRODUCT_LINK_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link'),
    'FK_PRODUCT_LINK_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute'),
    'FK_ATTRIBUTE_PRODUCT_LINK_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    'FK_DECIMAL_LINK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    'FK_DECIMAL_PRODUCT_LINK_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_int'),
    'FK_INT_PRODUCT_LINK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_int'),
    'FK_INT_PRODUCT_LINK_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    'FK_VARCHAR_LINK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    'FK_VARCHAR_PRODUCT_LINK_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option'),
    'FK_CATALOG_PRODUCT_OPTION_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_price'),
    'FK_CATALOG_PRODUCT_OPTION_PRICE_OPTION',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_price'),
    'FK_CATALOG_PRODUCT_OPTION_PRICE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_title'),
    'FK_CATALOG_PRODUCT_OPTION_TITLE_OPTION',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_title'),
    'FK_CATALOG_PRODUCT_OPTION_TITLE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_type_price'),
    'FK_CATALOG_PRODUCT_OPTION_TYPE_PRICE_OPTION',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_type_price'),
    'FK_CATALOG_PRODUCT_OPTION_TYPE_PRICE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_type_title'),
    'FK_CATALOG_PRODUCT_OPTION_TYPE_TITLE_OPTION',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_type_title'),
    'FK_CATALOG_PRODUCT_OPTION_TYPE_TITLE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_option_type_value'),
    'FK_CATALOG_PRODUCT_OPTION_TYPE_VALUE_OPTION',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_relation'),
    'FK_CATALOG_PRODUCT_RELATION_CHILD',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_relation'),
    'FK_CATALOG_PRODUCT_RELATION_PARENT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute'),
    'FK_SUPER_PRODUCT_ATTRIBUTE_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_CATALOG_PROD_SUPER_ATTR_LABEL_ATTR',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_CATALOG_PROD_SUPER_ATTR_LABEL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'CATALOG_PRODUCT_SUPER_ATTRIBUTE_PRICING_IBFK_1',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'FK_CATALOG_PRODUCT_SUPER_PRICE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'FK_SUPER_PRODUCT_ATTRIBUTE_PRICING',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_link'),
    'CATALOG_PRODUCT_SUPER_LINK_IBFK_1',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_link'),
    'CATALOG_PRODUCT_SUPER_LINK_IBFK_2',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_link'),
    'FK_SUPER_PRODUCT_LINK_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_link'),
    'FK_SUPER_PRODUCT_LINK_PARENT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_WEBSITE_PRODUCT_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'datetime']),
    'FK_CATALOG_CATEGORY_ENTITY_DATETIME_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'datetime']),
    'FK_CATALOG_CATEGORY_ENTITY_DATETIME_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'datetime']),
    'FK_CATALOG_CATEGORY_ENTITY_DATETIME_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'decimal']),
    'FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'decimal']),
    'FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'decimal']),
    'FK_CATALOG_CATEGORY_ENTITY_DECIMAL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'int']),
    'FK_CATALOG_CATEGORY_EMTITY_INT_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'int']),
    'FK_CATALOG_CATEGORY_EMTITY_INT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'int']),
    'FK_CATALOG_CATEGORY_EMTITY_INT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'text']),
    'FK_CATALOG_CATEGORY_ENTITY_TEXT_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'text']),
    'FK_CATALOG_CATEGORY_ENTITY_TEXT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'text']),
    'FK_CATALOG_CATEGORY_ENTITY_TEXT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'varchar']),
    'FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'varchar']),
    'FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/category', 'varchar']),
    'FK_CATALOG_CATEGORY_ENTITY_VARCHAR_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'datetime']),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'datetime']),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'datetime']),
    'FK_CATALOG_PROD_ENTITY_DATETIME_PROD_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'decimal']),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'decimal']),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'decimal']),
    'FK_CATALOG_PROD_ENTITY_DECIMAL_PROD_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'gallery']),
    'FK_CATALOG_PRODUCT_ENTITY_GALLERY_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'gallery']),
    'FK_CATALOG_PRODUCT_ENTITY_GALLERY_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'gallery']),
    'FK_CATALOG_PRODUCT_ENTITY_GALLERY_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'int']),
    'FK_CATALOG_PRODUCT_ENTITY_INT_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'int']),
    'FK_CATALOG_PRODUCT_ENTITY_INT_PRODUCT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'int']),
    'FK_CATALOG_PRODUCT_ENTITY_INT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'text']),
    'FK_CATALOG_PRODUCT_ENTITY_TEXT_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'text']),
    'FK_CATALOG_PRODUCT_ENTITY_TEXT_PRODUCT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'text']),
    'FK_CATALOG_PRODUCT_ENTITY_TEXT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'varchar']),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'varchar']),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['catalog/product', 'varchar']),
    'FK_CATALOG_PROD_ENTITY_VARCHAR_PROD_ENTITY',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute'),
    'IDX_USED_FOR_SORT_BY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute'),
    'IDX_USED_IN_PRODUCT_LISTING',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_anchor_indexer_idx'),
    'IDX_CATEGORY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_anchor_indexer_tmp'),
    'IDX_CATEGORY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category'),
    'IDX_LEVEL',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product'),
    'UNQ_CATEGORY_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product'),
    'CATALOG_CATEGORY_PRODUCT_CATEGORY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product'),
    'CATALOG_CATEGORY_PRODUCT_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_index'),
    'UNQ_CATEGORY_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_index'),
    'FK_CATALOG_CATEGORY_PRODUCT_INDEX_PRODUCT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_index'),
    'FK_CATALOG_CATEGORY_PRODUCT_INDEX_CATEGORY_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_index'),
    'IDX_JOIN',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_index'),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_enabled_indexer_idx'),
    'IDX_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_enabled_indexer_tmp'),
    'IDX_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/category_product_indexer_idx'),
    'IDX_PRODUCT_CATEGORY_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/compare_item'),
    'FK_CATALOG_COMPARE_ITEM_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/compare_item'),
    'FK_CATALOG_COMPARE_ITEM_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/compare_item'),
    'IDX_VISITOR_PRODUCTS',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/compare_item'),
    'IDX_CUSTOMER_PRODUCTS',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/compare_item'),
    'FK_CATALOG_COMPARE_ITEM_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/eav_attribute'),
    'IDX_USED_FOR_SORT_BY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/eav_attribute'),
    'IDX_USED_IN_PRODUCT_LISTING',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_enabled_index'),
    'UNQ_PRODUCT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_enabled_index'),
    'IDX_PRODUCT_VISIBILITY_IN_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_enabled_index'),
    'FK_CATALOG_PRODUCT_ENABLED_INDEX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product'),
    'FK_CATALOG_PRODUCT_ENTITY_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product'),
    'FK_CATALOG_PRODUCT_ENTITY_ATTRIBUTE_SET_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product'),
    'SKU',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_media_gallery_value'),
    'FK_CATALOG_PRODUCT_MEDIA_GALLERY_VALUE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'UNQ_CATALOG_PRODUCT_TIER_PRICE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_PRODUCT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_GROUP',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'FK_CATALOG_PRODUCT_TIER_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav'),
    'IDX_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav'),
    'IDX_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav'),
    'IDX_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'IDX_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'IDX_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    'IDX_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    'IDX_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    'IDX_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    'IDX_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    'IDX_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    'IDX_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    'IDX_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    'IDX_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    'IDX_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    'IDX_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    'IDX_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    'IDX_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    'IDX_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_price'),
    'IDX_CUSTOMER_GROUP',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_price'),
    'IDX_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_price'),
    'IDX_MIN_PRICE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_price_indexer_idx'),
    'IDX_CUSTOMER_GROUP',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_price_indexer_idx'),
    'IDX_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_price_indexer_idx'),
    'IDX_MIN_PRICE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_price_indexer_tmp'),
    'IDX_CUSTOMER_GROUP',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_price_indexer_tmp'),
    'IDX_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_price_indexer_tmp'),
    'IDX_MIN_PRICE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_tier_price'),
    'FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_tier_price'),
    'FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_index_website'),
    'IDX_DATE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link'),
    'IDX_UNIQUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link'),
    'FK_LINK_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link'),
    'FK_LINKED_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link'),
    'FK_PRODUCT_LINK_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute'),
    'FK_ATTRIBUTE_PRODUCT_LINK_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    'FK_DECIMAL_PRODUCT_LINK_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    'FK_DECIMAL_LINK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    'UNQ_PRODUCT_LINK_ATTRIBUTE_ID_LINK_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    'FK_INT_PRODUCT_LINK_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    'FK_INT_PRODUCT_LINK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    'FK_VARCHAR_PRODUCT_LINK_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    'FK_VARCHAR_LINK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option'),
    'CATALOG_PRODUCT_OPTION_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_price'),
    'UNQ_OPTION_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_price'),
    'CATALOG_PRODUCT_OPTION_PRICE_OPTION',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_price'),
    'CATALOG_PRODUCT_OPTION_TITLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_title'),
    'UNQ_OPTION_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_title'),
    'CATALOG_PRODUCT_OPTION_TITLE_OPTION',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_title'),
    'CATALOG_PRODUCT_OPTION_TITLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_price'),
    'UNQ_OPTION_TYPE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_price'),
    'CATALOG_PRODUCT_OPTION_TYPE_PRICE_OPTION_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_price'),
    'CATALOG_PRODUCT_OPTION_TYPE_PRICE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_title'),
    'UNQ_OPTION_TYPE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_title'),
    'CATALOG_PRODUCT_OPTION_TYPE_TITLE_OPTION',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_title'),
    'CATALOG_PRODUCT_OPTION_TYPE_TITLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_option_type_value'),
    'CATALOG_PRODUCT_OPTION_TYPE_VALUE_OPTION',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_relation'),
    'IDX_CHILD',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute'),
    'UNQ_PRODUCT_ID_ATTRIBUTE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute'),
    'FK_SUPER_PRODUCT_ATTRIBUTE_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    'UNQ_ATTRIBUTE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_SUPER_PRODUCT_ATTRIBUTE_LABEL',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_CATALOG_PROD_SUPER_ATTR_LABEL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'UNQ_PRODUCT_SUPER_ATTRIBUTE_ID_VALUE_INDEX_WEBSITE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'FK_SUPER_PRODUCT_ATTRIBUTE_PRICING',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'FK_CATALOG_PRODUCT_SUPER_PRICE_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_link'),
    'UNQ_PRODUCT_ID_PARENT_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_link'),
    'FK_SUPER_PRODUCT_LINK_PARENT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_link'),
    'FK_CATALOG_PRODUCT_SUPER_LINK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    'FK_ATTRIBUTE_DATETIME_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    'FK_CATALOG_CATEGORY_ENTITY_DATETIME_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    'FK_CATALOG_CATEGORY_ENTITY_DATETIME_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    'FK_ATTRIBUTE_DECIMAL_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    'FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    'FK_CATALOG_CATEGORY_ENTITY_DECIMAL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'int']),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'int']),
    'FK_ATTRIBUTE_INT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'int']),
    'FK_CATALOG_CATEGORY_EMTITY_INT_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'int']),
    'FK_CATALOG_CATEGORY_EMTITY_INT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'text']),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'text']),
    'FK_ATTRIBUTE_TEXT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'text']),
    'FK_CATALOG_CATEGORY_ENTITY_TEXT_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'text']),
    'FK_CATALOG_CATEGORY_ENTITY_TEXT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    'FK_ATTRIBUTE_VARCHAR_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    'FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    'FK_CATALOG_CATEGORY_ENTITY_VARCHAR_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    'IDX_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    'FK_CATALOG_PRODUCT_ENTITY_DATETIME_PRODUCT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    'IDX_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_PRODUCT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    'FK_CATALOG_PRODUCT_ENTITY_DECIMAL_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    'IDX_BASE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    'FK_ATTRIBUTE_GALLERY_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    'FK_CATALOG_CATEGORY_ENTITY_GALLERY_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    'FK_CATALOG_CATEGORY_ENTITY_GALLERY_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'int']),
    'IDX_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'int']),
    'FK_CATALOG_PRODUCT_ENTITY_INT_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'int']),
    'FK_CATALOG_PRODUCT_ENTITY_INT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'int']),
    'FK_CATALOG_PRODUCT_ENTITY_INT_PRODUCT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'text']),
    'IDX_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'text']),
    'FK_CATALOG_PRODUCT_ENTITY_TEXT_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'text']),
    'FK_CATALOG_PRODUCT_ENTITY_TEXT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'text']),
    'FK_CATALOG_PRODUCT_ENTITY_TEXT_PRODUCT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    'IDX_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    'FK_CATALOG_PRODUCT_ENTITY_VARCHAR_PRODUCT_ENTITY',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('catalog/product') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type ID',
            ],
            'attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set ID',
            ],
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'default'   => 'simple',
                'comment'   => 'Type ID',
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'SKU',
            ],
            'has_options' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Has Options',
            ],
            'required_options' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Required Options',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Creation Time',
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Update Time',
            ],
        ],
        'comment' => 'Catalog Product Table',
    ],
    $installer->getTable('catalog/category') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type ID',
            ],
            'attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attriute Set ID',
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent Category ID',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Creation Time',
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Update Time',
            ],
            'path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Tree Path',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position',
            ],
            'level' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Tree Level',
            ],
            'children_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Child Count',
            ],
        ],
        'comment' => 'Catalog Category Table',
    ],
    $installer->getTable('catalog/category_product') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position',
            ],
        ],
        'comment' => 'Catalog Product To Category Linkage Table',
    ],
    $installer->getTable('catalog/category_product_index') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Position',
            ],
            'is_parent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Parent',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'visibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility',
            ],
        ],
        'comment' => 'Catalog Category Product Index',
    ],
    $installer->getTable('catalog/compare_item') => [
        'columns' => [
            'catalog_compare_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Compare Item ID',
            ],
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visitor ID',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store ID',
            ],
        ],
        'comment' => 'Catalog Compare Table',
    ],
    $installer->getTable('catalog/product_website') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
        ],
        'comment' => 'Catalog Product To Website Linkage Table',
    ],
    $installer->getTable('catalog/product_enabled_index') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'visibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility',
            ],
        ],
        'comment' => 'Catalog Product Visibility Index Table',
    ],
    $installer->getTable('catalog/product_link_type') => [
        'columns' => [
            'link_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link Type ID',
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Code',
            ],
        ],
        'comment' => 'Catalog Product Link Type Table',
    ],
    $installer->getTable('catalog/product_link') => [
        'columns' => [
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'linked_product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Linked Product ID',
            ],
            'link_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link Type ID',
            ],
        ],
        'comment' => 'Catalog Product To Product Linkage Table',
    ],
    $installer->getTable('catalog/product_link_attribute') => [
        'columns' => [
            'product_link_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product Link Attribute ID',
            ],
            'link_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link Type ID',
            ],
            'product_link_attribute_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Product Link Attribute Code',
            ],
            'data_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Data Type',
            ],
        ],
        'comment' => 'Catalog Product Link Attribute Table',
    ],
    $installer->getTable('catalog/product_link_attribute_decimal') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'product_link_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Product Link Attribute ID',
            ],
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Link Decimal Attribute Table',
    ],
    $installer->getTable('catalog/product_link_attribute_int') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'product_link_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Product Link Attribute ID',
            ],
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Link Integer Attribute Table',
    ],
    $installer->getTable('catalog/product_link_attribute_varchar') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'product_link_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product Link Attribute ID',
            ],
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Link Varchar Attribute Table',
    ],
    $installer->getTable('catalog/product_super_attribute') => [
        'columns' => [
            'product_super_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product Super Attribute ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position',
            ],
        ],
        'comment' => 'Catalog Product Super Attribute Table',
    ],
    $installer->getTable('catalog/product_super_attribute_label') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'product_super_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product Super Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'use_default' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Use Default Value',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Super Attribute Label Table',
    ],
    $installer->getTable('catalog/product_super_attribute_pricing') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'product_super_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product Super Attribute ID',
            ],
            'value_index' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value Index',
            ],
            'is_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Percent',
            ],
            'pricing_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Pricing Value',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
        ],
        'comment' => 'Catalog Product Super Attribute Pricing Table',
    ],
    $installer->getTable('catalog/product_super_link') => [
        'columns' => [
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent ID',
            ],
        ],
        'comment' => 'Catalog Product Super Link Table',
    ],
    $installer->getTable('catalog/product_attribute_tier_price') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'all_groups' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Applicable To All Customer Groups',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '1.0000',
                'comment'   => 'QTY',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
        ],
        'comment' => 'Catalog Product Tier Price Attribute Backend Table',
    ],
    $installer->getTable('catalog/product_attribute_media_gallery') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Media Gallery Attribute Backend Table',
    ],
    $installer->getTable('catalog/product_attribute_media_gallery_value') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Label',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Position',
            ],
            'disabled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Disabled',
            ],
        ],
        'comment' => 'Catalog Product Media Gallery Attribute Value Table',
    ],
    $installer->getTable('catalog/product_option') => [
        'columns' => [
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Type',
            ],
            'is_require' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Required',
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'SKU',
            ],
            'max_characters' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Max Characters',
            ],
            'file_extension' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'File Extension',
            ],
            'image_size_x' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Image Size X',
            ],
            'image_size_y' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Image Size Y',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Catalog Product Option Table',
    ],
    $installer->getTable('catalog/product_option_price') => [
        'columns' => [
            'option_price_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Price ID',
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price',
            ],
            'price_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'fixed',
                'comment'   => 'Price Type',
            ],
        ],
        'comment' => 'Catalog Product Option Price Table',
    ],
    $installer->getTable('catalog/product_option_title') => [
        'columns' => [
            'option_title_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Title ID',
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title',
            ],
        ],
        'comment' => 'Catalog Product Option Title Table',
    ],
    $installer->getTable('catalog/product_option_type_value') => [
        'columns' => [
            'option_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Type ID',
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option ID',
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'SKU',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Catalog Product Option Type Value Table',
    ],
    $installer->getTable('catalog/product_option_type_price') => [
        'columns' => [
            'option_type_price_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Type Price ID',
            ],
            'option_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option Type ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price',
            ],
            'price_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'fixed',
                'comment'   => 'Price Type',
            ],
        ],
        'comment' => 'Catalog Product Option Type Price Table',
    ],
    $installer->getTable('catalog/product_option_type_title') => [
        'columns' => [
            'option_type_title_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Type Title ID',
            ],
            'option_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option Type ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title',
            ],
        ],
        'comment' => 'Catalog Product Option Type Title Table',
    ],
    $installer->getTable('catalog/eav_attribute') => [
        'columns' => [
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'frontend_input_renderer' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Input Renderer',
            ],
            'is_global' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Global',
            ],
            'is_visible' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Visible',
            ],
            'is_searchable' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Searchable',
            ],
            'is_filterable' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Filterable',
            ],
            'is_comparable' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Comparable',
            ],
            'is_visible_on_front' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Visible On Front',
            ],
            'is_html_allowed_on_front' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is HTML Allowed On Front',
            ],
            'is_used_for_price_rules' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used For Price Rules',
            ],
            'is_filterable_in_search' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Filterable In Search',
            ],
            'used_in_product_listing' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used In Product Listing',
            ],
            'used_for_sort_by' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used For Sorting',
            ],
            'is_configurable' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Configurable',
            ],
            'apply_to' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Apply To',
            ],
            'is_visible_in_advanced_search' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Visible In Advanced Search',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position',
            ],
            'is_wysiwyg_enabled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is WYSIWYG Enabled',
            ],
            'is_used_for_promo_rules' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used For Promo Rules',
            ],
        ],
        'comment' => 'Catalog EAV Attribute Table',
    ],
    $installer->getTable('catalog/product_relation') => [
        'columns' => [
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Parent ID',
            ],
            'child_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Child ID',
            ],
        ],
        'comment' => 'Catalog Product Relation Table',
    ],
    $installer->getTable('catalog/product_index_eav') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product EAV Index Table',
    ],
    $installer->getTable('catalog/product_index_eav_decimal') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0.0000',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product EAV Decimal Index Table',
    ],
    $installer->getTable('catalog/product_index_price') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'final_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Final Price',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Index Table',
    ],
    $installer->getTable('catalog/product_index_tier_price') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
        ],
        'comment' => 'Catalog Product Tier Price Index Table',
    ],
    $installer->getTable('catalog/product_index_website') => [
        'columns' => [
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_FLOAT,
                'default'   => '1',
                'comment'   => 'Rate',
            ],
        ],
        'comment' => 'Catalog Product Website Index Table',
    ],
    $installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx') => [
        'columns' => [
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Parent ID',
            ],
            'child_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Child ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Config Option Aggregate Index ',
    ],
    $installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp') => [
        'columns' => [
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Parent ID',
            ],
            'child_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Child ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Config Option Aggregate Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_price_indexer_cfg_option_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Config Option Index Table',
    ],
    $installer->getTable('catalog/product_price_indexer_cfg_option_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Config Option Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_price_indexer_final_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID',
            ],
            'orig_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Price',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
            'base_tier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tier',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Final Index Table',
    ],
    $installer->getTable('catalog/product_price_indexer_final_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID',
            ],
            'orig_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Price',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
            'base_tier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tier',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Final Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_price_indexer_option_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Option Index Table',
    ],
    $installer->getTable('catalog/product_price_indexer_option_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Option Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_price_indexer_option_aggregate_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Option Aggregate Index Table',
    ],
    $installer->getTable('catalog/product_price_indexer_option_aggregate_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Option Aggregate Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_eav_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product EAV Indexer Index Table',
    ],
    $installer->getTable('catalog/product_eav_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product EAV Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_eav_decimal_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0.0000',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product EAV Decimal Indexer Index Table',
    ],
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0.0000',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product EAV Decimal Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/product_price_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'final_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Final Price',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Index Table',
    ],
    $installer->getTable('catalog/product_price_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price',
            ],
            'final_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Final Price',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price',
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price',
            ],
        ],
        'comment' => 'Catalog Product Price Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/category_product_indexer_idx') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position',
            ],
            'is_parent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Parent',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'visibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility',
            ],
        ],
        'comment' => 'Catalog Category Product Indexer Index Table',
    ],
    $installer->getTable('catalog/category_product_indexer_tmp') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position',
            ],
            'is_parent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Parent',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID',
            ],
            'visibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility',
            ],
        ],
        'comment' => 'Catalog Category Product Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/category_product_enabled_indexer_idx') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'visibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility',
            ],
        ],
        'comment' => 'Catalog Category Product Enabled Indexer Index Table',
    ],
    $installer->getTable('catalog/category_product_enabled_indexer_tmp') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'visibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility',
            ],
        ],
        'comment' => 'Catalog Category Product Enabled Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/category_anchor_indexer_idx') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Path',
            ],
        ],
        'comment' => 'Catalog Category Anchor Indexer Index Table',
    ],
    $installer->getTable('catalog/category_anchor_indexer_tmp') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Path',
            ],
        ],
        'comment' => 'Catalog Category Anchor Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable('catalog/category_anchor_products_indexer_idx') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Position',
            ],
        ],
        'comment' => 'Catalog Category Anchor Product Indexer Index Table',
    ],
    $installer->getTable('catalog/category_anchor_products_indexer_tmp') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
        ],
        'comment' => 'Catalog Category Anchor Product Indexer Temp Table',
        'engine'  => 'InnoDB',
    ],
    $installer->getTable(['catalog/product','datetime']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Datetime Attribute Backend Table',
    ],
    $installer->getTable(['catalog/product','decimal']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Decimal Attribute Backend Table',
    ],
    $installer->getTable(['catalog/product','int']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Integer Attribute Backend Table',
    ],
    $installer->getTable(['catalog/product','text']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Text Attribute Backend Table',
    ],
    $installer->getTable(['catalog/product','varchar']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Varchar Attribute Backend Table',
    ],
    $installer->getTable(['catalog/product','gallery']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Position',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Product Gallery Attribute Backend Table',
    ],
    $installer->getTable(['catalog/category','datetime']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Category Datetime Attribute Backend Table',
    ],
    $installer->getTable(['catalog/category','decimal']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Category Decimal Attribute Backend Table',
    ],
    $installer->getTable(['catalog/category','int']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Category Integer Attribute Backend Table',
    ],
    $installer->getTable(['catalog/category','text']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Category Text Attribute Backend Table',
    ],
    $installer->getTable(['catalog/category','varchar']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Catalog Category Varchar Attribute Backend Table',
    ],
    $installer->getTable('core/url_rewrite') => [
        'columns' => [
            'category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => true,
                'comment'   => 'Category Id',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => true,
                'comment'   => 'Product Id',
            ],
        ],
    ],
];

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('catalog/product_index_website'),
    'date',
    'website_date',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'comment'   => 'Website Date',
    ],
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_anchor_indexer_idx'),
    $installer->getIdxName('catalog/category_anchor_indexer_idx', ['category_id']),
    ['category_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_anchor_indexer_tmp'),
    $installer->getIdxName('catalog/category_anchor_indexer_tmp', ['category_id']),
    ['category_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category'),
    $installer->getIdxName('catalog/category', ['level']),
    ['level'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product'),
    'PRIMARY',
    ['category_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product'),
    $installer->getIdxName('catalog/category_product', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_index'),
    'PRIMARY',
    ['category_id', 'product_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_index'),
    $installer->getIdxName(
        'catalog/category_product_index',
        ['product_id', 'store_id', 'category_id', 'visibility'],
    ),
    ['product_id', 'store_id', 'category_id', 'visibility'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_index'),
    $installer->getIdxName(
        'catalog/category_product_index',
        ['store_id', 'category_id', 'visibility', 'is_parent', 'position'],
    ),
    ['store_id', 'category_id', 'visibility', 'is_parent', 'position'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_index'),
    $installer->getIdxName(
        'catalog/category_product_index',
        ['product_id', 'store_id', 'category_id', 'visibility'],
    ),
    ['product_id', 'store_id', 'category_id', 'visibility'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_enabled_indexer_idx'),
    $installer->getIdxName('catalog/category_product_enabled_indexer_idx', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_enabled_indexer_tmp'),
    $installer->getIdxName('catalog/category_product_enabled_indexer_tmp', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/category_product_indexer_idx'),
    $installer->getIdxName('catalog/category_product_indexer_idx', ['product_id', 'category_id', 'store_id']),
    ['product_id', 'category_id', 'store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/compare_item'),
    $installer->getIdxName('catalog/compare_item', ['customer_id']),
    ['customer_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/compare_item'),
    $installer->getIdxName('catalog/compare_item', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/compare_item'),
    $installer->getIdxName('catalog/compare_item', ['visitor_id', 'product_id']),
    ['visitor_id', 'product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/compare_item'),
    $installer->getIdxName('catalog/compare_item', ['customer_id', 'product_id']),
    ['customer_id', 'product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/compare_item'),
    $installer->getIdxName('catalog/compare_item', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/eav_attribute'),
    $installer->getIdxName('catalog/eav_attribute', ['used_for_sort_by']),
    ['used_for_sort_by'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/eav_attribute'),
    $installer->getIdxName('catalog/eav_attribute', ['used_in_product_listing']),
    ['used_in_product_listing'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_enabled_index'),
    $installer->getIdxName('catalog/product_enabled_index', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_enabled_index'),
    'PRIMARY',
    ['product_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product'),
    $installer->getIdxName('catalog/product', ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product'),
    $installer->getIdxName('catalog/product', ['attribute_set_id']),
    ['attribute_set_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product'),
    $installer->getIdxName('catalog/product', ['sku']),
    ['sku'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_media_gallery'),
    $installer->getIdxName('catalog/product_attribute_media_gallery', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_media_gallery'),
    $installer->getIdxName('catalog/product_attribute_media_gallery', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_media_gallery_value'),
    $installer->getIdxName('catalog/product_attribute_media_gallery_value', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    $installer->getIdxName(
        'catalog/product_attribute_tier_price',
        ['entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'],
    ),
    ['entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    $installer->getIdxName('catalog/product_attribute_tier_price', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    $installer->getIdxName('catalog/product_attribute_tier_price', ['customer_group_id']),
    ['customer_group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_attribute_tier_price'),
    $installer->getIdxName('catalog/product_attribute_tier_price', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav'),
    $installer->getIdxName('catalog/product_index_eav', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav'),
    $installer->getIdxName('catalog/product_index_eav', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav'),
    $installer->getIdxName('catalog/product_index_eav', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav'),
    $installer->getIdxName('catalog/product_index_eav', ['value']),
    ['value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    $installer->getIdxName('catalog/product_index_eav_decimal', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    $installer->getIdxName('catalog/product_index_eav_decimal', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    $installer->getIdxName('catalog/product_index_eav_decimal', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_eav_decimal'),
    $installer->getIdxName('catalog/product_index_eav_decimal', ['value']),
    ['value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['value']),
    ['value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_decimal_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['value']),
    ['value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_indexer_idx', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_indexer_idx', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_indexer_idx', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_idx'),
    $installer->getIdxName('catalog/product_eav_indexer_idx', ['value']),
    ['value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_indexer_tmp', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_indexer_tmp', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_indexer_tmp', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_eav_indexer_tmp'),
    $installer->getIdxName('catalog/product_eav_indexer_tmp', ['value']),
    ['value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_price'),
    $installer->getIdxName('catalog/product_index_price', ['customer_group_id']),
    ['customer_group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_price'),
    $installer->getIdxName('catalog/product_index_price', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_price'),
    $installer->getIdxName('catalog/product_index_price', ['min_price']),
    ['min_price'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_price_indexer_idx'),
    $installer->getIdxName('catalog/product_price_indexer_idx', ['customer_group_id']),
    ['customer_group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_price_indexer_idx'),
    $installer->getIdxName('catalog/product_price_indexer_idx', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_price_indexer_idx'),
    $installer->getIdxName('catalog/product_price_indexer_idx', ['min_price']),
    ['min_price'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_price_indexer_tmp'),
    $installer->getIdxName('catalog/product_price_indexer_tmp', ['customer_group_id']),
    ['customer_group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_price_indexer_tmp'),
    $installer->getIdxName('catalog/product_price_indexer_tmp', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_price_indexer_tmp'),
    $installer->getIdxName('catalog/product_price_indexer_tmp', ['min_price']),
    ['min_price'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_tier_price'),
    $installer->getIdxName('catalog/product_index_tier_price', ['customer_group_id']),
    ['customer_group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_tier_price'),
    $installer->getIdxName('catalog/product_index_tier_price', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_website'),
    $installer->getIdxName('catalog/product_index_website', ['website_date']),
    ['website_date'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link'),
    $installer->getIdxName(
        'catalog/product_link',
        ['link_type_id', 'product_id', 'linked_product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['link_type_id', 'product_id', 'linked_product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link'),
    $installer->getIdxName('catalog/product_link', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link'),
    $installer->getIdxName('catalog/product_link', ['linked_product_id']),
    ['linked_product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link'),
    $installer->getIdxName('catalog/product_link', ['link_type_id']),
    ['link_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute'),
    $installer->getIdxName('catalog/product_link_attribute', ['link_type_id']),
    ['link_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    $installer->getIdxName(
        'catalog/product_link_attribute_decimal',
        ['product_link_attribute_id', 'link_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_link_attribute_id', 'link_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    $installer->getIdxName('catalog/product_link_attribute_decimal', ['product_link_attribute_id']),
    ['product_link_attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_decimal'),
    $installer->getIdxName('catalog/product_link_attribute_decimal', ['link_id']),
    ['link_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    $installer->getIdxName(
        'catalog/product_link_attribute_int',
        ['product_link_attribute_id', 'link_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_link_attribute_id', 'link_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    $installer->getIdxName('catalog/product_link_attribute_int', ['product_link_attribute_id']),
    ['product_link_attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    $installer->getIdxName('catalog/product_link_attribute_int', ['link_id']),
    ['link_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    $installer->getIdxName(
        'catalog/product_link_attribute_varchar',
        ['product_link_attribute_id', 'link_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_link_attribute_id', 'link_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    $installer->getIdxName('catalog/product_link_attribute_varchar', ['product_link_attribute_id']),
    ['product_link_attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_varchar'),
    $installer->getIdxName('catalog/product_link_attribute_varchar', ['link_id']),
    ['link_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option'),
    $installer->getIdxName('catalog/product_option', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_price'),
    $installer->getIdxName(
        'catalog/product_option_price',
        ['option_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['option_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_price'),
    $installer->getIdxName('catalog/product_option_price', ['option_id']),
    ['option_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_price'),
    $installer->getIdxName('catalog/product_option_price', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_title'),
    $installer->getIdxName(
        'catalog/product_option_title',
        ['option_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['option_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_title'),
    $installer->getIdxName('catalog/product_option_title', ['option_id']),
    ['option_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_title'),
    $installer->getIdxName('catalog/product_option_title', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_price'),
    $installer->getIdxName(
        'catalog/product_option_type_price',
        ['option_type_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['option_type_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_price'),
    $installer->getIdxName('catalog/product_option_type_price', ['option_type_id']),
    ['option_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_price'),
    $installer->getIdxName('catalog/product_option_type_price', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_title'),
    $installer->getIdxName(
        'catalog/product_option_type_title',
        ['option_type_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['option_type_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_title'),
    $installer->getIdxName('catalog/product_option_type_title', ['option_type_id']),
    ['option_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_title'),
    $installer->getIdxName('catalog/product_option_type_title', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_option_type_value'),
    $installer->getIdxName('catalog/product_option_type_value', ['option_id']),
    ['option_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_relation'),
    $installer->getIdxName('catalog/product_relation', ['child_id']),
    ['child_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute'),
    $installer->getIdxName(
        'catalog/product_super_attribute',
        ['product_id', 'attribute_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_id', 'attribute_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute'),
    $installer->getIdxName('catalog/product_super_attribute', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    $installer->getIdxName(
        'catalog/product_super_attribute_label',
        ['product_super_attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_super_attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    $installer->getIdxName('catalog/product_super_attribute_label', ['product_super_attribute_id']),
    ['product_super_attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_label'),
    $installer->getIdxName('catalog/product_super_attribute_label', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    $installer->getIdxName(
        'catalog/product_super_attribute_pricing',
        ['product_super_attribute_id', 'value_index', 'website_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_super_attribute_id', 'value_index', 'website_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    $installer->getIdxName('catalog/product_super_attribute_pricing', ['product_super_attribute_id']),
    ['product_super_attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    $installer->getIdxName('catalog/product_super_attribute_pricing', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_link'),
    $installer->getIdxName(
        'catalog/product_super_link',
        ['product_id', 'parent_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_id', 'parent_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_link'),
    $installer->getIdxName('catalog/product_super_link', ['parent_id']),
    ['parent_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_link'),
    $installer->getIdxName('catalog/product_super_link', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_website'),
    $installer->getIdxName('catalog/product_website', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    $installer->getIdxName(
        ['catalog/category', 'datetime'],
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    $installer->getIdxName(['catalog/category', 'datetime'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    $installer->getIdxName(['catalog/category', 'datetime'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'datetime']),
    $installer->getIdxName(['catalog/category', 'datetime'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    $installer->getIdxName(
        ['catalog/category', 'decimal'],
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    $installer->getIdxName(['catalog/category', 'decimal'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    $installer->getIdxName(['catalog/category', 'decimal'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'decimal']),
    $installer->getIdxName(['catalog/category', 'decimal'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'int']),
    $installer->getIdxName(
        ['catalog/category', 'int'],
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'int']),
    $installer->getIdxName(['catalog/category', 'int'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'int']),
    $installer->getIdxName(['catalog/category', 'int'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'int']),
    $installer->getIdxName(['catalog/category', 'int'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'text']),
    $installer->getIdxName(
        ['catalog/category', 'text'],
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'text']),
    $installer->getIdxName(['catalog/category', 'text'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'text']),
    $installer->getIdxName(['catalog/category', 'text'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'text']),
    $installer->getIdxName(['catalog/category', 'text'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    $installer->getIdxName(
        ['catalog/category', 'varchar'],
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    $installer->getIdxName(['catalog/category', 'varchar'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    $installer->getIdxName(['catalog/category', 'varchar'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/category', 'varchar']),
    $installer->getIdxName(['catalog/category', 'varchar'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    $installer->getIdxName(
        ['catalog/product', 'datetime'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    $installer->getIdxName(['catalog/product', 'datetime'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    $installer->getIdxName(['catalog/product', 'datetime'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'datetime']),
    $installer->getIdxName(['catalog/product', 'datetime'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    $installer->getIdxName(
        ['catalog/product', 'decimal'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    $installer->getIdxName(['catalog/product', 'decimal'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    $installer->getIdxName(['catalog/product', 'decimal'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'decimal']),
    $installer->getIdxName(['catalog/product', 'decimal'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    $installer->getIdxName(
        ['catalog/product', 'gallery'],
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    $installer->getIdxName(['catalog/product', 'gallery'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    $installer->getIdxName(['catalog/product', 'gallery'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'gallery']),
    $installer->getIdxName(['catalog/product', 'gallery'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'int']),
    $installer->getIdxName(
        ['catalog/product', 'int'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'int']),
    $installer->getIdxName(['catalog/product', 'int'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'int']),
    $installer->getIdxName(['catalog/product', 'int'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'int']),
    $installer->getIdxName(['catalog/product', 'int'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'text']),
    $installer->getIdxName(
        ['catalog/product', 'text'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'text']),
    $installer->getIdxName(['catalog/product', 'text'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'text']),
    $installer->getIdxName(['catalog/product', 'text'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'text']),
    $installer->getIdxName(['catalog/product', 'text'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    $installer->getIdxName(
        ['catalog/product', 'varchar'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    $installer->getIdxName(['catalog/product', 'varchar'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    $installer->getIdxName(['catalog/product', 'varchar'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['catalog/product', 'varchar']),
    $installer->getIdxName(['catalog/product', 'varchar'], ['entity_id']),
    ['entity_id'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/category_product', 'category_id', 'catalog/category', 'entity_id'),
    $installer->getTable('catalog/category_product'),
    'category_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/category_product', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/category_product'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/category_product_index', 'category_id', 'catalog/category', 'entity_id'),
    $installer->getTable('catalog/category_product_index'),
    'category_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/category_product_index', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/category_product_index'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/category_product_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/category_product_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/compare_item', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('catalog/compare_item'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/compare_item', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/compare_item'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/compare_item', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/compare_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/eav_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('catalog/eav_attribute'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_enabled_index', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_enabled_index'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_enabled_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_enabled_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product', 'attribute_set_id', 'eav_attribute_set', 'attribute_set_id'),
    $installer->getTable('catalog/product'),
    'attribute_set_id',
    $installer->getTable('eav_attribute_set'),
    'attribute_set_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    $installer->getTable('catalog/product'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_attribute_media_gallery', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_attribute_media_gallery', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_attribute_media_gallery_value',
        'value_id',
        'catalog/product_attribute_media_gallery',
        'value_id',
    ),
    $installer->getTable('catalog/product_attribute_media_gallery_value'),
    'value_id',
    $installer->getTable('catalog/product_attribute_media_gallery'),
    'value_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_attribute_media_gallery_value', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_attribute_media_gallery_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_attribute_tier_price',
        'customer_group_id',
        'customer/customer_group',
        'customer_group_id',
    ),
    $installer->getTable('catalog/product_attribute_tier_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_attribute_tier_price', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_attribute_tier_price'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_attribute_tier_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalog/product_attribute_tier_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_eav', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('catalog/product_index_eav'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_eav', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_index_eav'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_eav', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_index_eav'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_eav_decimal', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('catalog/product_index_eav_decimal'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_eav_decimal', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_index_eav_decimal'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_eav_decimal', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_index_eav_decimal'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_index_price',
        'customer_group_id',
        'customer/customer_group',
        'customer_group_id',
    ),
    $installer->getTable('catalog/product_index_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_price', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_index_price'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalog/product_index_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_index_tier_price',
        'customer_group_id',
        'customer/customer_group',
        'customer_group_id',
    ),
    $installer->getTable('catalog/product_index_tier_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_tier_price', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_index_tier_price'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_tier_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalog/product_index_tier_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_index_website', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalog/product_index_website'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_link', 'linked_product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_link'),
    'linked_product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_link', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_link'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_link', 'link_type_id', 'catalog/product_link_type', 'link_type_id'),
    $installer->getTable('catalog/product_link'),
    'link_type_id',
    $installer->getTable('catalog/product_link_type'),
    'link_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute',
        'link_type_id',
        'catalog/product_link_type',
        'link_type_id',
    ),
    $installer->getTable('catalog/product_link_attribute'),
    'link_type_id',
    $installer->getTable('catalog/product_link_type'),
    'link_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_link_attribute_decimal', 'link_id', 'catalog/product_link', 'link_id'),
    $installer->getTable('catalog/product_link_attribute_decimal'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_decimal',
        'product_link_attribute_id',
        'catalog/product_link_attribute',
        'product_link_attribute_id',
    ),
    $installer->getTable('catalog/product_link_attribute_decimal'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_int',
        'product_link_attribute_id',
        'catalog/product_link_attribute',
        'product_link_attribute_id',
    ),
    $installer->getTable('catalog/product_link_attribute_int'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_link_attribute_int', 'link_id', 'catalog/product_link', 'link_id'),
    $installer->getTable('catalog/product_link_attribute_int'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_link_attribute_varchar', 'link_id', 'catalog/product_link', 'link_id'),
    $installer->getTable('catalog/product_link_attribute_varchar'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_varchar',
        'product_link_attribute_id',
        'catalog/product_link_attribute',
        'product_link_attribute_id',
    ),
    $installer->getTable('catalog/product_link_attribute_varchar'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_option'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_price', 'option_id', 'catalog/product_option', 'option_id'),
    $installer->getTable('catalog/product_option_price'),
    'option_id',
    $installer->getTable('catalog/product_option'),
    'option_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_price', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_option_price'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_title', 'option_id', 'catalog/product_option', 'option_id'),
    $installer->getTable('catalog/product_option_title'),
    'option_id',
    $installer->getTable('catalog/product_option'),
    'option_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_option_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_option_type_price',
        'option_type_id',
        'catalog/product_option_type_value',
        'option_type_id',
    ),
    $installer->getTable('catalog/product_option_type_price'),
    'option_type_id',
    $installer->getTable('catalog/product_option_type_value'),
    'option_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_type_price', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_option_type_price'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_option_type_title',
        'option_type_id',
        'catalog/product_option_type_value',
        'option_type_id',
    ),
    $installer->getTable('catalog/product_option_type_title'),
    'option_type_id',
    $installer->getTable('catalog/product_option_type_value'),
    'option_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_type_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_option_type_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_option_type_value', 'option_id', 'catalog/product_option', 'option_id'),
    $installer->getTable('catalog/product_option_type_value'),
    'option_id',
    $installer->getTable('catalog/product_option'),
    'option_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_relation', 'child_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_relation'),
    'child_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_relation', 'parent_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_relation'),
    'parent_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_super_attribute', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_super_attribute'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_super_attribute_label',
        'product_super_attribute_id',
        'catalog/product_super_attribute',
        'product_super_attribute_id',
    ),
    $installer->getTable('catalog/product_super_attribute_label'),
    'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'),
    'product_super_attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_super_attribute_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalog/product_super_attribute_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_super_attribute_pricing', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_super_attribute_pricing',
        'product_super_attribute_id',
        'catalog/product_super_attribute',
        'product_super_attribute_id',
    ),
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'),
    'product_super_attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_super_link', 'parent_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_super_link'),
    'parent_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_super_link', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_super_link'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_website', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalog/product_website'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog/product_website', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalog/product_website'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'datetime'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/category', 'datetime']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'datetime'], 'entity_id', 'catalog/category', 'entity_id'),
    $installer->getTable(['catalog/category', 'datetime']),
    'entity_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'datetime'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/category', 'datetime']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'decimal'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/category', 'decimal']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'decimal'], 'entity_id', 'catalog/category', 'entity_id'),
    $installer->getTable(['catalog/category', 'decimal']),
    'entity_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'decimal'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/category', 'decimal']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'int'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/category', 'int']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'int'], 'entity_id', 'catalog/category', 'entity_id'),
    $installer->getTable(['catalog/category', 'int']),
    'entity_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'int'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/category', 'int']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'text'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/category', 'text']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'text'], 'entity_id', 'catalog/category', 'entity_id'),
    $installer->getTable(['catalog/category', 'text']),
    'entity_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'text'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/category', 'text']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'varchar'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/category', 'varchar']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'varchar'], 'entity_id', 'catalog/category', 'entity_id'),
    $installer->getTable(['catalog/category', 'varchar']),
    'entity_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/category', 'varchar'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/category', 'varchar']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'datetime'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/product', 'datetime']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'datetime'], 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable(['catalog/product', 'datetime']),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'datetime'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/product', 'datetime']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'decimal'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/product', 'decimal']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'decimal'], 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable(['catalog/product', 'decimal']),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'decimal'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/product', 'decimal']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'gallery'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/product', 'gallery']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'gallery'], 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable(['catalog/product', 'gallery']),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'gallery'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/product', 'gallery']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'int'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/product', 'int']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'int'], 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable(['catalog/product', 'int']),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'int'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/product', 'int']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'text'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/product', 'text']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'text'], 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable(['catalog/product', 'text']),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'text'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/product', 'text']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'varchar'], 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(['catalog/product', 'varchar']),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'varchar'], 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable(['catalog/product', 'varchar']),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['catalog/product', 'varchar'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['catalog/product', 'varchar']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->endSetup();
$installer->getConnection()->closeConnection();
