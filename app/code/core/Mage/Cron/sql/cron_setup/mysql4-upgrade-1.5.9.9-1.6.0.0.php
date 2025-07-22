<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cron
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('cron/schedule'),
    'TASK_NAME',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cron/schedule'),
    'SCHEDULED_AT',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('cron/schedule') => [
        'columns' => [
            'schedule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Schedule Id',
            ],
            'job_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Job Code',
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'pending',
                'comment'   => 'Status',
            ],
            'messages' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Messages',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At',
            ],
            'scheduled_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Scheduled At',
            ],
            'executed_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Executed At',
            ],
            'finished_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Finished At',
            ],
        ],
        'comment' => 'Cron Schedule',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('cron/schedule'),
    $installer->getIdxName('cron/schedule', ['job_code']),
    ['job_code'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('cron/schedule'),
    $installer->getIdxName('cron/schedule', ['scheduled_at', 'status']),
    ['scheduled_at', 'status'],
);

$installer->endSetup();
