<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('dataflow/batch'),
    'FK_DATAFLOW_BATCH_PROFILE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('dataflow/batch'),
    'FK_DATAFLOW_BATCH_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('dataflow/batch_export'),
    'FK_DATAFLOW_BATCH_EXPORT_BATCH'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('dataflow/batch_import'),
    'FK_DATAFLOW_BATCH_IMPORT_BATCH'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('dataflow_import_data'),
    'FK_DATAFLOW_IMPORT_DATA'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('dataflow/profile_history'),
    'FK_DATAFLOW_PROFILE_HISTORY'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/batch'),
    'FK_DATAFLOW_BATCH_PROFILE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/batch'),
    'FK_DATAFLOW_BATCH_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/batch'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/batch_export'),
    'FK_DATAFLOW_BATCH_EXPORT_BATCH'
);
$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/batch_import'),
    'FK_DATAFLOW_BATCH_IMPORT_BATCH'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/import'),
    'FK_DATAFLOW_IMPORT_DATA'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('dataflow/profile_history'),
    'FK_DATAFLOW_PROFILE_HISTORY'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('dataflow/session') => [
        'columns' => [
            'session_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Session Id'
            ],
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'User Id'
            ],
            'created_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created Date'
            ],
            'file' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'File'
            ],
            'type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Type'
            ],
            'direction' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Direction'
            ],
            'comment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Comment'
            ]
        ],
        'comment' => 'Dataflow Session'
    ],
    $installer->getTable('dataflow/import') => [
        'columns' => [
            'import_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Import Id'
            ],
            'session_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Session Id'
            ],
            'serial_number' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Serial Number'
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            ]
        ],
        'comment' => 'Dataflow Import Data'
    ],
    $installer->getTable('dataflow/profile') => [
        'columns' => [
            'profile_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Profile Id'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ],
            'actions_xml' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Actions Xml'
            ],
            'gui_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Gui Data'
            ],
            'direction' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 6,
                'comment'   => 'Direction'
            ],
            'entity_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Entity Type'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ],
            'data_transfer' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 11,
                'comment'   => 'Data Transfer'
            ]
        ],
        'comment' => 'Dataflow Profile'
    ],
    $installer->getTable('dataflow/profile_history') => [
        'columns' => [
            'history_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'History Id'
            ],
            'profile_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Profile Id'
            ],
            'action_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Action Code'
            ],
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User Id'
            ],
            'performed_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Performed At'
            ]
        ],
        'comment' => 'Dataflow Profile History'
    ],
    $installer->getTable('dataflow/batch') => [
        'columns' => [
            'batch_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Batch Id'
            ],
            'profile_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Profile ID'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ],
            'adapter' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'comment'   => 'Adapter'
            ],
            'params' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Parameters'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ]
        ],
        'comment' => 'Dataflow Batch'
    ],
    $installer->getTable('dataflow/batch_export') => [
        'columns' => [
            'batch_export_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Batch Export Id'
            ],
            'batch_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Batch Id'
            ],
            'batch_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2G',
                'comment'   => 'Batch Data'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            ]
        ],
        'comment' => 'Dataflow Batch Export'
    ],
    $installer->getTable('dataflow/batch_import') => [
        'columns' => [
            'batch_import_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Batch Import Id'
            ],
            'batch_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Batch Id'
            ],
            'batch_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2G',
                'comment'   => 'Batch Data'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            ]
        ],
        'comment' => 'Dataflow Batch Import'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/batch'),
    $installer->getIdxName('dataflow/batch', ['profile_id']),
    ['profile_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/batch'),
    $installer->getIdxName('dataflow/batch', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/batch'),
    $installer->getIdxName('dataflow/batch', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/batch_export'),
    $installer->getIdxName('dataflow/batch_export', ['batch_id']),
    ['batch_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/batch_import'),
    $installer->getIdxName('dataflow/batch_import', ['batch_id']),
    ['batch_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/import'),
    $installer->getIdxName('dataflow/import', ['session_id']),
    ['session_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('dataflow/profile_history'),
    $installer->getIdxName('dataflow/profile_history', ['profile_id']),
    ['profile_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('dataflow/batch', 'profile_id', 'dataflow/profile', 'profile_id'),
    $installer->getTable('dataflow/batch'),
    'profile_id',
    $installer->getTable('dataflow/profile'),
    'profile_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('dataflow/batch', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('dataflow/batch'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('dataflow/batch_import', 'batch_id', 'dataflow/batch', 'batch_id'),
    $installer->getTable('dataflow/batch_import'),
    'batch_id',
    $installer->getTable('dataflow/batch'),
    'batch_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('dataflow/batch_export', 'batch_id', 'dataflow/batch', 'batch_id'),
    $installer->getTable('dataflow/batch_export'),
    'batch_id',
    $installer->getTable('dataflow/batch'),
    'batch_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('dataflow/import', 'session_id', 'dataflow/session', 'session_id'),
    $installer->getTable('dataflow/import'),
    'session_id',
    $installer->getTable('dataflow/session'),
    'session_id',
    Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('dataflow/profile_history', 'profile_id', 'dataflow/profile', 'profile_id'),
    $installer->getTable('dataflow/profile_history'),
    'profile_id',
    $installer->getTable('dataflow/profile'),
    'profile_id'
);

$installer->endSetup();
