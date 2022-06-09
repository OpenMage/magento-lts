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
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'salesrule/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'To Date')
    ->addColumn('uses_per_customer', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Uses Per Customer')
    ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Customer Group Ids')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Actions Serialized')
    ->addColumn('stop_rules_processing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Stop Rules Processing')
    ->addColumn('is_advanced', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Advanced')
    ->addColumn('product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Product Ids')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addColumn('simple_action', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Simple Action')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Discount Amount')
    ->addColumn('discount_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        ), 'Discount Qty')
    ->addColumn('discount_step', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Discount Step')
    ->addColumn('simple_free_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Simple Free Shipping')
    ->addColumn('apply_to_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Apply To Shipping')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Times Used')
    ->addColumn('is_rss', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Rss')
    ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, array(
        ), 'Website Ids')
    ->addColumn('coupon_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Coupon Type')
    ->addIndex(
        $installer->getIdxName('salesrule/rule', array('is_active', 'sort_order', 'to_date', 'from_date')),
        array('is_active', 'sort_order', 'to_date', 'from_date')
    )
    ->setComment('Salesrule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon'))
    ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Coupon Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Rule Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Code')
    ->addColumn('usage_limit', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Usage Limit')
    ->addColumn('usage_per_customer', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Usage Per Customer')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Times Used')
    ->addColumn('expiration_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Expiration Date')
    ->addColumn('is_primary', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Is Primary')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon', array('code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon', array('rule_id', 'is_primary'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('rule_id', 'is_primary'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon', array('rule_id')),
        array('rule_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Salesrule Coupon');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon_usage'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon_usage'))
    ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Coupon Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Id')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Times Used')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_usage', array('coupon_id')),
        array('coupon_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_usage', array('customer_id')),
        array('customer_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_usage', 'coupon_id', 'salesrule/coupon', 'coupon_id'),
        'coupon_id',
        $installer->getTable('salesrule/coupon'),
        'coupon_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_usage', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Salesrule Coupon Usage');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/rule_customer'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/rule_customer'))
    ->addColumn('rule_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Customer Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Rule Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
    ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Times Used')
    ->addIndex(
        $installer->getIdxName('salesrule/rule_customer', array('rule_id', 'customer_id')),
        array('rule_id', 'customer_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/rule_customer', array('customer_id', 'rule_id')),
        array('customer_id', 'rule_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/rule_customer', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/rule_customer', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Salesrule Customer');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/label'))
    ->addColumn('label_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Label Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Rule Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addIndex(
        $installer->getIdxName('salesrule/label', array('rule_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('rule_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('salesrule/label', array('store_id')),
        array('store_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/label', array('rule_id')),
        array('rule_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/label', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/label', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Salesrule Label');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/product_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/product_attribute'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addIndex(
        $installer->getIdxName('salesrule/product_attribute', array('website_id')),
        array('website_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/product_attribute', array('customer_group_id')),
        array('customer_group_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/product_attribute', array('attribute_id')),
        array('attribute_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id',
        $installer->getTable('salesrule/rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/product_attribute', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Salesrule Product Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon_aggregated'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => false,
        ), 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Order Status')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Coupon Code')
    ->addColumn('coupon_uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Coupon Uses')
    ->addColumn('subtotal_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Subtotal Amount')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Discount Amount')
    ->addColumn('total_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Total Amount')
    ->addColumn('subtotal_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Subtotal Amount Actual')
    ->addColumn('discount_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Discount Amount Actual')
    ->addColumn('total_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Total Amount Actual')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated', array('period', 'store_id', 'order_status', 'coupon_code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('period', 'store_id', 'order_status', 'coupon_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Coupon Aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'salesrule/coupon_aggregated_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('salesrule/coupon_aggregated_order'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => false,
        ), 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Order Status')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Coupon Code')
    ->addColumn('coupon_uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Coupon Uses')
    ->addColumn('subtotal_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Subtotal Amount')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Discount Amount')
    ->addColumn('total_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Total Amount')
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated_order', array('period', 'store_id', 'order_status', 'coupon_code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('period', 'store_id', 'order_status', 'coupon_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('salesrule/coupon_aggregated_order', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/coupon_aggregated_order', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Coupon Aggregated Order');
$installer->getConnection()->createTable($table);

$installer->endSetup();
