<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
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
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['txn_id'], ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE])
    ->setComment('PayPal Payflow Link Payment Transaction');
$installer->getConnection()->createTable($table);
