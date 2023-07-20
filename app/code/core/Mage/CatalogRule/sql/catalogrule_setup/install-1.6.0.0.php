<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'catalogrule/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogrule/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rule Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'To Date')
    ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Customer Group Ids')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Active')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
    ], 'Conditions Serialized')
    ->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
    ], 'Actions Serialized')
    ->addColumn('stop_rules_processing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '1',
    ], 'Stop Rules Processing')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addColumn('simple_action', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Simple Action')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => 0.0000,
    ], 'Discount Amount')
    ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, [
    ], 'Website Ids')
    ->addIndex(
        $installer->getIdxName('catalogrule/rule', ['is_active', 'sort_order', 'to_date', 'from_date']),
        ['is_active', 'sort_order', 'to_date', 'from_date']
    )

    ->setComment('CatalogRule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogrule/rule_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogrule/rule_product'))
    ->addColumn('rule_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rule Product Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Rule Id')
    ->addColumn('from_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'From Time')
    ->addColumn('to_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'To time')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer Group Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product Id')
    ->addColumn('action_operator', Varien_Db_Ddl_Table::TYPE_TEXT, 10, [
        'default'   => 'to_fixed',
    ], 'Action Operator')
    ->addColumn('action_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Action Amount')
    ->addColumn('action_stop', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Action Stop')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Website Id')
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['rule_id', 'from_time', 'to_time', 'website_id', 'customer_group_id', 'product_id', 'sort_order'], true),
        ['rule_id', 'from_time', 'to_time', 'website_id', 'customer_group_id', 'product_id', 'sort_order'],
        ['type' => 'unique']
    )

    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['rule_id']),
        ['rule_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['from_time']),
        ['from_time']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['to_time']),
        ['to_time']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product', ['product_id']),
        ['product_id']
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product', 'rule_id', 'catalogrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('catalogrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->setComment('CatalogRule Product');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogrule/rule_product_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogrule/rule_product_price'))
    ->addColumn('rule_product_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rule Product PriceId')
    ->addColumn('rule_date', Varien_Db_Ddl_Table::TYPE_DATE, null, [
        'nullable'  => false,
    ], 'Rule Date')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer Group Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product Id')
    ->addColumn('rule_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Rule Price')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Website Id')
    ->addColumn('latest_start_date', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Latest StartDate')
    ->addColumn('earliest_end_date', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Earliest EndDate')

    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product_price', ['rule_date', 'website_id', 'customer_group_id', 'product_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['rule_date', 'website_id', 'customer_group_id', 'product_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product_price', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product_price', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_product_price', ['product_id']),
        ['product_id']
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product_price', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product_price', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_product_price', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->setComment('CatalogRule Product Price');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogrule/affected_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogrule/affected_product'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Product Id')
    ->setComment('CatalogRule Affected Product');

$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogrule/rule_group_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogrule/rule_group_website'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Rule Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Customer Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Website Id')
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_group_website', ['rule_id']),
        ['rule_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_group_website', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalogrule/rule_group_website', ['website_id']),
        ['website_id']
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_group_website', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_group_website', 'rule_id', 'catalogrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('catalogrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->addForeignKey(
        $installer->getFkName('catalogrule/rule_group_website', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('CatalogRule Group Website');

$installer->getConnection()->createTable($table);

$installer->endSetup();
