<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * @return array
     */
    public function getDefaultEntities()
    {
        return [
            'quote' => [
                'entity_model'  => 'sales/quote',
                'table'         => 'sales/quote',
                'attributes' => [
                    'entity_id'         => ['type' => 'static'],
                    'is_active'         => ['type' => 'static'],
                    'store_id'          => ['type' => 'static'],
                    'remote_ip'         => ['type' => 'static'],
                    'checkout_method'   => ['type' => 'static'],
                    'password_hash'     => ['type' => 'static'],
                    'quote_status_id'   => ['type' => 'static'],
                    'billing_address_id' => ['type' => 'static'],
                    'orig_order_id'     => ['type' => 'static'],
                    'converted_at'      => ['type' => 'static'],
                    'reserved_order_id' => ['type' => 'static'],

                    'coupon_code'           => ['type' => 'static'],
                    'global_currency_code'    => ['type' => 'static'],
                    'base_currency_code'    => ['type' => 'static'],
                    'store_currency_code'   => ['type' => 'static'],
                    'quote_currency_code'   => ['type' => 'static'],
                    'store_to_base_rate'    => ['type' => 'static'],
                    'store_to_quote_rate'   => ['type' => 'static'],
                    'base_to_global_rate'    => ['type' => 'static'],
                    'base_to_quote_rate'   => ['type' => 'static'],

                    'items_count' => ['type' => 'static'],
                    'items_qty' => ['type' => 'static'],

                    'custbalance_amount' => ['type' => 'static'],
                    'grand_total'       => ['type' => 'static'],
                    'base_grand_total'  => ['type' => 'static'],

                    'applied_rule_ids'  => ['type' => 'static'],

                    'is_virtual'        => ['type' => 'static'],
                    'is_multi_shipping' => ['type' => 'static'],
                    'is_multi_payment'  => ['type' => 'static'],

                    'customer_id'       => ['type' => 'static'],
                    'customer_tax_class_id' => ['type' => 'static'],
                    'customer_group_id' => ['type' => 'static'],
                    'customer_email'    => ['type' => 'static'],
                    'customer_prefix'   => ['type' => 'static'],
                    'customer_firstname' => ['type' => 'static'],
                    'customer_middlename' => ['type' => 'static'],
                    'customer_lastname' => ['type' => 'static'],
                    'customer_suffix'   => ['type' => 'static'],
                    'customer_note'     => ['type' => 'static'],
                    'customer_note_notify' => ['type' => 'static'],
                    'customer_is_guest' => ['type' => 'static'],
                ],
            ],

            'quote_item' => [
                'entity_model'  => 'sales/quote_item',
                'table'         => 'sales/quote_item',
                'attributes' => [
                    'parent_id'         => ['type' => 'static'],
                    'product_id'        => ['type' => 'static'],
                    'super_product_id'  => ['type' => 'static'],
                    'parent_product_id' => ['type' => 'static'],
                    'sku'               => ['type' => 'static'],
                    'name'              => ['type' => 'static'],
                    'description'       => ['type' => 'static'],

                    'weight'        => ['type' => 'static'],
                    'free_shipping' => ['type' => 'static'],
                    'qty'           => ['type' => 'static'],
                    'is_qty_decimal' => ['type' => 'static'],

                    'price'             => ['type' => 'static'],
                    'custom_price'      => ['type' => 'static'],
                    'discount_percent'  => ['type' => 'static'],
                    'discount_amount'   => ['type' => 'static'],
                    'no_discount'       => ['type' => 'static'],
                    'tax_percent'       => ['type' => 'static'],
                    'tax_amount'        => ['type' => 'static'],
                    'row_total'         => ['type' => 'static'],
                    'row_total_with_discount' => ['type' => 'static'],

                    'base_price'             => ['type' => 'static'],
                    'base_discount_amount'   => ['type' => 'static'],
                    'base_tax_amount'        => ['type' => 'static'],
                    'base_row_total'         => ['type' => 'static'],

                    'row_weight'        => ['type' => 'static'],
                    'applied_rule_ids'  => ['type' => 'static'],
                    'additional_data'   => ['type' => 'static'],

                    'tax_string'   => ['type' => 'text'],
                ],
            ],

            'quote_address' => [
                'entity_model'  => 'sales/quote_address',
                'table'         => 'sales/quote_address',
                'attributes' => [
                    'entity_id'     => ['type' => 'static'],
                    'parent_id'     => ['type' => 'static'],
                    'address_type'  => ['type' => 'static'],

                    'customer_id'   => ['type' => 'static'],
                    'customer_address_id' => ['type' => 'static'],
                    'save_in_address_book' => ['type' => 'static'],
                    'email'     => ['type' => 'static'],
                    'prefix'    => ['type' => 'static'],
                    'firstname' => ['type' => 'static'],
                    'middlename' => ['type' => 'static'],
                    'lastname'  => ['type' => 'static'],
                    'suffix'    => ['type' => 'static'],
                    'company'   => ['type' => 'static'],
                    'street'    => ['type' => 'static'],
                    'city'      => ['type' => 'static'],
                    'region'    => ['type' => 'static'],
                    'region_id' => ['type' => 'static'],
                    'postcode'  => ['type' => 'static'],
                    'country_id' => ['type' => 'static'],
                    'telephone' => ['type' => 'static'],
                    'fax'       => ['type' => 'static'],

                    'same_as_billing'   => ['type' => 'static'],
                    'free_shipping'     => ['type' => 'static'],
                    'weight'            => ['type' => 'static'],
                    'collect_shipping_rates' => ['type' => 'static'],

                    'shipping_method'       => ['type' => 'static'],
                    'shipping_description'  => ['type' => 'static'],

                    'subtotal'          => ['type' => 'static'],
                    'subtotal_with_discount' => ['type' => 'static'],
                    'tax_amount'        => ['type' => 'static'],
                    'shipping_amount'   => ['type' => 'static'],
                    'discount_amount'   => ['type' => 'static'],
                    'custbalance_amount' => ['type' => 'static'],
                    'grand_total'       => ['type' => 'static'],

                    'base_subtotal'             => ['type' => 'static'],
                    'base_subtotal_with_discount' => ['type' => 'static'],
                    'base_tax_amount'           => ['type' => 'static'],
                    'base_shipping_amount'      => ['type' => 'static'],
                    'base_discount_amount'      => ['type' => 'static'],
                    'base_custbalance_amount'   => ['type' => 'static'],
                    'base_grand_total'          => ['type' => 'static'],

                    'customer_notes' => ['type' => 'static'],

                    'tax_string'   => ['type' => 'text'],
                ],
            ],
            'quote_address_item' => [
                'entity_model'  => 'sales/quote_address_item',
                'table'         => 'sales/quote_entity',
                'attributes' => [
                    'parent_id'     => ['type' => 'static'],
                    'quote_item_id' => ['type' => 'int'],
                    'product_id'    => ['type' => 'int'],
                    'super_product_id'  => ['type' => 'int'],
                    'parent_product_id' => ['type' => 'int'],
                    'sku'   => [],
                    'image' => [],
                    'name'  => [],
                    'description' => ['type' => 'text'],

                    'weight'        => ['type' => 'decimal'],
                    'free_shipping' => ['type' => 'int'],
                    'qty'           => ['type' => 'decimal'],
                    'is_qty_decimal' => ['type' => 'int'],

                    'price'             => ['type' => 'decimal'],
                    'discount_percent'  => ['type' => 'decimal'],
                    'discount_amount'   => ['type' => 'decimal'],
                    'no_discount'       => ['type' => 'int'],
                    'tax_percent'       => ['type' => 'decimal'],
                    'tax_amount'        => ['type' => 'decimal'],
                    'row_total'         => ['type' => 'decimal'],
                    'row_total_with_discount' => ['type' => 'decimal'],

                    'base_price'             => ['type' => 'decimal'],
                    'base_discount_amount'   => ['type' => 'decimal'],
                    'base_tax_amount'        => ['type' => 'decimal'],
                    'base_row_total'         => ['type' => 'decimal'],

                    'row_weight'        => ['type' => 'decimal'],
                    'applied_rule_ids'  => [],
                    'additional_data'   => ['type' => 'text'],
                ],
            ],
            'quote_address_rate' => [
                'entity_model'  => 'sales/quote_address_rate',
                'table'         => 'sales/quote_entity',
                'attributes' => [
                    'parent_id'     => ['type' => 'static'],
                    'code'          => [],
                    'carrier'       => [],
                    'carrier_title' => [],
                    'method'        => [],
                    'method_description' => ['type' => 'text'],
                    'price'         => ['type' => 'decimal'],
                    'error_message' => ['type' => 'text'],
                ],
            ],
            'quote_payment' => [
                'entity_model'  => 'sales/quote_payment',
                'table'         => 'sales/quote_entity',
                'attributes' => [
                    'parent_id' => ['type' => 'static'],
                    'method'    => [],
                    'additional_data' => ['type' => 'text'],
                    'po_number' => [],
                    'cc_type'   => [],
                    'cc_number_enc' => [],
                    'cc_last4'  => [],
                    'cc_owner'  => [],
                    'cc_exp_month' => ['type' => 'int'],
                    'cc_exp_year' => ['type' => 'int'],
                    'cc_cid_enc' => [],
                    'cc_ss_issue' => [],
                    'cc_ss_start_month' => ['type' => 'int'],
                    'cc_ss_start_year' => ['type' => 'int'],
                ],
            ],

            'order' => [
                'entity_model'      => 'sales/order',
                'table' => 'sales/order',
                'increment_model' => 'eav/entity_increment_numeric',
                'increment_per_store' => true,
                'backend_prefix' => 'sales_entity/order_attribute_backend',
                'attributes' => [
                    'entity_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_attribute_backend_parent',
                    ],
                    'store_id'  => ['type' => 'static'],
                    'store_name' => ['type' => 'varchar'],
                    'remote_ip' => [],

                    'status'    => ['type' => 'varchar'],
                    'state'     => ['type' => 'varchar'],
                    'hold_before_status' => ['type' => 'varchar'],
                    'hold_before_state'  => ['type' => 'varchar'],

                    'relation_parent_id'        => ['type' => 'varchar'],
                    'relation_parent_real_id'   => ['type' => 'varchar'],
                    'relation_child_id'         => ['type' => 'varchar'],
                    'relation_child_real_id'    => ['type' => 'varchar'],
                    'original_increment_id'     => ['type' => 'varchar'],
                    'edit_increment'            => ['type' => 'int'],

                    'ext_order_id'         => ['type' => 'varchar'],
                    'ext_customer_id'      => ['type' => 'varchar'],

                    'quote_id' => ['type' => 'int'],
                    'quote_address_id' => ['type' => 'int'],
                    'billing_address_id' => ['type' => 'int', 'backend' => '_billing'],
                    'shipping_address_id' => ['type' => 'int', 'backend' => '_shipping'],

                    'coupon_code'       => [],
                    'applied_rule_ids'  => [],

                    'global_currency_code'    => [],
                    'base_currency_code'    => [],
                    'store_currency_code'   => [],
                    'order_currency_code'   => [],
                    'store_to_base_rate'    => ['type' => 'decimal'],
                    'store_to_order_rate'   => ['type' => 'decimal'],
                    'base_to_global_rate'    => ['type' => 'decimal'],
                    'base_to_order_rate'    => ['type' => 'decimal'],

                    'is_virtual'        => ['type' => 'int'],
                    'is_multi_payment'  => ['type' => 'int'],

                    'shipping_method' => [],
                    'shipping_description' => [],
                    'weight' => ['type' => 'decimal'],

                    'tax_amount'        => ['type' => 'static'],
                    'shipping_amount'   => ['type' => 'static'],
                    'discount_amount'   => ['type' => 'static'],
                    'custbalance_amount' => ['type' => 'decimal'],

                    'subtotal'          => ['type' => 'static'],
                    'grand_total'       => ['type' => 'static'],
                    'total_paid'        => ['type' => 'static'],
                    'total_due'         => ['type' => 'decimal'],
                    'total_refunded'    => ['type' => 'static'],
                    'total_qty_ordered' => ['type' => 'static'],
                    'total_canceled'    => ['type' => 'static'],
                    'total_invoiced'    => ['type' => 'static'],
                    'total_online_refunded' => ['type' => 'static'],
                    'total_offline_refunded' => ['type' => 'static'],
                    'adjustment_positive' => ['type' => 'decimal'],
                    'adjustment_negative' => ['type' => 'decimal'],

                    'base_tax_amount'        => ['type' => 'static'],
                    'base_shipping_amount'   => ['type' => 'static'],
                    'base_discount_amount'   => ['type' => 'static'],
                    'base_custbalance_amount' => ['type' => 'decimal'],

                    'base_subtotal'          => ['type' => 'static'],
                    'base_grand_total'       => ['type' => 'static'],
                    'base_total_paid'        => ['type' => 'static'],
                    'base_total_due'         => ['type' => 'decimal'],
                    'base_total_refunded'    => ['type' => 'static'],
                    'base_total_qty_ordered' => ['type' => 'static'],
                    'base_total_canceled'    => ['type' => 'static'],
                    'base_total_invoiced'    => ['type' => 'static'],
                    'base_total_online_refunded' => ['type' => 'static'],
                    'base_total_offline_refunded' => ['type' => 'static'],
                    'base_adjustment_positive' => ['type' => 'decimal'],
                    'base_adjustment_negative' => ['type' => 'decimal'],

                    'subtotal_refunded'     => ['type' => 'static'],
                    'subtotal_canceled'     => ['type' => 'static'],
                    'discount_refunded'     => ['type' => 'static'],
                    'discount_canceled'     => ['type' => 'static'],
                    'discount_invoiced'     => ['type' => 'static'],
                    'subtotal_invoiced'     => ['type' => 'static'],
                    'tax_refunded'     => ['type' => 'static'],
                    'tax_canceled'     => ['type' => 'static'],
                    'tax_invoiced'     => ['type' => 'static'],
                    'shipping_refunded'     => ['type' => 'static'],
                    'shipping_canceled'     => ['type' => 'static'],
                    'shipping_invoiced'     => ['type' => 'static'],
                    'base_subtotal_refunded'     => ['type' => 'static'],
                    'base_subtotal_canceled'     => ['type' => 'static'],
                    'base_discount_refunded'     => ['type' => 'static'],
                    'base_discount_canceled'     => ['type' => 'static'],
                    'base_discount_invoiced'     => ['type' => 'static'],
                    'base_subtotal_invoiced'     => ['type' => 'static'],
                    'base_tax_refunded'     => ['type' => 'static'],
                    'base_tax_canceled'     => ['type' => 'static'],
                    'base_tax_invoiced'     => ['type' => 'static'],
                    'base_shipping_refunded'     => ['type' => 'static'],
                    'base_shipping_canceled'     => ['type' => 'static'],
                    'base_shipping_invoiced'     => ['type' => 'static'],

                    'customer_id'       => ['type' => 'static', 'visible' => false],
                    'customer_group_id' => ['type' => 'int', 'visible' => false],
                    'customer_email'    => ['type' => 'varchar', 'visible' => false],
                    'customer_prefix'   => ['type' => 'varchar', 'visible' => false],
                    'customer_firstname' => ['type' => 'varchar', 'visible' => false],
                    'customer_middlename'   => ['type' => 'varchar', 'visible' => false],
                    'customer_lastname' => ['type' => 'varchar', 'visible' => false],
                    'customer_suffix'   => ['type' => 'varchar', 'visible' => false],
                    'customer_note'     => ['type' => 'text', 'visible' => false],
                    'customer_note_notify' => ['type' => 'int', 'visible' => false],
                    'customer_is_guest' => ['type' => 'int', 'visible' => false],
                    'email_sent' => ['type' => 'int', 'visible' => false],
                ],
            ],
            'order_address' => [
                'entity_model'      => 'sales/order_address',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => ['type' => 'static', 'backend' => 'sales_entity/order_attribute_backend_child'],
                    'quote_address_id' => ['type' => 'int'],
                    'address_type' => [],
                    'customer_id' => ['type' => 'int'],
                    'customer_address_id' => ['type' => 'int'],
                    'email' => [],
                    'prefix'    => [],
                    'firstname' => [],
                    'middlename' => [],
                    'lastname'  => [],
                    'suffix'    => [],
                    'company'   => [],
                    'street'    => [],
                    'city'      => [],
                    'region'    => [],
                    'region_id' => ['type' => 'int'],
                    'postcode'  => [],
                    'country_id' => ['type' => 'varchar'],
                    'telephone' => [],
                    'fax'       => [],
                    'tax_string'   => ['type' => 'text'],
                ],
            ],
            'order_item' => [
                'entity_model'      => 'sales/order_item',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_attribute_backend_child',
                    ],

                    'quote_item_id'     => ['type' => 'int'],
                    'product_id'        => ['type' => 'int'],
                    'super_product_id'  => ['type' => 'int'],
                    'parent_product_id' => ['type' => 'int'],
                    'sku'               => [],
                    'name'              => [],
                    'description'       => ['type' => 'text'],
                    'weight'            => ['type' => 'decimal'],

                    'is_qty_decimal'    => ['type' => 'int'],
                    'qty_ordered'       => ['type' => 'decimal'],
                    'qty_backordered'   => ['type' => 'decimal'],
                    'qty_invoiced'      => ['type' => 'decimal'],
                    'qty_canceled'      => ['type' => 'decimal'],
                    'qty_shipped'       => ['type' => 'decimal'],
                    'qty_refunded'      => ['type' => 'decimal'],

                    'original_price'    => ['type' => 'decimal'],
                    'price'             => ['type' => 'decimal'],
                    'cost'              => ['type' => 'decimal'],
                    'is_nominal'        => ['type' => 'int'],

                    'discount_percent'  => ['type' => 'decimal'],
                    'discount_amount'   => ['type' => 'decimal'],
                    'discount_invoiced' => ['type' => 'decimal'],

                    'tax_percent'       => ['type' => 'decimal'],
                    'tax_amount'        => ['type' => 'decimal'],
                    'tax_invoiced'      => ['type' => 'decimal'],
                    'tax_string'        => ['type' => 'text'],

                    'row_total'         => ['type' => 'decimal'],
                    'row_weight'        => ['type' => 'decimal'],
                    'row_invoiced'      => ['type' => 'decimal'],
                    'invoiced_total'    => ['type' => 'decimal'],
                    'amount_refunded'   => ['type' => 'decimal'],

                    'base_price'             => ['type' => 'decimal'],
                    'base_original_price'    => ['type' => 'decimal'],
                    'base_discount_amount'   => ['type' => 'decimal'],
                    'base_discount_invoiced' => ['type' => 'decimal'],
                    'base_tax_amount'        => ['type' => 'decimal'],
                    'base_tax_invoiced'      => ['type' => 'decimal'],
                    'base_row_total'         => ['type' => 'decimal'],
                    'base_row_invoiced'      => ['type' => 'decimal'],
                    'base_invoiced_total'    => ['type' => 'decimal'],
                    'base_amount_refunded'   => ['type' => 'decimal'],

                    'applied_rule_ids'  => [],
                    'additional_data'   => ['type' => 'text'],
                ],
            ],
            'order_payment' => [
                'entity_model'      => 'sales/order_payment',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_attribute_backend_child',
                    ],
                    'quote_payment_id'      => ['type' => 'int'],
                    'method'                => [],
                    'additional_data'       => ['type' => 'text'],
                    'last_trans_id'         => [],
                    'po_number'     => [],

                    'cc_type'       => [],
                    'cc_number_enc' => [],
                    'cc_last4'      => [],
                    'cc_owner'      => [],
                    'cc_exp_month'  => [],
                    'cc_exp_year'   => [],

                    'cc_ss_issue' => [],
                    'cc_ss_start_month' => [],
                    'cc_ss_start_year' => [],

                    'cc_status'             => [],
                    'cc_status_description' => [],
                    'cc_trans_id'           => [],
                    'cc_approval'           => [],
                    'cc_avs_status'         => [],
                    'cc_cid_status'         => [],

                    'cc_debug_request_body' => [],
                    'cc_debug_response_body' => [],
                    'cc_debug_response_serialized' => [],

                    'anet_trans_method'     => [],
                    'echeck_routing_number' => [],
                    'echeck_bank_name'      => [],
                    'echeck_account_type'   => [],
                    'echeck_account_name'   => [],
                    'echeck_type'           => [],

                    'amount_ordered'    => ['type' => 'decimal'],
                    'amount_authorized' => ['type' => 'decimal'],
                    'amount_paid'       => ['type' => 'decimal'],
                    'amount_canceled'   => ['type' => 'decimal'],
                    'amount_refunded'   => ['type' => 'decimal'],
                    'shipping_amount'   => ['type' => 'decimal'],
                    'shipping_captured' => ['type' => 'decimal'],
                    'shipping_refunded' => ['type' => 'decimal'],

                    'base_amount_ordered'    => ['type' => 'decimal'],
                    'base_amount_authorized' => ['type' => 'decimal'],
                    'base_amount_paid'       => ['type' => 'decimal'],
                    'base_amount_canceled'   => ['type' => 'decimal'],
                    'base_amount_refunded'   => ['type' => 'decimal'],
                    'base_shipping_amount'   => ['type' => 'decimal'],
                    'base_shipping_captured' => ['type' => 'decimal'],
                    'base_shipping_refunded' => ['type' => 'decimal'],
                ],
            ],

            'order_status_history' => [
                'entity_model'      => 'sales/order_status_history',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_attribute_backend_child',
                    ],
                    'status'    => ['type' => 'varchar'],
                    'comment'   => ['type' => 'text'],
                    'is_customer_notified' => ['type' => 'int'],
                ],
            ],

            'invoice' => [
                'entity_model'      => 'sales/order_invoice',
                'table'             => 'sales/order_entity',
                'increment_model'   => 'eav/entity_increment_numeric',
                'increment_per_store' => true,
                'backend_prefix'    => 'sales_entity/order_attribute_backend',
                'attributes' => [
                    'entity_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_invoice_attribute_backend_parent',
                    ],

                    'state'    => ['type' => 'int'],
                    'is_used_for_refund' => ['type' => 'int'],
                    'transaction_id' => [],

                    'order_id'              => [
                        'type' => 'int',
                        'backend' => 'sales_entity/order_invoice_attribute_backend_order',
                    ],

                    'billing_address_id'    => ['type' => 'int'],
                    'shipping_address_id'   => ['type' => 'int'],

                    'global_currency_code'    => [],
                    'base_currency_code'    => [],
                    'store_currency_code'   => [],
                    'order_currency_code'   => [],
                    'store_to_base_rate'    => ['type' => 'decimal'],
                    'store_to_order_rate'   => ['type' => 'decimal'],
                    'base_to_global_rate'    => ['type' => 'decimal'],
                    'base_to_order_rate'   => ['type' => 'decimal'],

                    'subtotal'          => ['type' => 'decimal'],
                    'discount_amount'   => ['type' => 'decimal'],
                    'tax_amount'        => ['type' => 'decimal'],
                    'shipping_amount'   => ['type' => 'decimal'],
                    'grand_total'       => ['type' => 'decimal'],
                    'total_qty'         => ['type' => 'decimal'],

                    'can_void_flag'     => ['type' => 'int'],

                    'base_subtotal'          => ['type' => 'decimal'],
                    'base_discount_amount'   => ['type' => 'decimal'],
                    'base_tax_amount'        => ['type' => 'decimal'],
                    'base_shipping_amount'   => ['type' => 'decimal'],
                    'base_grand_total'       => ['type' => 'decimal'],
                    'email_sent' => ['type' => 'int'],
                ],
            ],

            'invoice_item' => [
                'entity_model'      => 'sales/order_invoice_item',
                //'table'=>'sales/invoice',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id'     => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_invoice_attribute_backend_child',
                    ],
                    'order_item_id' => ['type' => 'int'],
                    'product_id'    => ['type' => 'int'],
                    'name'          => [],
                    'description'   => ['type' => 'text'],
                    'sku'           => [],
                    'qty'           => ['type' => 'decimal'],
                    'cost'          => ['type' => 'decimal'],
                    'price'         => ['type' => 'decimal'],
                    'discount_amount' => ['type' => 'decimal'],
                    'tax_amount'    => ['type' => 'decimal'],
                    'row_total'     => ['type' => 'decimal'],

                    'base_price'         => ['type' => 'decimal'],
                    'base_discount_amount' => ['type' => 'decimal'],
                    'base_tax_amount'    => ['type' => 'decimal'],
                    'base_row_total'     => ['type' => 'decimal'],

                    'additional_data'   => ['type' => 'text'],
                ],
            ],

            'invoice_comment' => [
                'entity_model'      => 'sales/order_invoice_comment',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_invoice_attribute_backend_child',
                    ],
                    'comment' => ['type' => 'text'],
                    'is_customer_notified' => ['type' => 'int'],
                ],
            ],

            'shipment' => [
                'entity_model'      => 'sales/order_shipment',
                //'table'=>'sales/shipment',
                'table' => 'sales/order_entity',
                'increment_model' => 'eav/entity_increment_numeric',
                'increment_per_store' => true,
                'backend_prefix' => 'sales_entity/order_attribute_backend',
                'attributes' => [
                    'entity_id'     => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_shipment_attribute_backend_parent',
                    ],

                    'customer_id'   => ['type' => 'int'],
                    'order_id'      => ['type' => 'int'],
                    'shipment_status'     => ['type' => 'int'],
                    'billing_address_id'    => ['type' => 'int'],
                    'shipping_address_id'   => ['type' => 'int'],

                    'total_qty'         => ['type' => 'decimal'],
                    'total_weight'      => ['type' => 'decimal'],
                    'email_sent'        => ['type' => 'int'],
                ],
            ],

            'shipment_item' => [
                'entity_model'      => 'sales/order_shipment_item',
                //'table'=>'sales/shipment',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id'     => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_shipment_attribute_backend_child',
                    ],
                    'order_item_id' => ['type' => 'int'],
                    'product_id'    => ['type' => 'int'],
                    'name'          => [],
                    'description'   => ['type' => 'text'],
                    'sku'           => [],
                    'qty'           => ['type' => 'decimal'],
                    'price'         => ['type' => 'decimal'],
                    'weight'        => ['type' => 'decimal'],
                    'row_total'     => ['type' => 'decimal'],

                    'additional_data'   => ['type' => 'text'],
                ],
            ],

            'shipment_comment' => [
                'entity_model'      => 'sales/order_shipment_comment',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_shipment_attribute_backend_child',
                    ],
                    'comment' => ['type' => 'text'],
                    'is_customer_notified' => ['type' => 'int'],
                ],
            ],

            'shipment_track' => [
                'entity_model'      => 'sales/order_shipment_track',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id'     => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_shipment_attribute_backend_child',
                    ],
                    'order_id'      => ['type' => 'int'],
                    'number'        => ['type' => 'text'],
                    'carrier_code'  => ['type' => 'varchar'],
                    'title'         => ['type' => 'varchar'],
                    'description'   => ['type' => 'text'],
                    'qty'           => ['type' => 'decimal'],
                    'weight'        => ['type' => 'decimal'],
                ],
            ],

            'creditmemo' => [
                'entity_model'      => 'sales/order_creditmemo',
                //'table'=>'sales/creditmemo',
                'table' => 'sales/order_entity',
                'increment_model' => 'eav/entity_increment_numeric',
                'increment_per_store' => true,
                'backend_prefix' => 'sales_entity/order_attribute_backend',
                'attributes' => [
                    'entity_id'     => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_creditmemo_attribute_backend_parent',
                    ],
                    'state'         => ['type' => 'int'],
                    'invoice_id'    => ['type' => 'int'],
                    'transaction_id' => [],

                    'order_id'      => ['type' => 'int'],
                    'creditmemo_status'     => ['type' => 'int'],
                    'billing_address_id'    => ['type' => 'int'],
                    'shipping_address_id'   => ['type' => 'int'],

                    'global_currency_code'    => [],
                    'base_currency_code'    => [],
                    'store_currency_code'   => [],
                    'order_currency_code'   => [],
                    'store_to_base_rate'    => ['type' => 'decimal'],
                    'store_to_order_rate'   => ['type' => 'decimal'],
                    'base_to_global_rate'    => ['type' => 'decimal'],
                    'base_to_order_rate'   => ['type' => 'decimal'],

                    'subtotal'          => ['type' => 'decimal'],
                    'discount_amount'   => ['type' => 'decimal'],
                    'tax_amount'        => ['type' => 'decimal'],
                    'shipping_amount'   => ['type' => 'decimal'],
                    'adjustment'        => ['type' => 'decimal'],
                    'adjustment_positive' => ['type' => 'decimal'],
                    'adjustment_negative' => ['type' => 'decimal'],
                    'grand_total'       => ['type' => 'decimal'],

                    'base_subtotal'          => ['type' => 'decimal'],
                    'base_discount_amount'   => ['type' => 'decimal'],
                    'base_tax_amount'        => ['type' => 'decimal'],
                    'base_shipping_amount'   => ['type' => 'decimal'],
                    'base_adjustment'        => ['type' => 'decimal'],
                    'base_adjustment_positive' => ['type' => 'decimal'],
                    'base_adjustment_negative' => ['type' => 'decimal'],
                    'base_grand_total'       => ['type' => 'decimal'],
                    'email_sent' => ['type' => 'int'],
                ],
            ],

            'creditmemo_item' => [
                'entity_model'      => 'sales/order_creditmemo_item',
                //'table'=>'sales/creditmemo',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id'     => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_creditmemo_attribute_backend_child',
                    ],
                    'order_item_id' => ['type' => 'int'],
                    'product_id'    => ['type' => 'int'],
                    'name'          => [],
                    'description'   => ['type' => 'text'],
                    'sku'           => [],
                    'qty'           => ['type' => 'decimal'],
                    'cost'          => ['type' => 'decimal'],
                    'price'         => ['type' => 'decimal'],
                    'discount_amount' => ['type' => 'decimal'],
                    'tax_amount'    => ['type' => 'decimal'],
                    'row_total'     => ['type' => 'decimal'],

                    'base_price'         => ['type' => 'decimal'],
                    'base_discount_amount' => ['type' => 'decimal'],
                    'base_tax_amount'    => ['type' => 'decimal'],
                    'base_row_total'     => ['type' => 'decimal'],

                    'additional_data'   => ['type' => 'text'],
                ],
            ],

            'creditmemo_comment' => [
                'entity_model'      => 'sales/order_creditmemo_comment',
                'table' => 'sales/order_entity',
                'attributes' => [
                    'parent_id' => [
                        'type' => 'static',
                        'backend' => 'sales_entity/order_creditmemo_attribute_backend_child',
                    ],
                    'comment' => ['type' => 'text'],
                    'is_customer_notified' => ['type' => 'int'],
                ],
            ],

        ];
    }
}
