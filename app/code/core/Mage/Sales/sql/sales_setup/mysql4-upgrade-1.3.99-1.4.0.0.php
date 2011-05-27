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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/* Include code from mysql4-upgrade-0.9.38-0.9.39.php */
$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_item'),
    'store_id', 'smallint(5) unsigned default null AFTER `product_id`');
$installer->getConnection()->addConstraint('FK_SALES_QUOTE_ITEM_STORE',
    $installer->getTable('sales_flat_quote_item'), 'store_id',
    $installer->getTable('core/store'), 'store_id',
    'set null', 'cascade'
);
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_item'),
    'store_id', 'smallint(5) unsigned default null AFTER `quote_item_id`');
$installer->getConnection()->addConstraint('FK_SALES_ORDER_ITEM_STORE',
    $installer->getTable('sales_flat_order_item'), 'store_id',
    $installer->getTable('core/store'), 'store_id',
    'set null', 'cascade'
);
$installer->addAttribute('quote_item', 'redirect_url', array(
    'type'  => 'varchar',
));
/* including end */

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
");

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales_flat_order_item'),
    'FK_SALES_ORDER_ITEM_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales_payment_transaction'),
    'FK_SALES_PAYMENT_TXN_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales_payment_transaction'),
    'FK_SALES_TXN_ORDER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales_payment_transaction'),
    'FK_SALES_TXN_PAYMENT'
);

$installer->getConnection()->dropKey(
    $installer->getTable('sales_flat_order_item'),
    'FK_SALES_ORDER_ITEM_STORE'
);

$installer->getConnection()->addKey(
    $installer->getTable('sales_flat_order_item'),
    'IDX_STORE_ID',
    'store_id'
);

$installer->getConnection()->dropColumn($installer->getTable('sales_flat_order_item'), 'is_active');

$excludeAttributes = array(
    'all'                   => array('entity_type_id', 'attribute_set_id', 'is_active'),
    'order_payment'         => array('increment_id','created_at', 'updated_at', 'store_id'),
    'order_status_history'  => array('updated_at', 'store_id', 'increment_id'),
    'invoice_comment'       => array('updated_at', 'store_id', 'increment_id'),
    'shipment_comment'      => array('updated_at', 'store_id', 'increment_id'),
    'creditmemo_comment'    => array('updated_at', 'store_id', 'increment_id'),
    'invoice_item'          => array('increment_id','created_at', 'updated_at', 'store_id'),
    'shipment_item'         => array('increment_id','created_at', 'updated_at', 'store_id'),
    'creditmemo_item'       => array('increment_id','created_at', 'updated_at', 'store_id'),
    'order_address'         => array('increment_id','created_at', 'updated_at', 'store_id'),
    'order'                 => array('payment_authorization_amount', 'parent_id'),
    'creditmemo'            => array('parent_id'),
    'invoice'               => array('parent_id'),
    'shipment'              => array('parent_id'),
    'shipment_track'        => array('increment_id', 'store_id'),
);

$entityToFlat = array(
    'order'                 => array('grid'=>true),
    'order_item'            => array('flat'=> true),
    'order_address'         => array(),
    'order_payment'         => array(),
    'order_status_history'  => array(),
    'invoice'               => array('grid' => true),
    'invoice_item'          => array(),
    'invoice_comment'       => array(),
    'creditmemo'            => array('grid' => true),
    'creditmemo_item'       => array(),
    'creditmemo_comment'    => array(),
    'shipment'              => array('grid' => true),
    'shipment_item'         => array(),
    'shipment_comment'      => array(),
    'shipment_track'        => array()
);

/* @var $select Varien_Db_Select */
$select = $installer->getConnection()->select();

