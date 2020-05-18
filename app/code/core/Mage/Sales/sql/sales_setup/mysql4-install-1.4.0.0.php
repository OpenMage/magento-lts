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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
/* Orders */
CREATE TABLE `{$installer->getTable('sales_flat_order')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `state` varchar(32) default NULL,
    `status` varchar(32) default NULL,
    `coupon_code` varchar(255) default NULL,
    `protect_code` varchar(255) default NULL,
    `shipping_description` varchar(255) default NULL,
    `is_virtual` tinyint(1) unsigned default NULL,
    `store_id` smallint(5) unsigned default NULL,
    `customer_id` int(10) unsigned default NULL,
    `base_discount_amount` decimal(12,4) default NULL,
    `base_discount_canceled` decimal(12,4) default NULL,
    `base_discount_invoiced` decimal(12,4) default NULL,
    `base_discount_refunded` decimal(12,4) default NULL,
    `base_grand_total` decimal(12,4) default NULL,
    `base_shipping_amount` decimal(12,4) default NULL,
    `base_shipping_canceled` decimal(12,4) default NULL,
    `base_shipping_invoiced` decimal(12,4) default NULL,
    `base_shipping_refunded` decimal(12,4) default NULL,
    `base_shipping_tax_amount` decimal(12,4) default NULL,
    `base_shipping_tax_refunded` decimal(12,4) default NULL,
    `base_subtotal` decimal(12,4) default NULL,
    `base_subtotal_canceled` decimal(12,4) default NULL,
    `base_subtotal_invoiced` decimal(12,4) default NULL,
    `base_subtotal_refunded` decimal(12,4) default NULL,
    `base_tax_amount` decimal(12,4) default NULL,
    `base_tax_canceled` decimal(12,4) default NULL,
    `base_tax_invoiced` decimal(12,4) default NULL,
    `base_tax_refunded` decimal(12,4) default NULL,
    `base_to_global_rate` decimal(12,4) default NULL,
    `base_to_order_rate` decimal(12,4) default NULL,
    `base_total_canceled` decimal(12,4) default NULL,
    `base_total_invoiced` decimal(12,4) default NULL,
    `base_total_invoiced_cost` decimal(12,4) default NULL,
    `base_total_offline_refunded` decimal(12,4) default NULL,
    `base_total_online_refunded` decimal(12,4) default NULL,
    `base_total_paid` decimal(12,4) default NULL,
    `base_total_qty_ordered` decimal(12,4) default NULL,
    `base_total_refunded` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) default NULL,
    `discount_canceled` decimal(12,4) default NULL,
    `discount_invoiced` decimal(12,4) default NULL,
    `discount_refunded` decimal(12,4) default NULL,
    `grand_total` decimal(12,4) default NULL,
    `shipping_amount` decimal(12,4) default NULL,
    `shipping_canceled` decimal(12,4) default NULL,
    `shipping_invoiced` decimal(12,4) default NULL,
    `shipping_refunded` decimal(12,4) default NULL,
    `shipping_tax_amount` decimal(12,4) default NULL,
    `shipping_tax_refunded` decimal(12,4) default NULL,
    `store_to_base_rate` decimal(12,4) default NULL,
    `store_to_order_rate` decimal(12,4) default NULL,
    `subtotal` decimal(12,4) default NULL,
    `subtotal_canceled` decimal(12,4) default NULL,
    `subtotal_invoiced` decimal(12,4) default NULL,
    `subtotal_refunded` decimal(12,4) default NULL,
    `tax_amount` decimal(12,4) default NULL,
    `tax_canceled` decimal(12,4) default NULL,
    `tax_invoiced` decimal(12,4) default NULL,
    `tax_refunded` decimal(12,4) default NULL,
    `total_canceled` decimal(12,4) default NULL,
    `total_invoiced` decimal(12,4) default NULL,
    `total_offline_refunded` decimal(12,4) default NULL,
    `total_online_refunded` decimal(12,4) default NULL,
    `total_paid` decimal(12,4) default NULL,
    `total_qty_ordered` decimal(12,4) default NULL,
    `total_refunded` decimal(12,4) default NULL,
    `can_ship_partially` tinyint(1) unsigned default NULL,
    `can_ship_partially_item` tinyint(1) unsigned default NULL,
    `customer_is_guest` tinyint(1) unsigned default NULL,
    `customer_note_notify` tinyint(1) unsigned default NULL,
    `billing_address_id` int(10) default NULL,
    `customer_group_id` smallint(5) default NULL,
    `edit_increment` int(10) default NULL,
    `email_sent` tinyint(1) unsigned default NULL,
    `forced_do_shipment_with_invoice` tinyint(1) unsigned default NULL,
    `gift_message_id` int(10) default NULL,
    `payment_authorization_expiration` int(10) default NULL,
    `paypal_ipn_customer_notified` int(10) default NULL,
    `quote_address_id` int(10) default NULL,
    `quote_id` int(10) default NULL,
    `shipping_address_id` int(10) default NULL,
    `adjustment_negative` decimal(12,4) default NULL,
    `adjustment_positive` decimal(12,4) default NULL,
    `base_adjustment_negative` decimal(12,4) default NULL,
    `base_adjustment_positive` decimal(12,4) default NULL,
    `base_shipping_discount_amount` decimal(12,4) default NULL,
    `base_subtotal_incl_tax` decimal(12,4) default NULL,
    `base_total_due` decimal(12,4) default NULL,
    `payment_authorization_amount` decimal(12,4) default NULL,
    `shipping_discount_amount` decimal(12,4) default NULL,
    `subtotal_incl_tax` decimal(12,4) default NULL,
    `total_due` decimal(12,4) default NULL,
    `weight` decimal(12,4) default NULL,
    `customer_dob` datetime default NULL,
    `increment_id` varchar(50) default NULL,
    `applied_rule_ids` varchar(255) default NULL,
    `base_currency_code` char(3) default NULL,
    `customer_email` varchar(255) default NULL,
    `customer_firstname` varchar(255) default NULL,
    `customer_lastname` varchar(255) default NULL,
    `customer_middlename` varchar(255) default NULL,
    `customer_prefix` varchar(255) default NULL,
    `customer_suffix` varchar(255) default NULL,
    `customer_taxvat` varchar(255) default NULL,
    `discount_description` varchar(255) default NULL,
    `ext_customer_id` varchar(255) default NULL,
    `ext_order_id` varchar(255) default NULL,
    `global_currency_code` char(3) default NULL,
    `hold_before_state` varchar(255) default NULL,
    `hold_before_status` varchar(255) default NULL,
    `order_currency_code` varchar(255) default NULL,
    `original_increment_id` varchar(50) default NULL,
    `relation_child_id` varchar(32) default NULL,
    `relation_child_real_id` varchar(32) default NULL,
    `relation_parent_id` varchar(32) default NULL,
    `relation_parent_real_id` varchar(32) default NULL,
    `remote_ip` varchar(255) default NULL,
    `shipping_method` varchar(255) default NULL,
    `store_currency_code` char(3) default NULL,
    `store_name` varchar(255) default NULL,
    `x_forwarded_for` varchar(255) default NULL,
    `customer_note` text,
    `created_at` datetime default NULL,
    `updated_at` datetime default NULL,
    `total_item_count` smallint(5) unsigned DEFAULT '0',
    `customer_gender` int(11) DEFAULT NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STATUS` (`status`),
    KEY `IDX_STATE` (`state`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_CUSTOMER_ID` (`customer_id`),
    KEY `IDX_EXT_ORDER_ID` (`ext_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Orders Grid */
CREATE TABLE `{$installer->getTable('sales_flat_order_grid')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `status` varchar(32) default NULL,
    `store_id` smallint(5) unsigned default NULL,
    `customer_id` int(10) unsigned default NULL,
    `base_grand_total` decimal(12,4) default NULL,
    `base_total_paid` decimal(12,4) default NULL,
    `grand_total` decimal(12,4) default NULL,
    `total_paid` decimal(12,4) default NULL,
    `increment_id` varchar(50) default NULL,
    `base_currency_code` char(3) default NULL,
    `order_currency_code` varchar(255) default NULL,
    `shipping_name` varchar(255) default NULL,
    `billing_name` varchar(255) default NULL,
    `created_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STATUS` (`status`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_BASE_GRAND_TOTAL` (`base_grand_total`),
    KEY `IDX_BASE_TOTAL_PAID` (`base_total_paid`),
    KEY `IDX_GRAND_TOTAL` (`grand_total`),
    KEY `IDX_TOTAL_PAID` (`total_paid`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_SHIPPING_NAME` (`shipping_name`),
    KEY `IDX_BILLING_NAME` (`billing_name`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_CUSTOMER_ID` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Order Address */
CREATE TABLE `{$installer->getTable('sales_flat_order_address')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned default NULL,
    `customer_address_id` int(10) default NULL,
    `quote_address_id` int(10) default NULL,
    `region_id` int(10) default NULL,
    `customer_id` int(10) default NULL,
    `fax` varchar(255) default NULL,
    `region` varchar(255) default NULL,
    `postcode` varchar(255) default NULL,
    `lastname` varchar(255) default NULL,
    `street` varchar(255) default NULL,
    `city` varchar(255) default NULL,
    `email` varchar(255) default NULL,
    `telephone` varchar(255) default NULL,
    `country_id` char(2) default NULL,
    `firstname` varchar(255) default NULL,
    `address_type` varchar(255) default NULL,
    `prefix` varchar(255) default NULL,
    `middlename` varchar(255) default NULL,
    `suffix` varchar(255) default NULL,
    `company` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Order Comments */
CREATE TABLE `{$installer->getTable('sales_flat_order_status_history')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `is_customer_notified` int(10) default NULL,
    `comment` text,
    `status` varchar(32) default NULL,
    `created_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`),
    KEY `IDX_CREATED_AT` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Order Items */

CREATE TABLE `{$installer->getTable('sales_flat_order_item')}` (
    `item_id` int(10) unsigned NOT NULL auto_increment,
    `order_id` int(10) unsigned NOT NULL default '0',
    `parent_item_id` int(10) unsigned default NULL,
    `quote_item_id` int(10) unsigned default NULL,
    `store_id` smallint(5) unsigned default NULL,
    `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `product_id` int(10) unsigned default NULL,
    `product_type` varchar(255) default NULL,
    `product_options` text,
    `weight` decimal(12,4) default '0.0000',
    `is_virtual` tinyint(1) unsigned default NULL,
    `sku` varchar(255) NOT NULL default '',
    `name` varchar(255) default NULL,
    `description` text,
    `applied_rule_ids` text,
    `additional_data` text,
    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `is_qty_decimal` tinyint(1) unsigned default NULL,
    `no_discount` tinyint(1) unsigned default '0',
    `qty_backordered` decimal(12,4) default '0.0000',
    `qty_canceled` decimal(12,4) default '0.0000',
    `qty_invoiced` decimal(12,4) default '0.0000',
    `qty_ordered` decimal(12,4) default '0.0000',
    `qty_refunded` decimal(12,4) default '0.0000',
    `qty_shipped` decimal(12,4) default '0.0000',
    `base_cost` decimal(12,4) default '0.0000',
    `price` decimal(12,4) NOT NULL default '0.0000',
    `base_price` decimal(12,4) NOT NULL default '0.0000',
    `original_price` decimal(12,4) default NULL,
    `base_original_price` decimal(12,4) default NULL,
    `tax_percent` decimal(12,4) default '0.0000',
    `tax_amount` decimal(12,4) default '0.0000',
    `base_tax_amount` decimal(12,4) default '0.0000',
    `tax_invoiced` decimal(12,4) default '0.0000',
    `base_tax_invoiced` decimal(12,4) default '0.0000',
    `discount_percent` decimal(12,4) default '0.0000',
    `discount_amount` decimal(12,4) default '0.0000',
    `base_discount_amount` decimal(12,4) default '0.0000',
    `discount_invoiced` decimal(12,4) default '0.0000',
    `base_discount_invoiced` decimal(12,4) default '0.0000',
    `amount_refunded` decimal(12,4) default '0.0000',
    `base_amount_refunded` decimal(12,4) default '0.0000',
    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_invoiced` decimal(12,4) NOT NULL default '0.0000',
    `base_row_invoiced` decimal(12,4) NOT NULL default '0.0000',
    `row_weight` decimal(12,4) default '0.0000',
    `gift_message_id` int(10) default NULL,
    `gift_message_available` int(10) default NULL,
    `base_tax_before_discount` decimal(12,4) default NULL,
    `tax_before_discount` decimal(12,4) default NULL,
    `ext_order_item_id` varchar(255) default NULL,
    `weee_tax_applied` text,
    `weee_tax_applied_amount` decimal(12,4) default NULL,
    `weee_tax_applied_row_amount` decimal(12,4) default NULL,
    `base_weee_tax_applied_amount` decimal(12,4) default NULL,
    `base_weee_tax_applied_row_amount` decimal(12,4) default NULL,
    `weee_tax_disposition` decimal(12,4) default NULL,
    `weee_tax_row_disposition` decimal(12,4) default NULL,
    `base_weee_tax_disposition` decimal(12,4) default NULL,
    `base_weee_tax_row_disposition` decimal(12,4) default NULL,
    `locked_do_invoice` tinyint(1) unsigned default NULL,
    `locked_do_ship` tinyint(1) unsigned default NULL,
    `price_incl_tax` decimal(12,4) default NULL,
    `base_price_incl_tax` decimal(12,4) default NULL,
    `row_total_incl_tax` decimal(12,4) default NULL,
    `base_row_total_incl_tax` decimal(12,4) default NULL,
    PRIMARY KEY (`item_id`),
    KEY `IDX_ORDER` (`order_id`),
    KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Order Payment */

CREATE TABLE `{$installer->getTable('sales_flat_order_payment')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `base_shipping_captured` decimal(12,4) default NULL,
    `shipping_captured` decimal(12,4) default NULL,
    `amount_refunded` decimal(12,4) default NULL,
    `base_amount_paid` decimal(12,4) default NULL,
    `amount_canceled` decimal(12,4) default NULL,
    `base_amount_authorized` decimal(12,4) default NULL,
    `base_amount_paid_online` decimal(12,4) default NULL,
    `base_amount_refunded_online` decimal(12,4) default NULL,
    `base_shipping_amount` decimal(12,4) default NULL,
    `shipping_amount` decimal(12,4) default NULL,
    `amount_paid` decimal(12,4) default NULL,
    `amount_authorized` decimal(12,4) default NULL,
    `base_amount_ordered` decimal(12,4) default NULL,
    `base_shipping_refunded` decimal(12,4) default NULL,
    `shipping_refunded` decimal(12,4) default NULL,
    `base_amount_refunded` decimal(12,4) default NULL,
    `amount_ordered` decimal(12,4) default NULL,
    `base_amount_canceled` decimal(12,4) default NULL,
    `ideal_transaction_checked` tinyint(1) unsigned default NULL,
    `quote_payment_id` int(10) default NULL,
    `additional_data` text,
    `cc_exp_month` varchar(255) default NULL,
    `cc_ss_start_year` varchar(255) default NULL,
    `echeck_bank_name` varchar(255) default NULL,
    `method` varchar(255) default NULL,
    `cc_debug_request_body` varchar(255) default NULL,
    `cc_secure_verify` varchar(255) default NULL,
    `cybersource_token` varchar(255) default NULL,
    `ideal_issuer_title` varchar(255) default NULL,
    `protection_eligibility` varchar(255) default NULL,
    `cc_approval` varchar(255) default NULL,
    `cc_last4` varchar(255) default NULL,
    `cc_status_description` varchar(255) default NULL,
    `echeck_type` varchar(255) default NULL,
    `paybox_question_number` varchar(255) default NULL,
    `cc_debug_response_serialized` varchar(255) default NULL,
    `cc_ss_start_month` varchar(255) default NULL,
    `echeck_account_type` varchar(255) default NULL,
    `last_trans_id` varchar(255) default NULL,
    `cc_cid_status` varchar(255) default NULL,
    `cc_owner` varchar(255) default NULL,
    `cc_type` varchar(255) default NULL,
    `ideal_issuer_id` varchar(255) default NULL,
    `po_number` varchar(255) default NULL,
    `cc_exp_year` varchar(255) default NULL,
    `cc_status` varchar(255) default NULL,
    `echeck_routing_number` varchar(255) default NULL,
    `account_status` varchar(255) default NULL,
    `anet_trans_method` varchar(255) default NULL,
    `cc_debug_response_body` varchar(255) default NULL,
    `cc_ss_issue` varchar(255) default NULL,
    `echeck_account_name` varchar(255) default NULL,
    `cc_avs_status` varchar(255) default NULL,
    `cc_number_enc` varchar(255) default NULL,
    `cc_trans_id` varchar(255) default NULL,
    `flo2cash_account_id` varchar(255) default NULL,
    `paybox_request_number` varchar(255) default NULL,
    `address_status` varchar(255) default NULL,
    `additional_information` text,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Shipments */

CREATE TABLE `{$installer->getTable('sales_flat_shipment')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned default NULL,
    `total_weight` decimal(12,4) default NULL,
    `total_qty` decimal(12,4) default NULL,
    `email_sent` tinyint(1) unsigned default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `customer_id` int(10) default NULL,
    `shipping_address_id` int(10) default NULL,
    `billing_address_id` int(10) default NULL,
    `shipment_status` int(10) default NULL,
    `increment_id` varchar(50) default NULL,
    `created_at` datetime default NULL,
    `updated_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_TOTAL_QTY` (`total_qty`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_ORDER_ID` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Shipments Grid */

CREATE TABLE `{$installer->getTable('sales_flat_shipment_grid')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned default NULL,
    `total_qty` decimal(12,4) default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `shipment_status` int(10) default NULL,
    `increment_id` varchar(50) default NULL,
    `order_increment_id` varchar(50) default NULL,
    `created_at` datetime default NULL,
    `order_created_at` datetime default NULL,
    `shipping_name` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_TOTAL_QTY` (`total_qty`),
    KEY `IDX_ORDER_ID` (`order_id`),
    KEY `IDX_SHIPMENT_STATUS` (`shipment_status`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_ORDER_INCREMENT_ID` (`order_increment_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_ORDER_CREATED_AT` (`order_created_at`),
    KEY `IDX_SHIPPING_NAME` (`shipping_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Shipment Items */

CREATE TABLE `{$installer->getTable('sales_flat_shipment_item')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `row_total` decimal(12,4) default NULL,
    `price` decimal(12,4) default NULL,
    `weight` decimal(12,4) default NULL,
    `qty` decimal(12,4) default NULL,
    `product_id` int(10) default NULL,
    `order_item_id` int(10) default NULL,
    `additional_data` text,
    `description` text,
    `name` varchar(255) default NULL,
    `sku` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Shipping tracking */

CREATE TABLE `{$installer->getTable('sales_flat_shipment_track')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `weight` decimal(12,4) default NULL,
    `qty` decimal(12,4) default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `number` text,
    `description` text,
    `title` varchar(255) default NULL,
    `carrier_code` varchar(32) default NULL,
    `created_at` datetime default NULL,
    `updated_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`),
    KEY `IDX_ORDER_ID` (`order_id`),
    KEY `IDX_CREATED_AT` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Shipment Comment */
CREATE TABLE `{$installer->getTable('sales_flat_shipment_comment')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `is_customer_notified` int(10) default NULL,
    `comment` text,
    `created_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Invoice Main Table */
CREATE TABLE `{$installer->getTable('sales_flat_invoice')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned default NULL,
    `base_grand_total` decimal(12,4) default NULL,
    `shipping_tax_amount` decimal(12,4) default NULL,
    `tax_amount` decimal(12,4) default NULL,
    `base_tax_amount` decimal(12,4) default NULL,
    `store_to_order_rate` decimal(12,4) default NULL,
    `base_shipping_tax_amount` decimal(12,4) default NULL,
    `base_discount_amount` decimal(12,4) default NULL,
    `base_to_order_rate` decimal(12,4) default NULL,
    `grand_total` decimal(12,4) default NULL,
    `shipping_amount` decimal(12,4) default NULL,
    `subtotal_incl_tax` decimal(12,4) default NULL,
    `base_subtotal_incl_tax` decimal(12,4) default NULL,
    `store_to_base_rate` decimal(12,4) default NULL,
    `base_shipping_amount` decimal(12,4) default NULL,
    `total_qty` decimal(12,4) default NULL,
    `base_to_global_rate` decimal(12,4) default NULL,
    `subtotal` decimal(12,4) default NULL,
    `base_subtotal` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) default NULL,
    `billing_address_id` int(10) default NULL,
    `is_used_for_refund` tinyint(1) unsigned default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `email_sent` tinyint(1) unsigned default NULL,
    `can_void_flag` tinyint(1) unsigned default NULL,
    `state` int(10) default NULL,
    `shipping_address_id` int(10) default NULL,
    `cybersource_token` varchar(255) default NULL,
    `store_currency_code` char(3) default NULL,
    `transaction_id` varchar(255) default NULL,
    `order_currency_code` char(3) default NULL,
    `base_currency_code` char(3) default NULL,
    `global_currency_code` char(3) default NULL,
    `increment_id` varchar(50) default NULL,
    `created_at` datetime default NULL,
    `updated_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_GRAND_TOTAL` (`grand_total`),
    KEY `IDX_ORDER_ID` (`order_id`),
    KEY `IDX_STATE` (`state`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_CREATED_AT` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Invoices Grid */
CREATE TABLE `{$installer->getTable('sales_flat_invoice_grid')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned default NULL,
    `base_grand_total` decimal(12,4) default NULL,
    `grand_total` decimal(12,4) default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `state` int(10) default NULL,
    `store_currency_code` char(3) default NULL,
    `order_currency_code` char(3) default NULL,
    `base_currency_code` char(3) default NULL,
    `global_currency_code` char(3) default NULL,
    `increment_id` varchar(50) default NULL,
    `order_increment_id` varchar(50) default NULL,
    `created_at` datetime default NULL,
    `order_created_at` datetime default NULL,
    `billing_name` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_GRAND_TOTAL` (`grand_total`),
    KEY `IDX_ORDER_ID` (`order_id`),
    KEY `IDX_STATE` (`state`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_ORDER_INCREMENT_ID` (`order_increment_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_ORDER_CREATED_AT` (`order_created_at`),
    KEY `IDX_BILLING_NAME` (`billing_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Invoice Items */

CREATE TABLE `{$installer->getTable('sales_flat_invoice_item')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `base_price` decimal(12,4) default NULL,
    `base_weee_tax_row_disposition` decimal(12,4) default NULL,
    `weee_tax_applied_row_amount` decimal(12,4) default NULL,
    `base_weee_tax_applied_amount` decimal(12,4) default NULL,
    `tax_amount` decimal(12,4) default NULL,
    `base_row_total` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) default NULL,
    `row_total` decimal(12,4) default NULL,
    `weee_tax_row_disposition` decimal(12,4) default NULL,
    `base_discount_amount` decimal(12,4) default NULL,
    `base_weee_tax_disposition` decimal(12,4) default NULL,
    `price_incl_tax` decimal(12,4) default NULL,
    `weee_tax_applied_amount` decimal(12,4) default NULL,
    `base_tax_amount` decimal(12,4) default NULL,
    `base_price_incl_tax` decimal(12,4) default NULL,
    `qty` decimal(12,4) default NULL,
    `weee_tax_disposition` decimal(12,4) default NULL,
    `base_cost` decimal(12,4) default NULL,
    `base_weee_tax_applied_row_amount` decimal(12,4) default NULL,
    `price` decimal(12,4) default NULL,
    `base_row_total_incl_tax` decimal(12,4) default NULL,
    `row_total_incl_tax` decimal(12,4) default NULL,
    `product_id` int(10) default NULL,
    `order_item_id` int(10) default NULL,
    `additional_data` text,
    `description` text,
    `weee_tax_applied` text,
    `sku` varchar(255) default NULL,
    `name` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Invoice Comments */
CREATE TABLE `{$installer->getTable('sales_flat_invoice_comment')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `is_customer_notified` tinyint(1) unsigned default NULL,
    `comment` text,
    `created_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* CreditMemo Main table */
CREATE TABLE `{$installer->getTable('sales_flat_creditmemo')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned default NULL,
    `adjustment_positive` decimal(12,4) default NULL,
    `base_shipping_tax_amount` decimal(12,4) default NULL,
    `store_to_order_rate` decimal(12,4) default NULL,
    `base_discount_amount` decimal(12,4) default NULL,
    `base_to_order_rate` decimal(12,4) default NULL,
    `grand_total` decimal(12,4) default NULL,
    `base_adjustment_negative` decimal(12,4) default NULL,
    `base_subtotal_incl_tax` decimal(12,4) default NULL,
    `shipping_amount` decimal(12,4) default NULL,
    `subtotal_incl_tax` decimal(12,4) default NULL,
    `adjustment_negative` decimal(12,4) default NULL,
    `base_shipping_amount` decimal(12,4) default NULL,
    `store_to_base_rate` decimal(12,4) default NULL,
    `base_to_global_rate` decimal(12,4) default NULL,
    `base_adjustment` decimal(12,4) default NULL,
    `base_subtotal` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) default NULL,
    `subtotal` decimal(12,4) default NULL,
    `adjustment` decimal(12,4) default NULL,
    `base_grand_total` decimal(12,4) default NULL,
    `base_adjustment_positive` decimal(12,4) default NULL,
    `base_tax_amount` decimal(12,4) default NULL,
    `shipping_tax_amount` decimal(12,4) default NULL,
    `tax_amount` decimal(12,4) default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `email_sent` tinyint(1) unsigned default NULL,
    `creditmemo_status` int(10) default NULL,
    `state` int(10) default NULL,
    `shipping_address_id` int(10) default NULL,
    `billing_address_id` int(10) default NULL,
    `invoice_id` int(10) default NULL,
    `cybersource_token` varchar(255) default NULL,
    `store_currency_code` char(3) default NULL,
    `order_currency_code` char(3) default NULL,
    `base_currency_code` char(3) default NULL,
    `global_currency_code` char(3) default NULL,
    `transaction_id` varchar(255) default NULL,
    `increment_id` varchar(50) default NULL,
    `created_at` datetime default NULL,
    `updated_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_ORDER_ID` (`order_id`),
    KEY `IDX_CREDITMEMO_STATUS` (`creditmemo_status`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_STATE` (`state`),
    KEY `IDX_CREATED_AT` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* CreditMemo Grid */
CREATE TABLE `{$installer->getTable('sales_flat_creditmemo_grid')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned default NULL,
    `store_to_order_rate` decimal(12,4) default NULL,
    `base_to_order_rate` decimal(12,4) default NULL,
    `grand_total` decimal(12,4) default NULL,
    `store_to_base_rate` decimal(12,4) default NULL,
    `base_to_global_rate` decimal(12,4) default NULL,
    `base_grand_total` decimal(12,4) default NULL,
    `order_id` int(10) unsigned NOT NULL,
    `creditmemo_status` int(10) default NULL,
    `state` int(10) default NULL,
    `invoice_id` int(10) default NULL,
    `store_currency_code` char(3) default NULL,
    `order_currency_code` char(3) default NULL,
    `base_currency_code` char(3) default NULL,
    `global_currency_code` char(3) default NULL,
    `increment_id` varchar(50) default NULL,
    `order_increment_id` varchar(50) default NULL,
    `created_at` datetime default NULL,
    `order_created_at` datetime default NULL,
    `billing_name` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_STORE_ID` (`store_id`),
    KEY `IDX_GRAND_TOTAL` (`grand_total`),
    KEY `IDX_BASE_GRAND_TOTAL` (`base_grand_total`),
    KEY `IDX_ORDER_ID` (`order_id`),
    KEY `IDX_CREDITMEMO_STATUS` (`creditmemo_status`),
    KEY `IDX_STATE` (`state`),
    KEY `IDX_INCREMENT_ID` (`increment_id`),
    KEY `IDX_ORDER_INCREMENT_ID` (`order_increment_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_ORDER_CREATED_AT` (`order_created_at`),
    KEY `IDX_BILLING_NAME` (`billing_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* CreditMemo Item */

CREATE TABLE `{$installer->getTable('sales_flat_creditmemo_item')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `weee_tax_applied_row_amount` decimal(12,4) default NULL,
    `base_price` decimal(12,4) default NULL,
    `base_weee_tax_row_disposition` decimal(12,4) default NULL,
    `tax_amount` decimal(12,4) default NULL,
    `base_weee_tax_applied_amount` decimal(12,4) default NULL,
    `weee_tax_row_disposition` decimal(12,4) default NULL,
    `base_row_total` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) default NULL,
    `row_total` decimal(12,4) default NULL,
    `weee_tax_applied_amount` decimal(12,4) default NULL,
    `base_discount_amount` decimal(12,4) default NULL,
    `base_weee_tax_disposition` decimal(12,4) default NULL,
    `price_incl_tax` decimal(12,4) default NULL,
    `base_tax_amount` decimal(12,4) default NULL,
    `weee_tax_disposition` decimal(12,4) default NULL,
    `base_price_incl_tax` decimal(12,4) default NULL,
    `qty` decimal(12,4) default NULL,
    `base_cost` decimal(12,4) default NULL,
    `base_weee_tax_applied_row_amount` decimal(12,4) default NULL,
    `price` decimal(12,4) default NULL,
    `base_row_total_incl_tax` decimal(12,4) default NULL,
    `row_total_incl_tax` decimal(12,4) default NULL,
    `product_id` int(10) default NULL,
    `order_item_id` int(10) default NULL,
    `additional_data` text,
    `description` text,
    `weee_tax_applied` text,
    `sku` varchar(255) default NULL,
    `name` varchar(255) default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* CreditMemo Comment */

CREATE TABLE `{$installer->getTable('sales_flat_creditmemo_comment')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL,
    `is_customer_notified` int(10) default NULL,
    `comment` text,
    `created_at` datetime default NULL,
    PRIMARY KEY (`entity_id`),
    KEY `IDX_CREATED_AT` (`created_at`),
    KEY `IDX_PARENT_ID` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `converted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned DEFAULT '1',
  `is_virtual` tinyint(1) unsigned DEFAULT '0',
  `is_multi_shipping` tinyint(1) unsigned DEFAULT '0',
  `items_count` int(10) unsigned DEFAULT '0',
  `items_qty` decimal(12,4) DEFAULT '0.0000',
  `orig_order_id` int(10) unsigned DEFAULT '0',
  `store_to_base_rate` decimal(12,4) DEFAULT '0.0000',
  `store_to_quote_rate` decimal(12,4) DEFAULT '0.0000',
  `base_currency_code` varchar(255) DEFAULT NULL,
  `store_currency_code` varchar(255) DEFAULT NULL,
  `quote_currency_code` varchar(255) DEFAULT NULL,
  `grand_total` decimal(12,4) DEFAULT '0.0000',
  `base_grand_total` decimal(12,4) DEFAULT '0.0000',
  `checkout_method` varchar(255) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT '0',
  `customer_tax_class_id` int(10) unsigned DEFAULT '0',
  `customer_group_id` int(10) unsigned DEFAULT '0',
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_prefix` varchar(40) DEFAULT NULL,
  `customer_firstname` varchar(255) DEFAULT NULL,
  `customer_middlename` varchar(40) DEFAULT NULL,
  `customer_lastname` varchar(255) DEFAULT NULL,
  `customer_suffix` varchar(40) DEFAULT NULL,
  `customer_dob` datetime DEFAULT NULL,
  `customer_note` varchar(255) DEFAULT NULL,
  `customer_note_notify` tinyint(1) unsigned DEFAULT '1',
  `customer_is_guest` tinyint(1) unsigned DEFAULT '0',
  `remote_ip` varchar(32) DEFAULT NULL,
  `applied_rule_ids` varchar(255) DEFAULT NULL,
  `reserved_order_id` varchar(64) DEFAULT '',
  `password_hash` varchar(255) DEFAULT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `global_currency_code` varchar(255) DEFAULT NULL,
  `base_to_global_rate` decimal(12,4) DEFAULT NULL,
  `base_to_quote_rate` decimal(12,4) DEFAULT NULL,
  `customer_taxvat` varchar(255) DEFAULT NULL,
  `customer_gender` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,4) DEFAULT NULL,
  `base_subtotal` decimal(12,4) DEFAULT NULL,
  `subtotal_with_discount` decimal(12,4) DEFAULT NULL,
  `base_subtotal_with_discount` decimal(12,4) DEFAULT NULL,
  `is_changed` int(10) unsigned DEFAULT NULL,
  `trigger_recollect` tinyint(1) NOT NULL DEFAULT '0',
  `ext_shipping_info` text,
  `gift_message_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  KEY `IDX_CUSTOMER` (`customer_id`,`store_id`,`is_active`),
  CONSTRAINT `FK_SALES_QUOTE_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_address')}` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `customer_id` int(10) unsigned DEFAULT NULL,
  `save_in_address_book` tinyint(1) DEFAULT '0',
  `customer_address_id` int(10) unsigned DEFAULT NULL,
  `address_type` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `prefix` varchar(40) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(40) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `suffix` varchar(40) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `country_id` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `same_as_billing` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `free_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `collect_shipping_rates` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_method` varchar(255) NOT NULL DEFAULT '',
  `shipping_description` varchar(255) NOT NULL DEFAULT '',
  `weight` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `subtotal_with_discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_subtotal_with_discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `shipping_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `shipping_tax_amount` decimal(12,4) DEFAULT NULL,
  `base_shipping_tax_amount` decimal(12,4) DEFAULT NULL,
  `discount_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_discount_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `grand_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_grand_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `customer_notes` text,
  `applied_taxes` text,
  `discount_description` varchar(255) DEFAULT NULL,
  `shipping_discount_amount` decimal(12,4) DEFAULT NULL,
  `base_shipping_discount_amount` decimal(12,4) DEFAULT NULL,
  `subtotal_incl_tax` decimal(12,4) DEFAULT NULL,
  `base_subtotal_total_incl_tax` decimal(12,4) DEFAULT NULL,
  `gift_message_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  CONSTRAINT `FK_SALES_QUOTE_ADDRESS_SALES_QUOTE` FOREIGN KEY (`quote_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_address_item')}` (
  `address_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_item_id` int(10) unsigned DEFAULT NULL,
  `quote_address_id` int(10) unsigned NOT NULL DEFAULT '0',
  `quote_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `applied_rule_ids` text,
  `additional_data` text,
  `weight` decimal(12,4) DEFAULT '0.0000',
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(12,4) DEFAULT '0.0000',
  `tax_amount` decimal(12,4) DEFAULT '0.0000',
  `row_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_row_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `row_total_with_discount` decimal(12,4) DEFAULT '0.0000',
  `base_discount_amount` decimal(12,4) DEFAULT '0.0000',
  `base_tax_amount` decimal(12,4) DEFAULT '0.0000',
  `row_weight` decimal(12,4) DEFAULT '0.0000',
  `product_id` int(10) unsigned DEFAULT NULL,
  `super_product_id` int(10) unsigned DEFAULT NULL,
  `parent_product_id` int(10) unsigned DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `free_shipping` int(10) unsigned DEFAULT NULL,
  `is_qty_decimal` int(10) unsigned DEFAULT NULL,
  `price` decimal(12,4) DEFAULT NULL,
  `discount_percent` decimal(12,4) DEFAULT NULL,
  `no_discount` int(10) unsigned DEFAULT NULL,
  `tax_percent` decimal(12,4) DEFAULT NULL,
  `base_price` decimal(12,4) DEFAULT NULL,
  `base_cost` decimal(12,4) DEFAULT NULL,
  `price_incl_tax` decimal(12,4) DEFAULT NULL,
  `base_price_incl_tax` decimal(12,4) DEFAULT NULL,
  `row_total_incl_tax` decimal(12,4) DEFAULT NULL,
  `base_row_total_incl_tax` decimal(12,4) DEFAULT NULL,
  `gift_message_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`address_item_id`),
  CONSTRAINT `FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS` FOREIGN KEY (`quote_address_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote_address')}` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_FLAT_QUOTE_ADDRESS_ITEM_PARENT` FOREIGN KEY (`parent_item_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote_address_item')}` (`address_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM` FOREIGN KEY (`quote_item_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_item')}` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `product_id` int(10) unsigned DEFAULT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `parent_item_id` int(10) unsigned DEFAULT NULL,
  `is_virtual` tinyint(1) unsigned DEFAULT NULL,
  `sku` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `applied_rule_ids` text,
  `additional_data` text,
  `free_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_qty_decimal` tinyint(1) unsigned DEFAULT NULL,
  `no_discount` tinyint(1) unsigned DEFAULT '0',
  `weight` decimal(12,4) DEFAULT '0.0000',
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `custom_price` decimal(12,4) DEFAULT NULL,
  `discount_percent` decimal(12,4) DEFAULT '0.0000',
  `discount_amount` decimal(12,4) DEFAULT '0.0000',
  `base_discount_amount` decimal(12,4) DEFAULT '0.0000',
  `tax_percent` decimal(12,4) DEFAULT '0.0000',
  `tax_amount` decimal(12,4) DEFAULT '0.0000',
  `base_tax_amount` decimal(12,4) DEFAULT '0.0000',
  `row_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_row_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `row_total_with_discount` decimal(12,4) DEFAULT '0.0000',
  `row_weight` decimal(12,4) DEFAULT '0.0000',
  `product_type` varchar(255) DEFAULT NULL,
  `base_tax_before_discount` decimal(12,4) DEFAULT NULL,
  `tax_before_discount` decimal(12,4) DEFAULT NULL,
  `original_custom_price` decimal(12,4) DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  `base_cost` decimal(12,4) DEFAULT NULL,
  `price_incl_tax` decimal(12,4) DEFAULT NULL,
  `base_price_incl_tax` decimal(12,4) DEFAULT NULL,
  `row_total_incl_tax` decimal(12,4) DEFAULT NULL,
  `base_row_total_incl_tax` decimal(12,4) DEFAULT NULL,
  `gift_message_id` int(10) unsigned DEFAULT NULL,
  `weee_tax_applied` text,
  `weee_tax_applied_amount` decimal(12,4) DEFAULT NULL,
  `weee_tax_applied_row_amount` decimal(12,4) DEFAULT NULL,
  `base_weee_tax_applied_amount` decimal(12,4) DEFAULT NULL,
  `base_weee_tax_applied_row_amount` decimal(12,4) DEFAULT NULL,
  `weee_tax_disposition` decimal(12,4) DEFAULT NULL,
  `weee_tax_row_disposition` decimal(12,4) DEFAULT NULL,
  `base_weee_tax_disposition` decimal(12,4) DEFAULT NULL,
  `base_weee_tax_row_disposition` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  CONSTRAINT `FK_SALES_FLAT_QUOTE_ITEM_PARENT_ITEM` FOREIGN KEY (`parent_item_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_QUOTE_ITEM_CATALOG_PRODUCT_ENTITY` FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_QUOTE_ITEM_SALES_QUOTE` FOREIGN KEY (`quote_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_QUOTE_ITEM_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_item_option')}` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`option_id`),
  CONSTRAINT `FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID` FOREIGN KEY (`item_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Additional options for quote item';

CREATE TABLE `{$installer->getTable('sales_flat_quote_payment')}` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `method` varchar(255) DEFAULT '',
  `cc_type` varchar(255) DEFAULT '',
  `cc_number_enc` varchar(255) DEFAULT '',
  `cc_last4` varchar(255) DEFAULT '',
  `cc_cid_enc` varchar(255) DEFAULT '',
  `cc_owner` varchar(255) DEFAULT '',
  `cc_exp_month` tinyint(2) unsigned DEFAULT '0',
  `cc_exp_year` smallint(4) unsigned DEFAULT '0',
  `cc_ss_owner` varchar(255) DEFAULT '',
  `cc_ss_start_month` tinyint(2) unsigned DEFAULT '0',
  `cc_ss_start_year` smallint(4) unsigned DEFAULT '0',
  `cybersource_token` varchar(255) DEFAULT '',
  `paypal_correlation_id` varchar(255) DEFAULT '',
  `paypal_payer_id` varchar(255) DEFAULT '',
  `paypal_payer_status` varchar(255) DEFAULT '',
  `po_number` varchar(255) DEFAULT '',
  `additional_data` text,
  `cc_ss_issue` varchar(255) DEFAULT NULL,
  `additional_information` text,
  `ideal_issuer_id` varchar(255) DEFAULT NULL,
  `ideal_issuer_list` text,
  PRIMARY KEY (`payment_id`),
  CONSTRAINT `FK_SALES_QUOTE_PAYMENT_SALES_QUOTE` FOREIGN KEY (`quote_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_shipping_rate')}` (
  `rate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `carrier` varchar(255) DEFAULT NULL,
  `carrier_title` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `method_description` text,
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `error_message` text,
  `method_title` text,
  PRIMARY KEY (`rate_id`),
  CONSTRAINT `FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS` FOREIGN KEY (`address_id`)
    REFERENCES `{$installer->getTable('sales_flat_quote_address')}` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_invoiced_aggregated')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `orders_invoiced` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `invoiced` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `invoiced_captured` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `invoiced_not_captured` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_invoiced_aggregated_order')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `orders_invoiced` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `invoiced` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `invoiced_captured` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `invoiced_not_captured` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_order_aggregated_created')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `total_qty_ordered` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_profit_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_subtotal_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_discount_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_grand_total_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_invoiced_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_canceled_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_tax_invoiced_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_tax_canceled_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_tax_refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_subtotal_invoiced_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_subtotal_refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_subtotal_canceled_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_discount_invoiced_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_discount_canceled_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_discount_refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_invoiced_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_canceled_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_discount_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `base_shipping_tax_refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_payment_transaction')}` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `payment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `txn_id` varchar(100) NOT NULL DEFAULT '',
  `parent_txn_id` varchar(100) DEFAULT NULL,
  `txn_type` varchar(15) NOT NULL DEFAULT '',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `additional_information` blob,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `UNQ_ORDER_PAYMENT_TXN` (`order_id`, `payment_id`,`txn_id`),
  KEY `IDX_ORDER_ID` (`order_id`),
  KEY `IDX_PARENT_ID` (`parent_id`),
  KEY `IDX_PAYMENT_ID` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_refunded_aggregated')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `refunded` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `online_refunded` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `offline_refunded` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_refunded_aggregated_order')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `refunded` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `online_refunded` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `offline_refunded` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_shipping_aggregated')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `shipping_description` varchar(255) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `total_shipping` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`,`shipping_description`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_shipping_aggregated_order')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period` date NOT NULL DEFAULT '0000-00-00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT '',
  `shipping_description` varchar(255) NOT NULL DEFAULT '',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  `total_shipping` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`,`order_status`,`shipping_description`),
  KEY `IDX_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$constraints = array(
    'sales_flat_order' => array(
        'customer' => array('customer_id', 'customer_entity', 'entity_id', 'set null'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_flat_order_grid' => array(
        'parent' => array('entity_id', 'sales_flat_order', 'entity_id'),
        'customer' => array('customer_id', 'customer_entity', 'entity_id', 'set null'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_flat_order_item' => array(
        'parent' => array('order_id', 'sales_flat_order', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_flat_order_address' => array(
        'parent' => array('parent_id', 'sales_flat_order', 'entity_id'),
    ),
    'sales_flat_order_payment' => array(
        'parent' => array('parent_id', 'sales_flat_order', 'entity_id'),
    ),
    'sales_flat_order_status_history' => array(
        'parent' => array('parent_id', 'sales_flat_order', 'entity_id'),
    ),
    'sales_flat_shipment' => array(
        'parent' => array('order_id', 'sales_flat_order', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
    ),
    'sales_flat_shipment_grid' => array(
        'parent' => array('entity_id', 'sales_flat_shipment', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
    ),
    'sales_flat_shipment_track' => array(
        'parent' => array('parent_id', 'sales_flat_shipment', 'entity_id'),
    ),
    'sales_flat_shipment_item' => array(
        'parent' => array('parent_id', 'sales_flat_shipment', 'entity_id'),
    ),
    'sales_flat_shipment_comment' => array(
        'parent' => array('parent_id', 'sales_flat_shipment', 'entity_id'),
    ),
    'sales_flat_invoice' => array(
        'parent' => array('order_id', 'sales_flat_order', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
    ),
    'sales_flat_invoice_grid' => array(
        'parent' => array('entity_id', 'sales_flat_invoice', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
    ),
    'sales_flat_invoice_item' => array(
        'parent' => array('parent_id', 'sales_flat_invoice', 'entity_id'),
    ),
    'sales_flat_invoice_comment' => array(
        'parent' => array('parent_id', 'sales_flat_invoice', 'entity_id'),
    ),
    'sales_flat_creditmemo' => array(
        'parent' => array('order_id', 'sales_flat_order', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
    ),
    'sales_flat_creditmemo_grid' => array(
        'parent' => array('entity_id', 'sales_flat_creditmemo', 'entity_id'),
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
    ),
    'sales_flat_creditmemo_item' => array(
        'parent' => array('parent_id', 'sales_flat_creditmemo', 'entity_id'),
    ),
    'sales_flat_creditmemo_comment' => array(
        'parent' => array('parent_id', 'sales_flat_creditmemo', 'entity_id'),
    ),
    'sales_payment_transaction' => array(
        'parent' => array('parent_id', 'sales_payment_transaction', 'transaction_id'),
        'order' => array('order_id', 'sales_flat_order', 'entity_id'),
        'payment' => array('payment_id', 'sales_flat_order_payment', 'entity_id'),
    ),
    'sales_invoiced_aggregated' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_invoiced_aggregated_order' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_order_aggregated_created' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_refunded_aggregated' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_refunded_aggregated_order' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_shipping_aggregated' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    ),
    'sales_shipping_aggregated_order' => array(
        'store' => array('store_id', 'core_store', 'store_id', 'set null'),
    )
);

foreach ($constraints as $table => $list) {
    foreach ($list as $code => $constraint) {
        $constraint[1] = $installer->getTable($constraint[1]);
        array_unshift($constraint, $installer->getTable($table));
        array_unshift($constraint, strtoupper($table . '_' . $code));

        call_user_func_array(array($installer->getConnection(), 'addConstraint'), $constraint);
    }
}

// Add eav entity types
$installer->addEntityType('order', array(
    'entity_model'          => 'sales/order',
    'table'                 =>'sales/order',
    'increment_model'       =>'eav/entity_increment_numeric',
    'increment_per_store'   =>true
));

$installer->addEntityType('invoice', array(
    'entity_model'          => 'sales/order_invoice',
    'table'                 =>'sales/invoice',
    'increment_model'       =>'eav/entity_increment_numeric',
    'increment_per_store'   =>true
));

$installer->addEntityType('creditmemo', array(
    'entity_model'          => 'sales/order_creditmemo',
    'table'                 =>'sales/creditmemo',
    'increment_model'       =>'eav/entity_increment_numeric',
    'increment_per_store'   =>true
));

$installer->addEntityType('shipment', array(
    'entity_model'          => 'sales/order_shipment',
    'table'                 =>'sales/shipment',
    'increment_model'       =>'eav/entity_increment_numeric',
    'increment_per_store'   =>true
));

$installer->endSetup();
