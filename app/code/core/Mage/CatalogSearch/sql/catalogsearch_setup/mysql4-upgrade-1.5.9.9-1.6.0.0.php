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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
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
$tables = array(
    $installer->getTable('catalogsearch/search_query') => array(
        'columns' => array(
            'query_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Query ID'
            ),
            'query_text' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Query text'
            ),
            'num_results' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Num results'
            ),
            'popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Popularity'
            ),
            'redirect' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Redirect'
            ),
            'synonym_for' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Synonym for'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'display_in_terms' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Display in terms'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '1',
                'comment'   => 'Active status'
            ),
            'is_processed' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '0',
                'comment'   => 'Processed status'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated at'
            )
        ),
        'comment' => 'Catalog search query table'
    ),
    $installer->getTable('catalogsearch/result') => array(
        'columns' => array(
            'query_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Query ID'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product ID'
            ),
            'relevance' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 20,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Relevance'
            )
        ),
        'comment' => 'Catalog search result table'
    ),
    $installer->getTable('catalogsearch/fulltext') => array(
        'columns' => array(
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'data_index' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '4g',
                'comment'   => 'Data index'
            )
        ),
        'comment' => 'Catalog search result table'
    )
);

$installer->getConnection()->modifyTables($tables);

/**
 * Change columns
 */
$installer->getConnection()->addColumn(
    $installer->getTable('catalogsearch/fulltext'),
    'fulltext_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'Entity ID'
    )
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/fulltext'),
    $installer->getIdxName(
        'catalogsearch/fulltext',
        array('product_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/fulltext'),
    $installer->getIdxName(
        'catalogsearch/fulltext',
        array('data_index'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
    ),
    array('data_index'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/search_query'),
    $installer->getIdxName('catalogsearch/search_query', array('query_text', 'store_id', 'popularity')),
    array('query_text', 'store_id', 'popularity')
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/search_query'),
    $installer->getIdxName('catalogsearch/search_query', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/result'),
    $installer->getIdxName('catalogsearch/result', array('query_id')),
    array('query_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogsearch/result'),
    $installer->getIdxName('catalogsearch/result', array('product_id')),
    array('product_id')
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
