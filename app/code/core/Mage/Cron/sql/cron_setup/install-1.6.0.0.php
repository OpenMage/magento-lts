<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cron
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'cron/schedule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cron/schedule'))
    ->addColumn('schedule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Schedule Id')
    ->addColumn('job_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Job Code')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 7, [
        'nullable'  => false,
        'default'   => 'pending',
    ], 'Status')
    ->addColumn('messages', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Messages')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('scheduled_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Scheduled At')
    ->addColumn('executed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Executed At')
    ->addColumn('finished_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Finished At')
    ->addIndex(
        $installer->getIdxName('cron/schedule', ['job_code']),
        ['job_code'],
    )
    ->addIndex(
        $installer->getIdxName('cron/schedule', ['scheduled_at', 'status']),
        ['scheduled_at', 'status'],
    )
    ->setComment('Cron Schedule');
$installer->getConnection()->createTable($table);

$installer->endSetup();
