<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
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
    $installer->getTable('downloadable/link'),
    'FK_DOWNLODABLE_LINK_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_price'),
    'FK_DOWNLOADABLE_LINK_PRICE_LINK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_price'),
    'FK_DOWNLOADABLE_LINK_PRICE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_purchased'),
    'FK_DOWNLOADABLE_LINK_ORDER_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_purchased_item'),
    'FK_DOWNLOADABLE_LINK_PURCHASED_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_purchased_item'),
    'FK_DOWNLOADABLE_LINK_ORDER_ITEM_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_title'),
    'FK_DOWNLOADABLE_LINK_TITLE_LINK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/link_title'),
    'FK_DOWNLOADABLE_LINK_TITLE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable/sample'),
    'FK_DOWNLODABLE_SAMPLE_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable_sample_title'),
    'FK_DOWNLOADABLE_SAMPLE_TITLE_SAMPLE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable_sample_title'),
    'FK_DOWNLOADABLE_SAMPLE_TITLE_STORE',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link'),
    'DOWNLODABLE_LINK_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link'),
    'DOWNLODABLE_LINK_PRODUCT_SORT_ORDER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_price'),
    'DOWNLOADABLE_LINK_PRICE_LINK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_price'),
    'DOWNLOADABLE_LINK_PRICE_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_purchased'),
    'DOWNLOADABLE_ORDER_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_purchased'),
    'DOWNLOADABLE_CUSTOMER_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_purchased'),
    'KEY_DOWNLOADABLE_ORDER_ITEM_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_purchased_item'),
    'DOWNLOADABLE_LINK_PURCHASED_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_purchased_item'),
    'DOWNLOADABLE_ORDER_ITEM_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_purchased_item'),
    'DOWNLOADALBE_LINK_HASH',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_title'),
    'UNQ_LINK_TITLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_title'),
    'DOWNLOADABLE_LINK_TITLE_LINK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/link_title'),
    'DOWNLOADABLE_LINK_TITLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/sample'),
    'DOWNLODABLE_SAMPLE_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/sample_title'),
    'UNQ_SAMPLE_TITLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/sample_title'),
    'DOWNLOADABLE_SAMPLE_TITLE_SAMPLE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('downloadable/sample_title'),
    'DOWNLOADABLE_SAMPLE_TITLE_STORE',
);

/**
 * Change columns
 */