$select
    ->from(array('attribute' => $installer->getTable('eav/attribute')), array(
        'id'   => 'attribute_id',
        'code' => 'attribute_code',
        'type' => 'backend_type',
        'table' => 'backend_table'))
    ->join(array('entity' => $installer->getTable('eav/entity_type')), 'attribute.entity_type_id = entity.entity_type_id', array(
        'entity' => 'entity_type_code',
        'type_id' => 'entity_type_id'
    ))
    ->where('entity.entity_type_code IN (?)', array_keys($entityToFlat))
    ->where('attribute.attribute_code NOT IN(?)', $excludeAttributes['all']);
;


$attributes = array();

foreach ($installer->getConnection()->fetchAll($select) as $attribute) {
    $attributes[$attribute['entity']][$attribute['code']] = $attribute;
}


$definitions = array(
    'datetime' => 'datetime default null',
    'int'      => 'int(11) default null',
    'varchar'  => 'varchar(255) default null',
    'text'  => 'text default null',
    'decimal'  => 'decimal(12,4) default null'
);

foreach ($entityToFlat as $entityCode => $flags) {
    $flatTablePrefix = 'sales_flat_' . $entityCode;
    $flatFields = $installer->getConnection()->fetchPairs(
        'DESCRIBE ' . $installer->getTable($flatTablePrefix)
    );

    if (!empty($flags['flat'])) {
        $entityTable = $installer->getTable('sales_flat_' . $entityCode);
    } else {
        $entityTable = $this->getTable($entityCode == 'order' ? 'sales_order' : 'sales_order_entity');
        $firstAttribute = current($attributes[$entityCode]);
        $entityTypeId = $firstAttribute['type_id'];
    }

    $entityFields = $installer->getConnection()->fetchPairs(
        'DESCRIBE ' . $entityTable
    );

    $entityIndex = $installer->getConnection()->getIndexList($entityTable);
    $entityFieldInIndex = array();

    // Create list of entity fields in index to not loose them.
    foreach ($entityIndex as $name => $info) {
        $entityFieldInIndex += array_combine($info['fields'], array_fill(0, count($info['fields']), $name));
    }

    // Copy all fields from entity tables
    $addIndex = array();

    foreach ($entityFields as $code => $definition) {
        if (!isset($flatFields[$code]) &&
            !in_array($code, $excludeAttributes['all']) &&
            (!isset($excludeAttributes[$entityCode]) ||
                !in_array($code, $excludeAttributes[$entityCode]))) {

            $installer->getConnection()->addColumn(
                $installer->getTable($flatTablePrefix),
                $code, $definition
            );

            if (isset($entityFieldInIndex[$code])) { // Add entity table indexes with custom fields
                $addIndex[] = $entityFieldInIndex[$code];
            }

            $flatFields[$code] = $definition;
        }
    }

    foreach ($addIndex as $indexName) { // Adding indexes to not loose them in customizations
        $fields = array_intersect($entityIndex[$indexName]['fields'], array_keys($flatFields));
        $installer->getConnection()->addKey(
            $installer->getTable($flatTablePrefix),
            $indexName,
            $fields,
            $entityIndex[$indexName]['type']
        );
    }

    $attributesByTable = array();

    if (empty($flags['flat']) && isset($attributes[$entityCode])) {
        // If our table is not flat we need to add field from attributes too
        foreach ($attributes[$entityCode] as $attributeCode => $attribute) {
            if ($attribute['type'] == 'static') {
                continue;
            }

            if (!isset($flatFields[$attributeCode]) &&
                isset($definitions[$attribute['type']]) &&
                !in_array($attributeCode, $excludeAttributes['all']) &&
                (!isset($excludeAttributes[$entityCode]) ||
                !in_array($attributeCode, $excludeAttributes[$entityCode]))) {

                $installer->getConnection()->addColumn(
                    $installer->getTable($flatTablePrefix),
                    $attributeCode, $definitions[$attribute['type']]
                );


                $flatFields[$attributeCode] = $definitions[$attribute['type']];
            }

            // Collect data for fast access on update
            $attributesByTable[$entityTable . '_' . $attribute['type']][] = $attribute;
        }
    }

    // Create list of keys inserted from base entity table
    $keys = array_keys(array_intersect_key($entityFields, $flatFields));

    $fields = array_combine($keys, $keys);

    if ($entityTable !== $installer->getTable($flatTablePrefix)) {
        $select->reset()
            ->from(array('e' => $entityTable), $fields);

        $select->where('e.entity_type_id = ?', $entityTypeId);


        $sql = $select->insertFromSelect($installer->getTable($flatTablePrefix), array_keys($fields), false) . "; \n";

        // Update base record with eav attributes values
        foreach ($attributesByTable as $table => $updateAttributes) {
            $select->reset();
            $joinCount = 0;
            foreach ($updateAttributes as $attribute) {
                if (isset($entityFields[$attribute['code']]) || !isset($flatFields[$attribute['code']])) {
                    continue;
                }

                $alias = '_table_' . $attribute['code'];
                $select->joinLeft(
                    array($alias=>$table),
                    $alias . '.entity_id = e.entity_id AND ' . $alias . '.attribute_id = ' . $attribute['id'],
                    array($attribute['code'] => 'value')
                );
                $joinCount ++;

                if ($joinCount > 60) { // If we have too much joins for mysql
                    $joinCount = 0;
                    $sql .= $select->crossUpdateFromSelect(array('e'=>$installer->getTable($flatTablePrefix)))  . "; \n";
                    $select->reset();
                }
            }

            if ($joinCount > 0) {
                $sql .= $select->crossUpdateFromSelect(array('e'=>$installer->getTable($flatTablePrefix)))  . "; \n";
            }
        }

    } else  {
        $sql = '';
    }

    if (!empty($flags['grid'])) { // Filling grid table with default base record
        $gridFields = array_keys(
            array_intersect_key(
                $installer->getConnection()->describeTable(
                    $installer->getTable($flatTablePrefix . '_grid')
                ),
                $flatFields
            )
        );

        $select->reset();
        $select->from($installer->getTable($flatTablePrefix), $gridFields);
        $sql .= $select->insertFromSelect($installer->getTable($flatTablePrefix . '_grid'), $gridFields, false);
    }

    $installer->run($sql);
}

