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

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('paypal/cert'),
    'FK_PAYPAL_CERT_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('paypal/settlement_report_row'),
    'FK_PAYPAL_SETTLEMENT_ROW_REPORT'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('paypal/cert'),
    'IDX_PAYPAL_CERT_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('paypal/settlement_report'),
    'UNQ_REPORT_DATE_ACCOUNT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('paypal/settlement_report_row'),
    'IDX_REPORT_ID'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('paypal/settlement_report') => [
        'columns' => [
            'report_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Report Id'
            ],
            'report_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Report Date'
            ],
            'account_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Account Id'
            ],
            'filename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 24,
                'comment'   => 'Filename'
            ],
            'last_modified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Last Modified'
            ]
        ],
        'comment' => 'Paypal Settlement Report Table'
    ],
    $installer->getTable('paypal/settlement_report_row') => [
        'columns' => [
            'row_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Row Id'
            ],
            'report_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Report Id'
            ],
            'transaction_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 19,
                'comment'   => 'Transaction Id'
            ],
            'invoice_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 127,
                'comment'   => 'Invoice Id'
            ],
            'paypal_reference_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 19,
                'comment'   => 'Paypal Reference Id'
            ],
            'paypal_reference_id_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Paypal Reference Id Type'
            ],
            'transaction_event_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 5,
                'comment'   => 'Transaction Event Code'
            ],
            'transaction_initiation_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Transaction Initiation Date'
            ],
            'transaction_completion_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Transaction Completion Date'
            ],
            'transaction_debit_or_credit' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'default'   => 'CR',
                'comment'   => 'Transaction Debit Or Credit'
            ],
            'gross_transaction_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 6,
                'precision' => 20,
                'nullable'  => false,
                'default'   => '0.000000',
                'comment'   => 'Gross Transaction Amount'
            ],
            'gross_transaction_currency' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'default'   => '',
                'comment'   => 'Gross Transaction Currency'
            ],
            'fee_debit_or_credit' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'comment'   => 'Fee Debit Or Credit'
            ],
            'fee_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 6,
                'precision' => 20,
                'nullable'  => false,
                'default'   => '0.000000',
                'comment'   => 'Fee Amount'
            ],
            'fee_currency' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Fee Currency'
            ],
            'custom_field' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Custom Field'
            ],
            'consumer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 127,
                'comment'   => 'Consumer Id'
            ]
        ],
        'comment' => 'Paypal Settlement Report Row Table'
    ],
    $installer->getTable('paypal/cert') => [
        'columns' => [
            'cert_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Cert Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ],
            'content' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Content'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ]
        ],
        'comment' => 'Paypal Certificate Table'
    ],
    $installer->getTable('sales/quote_payment') => [
        'columns' => [
            'paypal_payer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Paypal Payer Id'
            ],
            'paypal_payer_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Paypal Payer Status'
            ],
            'paypal_correlation_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Paypal Correlation Id'
            ]
        ]
    ],
    $installer->getTable('sales/order') => [
        'columns' => [
            'paypal_ipn_customer_notified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Paypal Ipn Customer Notified'
            ]
        ]
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('paypal/cert'),
    $installer->getIdxName('paypal/cert', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('paypal/settlement_report'),
    $installer->getIdxName(
        'paypal/settlement_report',
        ['report_date', 'account_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['report_date', 'account_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('paypal/settlement_report_row'),
    $installer->getIdxName('paypal/settlement_report_row', ['report_id']),
    ['report_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('paypal/settlement_report_row', 'report_id', 'paypal/settlement_report', 'report_id'),
    $installer->getTable('paypal/settlement_report_row'),
    'report_id',
    $installer->getTable('paypal/settlement_report'),
    'report_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('paypal/cert', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('paypal/cert'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->endSetup();
