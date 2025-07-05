<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_SEVERITY',
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_IS_READ',
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_IS_REMOVE',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('adminnotification/inbox') => [
        'columns' => [
            'notification_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Notification id',
            ],
            'severity' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Problem type',
            ],
            'date_added' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Create date',
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title',
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description',
            ],
            'url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Url',
            ],
            'is_read' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag if notification read',
            ],
            'is_remove' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag if notification might be removed',
            ],
        ],
        'comment' => 'Adminnotification Inbox',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', ['severity']),
    ['severity'],
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', ['is_read']),
    ['is_read'],
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', ['is_remove']),
    ['is_remove'],
);

$installer->endSetup();
