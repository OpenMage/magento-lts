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
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Paypal_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database for install
 */
$installer->startSetup();

/**
 * Create table 'paypal/settlement_report'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/settlement_report'))
    ->addColumn('report_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Report Id')
    ->addColumn('report_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Report Date')
    ->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        ), 'Account Id')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 24, array(
        ), 'Filename')
    ->addColumn('last_modified', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Last Modified')
    ->addIndex($installer->getIdxName('paypal/settlement_report', array('report_date', 'account_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('report_date', 'account_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Paypal Settlement Report Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'paypal/settlement_report_row'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/settlement_report_row'))
    ->addColumn('row_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Row Id')
    ->addColumn('report_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Report Id')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_TEXT, 19, array(
        ), 'Transaction Id')
    ->addColumn('invoice_id', Varien_Db_Ddl_Table::TYPE_TEXT, 127, array(
        ), 'Invoice Id')
    ->addColumn('paypal_reference_id', Varien_Db_Ddl_Table::TYPE_TEXT, 19, array(
        ), 'Paypal Reference Id')
    ->addColumn('paypal_reference_id_type', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Paypal Reference Id Type')
    ->addColumn('transaction_event_code', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(
        ), 'Transaction Event Code')
    ->addColumn('transaction_initiation_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Transaction Initiation Date')
    ->addColumn('transaction_completion_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Transaction Completion Date')
    ->addColumn('transaction_debit_or_credit', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => false,
        'default'   => 'CR',
        ), 'Transaction Debit Or Credit')
    ->addColumn('gross_transaction_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,6', array(
        'nullable'  => false,
        'default'   => '0.000000',
        ), 'Gross Transaction Amount')
    ->addColumn('gross_transaction_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'default'   => '',
        ), 'Gross Transaction Currency')
    ->addColumn('fee_debit_or_credit', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        ), 'Fee Debit Or Credit')
    ->addColumn('fee_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,6', array(
        'nullable'  => false,
        'default'   => '0.000000',
        ), 'Fee Amount')
    ->addColumn('fee_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Fee Currency')
    ->addColumn('custom_field', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Custom Field')
    ->addColumn('consumer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 127, array(
        ), 'Consumer Id')
    ->addIndex($installer->getIdxName('paypal/settlement_report_row', array('report_id')),
        array('report_id'))
    ->addForeignKey($installer->getFkName('paypal/settlement_report_row', 'report_id', 'paypal/settlement_report', 'report_id'),
        'report_id', $installer->getTable('paypal/settlement_report'), 'report_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Paypal Settlement Report Row Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'paypal/cert'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/cert'))
    ->addColumn('cert_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Cert Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Website Id')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '64K', array(
        ), 'Content')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')
    ->addIndex($installer->getIdxName('paypal/cert', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('paypal/cert', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Paypal Certificate Table');
$installer->getConnection()->createTable($table);

/**
 * Add paypal attributes to the:
 *  - sales/flat_quote_payment_item table
 *  - sales/flat_order table
 */
$installer->addAttribute('quote_payment', 'paypal_payer_id', array());
$installer->addAttribute('quote_payment', 'paypal_payer_status', array());
$installer->addAttribute('quote_payment', 'paypal_correlation_id', array());
$installer->addAttribute('order', 'paypal_ipn_customer_notified', array('type' => 'int', 'visible' => false, 'default' => 0));

/**
 * Prepare database after install
 */
$installer->endSetup();

