<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'salesrule/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/rule'))
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
    ->addColumn('uses_per_customer', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Uses Per Customer')
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
    ->addColumn('is_advanced', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Advanced')
    ->addColumn('product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Product Ids')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addColumn('simple_action', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Simple Action')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('discount_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
    ], 'Discount Qty')
    ->addColumn('discount_step', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Discount Step')
    ->addColumn('simple_free_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Simple Free Shipping')
    ->addColumn('apply_to_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Apply To Shipping')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Times Used')
    ->addColumn('is_rss', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Rss')
    ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, [
    ], 'Website Ids')
    ->addColumn('coupon_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Coupon Type')
    ->addIndex(
        $installer->getIdxName('salesrule/rule', ['is_active', 'sort_order', 'to_date', 'from_date']),
        ['is_active', 'sort_order', 'to_date', 'from_date'],
    )
    ->setComment('Salesrule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon'))
    ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Coupon Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Rule Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Code')
    ->addColumn('usage_limit', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Usage Limit')
    ->addColumn('usage_per_customer', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Usage Per Customer')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Times Used')
    ->addColumn('expiration_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Expiration Date')
    ->addColumn('is_primary', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Primary')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon', ['code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon', ['rule_id', 'is_primary'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['rule_id', 'is_primary'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon', ['rule_id']),
        ['rule_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Salesrule Coupon');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon_usage'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon_usage'))
    ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Coupon Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Id')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Times Used')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_usage', ['coupon_id']),
        ['coupon_id'],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_usage', ['customer_id']),
        ['customer_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_usage', 'coupon_id', 'salesrule/coupon', 'coupon_id'),
        'coupon_id',
        $installer->getTable('salesrule/coupon'),
        'coupon_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_usage', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Salesrule Coupon Usage');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/rule_customer'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/rule_customer'))
    ->addColumn('rule_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rule Customer Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Rule Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer Id')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Times Used')
    ->addIndex(
        $installer->getIdxName('salesrule/rule_customer', ['rule_id', 'customer_id']),
        ['rule_id', 'customer_id'],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/rule_customer', ['customer_id', 'rule_id']),
        ['customer_id', 'rule_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/rule_customer', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/rule_customer', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Salesrule Customer');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/label'))
    ->addColumn('label_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Label Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Rule Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Store Id')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Label')
    ->addIndex(
        $installer->getIdxName('salesrule/label', ['rule_id', 'store_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['rule_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/label', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/label', ['rule_id']),
        ['rule_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/label', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/label', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Salesrule Label');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/product_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/product_attribute'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rule Id')
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
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Id')
    ->addIndex(
        $installer->getIdxName('salesrule/product_attribute', ['website_id']),
        ['website_id'],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/product_attribute', ['customer_group_id']),
        ['customer_group_id'],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/product_attribute', ['attribute_id']),
        ['attribute_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    )
    ->setComment('Salesrule Product Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon_aggregated'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
        'nullable'  => false,
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Status')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Coupon Code')
    ->addColumn('coupon_uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Coupon Uses')
    ->addColumn('subtotal_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Subtotal Amount')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('total_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Amount')
    ->addColumn('subtotal_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Subtotal Amount Actual')
    ->addColumn('discount_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Discount Amount Actual')
    ->addColumn('total_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Amount Actual')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated', ['period', 'store_id', 'order_status', 'coupon_code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['period', 'store_id', 'order_status', 'coupon_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Coupon Aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon_aggregated_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon_aggregated_order'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
        'nullable'  => false,
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Status')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Coupon Code')
    ->addColumn('coupon_uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Coupon Uses')
    ->addColumn('subtotal_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Subtotal Amount')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('total_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, [12,4], [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Amount')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated_order', ['period', 'store_id', 'order_status', 'coupon_code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['period', 'store_id', 'order_status', 'coupon_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated_order', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_aggregated_order', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Coupon Aggregated Order');
$installer->getConnection()->createTable($table);

$installer->endSetup();
