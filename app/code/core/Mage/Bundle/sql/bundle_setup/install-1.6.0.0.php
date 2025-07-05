<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'catalog_product_bundle_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Required')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Type')
    ->addIndex(
        $installer->getIdxName('bundle/option', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('bundle/option', 'parent_id', 'catalog/product', 'entity_id'),
        'parent_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Bundle Option');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_bundle_option_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/option_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Option Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Store Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Title')
    ->addIndex(
        $installer->getIdxName('bundle/option_value', ['option_id', 'store_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['option_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName('bundle/option_value', 'option_id', 'bundle/option', 'option_id'),
        'option_id',
        $installer->getTable('bundle/option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Bundle Option Value');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_bundle_selection'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/selection'))
    ->addColumn('selection_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Selection Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Option Id')
    ->addColumn('parent_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Product Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Product Id')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Default')
    ->addColumn('selection_price_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Selection Price Type')
    ->addColumn('selection_price_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Selection Price Value')
    ->addColumn('selection_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Selection Qty')
    ->addColumn('selection_can_change_qty', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Selection Can Change Qty')
    ->addIndex(
        $installer->getIdxName('bundle/selection', ['option_id']),
        ['option_id'],
    )
    ->addIndex(
        $installer->getIdxName('bundle/selection', ['product_id']),
        ['product_id'],
    )
    ->addForeignKey(
        $installer->getFkName('bundle/selection', 'option_id', 'bundle/option', 'option_id'),
        'option_id',
        $installer->getTable('bundle/option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('bundle/selection', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Bundle Selection');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_bundle_selection_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/selection_price'))
    ->addColumn('selection_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Selection Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('selection_price_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Selection Price Type')
    ->addColumn('selection_price_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Selection Price Value')
    ->addIndex(
        $installer->getIdxName('bundle/selection_price', ['website_id']),
        ['website_id'],
    )
    ->addForeignKey(
        $installer->getFkName('bundle/selection_price', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('bundle/selection_price', 'selection_id', 'bundle/selection', 'selection_id'),
        'selection_id',
        $installer->getTable('bundle/selection'),
        'selection_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Bundle Selection Price');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_bundle_price_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/price_index'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
    ], 'Max Price')
    ->addIndex(
        $installer->getIdxName('bundle/price_index', ['website_id']),
        ['website_id'],
    )
    ->addIndex(
        $installer->getIdxName('bundle/price_index', ['customer_group_id']),
        ['customer_group_id'],
    )
    ->addForeignKey(
        $installer->getFkName('bundle/price_index', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('bundle/price_index', 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('bundle/price_index', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Catalog Product Bundle Price Index');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_bundle_stock_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/stock_index'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('stock_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Stock Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option Id')
    ->addColumn('stock_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'default'   => '0',
    ], 'Stock Status')
    ->setComment('Catalog Product Bundle Stock Index');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_bundle_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/price_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class Id')
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Price Type')
    ->addColumn('special_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Special Price')
    ->addColumn('tier_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Percent')
    ->addColumn('orig_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Orig Price')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addColumn('base_tier', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tier')
    ->setComment('Catalog Product Index Price Bundle Idx');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_bundle_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/price_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class Id')
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Price Type')
    ->addColumn('special_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Special Price')
    ->addColumn('tier_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Percent')
    ->addColumn('orig_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Orig Price')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addColumn('base_tier', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tier')
    ->setComment('Catalog Product Index Price Bundle Tmp');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_bundle_sel_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/selection_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option Id')
    ->addColumn('selection_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Selection Id')
    ->addColumn('group_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Group Type')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Is Required')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Index Price Bundle Sel Idx');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_bundle_sel_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/selection_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option Id')
    ->addColumn('selection_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Selection Id')
    ->addColumn('group_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Group Type')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Is Required')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Index Price Bundle Sel Tmp');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_bundle_opt_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/option_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option Id')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('alt_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Alt Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addColumn('alt_tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Alt Tier Price')
    ->setComment('Catalog Product Index Price Bundle Opt Idx');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_bundle_opt_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('bundle/option_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option Id')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('alt_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Alt Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addColumn('alt_tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Alt Tier Price')
    ->setComment('Catalog Product Index Price Bundle Opt Tmp');
$installer->getConnection()->createTable($table);

/**
 * Add attributes to the eav/attribute
 */
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'price_type', [
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => '',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'used_in_product_listing' => true,
    'unique'            => false,
    'apply_to'          => 'bundle',
    'is_configurable'   => false,
]);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'sku_type', [
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => '',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'bundle',
    'is_configurable'   => false,
]);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight_type', [
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => '',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'used_in_product_listing' => true,
    'unique'            => false,
    'apply_to'          => 'bundle',
    'is_configurable'   => false,
]);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'price_view', [
    'group'             => 'Prices',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Price View',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'bundle/product_attribute_source_price_view',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'used_in_product_listing' => true,
    'unique'            => false,
    'apply_to'          => 'bundle',
    'is_configurable'   => false,
]);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'shipment_type', [
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Shipment',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'used_in_product_listing' => true,
    'unique'            => false,
    'apply_to'          => 'bundle',
    'is_configurable'   => false,
]);

$installer->endSetup();
