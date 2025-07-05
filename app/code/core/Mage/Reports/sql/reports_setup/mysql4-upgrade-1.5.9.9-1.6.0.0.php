<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/compared_product_index'),
    'FK_REPORT_COMPARED_PRODUCT_INDEX_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/compared_product_index'),
    'FK_REPORT_COMPARED_PRODUCT_INDEX_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/compared_product_index'),
    'FK_REPORT_COMPARED_PRODUCT_INDEX_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/event'),
    'FK_REPORT_EVENT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/event'),
    'FK_REPORT_EVENT_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/viewed_product_index'),
    'FK_REPORT_VIEWED_PRODUCT_INDEX_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/viewed_product_index'),
    'FK_REPORT_VIEWED_PRODUCT_INDEX_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/viewed_product_index'),
    'FK_REPORT_VIEWED_PRODUCT_INDEX_STORE',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'UNQ_BY_VISITOR',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'UNQ_BY_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'IDX_SORT_ADDED_AT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'PRODUCT_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_EVENT_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_SUBJECT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_OBJECT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_SUBTYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'FK_REPORT_EVENT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'UNQ_BY_VISITOR',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'UNQ_BY_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'IDX_SORT_ADDED_AT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'PRODUCT_ID',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('reports/event') => [
        'columns' => [
            'event_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Id',
            ],
            'logged_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Logged At',
            ],
            'event_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Event Type Id',
            ],
            'object_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Object Id',
            ],
            'subject_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Subject Id',
            ],
            'subtype' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Subtype',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id',
            ],
        ],
        'comment' => 'Reports Event Table',
    ],
    $installer->getTable('reports/event_type') => [
        'columns' => [
            'event_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Type Id',
            ],
            'event_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Event Name',
            ],
            'customer_login' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Login',
            ],
        ],
        'comment' => 'Reports Event Type Table',
    ],
    $installer->getTable('reports/compared_product_index') => [
        'columns' => [
            'index_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Index Id',
            ],
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Visitor Id',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id',
            ],
            'added_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Added At',
            ],
        ],
        'comment' => 'Reports Compared Product Index Table',
    ],
    $installer->getTable('reports/viewed_product_index') => [
        'columns' => [
            'index_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Index Id',
            ],
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Visitor Id',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id',
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id',
            ],
            'added_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Added At',
            ],
        ],
        'comment' => 'Reports Viewed Product Index Table',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName(
        'reports/compared_product_index',
        ['visitor_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['visitor_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName(
        'reports/compared_product_index',
        ['customer_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['customer_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName('reports/compared_product_index', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName('reports/compared_product_index', ['added_at']),
    ['added_at'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName('reports/compared_product_index', ['product_id']),
    ['product_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', ['event_type_id']),
    ['event_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', ['subject_id']),
    ['subject_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', ['object_id']),
    ['object_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', ['subtype']),
    ['subtype'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName(
        'reports/viewed_product_index',
        ['visitor_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['visitor_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName(
        'reports/viewed_product_index',
        ['customer_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['customer_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName('reports/viewed_product_index', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName('reports/viewed_product_index', ['added_at']),
    ['added_at'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName('reports/viewed_product_index', ['product_id']),
    ['product_id'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/compared_product_index', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('reports/compared_product_index'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/compared_product_index', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('reports/compared_product_index'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/compared_product_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('reports/compared_product_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/event', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('reports/event'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/event', 'event_type_id', 'reports/event_type', 'event_type_id'),
    $installer->getTable('reports/event'),
    'event_type_id',
    $installer->getTable('reports/event_type'),
    'event_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/viewed_product_index', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('reports/viewed_product_index'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/viewed_product_index', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('reports/viewed_product_index'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/viewed_product_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('reports/viewed_product_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->endSetup();
