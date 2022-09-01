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
 * @package     Mage_CatalogSearch
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
    $installer->getTable('catalogsearch/search_query'),
    'FK_CATALOGSEARCH_QUERY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogsearch/result'),
    'FK_CATALOGSEARCH_RESULT_CATALOG_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogsearch/result'),
    'FK_CATALOGSEARCH_RESULT_QUERY'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/fulltext'),
    'PRIMARY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/fulltext'),
    'data_index'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/search_query'),
    'FK_CATALOGSEARCH_QUERY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/search_query'),
    'IDX_SEARCH_QUERY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/result'),
    'IDX_QUERY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/result'),
    'IDX_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogsearch/result'),
    'IDX_RELEVANCE'
);

/*
 * Change columns
 */
$tables = [
    $installer->getTable('catalogsearch/search_query') => [
        'columns' => [
            'query_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Query ID'
            ],
            'query_text' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Query text'
            ],
            'num_results' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Num results'
            ],
            'popularity' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Popularity'
            ],
            'redirect' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Redirect'
            ],
            'synonym_for' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Synonym for'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ],
            'display_in_terms' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Display in terms'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '1',
                'comment'   => 'Active status'
            ],
            'is_processed' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '0',
                'comment'   => 'Processed status'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated at'
            ]
        ],
        'comment' => 'Catalog search query table'
    ],
    $installer->getTable('catalogsearch/result') => [
        'columns' => [
            'query_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Query ID'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product ID'
            ],
            'relevance' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 20,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Relevance'
            ]
        ],
        'comment' => 'Catalog search result table'
    ],
    $installer->getTable('catalogsearch/fulltext') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product ID'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ],
            'data_index' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '4g',
                'comment'   => 'Data index'
            ]
        ],
        'comment' => 'Catalog search result table'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Change columns
 */
$installer->getConnection()->addColumn(
    $installer->getTable('catalogsearch/fulltext'),
    'fulltext_id',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'Entity ID'
    ]
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/fulltext'),
    $installer->getIdxName(
        'catalogsearch/fulltext',
        ['product_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['product_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/fulltext'),
    $installer->getIdxName(
        'catalogsearch/fulltext',
        ['data_index'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
    ),
    ['data_index'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/search_query'),
    $installer->getIdxName('catalogsearch/search_query', ['query_text', 'store_id', 'popularity']),
    ['query_text', 'store_id', 'popularity']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/search_query'),
    $installer->getIdxName('catalogsearch/search_query', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/result'),
    $installer->getIdxName('catalogsearch/result', ['query_id']),
    ['query_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/result'),
    $installer->getIdxName('catalogsearch/result', ['product_id']),
    ['product_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogsearch/search_query', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('catalogsearch/search_query'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogsearch/result', 'query_id', 'catalogsearch/search_query', 'query_id'),
    $installer->getTable('catalogsearch/result'),
    'query_id',
    $installer->getTable('catalogsearch/search_query'),
    'query_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogsearch/result', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalogsearch/result'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->endSetup();
