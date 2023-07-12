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
 * Create table 'dataflow/session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/session'))
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Session Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
    ], 'User Id')
    ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created Date')
    ->addColumn('file', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'File')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Type')
    ->addColumn('direction', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Direction')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Comment')
    ->setComment('Dataflow Session');
$installer->getConnection()->createTable($table);

/**
 * Create table 'dataflow/import'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/import'))
    ->addColumn('import_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Import Id')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Session Id')
    ->addColumn('serial_number', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Serial Number')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Value')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Status')
    ->addIndex(
        $installer->getIdxName('dataflow/import', ['session_id']),
        ['session_id']
    )
    ->addForeignKey(
        $installer->getFkName('dataflow/import', 'session_id', 'dataflow/session', 'session_id'),
        'session_id',
        $installer->getTable('dataflow/session'),
        'session_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Dataflow Import Data');
$installer->getConnection()->createTable($table);

/**
 * Create table 'dataflow/profile'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/profile'))
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Profile Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addColumn('actions_xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Actions Xml')
    ->addColumn('gui_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Gui Data')
    ->addColumn('direction', Varien_Db_Ddl_Table::TYPE_TEXT, 6, [
    ], 'Direction')
    ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Entity Type')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('data_transfer', Varien_Db_Ddl_Table::TYPE_TEXT, 11, [
    ], 'Data Transfer')
    ->setComment('Dataflow Profile');
$installer->getConnection()->createTable($table);

/**
 * Create table 'dataflow/profile_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/profile_history'))
    ->addColumn('history_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'History Id')
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Profile Id')
    ->addColumn('action_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Action Code')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'User Id')
    ->addColumn('performed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Performed At')
    ->addIndex(
        $installer->getIdxName('dataflow/profile_history', ['profile_id']),
        ['profile_id']
    )
    ->addForeignKey(
        $installer->getFkName('dataflow/profile_history', 'profile_id', 'dataflow/profile', 'profile_id'),
        'profile_id',
        $installer->getTable('dataflow/profile'),
        'profile_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Dataflow Profile History');
$installer->getConnection()->createTable($table);

/**
 * Create table 'dataflow/batch'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/batch'))
    ->addColumn('batch_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Batch Id')
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Profile ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('adapter', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
    ], 'Adapter')
    ->addColumn('params', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Parameters')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('dataflow/batch', ['profile_id']),
        ['profile_id']
    )
    ->addIndex(
        $installer->getIdxName('dataflow/batch', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('dataflow/batch', ['created_at']),
        ['created_at']
    )
    ->addForeignKey(
        $installer->getFkName('dataflow/batch', 'profile_id', 'dataflow/profile', 'profile_id'),
        'profile_id',
        $installer->getTable('dataflow/profile'),
        'profile_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('dataflow/batch', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Dataflow Batch');
$installer->getConnection()->createTable($table);

/**
 * Create table 'dataflow/batch_export'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/batch_export'))
    ->addColumn('batch_export_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Batch Export Id')
    ->addColumn('batch_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Batch Id')
    ->addColumn('batch_data', Varien_Db_Ddl_Table::TYPE_TEXT, '2G', [
    ], 'Batch Data')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Status')
    ->addIndex(
        $installer->getIdxName('dataflow/batch_export', ['batch_id']),
        ['batch_id']
    )
    ->addForeignKey(
        $installer->getFkName('dataflow/batch_export', 'batch_id', 'dataflow/batch', 'batch_id'),
        'batch_id',
        $installer->getTable('dataflow/batch'),
        'batch_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Dataflow Batch Export');
$installer->getConnection()->createTable($table);

/**
 * Create table 'dataflow/batch_import'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('dataflow/batch_import'))
    ->addColumn('batch_import_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Batch Import Id')
    ->addColumn('batch_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Batch Id')
    ->addColumn('batch_data', Varien_Db_Ddl_Table::TYPE_TEXT, '2G', [
    ], 'Batch Data')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Status')
    ->addIndex(
        $installer->getIdxName('dataflow/batch_import', ['batch_id']),
        ['batch_id']
    )
    ->addForeignKey(
        $installer->getFkName('dataflow/batch_import', 'batch_id', 'dataflow/batch', 'batch_id'),
        'batch_id',
        $installer->getTable('dataflow/batch'),
        'batch_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Dataflow Batch Import');
$installer->getConnection()->createTable($table);

$installer->endSetup();
