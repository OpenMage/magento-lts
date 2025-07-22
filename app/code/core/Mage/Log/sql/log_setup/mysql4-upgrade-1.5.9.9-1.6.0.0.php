<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('log/customer'),
    'IDX_VISITOR',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_visitor_online'),
    'IDX_VISITOR_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_visitor_online'),
    'IDX_VISIT_TIME',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_visitor_online'),
    'IDX_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_url'),
    'IDX_VISITOR',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('log/customer') => [
        'columns' => [
            'log_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Log ID',
            ],
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'comment'   => 'Visitor ID',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer ID',
            ],
            'login_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Login Time',
            ],
            'logout_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Logout Time',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
        ],
        'comment' => 'Log Customers Table',
    ],
    $installer->getTable('log/visitor') => [
        'columns' => [
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Visitor ID',
            ],
            'session_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Session ID',
            ],
            'first_visit_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'First Visit Time',
            ],
            'last_visit_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Last Visit Time',
            ],
            'last_url_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Last URL ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
        ],
        'comment' => 'Log Visitors Table',
    ],
    $installer->getTable('log/visitor_info') => [
        'columns' => [
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Visitor ID',
            ],
            'http_referer' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP Referrer',
            ],
            'http_user_agent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP User-Agent',
            ],
            'http_accept_charset' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP Accept-Charset',
            ],
            'http_accept_language' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP Accept-Language',
            ],
            'server_addr' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'comment'   => 'Server Address',
            ],
            'remote_addr' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'comment'   => 'Remote Address',
            ],
        ],
        'comment' => 'Log Visitor Info Table',
    ],
    $installer->getTable('log/url_table') => [
        'columns' => [
            'url_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'URL ID',
            ],
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'comment'   => 'Visitor ID',
            ],
            'visit_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Visit Time',
            ],
        ],
        'comment' => 'Log URL Table',
    ],
    $installer->getTable('log/url_info_table') => [
        'columns' => [
            'url_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'URL ID',
            ],
            'url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'URL',
            ],
            'referer' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Referrer',
            ],
        ],
        'comment' => 'Log URL Info Table',
    ],
    $installer->getTable('log/summary_table') => [
        'columns' => [
            'summary_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Summary ID',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID',
            ],
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Type ID',
            ],
            'visitor_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visitor Count',
            ],
            'customer_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Count',
            ],
            'add_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Date',
            ],
        ],
        'comment' => 'Log Summary Table',
    ],
    $installer->getTable('log/summary_type_table') => [
        'columns' => [
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Type ID',
            ],
            'type_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Type Code',
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Period',
            ],
            'period_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 6,
                'nullable'  => false,
                'default'   => 'MINUTE',
                'comment'   => 'Period Type',
            ],
        ],
        'comment' => 'Log Summary Types Table',
    ],
    $installer->getTable('log/quote_table') => [
        'columns' => [
            'quote_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Quote ID',
            ],
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'comment'   => 'Visitor ID',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Creation Time',
            ],
            'deleted_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Deletion Time',
            ],
        ],
        'comment' => 'Log Quotes Table',
    ],
    $installer->getTable('log/visitor_online') => [
        'columns' => [
            'visitor_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Visitor ID',
            ],
            'visitor_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'comment'   => 'Visitor Type',
            ],
            'remote_addr' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'nullable'  => false,
                'comment'   => 'Remote Address',
            ],
            'first_visit_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'First Visit Time',
            ],
            'last_visit_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Last Visit Time',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer ID',
            ],
            'last_url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Last URL',
            ],
        ],
        'comment' => 'Log Visitor Online Table',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('log/customer'),
    $installer->getIdxName('log/customer', ['visitor_id']),
    ['visitor_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/url_table'),
    $installer->getIdxName('log/url_table', ['visitor_id']),
    ['visitor_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/visitor_online'),
    $installer->getIdxName('log/visitor_online', ['visitor_type']),
    ['visitor_type'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/visitor_online'),
    $installer->getIdxName('log/visitor_online', ['first_visit_at', 'last_visit_at']),
    ['first_visit_at', 'last_visit_at'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/visitor_online'),
    $installer->getIdxName('log/visitor_online', ['customer_id']),
    ['customer_id'],
);

$installer->endSetup();
