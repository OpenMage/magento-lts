<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('cms/block_store'),
    'FK_CMS_BLOCK_STORE_BLOCK'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cms/block_store'),
    'FK_CMS_BLOCK_STORE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cms/page_store'),
    'FK_CMS_PAGE_STORE_PAGE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('cms/page_store'),
    'FK_CMS_PAGE_STORE_STORE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('cms/block_store'),
    'FK_CMS_BLOCK_STORE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cms/page'),
    'IDENTIFIER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cms/page_store'),
    'FK_CMS_PAGE_STORE_STORE'
);

/*
 * Change columns
 */
$tables = [
    $installer->getTable('cms/page') => [
        'columns' => [
            'page_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Page ID'
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Page Title'
            ],
            'root_template' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Page Template'
            ],
            'meta_keywords' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Page Meta Keywords'
            ],
            'meta_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Page Meta Description'
            ],
            'identifier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'nullable'  => false,
                'comment'   => 'Page String Identifier'
            ],
            'content_heading' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Page Content Heading'
            ],
            'content' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Page Content'
            ],
            'creation_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Page Creation Time'
            ],
            'update_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Page Modification Time'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Page Active'
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Page Sort Order'
            ],
            'layout_update_xml' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Page Layout Update Content'
            ],
            'custom_theme' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'comment'   => 'Page Custom Theme'
            ],
            'custom_root_template' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Page Custom Template'
            ],
            'custom_layout_update_xml' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Page Custom Layout Update Content'
            ],
            'custom_theme_from' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Page Custom Theme Active From Date'
            ],
            'custom_theme_to' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Page Custom Theme Active To Date'
            ]
        ],
        'comment' => 'CMS Page Table'
    ],
    $installer->getTable('cms/page_store') => [
        'columns' => [
            'page_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Page ID'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store ID'
            ]
        ],
        'comment' => 'CMS Page To Store Linkage Table'
    ],
    $installer->getTable('cms_block') => [
        'columns' => [
            'block_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Block ID'
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Block Title'
            ],
            'identifier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Block String Identifier'
            ],
            'content' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Block Content'
            ],
            'creation_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Block Creation Time'
            ],
            'update_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Block Modification Time'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Block Active'
            ]
        ],
        'comment' => 'CMS Block Table'
    ],
    $installer->getTable('cms/block_store') => [
        'columns' => [
            'block_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Block ID'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store ID'
            ]
        ],
        'comment' => 'CMS Block To Store Linkage Table'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('cms/page'),
    $installer->getIdxName('cms/page', ['identifier']),
    ['identifier']
);

$installer->getConnection()->addIndex(
    $installer->getTable('cms/page_store'),
    $installer->getIdxName('cms/page_store', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('cms/block_store'),
    $installer->getIdxName('cms/block_store', ['store_id']),
    ['store_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('cms/block_store', 'block_id', 'cms/block', 'block_id'),
    $installer->getTable('cms/block_store'),
    'block_id',
    $installer->getTable('cms/block'),
    'block_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cms/block_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('cms/block_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cms/page_store', 'page_id', 'cms/page', 'page_id'),
    $installer->getTable('cms/page_store'),
    'page_id',
    $installer->getTable('cms/page'),
    'page_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('cms/page_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('cms/page_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
