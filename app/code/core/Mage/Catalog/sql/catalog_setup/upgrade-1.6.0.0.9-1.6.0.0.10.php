<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer  = $this;
$connection = $installer->getConnection();

/**
 * Create table 'catalog/product_attribute_group_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_attribute_group_price'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('all_groups', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Applicable To All Customer Groups')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer Group ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Website ID')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_attribute_group_price',
            ['entity_id', 'all_groups', 'customer_group_id', 'website_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'all_groups', 'customer_group_id', 'website_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_group_price', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_group_price', ['customer_group_id']),
        ['customer_group_id'],
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_group_price', ['website_id']),
        ['website_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_group_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id',
        ),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_group_price',
            'entity_id',
            'catalog/product',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_group_price',
            'website_id',
            'core/website',
            'website_id',
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Group Price Attribute Backend Table');
$installer->getConnection()->createTable($table);

$installer->addAttribute('catalog_product', 'group_price', [
    'type'                       => 'decimal',
    'label'                      => 'Group Price',
    'input'                      => 'text',
    'backend'                    => 'catalog/product_attribute_backend_groupprice',
    'required'                   => false,
    'sort_order'                 => 6,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'apply_to'                   => 'simple,configurable,virtual',
    'group'                      => 'Prices',
]);

/**
 * Create table 'catalog/product_index_group_price'
 */
$table = $connection
    ->newTable($installer->getTable('catalog/product_index_group_price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addIndex(
        $installer->getIdxName('catalog/product_index_group_price', ['customer_group_id']),
        ['customer_group_id'],
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_group_price', ['website_id']),
        ['website_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_group_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id',
        ),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_group_price',
            'entity_id',
            'catalog/product',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_group_price',
            'website_id',
            'core/website',
            'website_id',
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Group Price Index Table');
$connection->createTable($table);

$finalPriceIndexerTables = [
    'catalog/product_price_indexer_final_idx',
    'catalog/product_price_indexer_final_tmp',
];

$priceIndexerTables =  [
    'catalog/product_price_indexer_option_aggregate_idx',
    'catalog/product_price_indexer_option_aggregate_tmp',
    'catalog/product_price_indexer_option_idx',
    'catalog/product_price_indexer_option_tmp',
    'catalog/product_price_indexer_idx',
    'catalog/product_price_indexer_tmp',
    'catalog/product_price_indexer_cfg_option_aggregate_idx',
    'catalog/product_price_indexer_cfg_option_aggregate_tmp',
    'catalog/product_price_indexer_cfg_option_idx',
    'catalog/product_price_indexer_cfg_option_tmp',
    'catalog/product_index_price',
];

foreach ($finalPriceIndexerTables as $table) {
    $connection->addColumn($installer->getTable($table), 'group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group price',
    ]);
    $connection->addColumn($installer->getTable($table), 'base_group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Base Group Price',
    ]);
}

foreach ($priceIndexerTables as $table) {
    $connection->addColumn($installer->getTable($table), 'group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group price',
    ]);
}
