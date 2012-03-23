<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Index_Model_Resource_Setup */
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
$tables = array(
    $installer->getTable('index/event') => array(
        'columns' => array(
            'event_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Id'
            ),
            'type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Type'
            ),
            'entity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Entity'
            ),
            'entity_pk' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'comment'   => 'Entity Primary Key'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Creation Time'
            ),
            'old_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Old Data'
            ),
            'new_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'New Data'
            )
        ),
        'comment' => 'Index Event'
    ),
    $installer->getTable('index/process') => array(
        'columns' => array(
            'process_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Process Id'
            ),
            'indexer_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Indexer Code'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 15,
                'nullable'  => false,
                'default'   => 'pending',
                'comment'   => 'Status'
            ),
            'started_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Started At'
            ),
            'ended_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Ended At'
            ),
            'mode' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 9,
                'nullable'  => false,
                'default'   => 'real_time',
                'comment'   => 'Mode'
            )
        ),
        'comment' => 'Index Process'
    ),
    $installer->getTable('index/process_event') => array(
        'columns' => array(
            'process_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Process Id'
            ),
            'event_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Id'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'new',
                'comment'   => 'Status'
            )
        ),
        'comment' => 'Index Process Event'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('index/event'),
    $installer->getIdxName(
        'index/event',
        array('type', 'entity', 'entity_pk'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('type', 'entity', 'entity_pk'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('index/process'),
    $installer->getIdxName(
        'index/process',
        array('indexer_code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('indexer_code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('index/process_event'),
    $installer->getIdxName('index/process_event', array('event_id')),
    array('event_id')
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