// Insert virtual grid fields (shipping_name, billing_name, etc)

// Order Grid
$select->reset();
$select->join(
    array('order' => $installer->getTable('sales_flat_order')),
    'order.entity_id = e.entity_id',
    array()
);

$select->joinLeft(
    array('shipping_address' => $installer->getTable('sales_flat_order_address')),
    'order.shipping_address_id = shipping_address.entity_id',
    array('shipping_name' => 'IF(shipping_address.entity_id IS NOT NULL, CONCAT(shipping_address.firstname, \' \', shipping_address.lastname), NULL)')
);

$select->joinLeft(
    array('billing_address' => $installer->getTable('sales_flat_order_address')),
    'order.billing_address_id = billing_address.entity_id',
    array('billing_name' => 'IF(billing_address.entity_id IS NOT NULL, CONCAT(billing_address.firstname, \' \', billing_address.lastname), NULL)')
);

$installer->run($select->crossUpdateFromSelect(array('e'=>$installer->getTable('sales_flat_order_grid'))));

// Invoice and Creditmemo grid
$select->reset();
$select->join(
    array('order' => $installer->getTable('sales_flat_order')),
    'order.entity_id = e.order_id',
    array('order_increment_id' => 'increment_id', 'order_created_at' => 'created_at')
);

$select->joinLeft(
    array('billing_address' => $installer->getTable('sales_flat_order_address')),
    'order.billing_address_id = billing_address.entity_id',
    array('billing_name' => 'IF(billing_address.entity_id IS NOT NULL, CONCAT(billing_address.firstname, \' \', billing_address.lastname), NULL)')
);

$installer->run($select->crossUpdateFromSelect(array('e'=>$installer->getTable('sales_flat_creditmemo_grid'))));
$installer->run($select->crossUpdateFromSelect(array('e'=>$installer->getTable('sales_flat_invoice_grid'))));

