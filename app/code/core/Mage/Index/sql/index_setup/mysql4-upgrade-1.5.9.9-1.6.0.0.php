<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Index_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('index_process_event'),
    'FK_INDEX_EVNT_PROCESS'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('index/process_event'),
    'FK_INDEX_PROCESS_EVENT'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('index/event'),
    'IDX_UNIQUE_EVENT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('index/process'),
    'IDX_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('index/process_event'),
    'FK_INDEX_EVNT_PROCESS'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('index/event') => [
        'columns' => [
            'event_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Id'
            ],
            'type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Type'
            ],
            'entity' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Entity'
            ],
            'entity_pk' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'comment'   => 'Entity Primary Key'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Creation Time'
            ],
            'old_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Old Data'
            ],
            'new_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'New Data'
            ]
        ],
        'comment' => 'Index Event'
    ],
    $installer->getTable('index/process') => [
        'columns' => [
            'process_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Process Id'
            ],
            'indexer_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Indexer Code'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 15,
                'nullable'  => false,
                'default'   => 'pending',
                'comment'   => 'Status'
            ],
            'started_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Started At'
            ],
            'ended_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Ended At'
            ],
            'mode' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 9,
                'nullable'  => false,
                'default'   => 'real_time',
                'comment'   => 'Mode'
            ]
        ],
        'comment' => 'Index Process'
    ],
    $installer->getTable('index/process_event') => [
        'columns' => [
            'process_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Process Id'
            ],
            'event_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Id'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'new',
                'comment'   => 'Status'
            ]
        ],
        'comment' => 'Index Process Event'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('index/event'),
    $installer->getIdxName(
        'index/event',
        ['type', 'entity', 'entity_pk'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['type', 'entity', 'entity_pk'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('index/process'),
    $installer->getIdxName(
        'index/process',
        ['indexer_code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['indexer_code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('index/process_event'),
    $installer->getIdxName('index/process_event', ['event_id']),
    ['event_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('index/process_event', 'event_id', 'index/event', 'event_id'),
    $installer->getTable('index/process_event'),
    'event_id',
    $installer->getTable('index/event'),
    'event_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('index/process_event', 'process_id', 'index/process', 'process_id'),
    $installer->getTable('index/process_event'),
    'process_id',
    $installer->getTable('index/process'),
    'process_id'
);

$installer->endSetup();
