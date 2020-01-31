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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'log/customer'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/customer'))
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Log ID')
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        ), 'Visitor ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer ID')
    ->addColumn('login_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Login Time')
    ->addColumn('logout_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Logout Time')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store ID')
    ->addIndex($installer->getIdxName('log/customer', array('visitor_id')),
        array('visitor_id'))
    ->setComment('Log Customers Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/quote_table'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/quote_table'))
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Quote ID')
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        ), 'Visitor ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Creation Time')
    ->addColumn('deleted_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Deletion Time')
    ->setComment('Log Quotes Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/summary_table'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/summary_table'))
    ->addColumn('summary_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Summary ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store ID')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Type ID')
    ->addColumn('visitor_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Visitor Count')
    ->addColumn('customer_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Count')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Date')
    ->setComment('Log Summary Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/summary_type_table'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/summary_type_table'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Type ID')
    ->addColumn('type_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Type Code')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Period')
    ->addColumn('period_type', Varien_Db_Ddl_Table::TYPE_TEXT, 6, array(
        'nullable'  => false,
        'default'   => 'MINUTE',
        ), 'Period Type')
    ->setComment('Log Summary Types Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/url_table'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/url_table'))
    ->addColumn('url_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'URL ID')
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        ), 'Visitor ID')
    ->addColumn('visit_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Visit Time')
    ->addIndex($installer->getIdxName('log/url_table', array('visitor_id')),
        array('visitor_id'))
    ->setComment('Log URL Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/url_info_table'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/url_info_table'))
    ->addColumn('url_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'URL ID')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'URL')
    ->addColumn('referer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Referrer')
    ->setComment('Log URL Info Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/visitor'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/visitor'))
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Visitor ID')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Session ID')
    ->addColumn('first_visit_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'First Visit Time')
    ->addColumn('last_visit_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Last Visit Time')
    ->addColumn('last_url_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Last URL ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store ID')
    ->setComment('Log Visitors Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/visitor_info'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/visitor_info'))
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Visitor ID')
    ->addColumn('http_referer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'HTTP Referrer')
    ->addColumn('http_user_agent', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'HTTP User-Agent')
    ->addColumn('http_accept_charset', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'HTTP Accept-Charset')
    ->addColumn('http_accept_language', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'HTTP Accept-Language')
    ->addColumn('server_addr', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        ), 'Server Address')
    ->addColumn('remote_addr', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        ), 'Remote Address')
    ->setComment('Log Visitor Info Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'log/visitor_online'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('log/visitor_online'))
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Visitor ID')
    ->addColumn('visitor_type', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(
        'nullable'  => false,
        ), 'Visitor Type')
    ->addColumn('remote_addr', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'nullable'  => false,
        ), 'Remote Address')
    ->addColumn('first_visit_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'First Visit Time')
    ->addColumn('last_visit_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Last Visit Time')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer ID')
    ->addColumn('last_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Last URL')
    ->addIndex($installer->getIdxName('log/visitor_online', array('visitor_type')),
        array('visitor_type'))
    ->addIndex($installer->getIdxName('log/visitor_online', array('first_visit_at', 'last_visit_at')),
        array('first_visit_at', 'last_visit_at'))
    ->addIndex($installer->getIdxName('log/visitor_online', array('customer_id')),
        array('customer_id'))
    ->setComment('Log Visitor Online Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