// Shipment grid
$select->reset();
$select->join(
    array('order' => $installer->getTable('sales_flat_order')),
    'order.entity_id = e.order_id',
    array('order_increment_id' => 'increment_id', 'order_created_at' => 'created_at')
);

$select->joinLeft(
    array('shipping_address' => $installer->getTable('sales_flat_order_address')),
    'order.shipping_address_id = shipping_address.entity_id',
    array('shipping_name' => 'IF(shipping_address.entity_id IS NOT NULL, CONCAT(shipping_address.firstname, \' \', shipping_address.lastname), NULL)')
);

$installer->run($select->crossUpdateFromSelect(array('e'=>$installer->getTable('sales_flat_shipment_grid'))));

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
        'store' => array('store_id', 'core_store', 'store_id', 'set null')
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

/**
 * Add additional columns for order aggregation table
 */
$table = $installer->getTable('sales_order_aggregated_created');
$installer->run('TRUNCATE TABLE ' . $installer->getConnection()->quoteIdentifier($table));
$installer->getConnection()->addColumn($table, 'base_tax_invoiced_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_tax_canceled_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_tax_refunded_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');

$installer->getConnection()->addColumn($table, 'base_subtotal_invoiced_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_subtotal_refunded_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_subtotal_canceled_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');

$installer->getConnection()->addColumn($table, 'base_discount_invoiced_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_discount_canceled_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_discount_refunded_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');

$installer->getConnection()->addColumn($table, 'base_shipping_invoiced_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_shipping_canceled_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_shipping_refunded_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_shipping_discount_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_shipping_tax_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');
$installer->getConnection()->addColumn($table, 'base_shipping_tax_refunded_amount', 'decimal(12,4) NOT NULL DEFAULT \'0.0000\'');

$flag = Mage::getModel('reports/flag')
    ->setReportFlagCode(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE)
    ->loadSelf();

if($flag->getId()) {
    $flag->delete();
}


$select = $installer->getConnection()->select();
$select->from($installer->getTable('sales/order_item'), array(
        'total_item_count'   => 'COUNT(item_id)',
        'entity_id'           => 'order_id'))
    ->where('parent_item_id IS NULL')
    ->group(array('order_id'));

$temporaryTable =  'tmp_sales_order_item_count_' . md5(uniqid('order_item_count'));

$installer->getConnection()->query('CREATE TEMPORARY TABLE ' . $installer->getConnection()->quoteIdentifier($temporaryTable) . ' ' . $select->assemble());

$select->reset()
    ->join(array('items_count_table'=>$temporaryTable), 'items_count_table.entity_id = order_table.entity_id', array(
        'total_item_count'=>'total_item_count'
    ));

$installer->getConnection()->query($select->crossUpdateFromSelect(array('order_table' => $installer->getTable('sales/order'))));
$installer->getConnection()->query('DROP TEMPORARY TABLE ' . $temporaryTable);


/**
 * Workaround for the coupon_code attribute that may be missed in the Mage_SalesRule/sql/mysql4-upgrade-0.7.10-0.7.11.php
 * The problem is that Mage_SalesRule depends on Mage_Sales and sometimes the attribute doesn't get updated before this line of code.
 * As a result: an existing column in the sales_flat_order table, but wrong type in the attribute registry and sometimes even data lost
 * Reproduces on upgrading from 1.4.0.x to 1.4.1.0
 *
 * Test case:
 * 1) Have Magento instance without flat sales yet, and without Mage_SalesRule/sql/mysql4-upgrade-0.7.10-0.7.11.php
 * 2) Upgrade it to the flat one instantly (runs this upgrade). Without this code the proper upgrade of coupon_code is missed. Data is lost.
 * 3) The Mage_SalesRule/sql/mysql4-upgrade-0.7.10-0.7.11.php runs AFTER this code, because it depends on Mage_Sales. But it is too late.
 * Result: the attribute has wrong type and data may be lost depending on upgrade history.
 */
