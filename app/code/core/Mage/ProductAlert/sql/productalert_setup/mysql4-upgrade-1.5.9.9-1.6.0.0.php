<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_WEBSITE',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_WEBSITE',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('productalert/price') => [
        'columns' => [
            'alert_price_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product alert price id',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer id',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id',
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price amount',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website id',
            ],
            'add_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Product alert add date',
            ],
            'last_send_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Product alert last send date',
            ],
            'send_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product alert send count',
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product alert status',
            ],
        ],
        'comment' => 'Product Alert Price',
    ],
    $installer->getTable('productalert/stock') => [
        'columns' => [
            'alert_stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product alert stock id',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer id',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website id',
            ],
            'add_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Product alert add date',
            ],
            'send_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Product alert send date',
            ],
            'send_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Send Count',
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product alert status',
            ],
        ],
        'comment' => 'Product Alert Stock',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('productalert/price'),
    $installer->getIdxName('productalert/price', ['customer_id']),
    ['customer_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/price'),
    $installer->getIdxName('productalert/price', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/price'),
    $installer->getIdxName('productalert/price', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/stock'),
    $installer->getIdxName('productalert/stock', ['customer_id']),
    ['customer_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/stock'),
    $installer->getIdxName('productalert/stock', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/stock'),
    $installer->getIdxName('productalert/stock', ['website_id']),
    ['website_id'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/price', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('productalert/price'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/price', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('productalert/price'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('productalert/price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/stock', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('productalert/stock'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/stock', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('productalert/stock'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/stock', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('productalert/stock'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->endSetup();
