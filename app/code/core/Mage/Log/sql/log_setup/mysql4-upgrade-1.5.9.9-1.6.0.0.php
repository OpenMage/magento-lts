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
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('log/customer'),
    'IDX_VISITOR'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_visitor_online'),
    'IDX_VISITOR_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_visitor_online'),
    'IDX_VISIT_TIME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_visitor_online'),
    'IDX_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('log_url'),
    'IDX_VISITOR'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('log/customer') => array(
        'columns' => array(
            'log_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Log ID'
            ),
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'comment'   => 'Visitor ID'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer ID'
            ),
            'login_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Login Time'
            ),
            'logout_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Logout Time'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            )
        ),
        'comment' => 'Log Customers Table'
    ),
    $installer->getTable('log/visitor') => array(
        'columns' => array(
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Visitor ID'
            ),
            'session_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Session ID'
            ),
            'first_visit_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'First Visit Time'
            ),
            'last_visit_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Last Visit Time'
            ),
            'last_url_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Last URL ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            )
        ),
        'comment' => 'Log Visitors Table'
    ),
    $installer->getTable('log/visitor_info') => array(
        'columns' => array(
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Visitor ID'
            ),
            'http_referer' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP Referrer'
            ),
            'http_user_agent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP User-Agent'
            ),
            'http_accept_charset' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP Accept-Charset'
            ),
            'http_accept_language' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'HTTP Accept-Language'
            ),
            'server_addr' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'comment'   => 'Server Address'
            ),
            'remote_addr' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'comment'   => 'Remote Address'
            )
        ),
        'comment' => 'Log Visitor Info Table'
    ),
    $installer->getTable('log/url_table') => array(
        'columns' => array(
            'url_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'URL ID'
            ),
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'comment'   => 'Visitor ID'
            ),
            'visit_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Visit Time'
            )
        ),
        'comment' => 'Log URL Table'
    ),
    $installer->getTable('log/url_info_table') => array(
        'columns' => array(
            'url_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'URL ID'
            ),
            'url' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'URL'
            ),
            'referer' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Referrer'
            )
        ),
        'comment' => 'Log URL Info Table'
    ),
    $installer->getTable('log/summary_table') => array(
        'columns' => array(
            'summary_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Summary ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Type ID'
            ),
            'visitor_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visitor Count'
            ),
            'customer_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Count'
            ),
            'add_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Date'
            )
        ),
        'comment' => 'Log Summary Table'
    ),
    $installer->getTable('log/summary_type_table') => array(
        'columns' => array(
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Type ID'
            ),
            'type_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Type Code'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Period'
            ),
            'period_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 6,
                'nullable'  => false,
                'default'   => 'MINUTE',
                'comment'   => 'Period Type'
            )
        ),
        'comment' => 'Log Summary Types Table'
    ),
    $installer->getTable('log/quote_table') => array(
        'columns' => array(
            'quote_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Quote ID'
            ),
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'comment'   => 'Visitor ID'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Creation Time'
            ),
            'deleted_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Deletion Time'
            )
        ),
        'comment' => 'Log Quotes Table'
    ),
    $installer->getTable('log/visitor_online') => array(
        'columns' => array(
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Visitor ID'
            ),
            'visitor_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'comment'   => 'Visitor Type'
            ),
            'remote_addr' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'nullable'  => false,
                'comment'   => 'Remote Address'
            ),
            'first_visit_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'First Visit Time'
            ),
            'last_visit_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Last Visit Time'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer ID'
            ),
            'last_url' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Last URL'
            )
        ),
        'comment' => 'Log Visitor Online Table'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('log/customer'),
    $installer->getIdxName('log/customer', array('visitor_id')),
    array('visitor_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/url_table'),
    $installer->getIdxName('log/url_table', array('visitor_id')),
    array('visitor_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/visitor_online'),
    $installer->getIdxName('log/visitor_online', array('visitor_type')),
    array('visitor_type')
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/visitor_online'),
    $installer->getIdxName('log/visitor_online', array('first_visit_at', 'last_visit_at')),
    array('first_visit_at', 'last_visit_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('log/visitor_online'),
    $installer->getIdxName('log/visitor_online', array('customer_id')),
    array('customer_id')
);

$installer->endSetup();
