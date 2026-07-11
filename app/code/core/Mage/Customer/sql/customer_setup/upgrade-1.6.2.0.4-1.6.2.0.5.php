<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/flowpassword'))
    ->addColumn('flowpassword_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ], 'Flow password Id')
    ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, [
        'nullable' => false,
    ], 'User IP')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Requested email for change')
    ->addColumn('requested_date', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
        'default'  => '0000-00-00 00:00:00',
    ], 'Requested date for change')
    ->addIndex(
        $installer->getIdxName('customer/flowpassword', ['email']),
        ['email'],
    )
    ->addIndex(
        $installer->getIdxName('customer/flowpassword', ['ip']),
        ['ip'],
    )
    ->addIndex(
        $installer->getIdxName('customer/flowpassword', ['requested_date']),
        ['requested_date'],
    )
    ->setComment('Customer flow password');
$installer->getConnection()->createTable($table);

$installer->endSetup();
