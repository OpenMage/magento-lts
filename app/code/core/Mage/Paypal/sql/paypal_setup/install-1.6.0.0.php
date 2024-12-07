<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'paypal/settlement_report'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/settlement_report'))
    ->addColumn('report_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Report Id')
    ->addColumn('report_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Report Date')
    ->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Account Id')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 24, [
    ], 'Filename')
    ->addColumn('last_modified', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Last Modified')
    ->addIndex(
        $installer->getIdxName('paypal/settlement_report', ['report_date', 'account_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['report_date', 'account_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Paypal Settlement Report Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'paypal/settlement_report_row'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/settlement_report_row'))
    ->addColumn('row_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Row Id')
    ->addColumn('report_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Report Id')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_TEXT, 19, [
    ], 'Transaction Id')
    ->addColumn('invoice_id', Varien_Db_Ddl_Table::TYPE_TEXT, 127, [
    ], 'Invoice Id')
    ->addColumn('paypal_reference_id', Varien_Db_Ddl_Table::TYPE_TEXT, 19, [
    ], 'Paypal Reference Id')
    ->addColumn('paypal_reference_id_type', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Paypal Reference Id Type')
    ->addColumn('transaction_event_code', Varien_Db_Ddl_Table::TYPE_TEXT, 5, [
    ], 'Transaction Event Code')
    ->addColumn('transaction_initiation_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Transaction Initiation Date')
    ->addColumn('transaction_completion_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Transaction Completion Date')
    ->addColumn('transaction_debit_or_credit', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
        'nullable'  => false,
        'default'   => 'CR',
    ], 'Transaction Debit Or Credit')
    ->addColumn('gross_transaction_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,6', [
        'nullable'  => false,
        'default'   => '0.000000',
    ], 'Gross Transaction Amount')
    ->addColumn('gross_transaction_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
        'default'   => '',
    ], 'Gross Transaction Currency')
    ->addColumn('fee_debit_or_credit', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
    ], 'Fee Debit Or Credit')
    ->addColumn('fee_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,6', [
        'nullable'  => false,
        'default'   => '0.000000',
    ], 'Fee Amount')
    ->addColumn('fee_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Fee Currency')
    ->addColumn('custom_field', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Custom Field')
    ->addColumn('consumer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 127, [
    ], 'Consumer Id')
    ->addIndex(
        $installer->getIdxName('paypal/settlement_report_row', ['report_id']),
        ['report_id']
    )
    ->addForeignKey(
        $installer->getFkName('paypal/settlement_report_row', 'report_id', 'paypal/settlement_report', 'report_id'),
        'report_id',
        $installer->getTable('paypal/settlement_report'),
        'report_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Paypal Settlement Report Row Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'paypal/cert'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/cert'))
    ->addColumn('cert_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Cert Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
    ], 'Website Id')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '64K', [
    ], 'Content')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addIndex(
        $installer->getIdxName('paypal/cert', ['website_id']),
        ['website_id']
    )
    ->addForeignKey(
        $installer->getFkName('paypal/cert', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Paypal Certificate Table');
$installer->getConnection()->createTable($table);

/**
 * Add paypal attributes to the:
 *  - sales/flat_quote_payment_item table
 *  - sales/flat_order table
 */
$installer->addAttribute('quote_payment', 'paypal_payer_id', []);
$installer->addAttribute('quote_payment', 'paypal_payer_status', []);
$installer->addAttribute('quote_payment', 'paypal_correlation_id', []);
$installer->addAttribute('order', 'paypal_ipn_customer_notified', ['type' => 'int', 'visible' => false, 'default' => 0]);

/**
 * Prepare database after install
 */
$installer->endSetup();