$orderEntityType = $installer->getEntityType('order');
$orderEntityTypeId = $orderEntityType['entity_type_id'];
$attribute = $installer->getAttribute($orderEntityTypeId, 'coupon_code');

if ($attribute && is_array($attribute) && isset($attribute['backend_type']) && $attribute['backend_type'] !== 'static') {
    try {
        $installer->getConnection()->beginTransaction();
        $installer->run("
            UPDATE {$installer->getTable('sales_flat_order')} AS o, {$installer->getTable('sales_order_entity_varchar')} AS od
            SET o.{$attribute['attribute_code']} = od.value
            WHERE od.entity_id = o.entity_id
                AND od.attribute_id = {$attribute['attribute_id']}
                AND od.entity_type_id = {$orderEntityTypeId}
        ");
        $installer->updateAttribute($orderEntityTypeId, $attribute['attribute_code'], array('backend_type' => 'static'));
        $installer->getConnection()->commit();
    } catch (Exception $e) {
        $installer->getConnection()->rollback();
        throw $e;
    }
}

// Remove previous tables
$tablesToDrop = array(
    'sales_order_entity_decimal',
    'sales_order_entity_datetime',
    'sales_order_entity_int',
    'sales_order_entity_text',
    'sales_order_entity_varchar',
    'sales_order_entity',
    'sales_order_decimal',
    'sales_order_datetime',
    'sales_order_int',
    'sales_order_text',
    'sales_order_varchar',
    'sales_order'
);

foreach ($tablesToDrop as $table) {
    $table = $installer->getTable($table);
    if (!$installer->tableExists($table)) {
        continue;
    }
    $installer->getConnection()->query(
        'DROP TABLE ' . $installer->getConnection()->quoteIdentifier($table)
    );
}


/* Add columns to tables */
$tableData = array(
    'sales/quote_item' => array(
        'price_incl_tax' => 'decimal',
        'base_price_incl_tax' => 'decimal',
        'row_total_incl_tax' => 'decimal',
        'base_row_total_incl_tax' => 'decimal'
    ),
    'sales/order_item' => array(
        'price_incl_tax' => 'decimal',
        'base_price_incl_tax' => 'decimal',
        'row_total_incl_tax' => 'decimal',
        'base_row_total_incl_tax' => 'decimal'
    ),
    'sales/quote_address' => array(
        'shipping_discount_amount' => 'decimal',
        'base_shipping_discount_amount' => 'decimal',
        'subtotal_incl_tax' => 'decimal',
        'base_subtotal_total_incl_tax' => 'decimal',
        'discount_description' => 'varchar'
    ),
    'sales/quote_address_item' => array(
        'product_id' => 'int',
        'super_product_id' => 'int',
        'parent_product_id' => 'int',
        'sku' => 'varchar',
        'image' => 'varchar',
        'name' => 'varchar',
        'description' => 'text',
        'free_shipping' => 'int',
        'is_qty_decimal' => 'int',
        'price' => 'decimal',
        'discount_percent' => 'decimal',
        'no_discount' => 'int',
        'tax_percent' => 'decimal',
        'base_price' => 'decimal',
        'price_incl_tax' => 'decimal',
        'base_price_incl_tax' => 'decimal',
        'row_total_incl_tax' => 'decimal',
        'base_row_total_incl_tax' => 'decimal'
    ),
    'sales/quote_payment' => array(
        'additional_data' => 'text',
        'cc_ss_issue' => 'varchar'
    ),
    'sales/quote_address_shipping_rate' => array(
        'error_message' => 'text'
    )
);

foreach ($tableData as $table => $columns) {
    foreach ($columns as $columnName => $columnType) {
        $installer->getConnection()->addColumn(
            $installer->getTable($table), $columnName, $definitions[$columnType]
        );
    }
}

$installer->endSetup();
