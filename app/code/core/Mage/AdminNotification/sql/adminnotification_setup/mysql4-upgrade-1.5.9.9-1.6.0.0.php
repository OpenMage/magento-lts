<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_SEVERITY'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_IS_READ'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_IS_REMOVE'
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
                'comment'   => 'Notification id'
            ],
            'severity' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Problem type'
            ],
            'date_added' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Create date'
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Url'
            ],
            'is_read' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag if notification read'
            ],
            'is_remove' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag if notification might be removed'
            ]
        ],
        'comment' => 'Adminnotification Inbox'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', ['severity']),
    ['severity']
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', ['is_read']),
    ['is_read']
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', ['is_remove']),
    ['is_remove']
);

$installer->endSetup();
