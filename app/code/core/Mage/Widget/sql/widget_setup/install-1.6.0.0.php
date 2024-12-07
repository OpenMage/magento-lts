<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'widget/widget'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('widget/widget'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('widget/widget'))
        ->addColumn('widget_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ], 'Widget Id')
        ->addColumn('widget_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        ], 'Widget code for template directive')
        ->addColumn('widget_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        ], 'Widget Type')
        ->addColumn('parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
            'nullable'  => true,
        ], 'Parameters')
        ->addIndex($installer->getIdxName('widget/widget', 'widget_code'), 'widget_code')
        ->setComment('Preconfigured Widgets');
    $installer->getConnection()->createTable($table);
} else {
    $installer->getConnection()->dropIndex(
        $installer->getTable('widget/widget'),
        'IDX_CODE'
    );

    $tables = [
        $installer->getTable('widget/widget') => [
            'columns' => [
                'widget_id' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'comment'   => 'Widget Id'
                ],
                'parameters' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => '64K',
                    'comment'   => 'Parameters'
                ]
            ],
            'comment' => 'Preconfigured Widgets'
        ]
    ];

    $installer->getConnection()->modifyTables($tables);

    $installer->getConnection()->changeColumn(
        $installer->getTable('widget/widget'),
        'code',
        'widget_code',
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Widget code for template directive'
        ]
    );

    $installer->getConnection()->changeColumn(
        $installer->getTable('widget/widget'),
        'type',
        'widget_type',
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Widget Type'
        ]
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('widget/widget'),
        $installer->getIdxName('widget/widget', ['widget_code']),
        ['widget_code']
    );
}

/**
 * Create table 'widget/widget_instance'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget_instance'))
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Instance Id')
    ->addColumn('instance_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Instance Type')
    ->addColumn('package_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Package Theme')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Widget Title')
    ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ids')
    ->addColumn('widget_parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Widget parameters')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort order')
    ->setComment('Instances of Widget for Package Theme');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget/widget_instance_page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget_instance_page'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Page Id')
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Instance Id')
    ->addColumn('page_group', Varien_Db_Ddl_Table::TYPE_TEXT, 25, [
    ], 'Block Group Type')
    ->addColumn('layout_handle', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Layout Handle')
    ->addColumn('block_reference', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Block Reference')
    ->addColumn('page_for', Varien_Db_Ddl_Table::TYPE_TEXT, 25, [
    ], 'For instance entities')
    ->addColumn('entities', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Catalog entities (comma separated)')
    ->addColumn('page_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Path to widget template')
    ->addIndex($installer->getIdxName('widget/widget_instance_page', 'instance_id'), 'instance_id')
    ->addForeignKey(
        $installer->getFkName('widget/widget_instance_page', 'instance_id', 'widget/widget_instance', 'instance_id'),
        'instance_id',
        $installer->getTable('widget/widget_instance'),
        'instance_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Instance of Widget on Page');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget/widget_instance_page_layout'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget_instance_page_layout'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Page Id')
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Layout Update Id')
    ->addIndex($installer->getIdxName('widget/widget_instance_page_layout', 'page_id'), 'page_id')
    ->addIndex($installer->getIdxName('widget/widget_instance_page_layout', 'layout_update_id'), 'layout_update_id')
    ->addIndex(
        $installer->getIdxName('widget/widget_instance_page_layout', ['layout_update_id', 'page_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['layout_update_id', 'page_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName('widget/widget_instance_page_layout', 'page_id', 'widget/widget_instance_page', 'page_id'),
        'page_id',
        $installer->getTable('widget/widget_instance_page'),
        'page_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('widget/widget_instance_page_layout', 'layout_update_id', 'core/layout_update', 'layout_update_id'),
        'layout_update_id',
        $installer->getTable('core/layout_update'),
        'layout_update_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Layout updates');
$installer->getConnection()->createTable($table);

$installer->endSetup();
