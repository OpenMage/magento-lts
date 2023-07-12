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
 * Create table 'index/event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('index/event'))
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Event Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => false,
    ], 'Type')
    ->addColumn('entity', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => false,
    ], 'Entity')
    ->addColumn('entity_pk', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
    ], 'Entity Primary Key')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Creation Time')
    ->addColumn('old_data', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
    ], 'Old Data')
    ->addColumn('new_data', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
    ], 'New Data')
    ->addIndex(
        $installer->getIdxName('index/event', ['type', 'entity', 'entity_pk'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['type', 'entity', 'entity_pk'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Index Event');
$installer->getConnection()->createTable($table);

/**
 * Create table 'index/process'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('index/process'))
    ->addColumn('process_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Process Id')
    ->addColumn('indexer_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
    ], 'Indexer Code')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 15, [
        'nullable'  => false,
        'default'   => 'pending',
    ], 'Status')
    ->addColumn('started_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Started At')
    ->addColumn('ended_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Ended At')
    ->addColumn('mode', Varien_Db_Ddl_Table::TYPE_TEXT, 9, [
        'nullable'  => false,
        'default'   => 'real_time',
    ], 'Mode')
    ->addIndex(
        $installer->getIdxName('index/process', ['indexer_code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['indexer_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Index Process');
$installer->getConnection()->createTable($table);

/**
 * Create table 'index/process_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('index/process_event'))
    ->addColumn('process_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Process Id')
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Event Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 7, [
        'nullable'  => false,
        'default'   => 'new',
    ], 'Status')
    ->addIndex(
        $installer->getIdxName('index/process_event', ['event_id']),
        ['event_id']
    )
    ->addForeignKey(
        $installer->getFkName('index/process_event', 'event_id', 'index/event', 'event_id'),
        'event_id',
        $installer->getTable('index/event'),
        'event_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('index/process_event', 'process_id', 'index/process', 'process_id'),
        'process_id',
        $installer->getTable('index/process'),
        'process_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Index Process Event');
$installer->getConnection()->createTable($table);

$installer->endSetup();
