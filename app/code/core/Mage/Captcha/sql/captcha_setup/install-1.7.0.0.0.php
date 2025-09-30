<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Captcha
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('captcha/log'))
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Type')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'unsigned'  => true,
        'primary'   => true,
    ], 'Value')
    ->addColumn('count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Count')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [], 'Update Time')
    ->setComment('Count Login Attempts');
$installer->getConnection()->createTable($table);

$installer->endSetup();