$tables = [
    $installer->getTable('downloadable/link') => [
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
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort order',
            ],
            'number_of_downloads' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Number of downloads',
            ],
            'is_shareable' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Shareable flag',
            ],
            'link_url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link Url',
            ],
            'link_file' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link File',
            ],
            'link_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Link Type',
            ],
            'sample_url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sample Url',
            ],
            'sample_file' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sample File',
            ],
            'sample_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Sample Type',
            ],
        ],
        'comment' => 'Downloadable Link Table',
    ],
    $installer->getTable('downloadable/link_title') => [
        'columns' => [
            'title_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Title ID',
            ],
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID',
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
                'comment'   => 'Title',
            ],
        ],
        'comment' => 'Link Title Table',
    ],
    $installer->getTable('downloadable/link_price') => [
        'columns' => [
            'price_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Price ID',
            ],
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website ID',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price',
            ],
        ],
        'comment' => 'Downloadable Link Price Table',
    ],
    $installer->getTable('downloadable/sample') => [
        'columns' => [
            'sample_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Sample ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'sample_url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sample URL',
            ],
            'sample_file' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sample file',
            ],
            'sample_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Sample Type',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Downloadable Sample Table',
    ],
    $installer->getTable('downloadable/sample_title') => [
        'columns' => [
            'title_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Title ID',
            ],
            'sample_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sample ID',
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
                'comment'   => 'Title',
            ],
        ],
        'comment' => 'Downloadable Sample Title Table',
    ],
    $installer->getTable('downloadable/link_purchased') => [
        'columns' => [
            'purchased_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Purchased ID',
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Order ID',
            ],
            'order_increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment ID',
            ],
            'order_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Order Item ID',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Date of creation',
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Date of modification',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer ID',
            ],
            'product_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product name',
            ],
            'product_sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product sku',
            ],
            'link_section_title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link_section_title',
            ],
        ],
        'comment' => 'Downloadable Link Purchased Table',
    ],
    $installer->getTable('downloadable/link_purchased_item') => [
        'columns' => [
            'item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item ID',
            ],
            'purchased_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Purchased ID',
            ],
            'order_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Order Item ID',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Product ID',
            ],
            'link_hash' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link hash',
            ],
            'number_of_downloads_bought' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Number of downloads bought',
            ],
            'number_of_downloads_used' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Number of downloads used',
            ],
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID',
            ],
            'link_title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link Title',
            ],
            'is_shareable' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Shareable Flag',
            ],
            'link_url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link Url',
            ],
            'link_file' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link File',
            ],
            'link_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Link Type',
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Status',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Creation Time',
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Update Time',
            ],
        ],
        'comment' => 'Downloadable Link Purchased Item Table',
    ],
    $installer->getTable('downloadable/product_price_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Minimum price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Maximum price',
            ],
        ],
        'comment' => 'Indexer Table for price of downloadable products',
    ],
    $installer->getTable('downloadable/product_price_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity ID',
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group ID',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website ID',
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Minimum price',
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Maximum price',
            ],
        ],
        'comment' => 'Temporary Indexer Table for price of downloadable products',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link'),
    $installer->getIdxName('downloadable/link', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link'),
    $installer->getIdxName('downloadable/link', ['product_id', 'sort_order']),
    ['product_id', 'sort_order'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_price'),
    $installer->getIdxName('downloadable/link_price', ['link_id']),
    ['link_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_price'),
    $installer->getIdxName('downloadable/link_price', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_purchased'),
    $installer->getIdxName('downloadable/link_purchased', ['order_id']),
    ['order_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_purchased'),
    $installer->getIdxName('downloadable/link_purchased', ['order_item_id']),
    ['order_item_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_purchased'),
    $installer->getIdxName('downloadable/link_purchased', ['customer_id']),
    ['customer_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_purchased_item'),
    $installer->getIdxName('downloadable/link_purchased_item', ['link_hash']),
    ['link_hash'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_purchased_item'),
    $installer->getIdxName('downloadable/link_purchased_item', ['order_item_id']),
    ['order_item_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_purchased_item'),
    $installer->getIdxName('downloadable/link_purchased_item', ['purchased_id']),
    ['purchased_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_title'),
    $installer->getIdxName(
        'downloadable/link_title',
        ['link_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['link_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_title'),
    $installer->getIdxName('downloadable/link_title', ['link_id']),
    ['link_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/link_title'),
    $installer->getIdxName('downloadable/link_title', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/sample'),
    $installer->getIdxName('downloadable/sample', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/sample_title'),
    $installer->getIdxName(
        'downloadable/sample_title',
        ['sample_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['sample_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/sample_title'),
    $installer->getIdxName('downloadable/sample_title', ['sample_id']),
    ['sample_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('downloadable/sample_title'),
    $installer->getIdxName('downloadable/sample_title', ['store_id']),
    ['store_id'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('downloadable/link'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_price', 'link_id', 'downloadable/link', 'link_id'),
    $installer->getTable('downloadable/link_price'),
    'link_id',
    $installer->getTable('downloadable/link'),
    'link_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('downloadable/link_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_purchased', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('downloadable/link_purchased'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_purchased', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('downloadable/link_purchased'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'downloadable/link_purchased_item',
        'purchased_id',
        'downloadable/link_purchased',
        'purchased_id',
    ),
    $installer->getTable('downloadable/link_purchased_item'),
    'purchased_id',
    $installer->getTable('downloadable/link_purchased'),
    'purchased_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_purchased_item', 'order_item_id', 'sales/order_item', 'item_id'),
    $installer->getTable('downloadable/link_purchased_item'),
    'order_item_id',
    $installer->getTable('sales/order_item'),
    'item_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_title', 'link_id', 'downloadable/link', 'link_id'),
    $installer->getTable('downloadable/link_title'),
    'link_id',
    $installer->getTable('downloadable/link'),
    'link_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/link_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('downloadable/link_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/sample', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('downloadable/sample'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/sample_title', 'sample_id', 'downloadable/sample', 'sample_id'),
    $installer->getTable('downloadable/sample_title'),
    'sample_id',
    $installer->getTable('downloadable/sample'),
    'sample_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('downloadable/sample_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('downloadable/sample_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->endSetup();
