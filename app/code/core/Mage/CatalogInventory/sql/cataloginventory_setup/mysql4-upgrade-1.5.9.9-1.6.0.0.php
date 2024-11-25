<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('cataloginventory/stock_item'),
    'FK_CATALOGINVENTORY_STOCK_ITEM_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cataloginventory/stock_item'),
    'FK_CATALOGINVENTORY_STOCK_ITEM_STOCK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cataloginventory/stock_status'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cataloginventory/stock_status'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_STOCK',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cataloginventory/stock_status'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_item'),
    'IDX_STOCK_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_item'),
    'FK_CATALOGINVENTORY_STOCK_ITEM_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_item'),
    'FK_CATALOGINVENTORY_STOCK_ITEM_STOCK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_status'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_STOCK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_status'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory_stock_status_idx'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_STOCK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_status_indexer_idx'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory_stock_status_tmp'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_STOCK',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cataloginventory/stock_status_indexer_tmp'),
    'FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE',
);

/*
 * Change columns
 */
$tables = [
    $installer->getTable('cataloginventory/stock') => [
        'columns' => [
            'stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Stock Id',
            ],
            'stock_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Stock Name',
            ],
        ],
        'comment' => 'Cataloginventory Stock',
    ],
    $installer->getTable('cataloginventory/stock_item') => [
        'columns' => [
            'item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item Id',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product Id',
            ],
            'stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Stock Id',
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty',
            ],
            'min_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Min Qty',
            ],
            'use_config_min_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Min Qty',
            ],
            'is_qty_decimal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Qty Decimal',
            ],
            'backorders' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Backorders',
            ],
            'use_config_backorders' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Backorders',
            ],
            'min_sale_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '1.0000',
                'comment'   => 'Min Sale Qty',
            ],
            'use_config_min_sale_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Min Sale Qty',
            ],
            'max_sale_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Max Sale Qty',
            ],
            'use_config_max_sale_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Max Sale Qty',
            ],
            'is_in_stock' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is In Stock',
            ],
            'low_stock_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Low Stock Date',
            ],
            'notify_stock_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Notify Stock Qty',
            ],
            'use_config_notify_stock_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Notify Stock Qty',
            ],
            'manage_stock' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Manage Stock',
            ],
            'use_config_manage_stock' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Manage Stock',
            ],
            'use_config_qty_increments' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Qty Increments',
            ],
            'qty_increments' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Increments',
            ],
            'enable_qty_increments' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Enable Qty Increments',
            ],
        ],
        'comment' => 'Cataloginventory Stock Item',
    ],
    $installer->getTable('cataloginventory/stock_status') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product Id',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id',
            ],
            'stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Stock Id',
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty',
            ],
            'stock_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Stock Status',
            ],
        ],
        'comment' => 'Cataloginventory Stock Status',
    ],
    $installer->getTable('cataloginventory/stock_status_indexer_idx') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product Id',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id',
            ],
            'stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Stock Id',
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty',
            ],
            'stock_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Stock Status',
            ],
        ],
        'comment' => 'Cataloginventory Stock Status Indexer Idx',
    ],
    $installer->getTable('cataloginventory/stock_status_indexer_tmp') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product Id',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id',
            ],
            'stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Stock Id',
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty',
            ],
            'stock_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Stock Status',
            ],
        ],
        'comment' => 'Cataloginventory Stock Status Indexer Tmp',
        'engine'  => 'InnoDB',
    ],
];

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('cataloginventory/stock_item'),
    'stock_status_changed_automatically',
    'stock_status_changed_auto',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        'comment'   => 'Stock Status Changed Automatically',
    ],
);

$installer->getConnection()->changeColumn(
    $installer->getTable('cataloginventory/stock_item'),
    'use_config_enable_qty_increments',
    'use_config_enable_qty_inc',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        'comment'   => 'Use Config Enable Qty Increments',
    ],
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_item'),
    $installer->getIdxName(
        'cataloginventory/stock_item',
        ['product_id', 'stock_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['product_id', 'stock_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_item'),
    $installer->getIdxName('cataloginventory/stock_item', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_item'),
    $installer->getIdxName('cataloginventory/stock_item', ['stock_id']),
    ['stock_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_status'),
    $installer->getIdxName('cataloginventory/stock_status', ['stock_id']),
    ['stock_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_status'),
    $installer->getIdxName('cataloginventory/stock_status', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_status_indexer_idx'),
    $installer->getIdxName('cataloginventory/stock_status_indexer_idx', ['stock_id']),
    ['stock_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_status_indexer_idx'),
    $installer->getIdxName('cataloginventory/stock_status_indexer_idx', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_status_indexer_tmp'),
    $installer->getIdxName('cataloginventory/stock_status_indexer_tmp', ['stock_id']),
    ['stock_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cataloginventory/stock_status_indexer_tmp'),
    $installer->getIdxName('cataloginventory/stock_status_indexer_tmp', ['website_id']),
    ['website_id'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('cataloginventory/stock_item', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('cataloginventory/stock_item'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cataloginventory/stock_item', 'stock_id', 'cataloginventory/stock', 'stock_id'),
    $installer->getTable('cataloginventory/stock_item'),
    'stock_id',
    $installer->getTable('cataloginventory/stock'),
    'stock_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cataloginventory/stock_status', 'stock_id', 'cataloginventory/stock', 'stock_id'),
    $installer->getTable('cataloginventory/stock_status'),
    'stock_id',
    $installer->getTable('cataloginventory/stock'),
    'stock_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cataloginventory/stock_status', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('cataloginventory/stock_status'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cataloginventory/stock_status', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('cataloginventory/stock_status'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->endSetup();
