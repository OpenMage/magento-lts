<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
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
    $installer->getTable('wishlist/wishlist'),
    'FK_WISHLIST_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item'),
    'FK_WISHLIST_ITEM_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item'),
    'FK_WISHLIST_ITEM_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item'),
    'FK_WISHLIST_ITEM_WISHLIST'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item_option'),
    'FK_WISHLIST_ITEM_OPTION_ITEM_ID'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/wishlist'),
    'UNQ_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/wishlist'),
    'IDX_IS_SHARED'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item'),
    'IDX_WISHLIST'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item'),
    'IDX_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item_option'),
    'FK_WISHLIST_ITEM_OPTION_ITEM_ID'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('wishlist/wishlist') => [
        'columns' => [
            'wishlist_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Wishlist ID'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer ID'
            ],
            'shared' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sharing flag (0 or 1)'
            ],
            'sharing_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Sharing encrypted code'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Last updated date'
            ]
        ],
        'comment' => 'Wishlist main Table'
    ],
    $installer->getTable('wishlist/item') => [
        'columns' => [
            'wishlist_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Wishlist item ID'
            ],
            'wishlist_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Wishlist ID'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store ID'
            ],
            'added_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Add date and time'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Short description of wish list item'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'comment'   => 'Qty'
            ]
        ],
        'comment' => 'Wishlist items'
    ],
    $installer->getTable('wishlist/item_option') => [
        'columns' => [
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id'
            ],
            'wishlist_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Wishlist Item Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => true,
                'comment'   => 'Value'
            ]
        ],
        'comment' => 'Wishlist Item Option Table'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/wishlist'),
    $installer->getIdxName(
        'wishlist/wishlist',
        ['customer_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['customer_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/wishlist'),
    $installer->getIdxName('wishlist/wishlist', ['shared']),
    ['shared']
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/item'),
    $installer->getIdxName('wishlist/item', ['wishlist_id']),
    ['wishlist_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/item'),
    $installer->getIdxName('wishlist/item', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/item'),
    $installer->getIdxName('wishlist/item', ['store_id']),
    ['store_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/wishlist', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('wishlist/wishlist'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item', 'wishlist_id', 'wishlist/wishlist', 'wishlist_id'),
    $installer->getTable('wishlist/item'),
    'wishlist_id',
    $installer->getTable('wishlist/wishlist'),
    'wishlist_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('wishlist/item'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('wishlist/item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item_option', 'wishlist_item_id', 'wishlist/item', 'wishlist_item_id'),
    $installer->getTable('wishlist/item_option'),
    'wishlist_item_id',
    $installer->getTable('wishlist/item'),
    'wishlist_item_id'
);

$installer->endSetup();
