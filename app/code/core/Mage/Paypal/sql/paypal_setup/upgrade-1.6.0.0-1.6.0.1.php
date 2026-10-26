<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $this */
$installer = $this;

/**
 * Create table 'paypal/payment_transaction'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal/payment_transaction'))
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('txn_id', Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
    ], 'Txn Id')
    ->addColumn('additional_information', Varien_Db_Ddl_Table::TYPE_BLOB, '64K', [
    ], 'Additional Information')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName(
            'paypal/payment_transaction',
            ['txn_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['txn_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->setComment('PayPal Payflow Link Payment Transaction');
$installer->getConnection()->createTable($table);
