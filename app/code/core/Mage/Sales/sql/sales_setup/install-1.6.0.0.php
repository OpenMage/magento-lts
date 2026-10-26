<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $this */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'sales/order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'State')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Status')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Coupon Code')
    ->addColumn('protect_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Protect Code')
    ->addColumn('shipping_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Description')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Virtual')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer Id')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Amount')
    ->addColumn('base_discount_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Canceled')
    ->addColumn('base_discount_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Invoiced')
    ->addColumn('base_discount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Refunded')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Grand Total')
    ->addColumn('base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Amount')
    ->addColumn('base_shipping_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Canceled')
    ->addColumn('base_shipping_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Invoiced')
    ->addColumn('base_shipping_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Refunded')
    ->addColumn('base_shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Tax Amount')
    ->addColumn('base_shipping_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Tax Refunded')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal')
    ->addColumn('base_subtotal_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Canceled')
    ->addColumn('base_subtotal_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Invoiced')
    ->addColumn('base_subtotal_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Refunded')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Amount')
    ->addColumn('base_tax_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Canceled')
    ->addColumn('base_tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Invoiced')
    ->addColumn('base_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Refunded')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Global Rate')
    ->addColumn('base_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Order Rate')
    ->addColumn('base_total_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Canceled')
    ->addColumn('base_total_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Invoiced')
    ->addColumn('base_total_invoiced_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Invoiced Cost')
    ->addColumn('base_total_offline_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Offline Refunded')
    ->addColumn('base_total_online_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Online Refunded')
    ->addColumn('base_total_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Paid')
    ->addColumn('base_total_qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Qty Ordered')
    ->addColumn('base_total_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Refunded')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Amount')
    ->addColumn('discount_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Canceled')
    ->addColumn('discount_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Invoiced')
    ->addColumn('discount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Refunded')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Grand Total')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Amount')
    ->addColumn('shipping_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Canceled')
    ->addColumn('shipping_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Invoiced')
    ->addColumn('shipping_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Refunded')
    ->addColumn('shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Tax Amount')
    ->addColumn('shipping_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Tax Refunded')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Base Rate')
    ->addColumn('store_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Order Rate')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal')
    ->addColumn('subtotal_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Canceled')
    ->addColumn('subtotal_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Invoiced')
    ->addColumn('subtotal_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Refunded')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Amount')
    ->addColumn('tax_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Canceled')
    ->addColumn('tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Invoiced')
    ->addColumn('tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Refunded')
    ->addColumn('total_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Canceled')
    ->addColumn('total_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Invoiced')
    ->addColumn('total_offline_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Offline Refunded')
    ->addColumn('total_online_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Online Refunded')
    ->addColumn('total_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Paid')
    ->addColumn('total_qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Qty Ordered')
    ->addColumn('total_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Refunded')
    ->addColumn('can_ship_partially', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Can Ship Partially')
    ->addColumn('can_ship_partially_item', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Can Ship Partially Item')
    ->addColumn('customer_is_guest', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Customer Is Guest')
    ->addColumn('customer_note_notify', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Customer Note Notify')
    ->addColumn('billing_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Billing Address Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
    ], 'Customer Group Id')
    ->addColumn('edit_increment', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Edit Increment')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Email Sent')
    ->addColumn('forced_shipment_with_invoice', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Forced Do Shipment With Invoice')
    ->addColumn('payment_auth_expiration', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Payment Authorization Expiration')
    ->addColumn('quote_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Quote Address Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Quote Id')
    ->addColumn('shipping_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Shipping Address Id')
    ->addColumn('adjustment_negative', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Adjustment Negative')
    ->addColumn('adjustment_positive', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Adjustment Positive')
    ->addColumn('base_adjustment_negative', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Adjustment Negative')
    ->addColumn('base_adjustment_positive', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Adjustment Positive')
    ->addColumn('base_shipping_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Discount Amount')
    ->addColumn('base_subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Incl Tax')
    ->addColumn('base_total_due', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Due')
    ->addColumn('payment_authorization_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Payment Authorization Amount')
    ->addColumn('shipping_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Discount Amount')
    ->addColumn('subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Incl Tax')
    ->addColumn('total_due', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Due')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Weight')
    ->addColumn('customer_dob', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
    ], 'Customer Dob')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Applied Rule Ids')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Base Currency Code')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Email')
    ->addColumn('customer_firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Firstname')
    ->addColumn('customer_lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Lastname')
    ->addColumn('customer_middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Middlename')
    ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Prefix')
    ->addColumn('customer_suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Suffix')
    ->addColumn('customer_taxvat', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Taxvat')
    ->addColumn('discount_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Discount Description')
    ->addColumn('ext_customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Ext Customer Id')
    ->addColumn('ext_order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Ext Order Id')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Global Currency Code')
    ->addColumn('hold_before_state', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Hold Before State')
    ->addColumn('hold_before_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Hold Before Status')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Order Currency Code')
    ->addColumn('original_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Original Increment Id')
    ->addColumn('relation_child_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Relation Child Id')
    ->addColumn('relation_child_real_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Relation Child Real Id')
    ->addColumn('relation_parent_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Relation Parent Id')
    ->addColumn('relation_parent_real_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Relation Parent Real Id')
    ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Remote Ip')
    ->addColumn('shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Method')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Store Currency Code')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Store Name')
    ->addColumn('x_forwarded_for', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'X Forwarded For')
    ->addColumn('customer_note', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Customer Note')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addColumn('total_item_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Total Item Count')
    ->addColumn('customer_gender', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Customer Gender')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addColumn('shipping_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Hidden Tax Amount')
    ->addColumn('base_shipping_hidden_tax_amnt', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Hidden Tax Amount')
    ->addColumn('hidden_tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Invoiced')
    ->addColumn('base_hidden_tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Invoiced')
    ->addColumn('hidden_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Refunded')
    ->addColumn('base_hidden_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Refunded')
    ->addColumn('shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Incl Tax')
    ->addColumn('base_shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Incl Tax')
    ->addIndex(
        $installer->getIdxName('sales/order', ['status']),
        ['status'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['state']),
        ['state'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/order',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['customer_id']),
        ['customer_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['ext_order_id']),
        ['ext_order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['quote_id']),
        ['quote_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order', ['updated_at']),
        ['updated_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/order', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Status')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Store Name')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer Id')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Grand Total')
    ->addColumn('base_total_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Paid')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Grand Total')
    ->addColumn('total_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Paid')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Base Currency Code')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Order Currency Code')
    ->addColumn('shipping_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Name')
    ->addColumn('billing_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Billing Name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['status']),
        ['status'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['base_grand_total']),
        ['base_grand_total'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['base_total_paid']),
        ['base_total_paid'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['grand_total']),
        ['grand_total'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['total_paid']),
        ['total_paid'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/order_grid',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['shipping_name']),
        ['shipping_name'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['billing_name']),
        ['billing_name'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['customer_id']),
        ['customer_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_grid', ['updated_at']),
        ['updated_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_grid', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_grid', 'entity_id', 'sales/order', 'entity_id'),
        'entity_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_grid', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Order Grid');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_address'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_address'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Parent Id')
    ->addColumn('customer_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Customer Address Id')
    ->addColumn('quote_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Quote Address Id')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Region Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Customer Id')
    ->addColumn('fax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Fax')
    ->addColumn('region', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Region')
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Postcode')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Lastname')
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Street')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'City')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Email')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Telephone')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
    ], 'Country Id')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Firstname')
    ->addColumn('address_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Address Type')
    ->addColumn('prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Prefix')
    ->addColumn('middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Middlename')
    ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Suffix')
    ->addColumn('company', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Company')
    ->addIndex(
        $installer->getIdxName('sales/order_address', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_address', 'parent_id', 'sales/order', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Order Address');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_status_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_status_history'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Comment')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('sales/order_status_history', ['parent_id']),
        ['parent_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_status_history', ['created_at']),
        ['created_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_status_history', 'parent_id', 'sales/order', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Order Status History');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_item'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Item Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Order Id')
    ->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Parent Item Id')
    ->addColumn('quote_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Quote Item Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Product Id')
    ->addColumn('product_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Product Type')
    ->addColumn('product_options', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Product Options')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Weight')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Virtual')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Applied Rule Ids')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('free_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Free Shipping')
    ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Qty Decimal')
    ->addColumn('no_discount', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'No Discount')
    ->addColumn('qty_backordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Qty Backordered')
    ->addColumn('qty_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Qty Canceled')
    ->addColumn('qty_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Qty Invoiced')
    ->addColumn('qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Qty Ordered')
    ->addColumn('qty_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Qty Refunded')
    ->addColumn('qty_shipped', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Qty Shipped')
    ->addColumn('base_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Cost')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Price')
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Price')
    ->addColumn('original_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Original Price')
    ->addColumn('base_original_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Original Price')
    ->addColumn('tax_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Tax Percent')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Tax Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Tax Amount')
    ->addColumn('tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Tax Invoiced')
    ->addColumn('base_tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Tax Invoiced')
    ->addColumn('discount_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Discount Percent')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Discount Amount')
    ->addColumn('discount_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Discount Invoiced')
    ->addColumn('base_discount_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Discount Invoiced')
    ->addColumn('amount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Amount Refunded')
    ->addColumn('base_amount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Amount Refunded')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Row Total')
    ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Row Total')
    ->addColumn('row_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Row Invoiced')
    ->addColumn('base_row_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Row Invoiced')
    ->addColumn('row_weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Row Weight')
    ->addColumn('base_tax_before_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Before Discount')
    ->addColumn('tax_before_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Before Discount')
    ->addColumn('ext_order_item_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Ext Order Item Id')
    ->addColumn('locked_do_invoice', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Locked Do Invoice')
    ->addColumn('locked_do_ship', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Locked Do Ship')
    ->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price Incl Tax')
    ->addColumn('base_price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price Incl Tax')
    ->addColumn('row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total Incl Tax')
    ->addColumn('base_row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total Incl Tax')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addColumn('hidden_tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Invoiced')
    ->addColumn('base_hidden_tax_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Invoiced')
    ->addColumn('hidden_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Refunded')
    ->addColumn('base_hidden_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Refunded')
    ->addColumn('is_nominal', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Nominal')
    ->addColumn('tax_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Canceled')
    ->addColumn('hidden_tax_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Canceled')
    ->addColumn('tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Refunded')
    ->addIndex(
        $installer->getIdxName('sales/order_item', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_item', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_item', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_item', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Order Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_payment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_payment'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('base_shipping_captured', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Captured')
    ->addColumn('shipping_captured', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Captured')
    ->addColumn('amount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Amount Refunded')
    ->addColumn('base_amount_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Paid')
    ->addColumn('amount_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Amount Canceled')
    ->addColumn('base_amount_authorized', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Authorized')
    ->addColumn('base_amount_paid_online', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Paid Online')
    ->addColumn('base_amount_refunded_online', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Refunded Online')
    ->addColumn('base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Amount')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Amount')
    ->addColumn('amount_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Amount Paid')
    ->addColumn('amount_authorized', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Amount Authorized')
    ->addColumn('base_amount_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Ordered')
    ->addColumn('base_shipping_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Refunded')
    ->addColumn('shipping_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Refunded')
    ->addColumn('base_amount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Refunded')
    ->addColumn('amount_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Amount Ordered')
    ->addColumn('base_amount_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount Canceled')
    ->addColumn('quote_payment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Quote Payment Id')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('cc_exp_month', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Exp Month')
    ->addColumn('cc_ss_start_year', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Ss Start Year')
    ->addColumn('echeck_bank_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Echeck Bank Name')
    ->addColumn('method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Method')
    ->addColumn('cc_debug_request_body', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Debug Request Body')
    ->addColumn('cc_secure_verify', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Secure Verify')
    ->addColumn('protection_eligibility', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Protection Eligibility')
    ->addColumn('cc_approval', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Approval')
    ->addColumn('cc_last4', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Last4')
    ->addColumn('cc_status_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Status Description')
    ->addColumn('echeck_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Echeck Type')
    ->addColumn('cc_debug_response_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Debug Response Serialized')
    ->addColumn('cc_ss_start_month', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Ss Start Month')
    ->addColumn('echeck_account_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Echeck Account Type')
    ->addColumn('last_trans_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Last Trans Id')
    ->addColumn('cc_cid_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Cid Status')
    ->addColumn('cc_owner', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Owner')
    ->addColumn('cc_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Type')
    ->addColumn('po_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Po Number')
    ->addColumn('cc_exp_year', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Exp Year')
    ->addColumn('cc_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Status')
    ->addColumn('echeck_routing_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Echeck Routing Number')
    ->addColumn('account_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Account Status')
    ->addColumn('anet_trans_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Anet Trans Method')
    ->addColumn('cc_debug_response_body', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Debug Response Body')
    ->addColumn('cc_ss_issue', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Ss Issue')
    ->addColumn('echeck_account_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Echeck Account Name')
    ->addColumn('cc_avs_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Avs Status')
    ->addColumn('cc_number_enc', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Number Enc')
    ->addColumn('cc_trans_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Trans Id')
    ->addColumn('paybox_request_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Paybox Request Number')
    ->addColumn('address_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Address Status')
    ->addColumn('additional_information', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Information')
    ->addIndex(
        $installer->getIdxName('sales/order_payment', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_payment', 'parent_id', 'sales/order', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Order Payment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipment'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('total_weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Weight')
    ->addColumn('total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Qty')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Email Sent')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Customer Id')
    ->addColumn('shipping_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Shipping Address Id')
    ->addColumn('billing_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Billing Address Id')
    ->addColumn('shipment_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Shipment Status')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addIndex(
        $installer->getIdxName('sales/shipment', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment', ['total_qty']),
        ['total_qty'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/shipment',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment', ['updated_at']),
        ['updated_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Shipment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipment_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipment_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Qty')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('shipment_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Shipment Status')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('order_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Order Created At')
    ->addColumn('shipping_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Name')
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['total_qty']),
        ['total_qty'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['shipment_status']),
        ['shipment_status'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/shipment_grid',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['order_increment_id']),
        ['order_increment_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['order_created_at']),
        ['order_created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_grid', ['shipping_name']),
        ['shipping_name'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment_grid', 'entity_id', 'sales/shipment', 'entity_id'),
        'entity_id',
        $installer->getTable('sales/shipment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment_grid', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Shipment Grid');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipment_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipment_item'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Weight')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Qty')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Product Id')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Order Item Id')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sku')
    ->addIndex(
        $installer->getIdxName('sales/shipment_item', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment_item', 'parent_id', 'sales/shipment', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/shipment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Shipment Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipment_track'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipment_track'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Weight')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Qty')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('track_number', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Number')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Title')
    ->addColumn('carrier_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Carrier Code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addIndex(
        $installer->getIdxName('sales/shipment_track', ['parent_id']),
        ['parent_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_track', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_track', ['created_at']),
        ['created_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment_track', 'parent_id', 'sales/shipment', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/shipment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Shipment Track');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipment_comment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipment_comment'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Comment')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('sales/shipment_comment', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipment_comment', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipment_comment', 'parent_id', 'sales/shipment', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/shipment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Shipment Comment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/invoice'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/invoice'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'identity'  => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Grand Total')
    ->addColumn('shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Tax Amount')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Amount')
    ->addColumn('store_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Order Rate')
    ->addColumn('base_shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Tax Amount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Amount')
    ->addColumn('base_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Order Rate')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Grand Total')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Amount')
    ->addColumn('subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Incl Tax')
    ->addColumn('base_subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Incl Tax')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Base Rate')
    ->addColumn('base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Amount')
    ->addColumn('total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Qty')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Global Rate')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Amount')
    ->addColumn('billing_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Billing Address Id')
    ->addColumn('is_used_for_refund', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Used For Refund')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Email Sent')
    ->addColumn('can_void_flag', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Can Void Flag')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'State')
    ->addColumn('shipping_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Shipping Address Id')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Store Currency Code')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Transaction Id')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Order Currency Code')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Base Currency Code')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Global Currency Code')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addColumn('shipping_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Hidden Tax Amount')
    ->addColumn('base_shipping_hidden_tax_amnt', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Hidden Tax Amount')
    ->addColumn('shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Incl Tax')
    ->addColumn('base_shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Incl Tax')
    ->addColumn('base_total_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Total Refunded')
    ->addIndex(
        $installer->getIdxName('sales/invoice', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice', ['grand_total']),
        ['grand_total'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice', ['state']),
        ['state'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/invoice',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice', ['created_at']),
        ['created_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoice', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoice', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Invoice');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/invoice_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/invoice_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Grand Total')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Grand Total')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'State')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Store Currency Code')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Order Currency Code')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Base Currency Code')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Global Currency Code')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('order_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Order Created At')
    ->addColumn('billing_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Billing Name')
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['grand_total']),
        ['grand_total'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['state']),
        ['state'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/invoice_grid',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['order_increment_id']),
        ['order_increment_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['order_created_at']),
        ['order_created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_grid', ['billing_name']),
        ['billing_name'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoice_grid', 'entity_id', 'sales/invoice', 'entity_id'),
        'entity_id',
        $installer->getTable('sales/invoice'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoice_grid', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Invoice Grid');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/invoice_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/invoice_item'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Amount')
    ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Amount')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Amount')
    ->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price Incl Tax')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Amount')
    ->addColumn('base_price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price Incl Tax')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Qty')
    ->addColumn('base_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Cost')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('base_row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total Incl Tax')
    ->addColumn('row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total Incl Tax')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Product Id')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Order Item Id')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addIndex(
        $installer->getIdxName('sales/invoice_item', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoice_item', 'parent_id', 'sales/invoice', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/invoice'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Invoice Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/invoice_comment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/invoice_comment'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Comment')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('sales/invoice_comment', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoice_comment', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoice_comment', 'parent_id', 'sales/invoice', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/invoice'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Invoice Comment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/creditmemo'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/creditmemo'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('adjustment_positive', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Adjustment Positive')
    ->addColumn('base_shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Tax Amount')
    ->addColumn('store_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Order Rate')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Amount')
    ->addColumn('base_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Order Rate')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Grand Total')
    ->addColumn('base_adjustment_negative', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Adjustment Negative')
    ->addColumn('base_subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Incl Tax')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Amount')
    ->addColumn('subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Incl Tax')
    ->addColumn('adjustment_negative', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Adjustment Negative')
    ->addColumn('base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Amount')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Base Rate')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Global Rate')
    ->addColumn('base_adjustment', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Adjustment')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Amount')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal')
    ->addColumn('adjustment', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Adjustment')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Grand Total')
    ->addColumn('base_adjustment_positive', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Adjustment Positive')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Amount')
    ->addColumn('shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Tax Amount')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Amount')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Email Sent')
    ->addColumn('creditmemo_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Creditmemo Status')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'State')
    ->addColumn('shipping_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Shipping Address Id')
    ->addColumn('billing_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Billing Address Id')
    ->addColumn('invoice_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Invoice Id')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Store Currency Code')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Order Currency Code')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Base Currency Code')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Global Currency Code')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Transaction Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addColumn('shipping_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Hidden Tax Amount')
    ->addColumn('base_shipping_hidden_tax_amnt', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Hidden Tax Amount')
    ->addColumn('shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Incl Tax')
    ->addColumn('base_shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Incl Tax')
    ->addIndex(
        $installer->getIdxName('sales/creditmemo', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo', ['creditmemo_status']),
        ['creditmemo_status'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/creditmemo',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo', ['state']),
        ['state'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo', ['created_at']),
        ['created_at'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/creditmemo', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/creditmemo', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Creditmemo');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/creditmemo_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/creditmemo_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('store_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Order Rate')
    ->addColumn('base_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Order Rate')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Grand Total')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Store To Base Rate')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Global Rate')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Grand Total')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('creditmemo_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Creditmemo Status')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'State')
    ->addColumn('invoice_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Invoice Id')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Store Currency Code')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Order Currency Code')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Base Currency Code')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
    ], 'Global Currency Code')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addColumn('order_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Order Created At')
    ->addColumn('billing_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Billing Name')
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['grand_total']),
        ['grand_total'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['base_grand_total']),
        ['base_grand_total'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['creditmemo_status']),
        ['creditmemo_status'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['state']),
        ['state'],
    )
    ->addIndex(
        $installer->getIdxName(
            'sales/creditmemo_grid',
            ['increment_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['increment_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['order_increment_id']),
        ['order_increment_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['order_created_at']),
        ['order_created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_grid', ['billing_name']),
        ['billing_name'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/creditmemo_grid', 'entity_id', 'sales/creditmemo', 'entity_id'),
        'entity_id',
        $installer->getTable('sales/creditmemo'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/creditmemo_grid', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Creditmemo Grid');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/creditmemo_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/creditmemo_item'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Amount')
    ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Amount')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Discount Amount')
    ->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price Incl Tax')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Amount')
    ->addColumn('base_price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price Incl Tax')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Qty')
    ->addColumn('base_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Cost')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('base_row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total Incl Tax')
    ->addColumn('row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total Incl Tax')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Product Id')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Order Item Id')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_item', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/creditmemo_item', 'parent_id', 'sales/creditmemo', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/creditmemo'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Creditmemo Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/creditmemo_comment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/creditmemo_comment'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Parent Id')
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Comment')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_comment', ['created_at']),
        ['created_at'],
    )
    ->addIndex(
        $installer->getIdxName('sales/creditmemo_comment', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/creditmemo_comment', 'parent_id', 'sales/creditmemo', 'entity_id'),
        'parent_id',
        $installer->getTable('sales/creditmemo'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Creditmemo Comment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('converted_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Converted At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '1',
    ], 'Is Active')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Is Virtual')
    ->addColumn('is_multi_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Is Multi Shipping')
    ->addColumn('items_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Items Count')
    ->addColumn('items_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Items Qty')
    ->addColumn('orig_order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Orig Order Id')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Store To Base Rate')
    ->addColumn('store_to_quote_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Store To Quote Rate')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Base Currency Code')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Store Currency Code')
    ->addColumn('quote_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Quote Currency Code')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Grand Total')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Grand Total')
    ->addColumn('checkout_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Checkout Method')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Customer Id')
    ->addColumn('customer_tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Customer Tax Class Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Customer Group Id')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Email')
    ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Customer Prefix')
    ->addColumn('customer_firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Firstname')
    ->addColumn('customer_middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Customer Middlename')
    ->addColumn('customer_lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Lastname')
    ->addColumn('customer_suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Customer Suffix')
    ->addColumn('customer_dob', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
    ], 'Customer Dob')
    ->addColumn('customer_note', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Note')
    ->addColumn('customer_note_notify', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '1',
    ], 'Customer Note Notify')
    ->addColumn('customer_is_guest', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Customer Is Guest')
    ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Remote Ip')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Applied Rule Ids')
    ->addColumn('reserved_order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => true,
    ], 'Reserved Order Id')
    ->addColumn('password_hash', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Password Hash')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Coupon Code')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Global Currency Code')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Global Rate')
    ->addColumn('base_to_quote_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base To Quote Rate')
    ->addColumn('customer_taxvat', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Taxvat')
    ->addColumn('customer_gender', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Customer Gender')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal')
    ->addColumn('subtotal_with_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal With Discount')
    ->addColumn('base_subtotal_with_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal With Discount')
    ->addColumn('is_changed', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Is Changed')
    ->addColumn('trigger_recollect', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Trigger Recollect')
    ->addColumn('ext_shipping_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Ext Shipping Info')
    ->addIndex(
        $installer->getIdxName('sales/quote', ['customer_id', 'store_id', 'is_active']),
        ['customer_id', 'store_id', 'is_active'],
    )
    ->addIndex(
        $installer->getIdxName('sales/quote', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote_address'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote_address'))
    ->addColumn('address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Address Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Quote Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer Id')
    ->addColumn('save_in_address_book', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'default'   => '0',
    ], 'Save In Address Book')
    ->addColumn('customer_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer Address Id')
    ->addColumn('address_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Address Type')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Email')
    ->addColumn('prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Prefix')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Firstname')
    ->addColumn('middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Middlename')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Lastname')
    ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Suffix')
    ->addColumn('company', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Company')
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Street')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'City')
    ->addColumn('region', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Region')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Region Id')
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Postcode')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Country Id')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Telephone')
    ->addColumn('fax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Fax')
    ->addColumn('same_as_billing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Same As Billing')
    ->addColumn('free_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Free Shipping')
    ->addColumn('collect_shipping_rates', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Collect Shipping Rates')
    ->addColumn('shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Method')
    ->addColumn('shipping_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Description')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Weight')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Subtotal')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Subtotal')
    ->addColumn('subtotal_with_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Subtotal With Discount')
    ->addColumn('base_subtotal_with_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Subtotal With Discount')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Tax Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Tax Amount')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Shipping Amount')
    ->addColumn('base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Shipping Amount')
    ->addColumn('shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Tax Amount')
    ->addColumn('base_shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Tax Amount')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Discount Amount')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Grand Total')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Grand Total')
    ->addColumn('customer_notes', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Customer Notes')
    ->addColumn('applied_taxes', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Applied Taxes')
    ->addColumn('discount_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Discount Description')
    ->addColumn('shipping_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Discount Amount')
    ->addColumn('base_shipping_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Discount Amount')
    ->addColumn('subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Subtotal Incl Tax')
    ->addColumn('base_subtotal_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Subtotal Total Incl Tax')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addColumn('shipping_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Hidden Tax Amount')
    ->addColumn('base_shipping_hidden_tax_amnt', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Hidden Tax Amount')
    ->addColumn('shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Incl Tax')
    ->addColumn('base_shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Shipping Incl Tax')
    ->addIndex(
        $installer->getIdxName('sales/quote_address', ['quote_id']),
        ['quote_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_address', 'quote_id', 'sales/quote', 'entity_id'),
        'quote_id',
        $installer->getTable('sales/quote'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote Address');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote_item'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Item Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Quote Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Product Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Parent Item Id')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Virtual')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Applied Rule Ids')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('free_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Free Shipping')
    ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Qty Decimal')
    ->addColumn('no_discount', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'No Discount')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Weight')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Qty')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Price')
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Price')
    ->addColumn('custom_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Custom Price')
    ->addColumn('discount_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Discount Percent')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Discount Amount')
    ->addColumn('tax_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Tax Percent')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Tax Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Tax Amount')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Row Total')
    ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Row Total')
    ->addColumn('row_total_with_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Row Total With Discount')
    ->addColumn('row_weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Row Weight')
    ->addColumn('product_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Product Type')
    ->addColumn('base_tax_before_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tax Before Discount')
    ->addColumn('tax_before_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Before Discount')
    ->addColumn('original_custom_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Original Custom Price')
    ->addColumn('redirect_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Redirect Url')
    ->addColumn('base_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Cost')
    ->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price Incl Tax')
    ->addColumn('base_price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price Incl Tax')
    ->addColumn('row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total Incl Tax')
    ->addColumn('base_row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total Incl Tax')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addIndex(
        $installer->getIdxName('sales/quote_item', ['parent_item_id']),
        ['parent_item_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/quote_item', ['product_id']),
        ['product_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/quote_item', ['quote_id']),
        ['quote_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/quote_item', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_item', 'parent_item_id', 'sales/quote_item', 'item_id'),
        'parent_item_id',
        $installer->getTable('sales/quote_item'),
        'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_item', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_item', 'quote_id', 'sales/quote', 'entity_id'),
        'quote_id',
        $installer->getTable('sales/quote'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_item', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote_address_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote_address_item'))
    ->addColumn('address_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Address Item Id')
    ->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Parent Item Id')
    ->addColumn('quote_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Quote Address Id')
    ->addColumn('quote_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Quote Item Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Applied Rule Ids')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Weight')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Qty')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Discount Amount')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Tax Amount')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Row Total')
    ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Base Row Total')
    ->addColumn('row_total_with_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Row Total With Discount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Discount Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Base Tax Amount')
    ->addColumn('row_weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'default'   => '0.0000',
    ], 'Row Weight')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Product Id')
    ->addColumn('super_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Super Product Id')
    ->addColumn('parent_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Parent Product Id')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sku')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Image')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('free_shipping', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Free Shipping')
    ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Is Qty Decimal')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('discount_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Discount Percent')
    ->addColumn('no_discount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'No Discount')
    ->addColumn('tax_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Percent')
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price')
    ->addColumn('base_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Cost')
    ->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price Incl Tax')
    ->addColumn('base_price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Price Incl Tax')
    ->addColumn('row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Row Total Incl Tax')
    ->addColumn('base_row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Row Total Incl Tax')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Hidden Tax Amount')
    ->addIndex(
        $installer->getIdxName('sales/quote_address_item', ['quote_address_id']),
        ['quote_address_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/quote_address_item', ['parent_item_id']),
        ['parent_item_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/quote_address_item', ['quote_item_id']),
        ['quote_item_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/quote_address_item',
            'quote_address_id',
            'sales/quote_address',
            'address_id',
        ),
        'quote_address_id',
        $installer->getTable('sales/quote_address'),
        'address_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/quote_address_item',
            'parent_item_id',
            'sales/quote_address_item',
            'address_item_id',
        ),
        'parent_item_id',
        $installer->getTable('sales/quote_address_item'),
        'address_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/quote_address_item',
            'quote_item_id',
            'sales/quote_item',
            'item_id',
        ),
        'quote_item_id',
        $installer->getTable('sales/quote_item'),
        'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote Address Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote_item_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote_item_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Id')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Item Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Product Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Code')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('sales/quote_item_option', ['item_id']),
        ['item_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_item_option', 'item_id', 'sales/quote_item', 'item_id'),
        'item_id',
        $installer->getTable('sales/quote_item'),
        'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote Item Option');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote_payment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote_payment'))
    ->addColumn('payment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Payment Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Quote Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Method')
    ->addColumn('cc_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Type')
    ->addColumn('cc_number_enc', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Number Enc')
    ->addColumn('cc_last4', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Last4')
    ->addColumn('cc_cid_enc', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Cid Enc')
    ->addColumn('cc_owner', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Owner')
    ->addColumn('cc_exp_month', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Cc Exp Month')
    ->addColumn('cc_exp_year', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Cc Exp Year')
    ->addColumn('cc_ss_owner', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Ss Owner')
    ->addColumn('cc_ss_start_month', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Cc Ss Start Month')
    ->addColumn('cc_ss_start_year', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Cc Ss Start Year')
    ->addColumn('po_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Po Number')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Data')
    ->addColumn('cc_ss_issue', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Cc Ss Issue')
    ->addColumn('additional_information', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Information')
    ->addIndex(
        $installer->getIdxName('sales/quote_payment', ['quote_id']),
        ['quote_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/quote_payment', 'quote_id', 'sales/quote', 'entity_id'),
        'quote_id',
        $installer->getTable('sales/quote'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote Payment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/quote_address_shipping_rate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/quote_address_shipping_rate'))
    ->addColumn('rate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rate Id')
    ->addColumn('address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Address Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('carrier', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Carrier')
    ->addColumn('carrier_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Carrier Title')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Code')
    ->addColumn('method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Method')
    ->addColumn('method_description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Method Description')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Price')
    ->addColumn('error_message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Error Message')
    ->addColumn('method_title', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Method Title')
    ->addIndex(
        $installer->getIdxName('sales/quote_address_shipping_rate', ['address_id']),
        ['address_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/quote_address_shipping_rate',
            'address_id',
            'sales/quote_address',
            'address_id',
        ),
        'address_id',
        $installer->getTable('sales/quote_address'),
        'address_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Flat Quote Shipping Rate');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/invoiced_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/invoiced_aggregated'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Status')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('orders_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Orders Invoiced')
    ->addColumn('invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Invoiced')
    ->addColumn('invoiced_captured', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Invoiced Captured')
    ->addColumn('invoiced_not_captured', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Invoiced Not Captured')
    ->addIndex(
        $installer->getIdxName(
            'sales/invoiced_aggregated',
            ['period', 'store_id', 'order_status'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoiced_aggregated', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoiced_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Invoiced Aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/invoiced_aggregated_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/invoiced_aggregated_order'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => false,
        'default'   => '',
    ], 'Order Status')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('orders_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Orders Invoiced')
    ->addColumn('invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Invoiced')
    ->addColumn('invoiced_captured', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Invoiced Captured')
    ->addColumn('invoiced_not_captured', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Invoiced Not Captured')
    ->addIndex(
        $installer->getIdxName(
            'sales/invoiced_aggregated_order',
            ['period', 'store_id', 'order_status'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/invoiced_aggregated_order', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/invoiced_aggregated_order', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Invoiced Aggregated Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_aggregated_created'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_aggregated_created'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => false,
        'default'   => '',
    ], 'Order Status')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('total_qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Qty Ordered')
    ->addColumn('total_qty_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Qty Invoiced')
    ->addColumn('total_income_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Income Amount')
    ->addColumn('total_revenue_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Revenue Amount')
    ->addColumn('total_profit_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Profit Amount')
    ->addColumn('total_invoiced_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Invoiced Amount')
    ->addColumn('total_canceled_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Canceled Amount')
    ->addColumn('total_paid_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Paid Amount')
    ->addColumn('total_refunded_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Refunded Amount')
    ->addColumn('total_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Tax Amount')
    ->addColumn('total_tax_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Tax Amount Actual')
    ->addColumn('total_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Shipping Amount')
    ->addColumn('total_shipping_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Shipping Amount Actual')
    ->addColumn('total_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Discount Amount')
    ->addColumn('total_discount_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Total Discount Amount Actual')
    ->addIndex(
        $installer->getIdxName(
            'sales/order_aggregated_created',
            ['period', 'store_id', 'order_status'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/order_aggregated_created', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_aggregated_created', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Order Aggregated Created');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/payment_transaction'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/payment_transaction'))
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Transaction Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Parent Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Order Id')
    ->addColumn('payment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Payment Id')
    ->addColumn('txn_id', Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
    ], 'Txn Id')
    ->addColumn('parent_txn_id', Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
    ], 'Parent Txn Id')
    ->addColumn('txn_type', Varien_Db_Ddl_Table::TYPE_TEXT, 15, [
    ], 'Txn Type')
    ->addColumn('is_closed', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Closed')
    ->addColumn('additional_information', Varien_Db_Ddl_Table::TYPE_BLOB, '64K', [
    ], 'Additional Information')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName(
            'sales/payment_transaction',
            ['order_id', 'payment_id', 'txn_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['order_id', 'payment_id', 'txn_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/payment_transaction', ['order_id']),
        ['order_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/payment_transaction', ['parent_id']),
        ['parent_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/payment_transaction', ['payment_id']),
        ['payment_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/payment_transaction', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/payment_transaction',
            'parent_id',
            'sales/payment_transaction',
            'transaction_id',
        ),
        'parent_id',
        $installer->getTable('sales/payment_transaction'),
        'transaction_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/payment_transaction',
            'payment_id',
            'sales/order_payment',
            'entity_id',
        ),
        'payment_id',
        $installer->getTable('sales/order_payment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Payment Transaction');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/refunded_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/refunded_aggregated'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => false,
        'default'   => '',
    ], 'Order Status')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Refunded')
    ->addColumn('online_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Online Refunded')
    ->addColumn('offline_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Offline Refunded')
    ->addIndex(
        $installer->getIdxName(
            'sales/refunded_aggregated',
            ['period', 'store_id', 'order_status'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/refunded_aggregated', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/refunded_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Refunded Aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/refunded_aggregated_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/refunded_aggregated_order'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Status')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Refunded')
    ->addColumn('online_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Online Refunded')
    ->addColumn('offline_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Offline Refunded')
    ->addIndex(
        $installer->getIdxName(
            'sales/refunded_aggregated_order',
            ['period', 'store_id', 'order_status'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/refunded_aggregated_order', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/refunded_aggregated_order', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Refunded Aggregated Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipping_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipping_aggregated'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Status')
    ->addColumn('shipping_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Description')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('total_shipping', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Shipping')
    ->addColumn('total_shipping_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Shipping Actual')
    ->addIndex(
        $installer->getIdxName(
            'sales/shipping_aggregated',
            ['period', 'store_id', 'order_status', 'shipping_description'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status', 'shipping_description'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipping_aggregated', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipping_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Shipping Aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/shipping_aggregated_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/shipping_aggregated_order'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Order Status')
    ->addColumn('shipping_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Shipping Description')
    ->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Orders Count')
    ->addColumn('total_shipping', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Shipping')
    ->addColumn('total_shipping_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Total Shipping Actual')
    ->addIndex(
        $installer->getIdxName(
            'sales/shipping_aggregated_order',
            ['period', 'store_id', 'order_status', 'shipping_description'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'order_status', 'shipping_description'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/shipping_aggregated_order', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/shipping_aggregated_order', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Shipping Aggregated Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/bestsellers_aggregated_daily'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/bestsellers_aggregated_daily'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Product Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Product Name')
    ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Product Price')
    ->addColumn('qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Qty Ordered')
    ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Rating Pos')
    ->addIndex(
        $installer->getIdxName(
            'sales/bestsellers_aggregated_daily',
            ['period', 'store_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'product_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/bestsellers_aggregated_daily', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/bestsellers_aggregated_daily', ['product_id']),
        ['product_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/bestsellers_aggregated_daily', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/bestsellers_aggregated_daily',
            'product_id',
            'catalog/product',
            'entity_id',
        ),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Bestsellers Aggregated Daily');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/bestsellers_aggregated_monthly'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/bestsellers_aggregated_monthly'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Product Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Product Name')
    ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Product Price')
    ->addColumn('qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Qty Ordered')
    ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Rating Pos')
    ->addIndex(
        $installer->getIdxName(
            'sales/bestsellers_aggregated_monthly',
            ['period', 'store_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'product_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/bestsellers_aggregated_monthly', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/bestsellers_aggregated_monthly', ['product_id']),
        ['product_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/bestsellers_aggregated_monthly',
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/bestsellers_aggregated_monthly',
            'product_id',
            'catalog/product',
            'entity_id',
        ),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Bestsellers Aggregated Monthly');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/bestsellers_aggregated_yearly'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/bestsellers_aggregated_yearly'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Product Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Product Name')
    ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Product Price')
    ->addColumn('qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Qty Ordered')
    ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Rating Pos')
    ->addIndex(
        $installer->getIdxName(
            'sales/bestsellers_aggregated_yearly',
            ['period', 'store_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['period', 'store_id', 'product_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/bestsellers_aggregated_yearly', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/bestsellers_aggregated_yearly', ['product_id']),
        ['product_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/bestsellers_aggregated_yearly',
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/bestsellers_aggregated_yearly',
            'product_id',
            'catalog/product',
            'entity_id',
        ),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Bestsellers Aggregated Yearly');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/billing_agreement'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/billing_agreement'))
    ->addColumn('agreement_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Agreement Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Customer Id')
    ->addColumn('method_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
    ], 'Method Code')
    ->addColumn('reference_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
    ], 'Reference Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => false,
    ], 'Status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('agreement_label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Agreement Label')
    ->addIndex(
        $installer->getIdxName('sales/billing_agreement', ['customer_id']),
        ['customer_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/billing_agreement', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/billing_agreement', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/billing_agreement', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Billing Agreement');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/billing_agreement_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/billing_agreement_order'))
    ->addColumn('agreement_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Agreement Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Order Id')
    ->addIndex(
        $installer->getIdxName('sales/billing_agreement_order', ['order_id']),
        ['order_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/billing_agreement_order',
            'agreement_id',
            'sales/billing_agreement',
            'agreement_id',
        ),
        'agreement_id',
        $installer->getTable('sales/billing_agreement'),
        'agreement_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/billing_agreement_order', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Billing Agreement Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/recurring_profile'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/recurring_profile'))
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Profile Id')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => false,
    ], 'State')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store Id')
    ->addColumn('method_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
    ], 'Method Code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Updated At')
    ->addColumn('reference_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Reference Id')
    ->addColumn('subscriber_name', Varien_Db_Ddl_Table::TYPE_TEXT, 150, [
    ], 'Subscriber Name')
    ->addColumn('start_datetime', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Start Datetime')
    ->addColumn('internal_reference_id', Varien_Db_Ddl_Table::TYPE_TEXT, 42, [
        'nullable'  => false,
    ], 'Internal Reference Id')
    ->addColumn('schedule_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Schedule Description')
    ->addColumn('suspension_threshold', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Suspension Threshold')
    ->addColumn('bill_failed_later', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Bill Failed Later')
    ->addColumn('period_unit', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => false,
    ], 'Period Unit')
    ->addColumn('period_frequency', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Period Frequency')
    ->addColumn('period_max_cycles', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Period Max Cycles')
    ->addColumn('billing_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Billing Amount')
    ->addColumn('trial_period_unit', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
    ], 'Trial Period Unit')
    ->addColumn('trial_period_frequency', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Trial Period Frequency')
    ->addColumn('trial_period_max_cycles', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Trial Period Max Cycles')
    ->addColumn('trial_billing_amount', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
    ], 'Trial Billing Amount')
    ->addColumn('currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
        'nullable'  => false,
    ], 'Currency Code')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Shipping Amount')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tax Amount')
    ->addColumn('init_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Init Amount')
    ->addColumn('init_may_fail', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Init May Fail')
    ->addColumn('order_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Order Info')
    ->addColumn('order_item_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Order Item Info')
    ->addColumn('billing_address_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Billing Address Info')
    ->addColumn('shipping_address_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Shipping Address Info')
    ->addColumn('profile_vendor_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Profile Vendor Info')
    ->addColumn('additional_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Additional Info')
    ->addIndex(
        $installer->getIdxName(
            'sales/recurring_profile',
            ['internal_reference_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['internal_reference_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/recurring_profile', ['customer_id']),
        ['customer_id'],
    )
    ->addIndex(
        $installer->getIdxName('sales/recurring_profile', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/recurring_profile', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/recurring_profile', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Recurring Profile');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/recurring_profile_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/recurring_profile_order'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Link Id')
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Profile Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Order Id')
    ->addIndex(
        $installer->getIdxName(
            'sales/recurring_profile_order',
            ['profile_id', 'order_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['profile_id', 'order_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('sales/recurring_profile_order', ['order_id']),
        ['order_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/recurring_profile_order',
            'order_id',
            'sales/order',
            'entity_id',
        ),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales/recurring_profile_order',
            'profile_id',
            'sales/recurring_profile',
            'profile_id',
        ),
        'profile_id',
        $installer->getTable('sales/recurring_profile'),
        'profile_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Recurring Profile Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_tax'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_tax'))
    ->addColumn('tax_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Tax Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Order Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Code')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Title')
    ->addColumn('percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Percent')
    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Amount')
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
    ], 'Priority')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
    ], 'Position')
    ->addColumn('base_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Amount')
    ->addColumn('process', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
    ], 'Process')
    ->addColumn('base_real_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Real Amount')
    ->addColumn('hidden', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Hidden')
    ->addIndex(
        $installer->getIdxName('sales/order_tax', ['order_id', 'priority', 'position']),
        ['order_id', 'priority', 'position'],
    )
    ->setComment('Sales Order Tax Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_status'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_status'))
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Status')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
        'nullable'  => false,
    ], 'Label')
    ->setComment('Sales Order Status Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_status_state'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_status_state'))
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Status')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Label')
    ->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Default')
    ->addForeignKey(
        $installer->getFkName('sales/order_status_state', 'status', 'sales/order_status', 'status'),
        'status',
        $installer->getTable('sales/order_status'),
        'status',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Order Status Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'sales/order_status_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales/order_status_label'))
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Status')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store Id')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
        'nullable'  => false,
    ], 'Label')
    ->addIndex(
        $installer->getIdxName('sales/order_status_label', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_status_label', 'status', 'sales/order_status', 'status'),
        'status',
        $installer->getTable('sales/order_status'),
        'status',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('sales/order_status_label', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Sales Order Status Label Table');
$installer->getConnection()->createTable($table);

/**
 * Install eav entity types to the eav/entity_type table
 */
$installer->addEntityType('order', [
    'entity_model'          => 'sales/order',
    'table'                 => 'sales/order',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true,
]);

$installer->addEntityType('invoice', [
    'entity_model'          => 'sales/order_invoice',
    'table'                 => 'sales/invoice',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true,
]);

$installer->addEntityType('creditmemo', [
    'entity_model'          => 'sales/order_creditmemo',
    'table'                 => 'sales/creditmemo',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true,
]);

$installer->addEntityType('shipment', [
    'entity_model'          => 'sales/order_shipment',
    'table'                 => 'sales/shipment',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true,
]);

$installer->endSetup();
