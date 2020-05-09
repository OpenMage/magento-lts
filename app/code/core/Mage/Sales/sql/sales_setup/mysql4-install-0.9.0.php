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

$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `converted_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `is_active` tinyint(1) unsigned default '1',
    `is_virtual` tinyint(1) unsigned default '0',
    `is_multi_shipping` tinyint(1) unsigned default '0',

    `items_count` int(10) unsigned default '0',
    `items_qty` decimal(12,4) default '0.0000',

    `orig_order_id` int(10) unsigned default '0',

    `store_to_base_rate` decimal(12,4) default '0.0000',
    `store_to_quote_rate` decimal(12,4) default '0.0000',
    `base_currency_code` varchar(255) default NULL,
    `store_currency_code` varchar(255) default NULL,
    `quote_currency_code` varchar(255) default NULL,

    `grand_total` decimal(12,4) default '0.0000',
    `base_grand_total` decimal(12,4) default '0.0000',

    `checkout_method` varchar(255) default NULL,

    `customer_id` int(10) unsigned default '0',
    `customer_tax_class_id` int(10) unsigned default '0',
    `customer_group_id` int(10) unsigned default '0',
    `customer_email` varchar(255) default NULL,
    `customer_prefix` varchar(40) default NULL,
    `customer_firstname` varchar(255) default NULL,
    `customer_middlename` varchar(40) default NULL,
    `customer_lastname` varchar(255) default NULL,
    `customer_suffix` varchar(40) default NULL,
    `customer_dob` datetime default NULL,
    `customer_note` varchar(255) default NULL,
    `customer_note_notify` tinyint(1) unsigned default '1',
    `customer_is_guest` tinyint(1) unsigned default '0',

    `remote_ip` varchar(32) default NULL,
    `applied_rule_ids` varchar(255) default NULL,
    `reserved_order_id` varchar(64) default '',
    `password_hash` varchar(255) default NULL,
    `coupon_code` varchar(255) default NULL,
    PRIMARY KEY  (`entity_id`),
    KEY `FK_SALES_QUOTE_STORE` (`store_id`),
    KEY `IDX_CUSTOMER` (`customer_id`,`store_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote_address')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote_address')}` (
    `address_id` int(10) unsigned NOT NULL auto_increment,
    `quote_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `customer_id` int(10) unsigned default NULL,
    `save_in_address_book` tinyint(1) default '0',
    `customer_address_id` int(10) unsigned default NULL,
    `address_type` varchar(255) default NULL,
    `email` varchar(255) default NULL,
    `prefix` varchar(40) default NULL,
    `firstname` varchar(255) default NULL,
    `middlename` varchar(40) default NULL,
    `lastname` varchar(255) default NULL,
    `suffix` varchar(40) default NULL,
    `company` varchar(255) default NULL,
    `street` varchar(255) default NULL,
    `city` varchar(255) default NULL,
    `region` varchar(255) default NULL,
    `region_id` int(10) unsigned default NULL,
    `postcode` varchar(255) default NULL,
    `country_id` varchar(255) default NULL,
    `telephone` varchar(255) default NULL,
    `fax` varchar(255) default NULL,

    `same_as_billing` tinyint(1) unsigned NOT NULL default '0',
    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `collect_shipping_rates` tinyint(1) unsigned NOT NULL default '0',
    `shipping_method` varchar(255) NOT NULL default '',
    `shipping_description` varchar(255) NOT NULL default '',
    `weight` decimal(12,4) NOT NULL default '0.0000',

    `subtotal` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal` decimal(12,4) NOT NULL default '0.0000',
    `subtotal_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `shipping_tax_amount` decimal(12,4) default NULL,
    `base_shipping_tax_amount` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `grand_total` decimal(12,4) NOT NULL default '0.0000',
    `base_grand_total` decimal(12,4) NOT NULL default '0.0000',

    `customer_notes` text,
    PRIMARY KEY  (`address_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_SALES_QUOTE` (`quote_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_SALES_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote_address_item')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote_address_item')}` (
    `address_item_id` int(10) unsigned NOT NULL auto_increment,
    `quote_address_id` int(10) unsigned NOT NULL default '0',
    `quote_item_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `applied_rule_ids` text,
    `additional_data` text,
    `weight` decimal(12,4) default '0.0000',
    `qty` decimal(12,4) NOT NULL default '0.0000',
    `discount_amount` decimal(12,4) default '0.0000',
    `tax_amount` decimal(12,4) default '0.0000',

    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_total_with_discount` decimal(12,4) default '0.0000',
    `base_discount_amount` decimal(12,4) default '0.0000',
    `base_tax_amount` decimal(12,4) default '0.0000',
    `row_weight` decimal(12,4) default '0.0000',
    PRIMARY KEY  (`address_item_id`),
    KEY `FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS` (`quote_address_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM` (`quote_item_id`),
    CONSTRAINT `FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS` FOREIGN KEY (`quote_address_id`) REFERENCES `{$installer->getTable('sales_flat_quote_address')}` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM` FOREIGN KEY (`quote_item_id`) REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote_item')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote_item')}` (
    `item_id` int(10) unsigned NOT NULL auto_increment,
    `quote_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `product_id` int(10) unsigned default NULL,
    `super_product_id` int(10) unsigned default NULL,
    `parent_product_id` int(10) unsigned default NULL,
    `is_virtual` tinyint(1) unsigned default NULL,

    `sku` varchar(255) NOT NULL default '',
    `name` varchar(255) default NULL,
    `description` text,
    `applied_rule_ids` text,
    `additional_data` text,
    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `is_qty_decimal` tinyint(1) unsigned default NULL,
    `no_discount` tinyint(1) unsigned default '0',

    `weight` decimal(12,4) default '0.0000',
    `qty` decimal(12,4) NOT NULL default '0.0000',
    `price` decimal(12,4) NOT NULL default '0.0000',
    `base_price` decimal(12,4) NOT NULL default '0.0000',
    `custom_price` decimal(12,4) default NULL,
    `discount_percent` decimal(12,4) default '0.0000',
    `discount_amount` decimal(12,4) default '0.0000',
    `base_discount_amount` decimal(12,4) default '0.0000',
    `tax_percent` decimal(12,4) default '0.0000',
    `tax_amount` decimal(12,4) default '0.0000',
    `base_tax_amount` decimal(12,4) default '0.0000',
    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_total_with_discount` decimal(12,4) default '0.0000',
    `row_weight` decimal(12,4) default '0.0000',
    PRIMARY KEY  (`item_id`),
    KEY `FK_SALES_QUOTE_ITEM_SALES_QUOTE` (`quote_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_SALES_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote_item_option')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote_item_option')}` (
    `option_id` int(10) unsigned NOT NULL auto_increment,
    `item_id` int(10) unsigned NOT NULL,
    `product_id` int(10) unsigned NOT NULL,
    `code` varchar(255) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY  (`option_id`),
    KEY `FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID` (`item_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID` FOREIGN KEY (`item_id`) REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Additional options for quote item';

DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote_payment')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote_payment')}` (
    `payment_id` int(10) unsigned NOT NULL auto_increment,
    `quote_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `method` varchar(255) default '',

    `cc_type` varchar(255) default '',
    `cc_number_enc` varchar(255) default '',
    `cc_last4` varchar(255) default '',
    `cc_cid_enc` varchar(255) default '',
    `cc_owner` varchar(255) default '',
    `cc_exp_month` tinyint(2) unsigned default '0',
    `cc_exp_year` smallint(4) unsigned default '0',
    `cc_ss_owner` varchar(255) default '',
    `cc_ss_start_month` tinyint(2) unsigned default '0',
    `cc_ss_start_year` smallint(4) unsigned default '0',

    `cybersource_token` varchar(255) default '',
    `paypal_correlation_id` varchar(255) default '',
    `paypal_payer_id` varchar(255) default '',
    `paypal_payer_status` varchar(255) default '',
    `po_number` varchar(255) default '',
    PRIMARY KEY  (`payment_id`),
    KEY `FK_SALES_QUOTE_PAYMENT_SALES_QUOTE` (`quote_id`),
    CONSTRAINT `FK_SALES_QUOTE_PAYMENT_SALES_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_flat_quote_shipping_rate')}`;
CREATE TABLE `{$installer->getTable('sales_flat_quote_shipping_rate')}` (
    `rate_id` int(10) unsigned NOT NULL auto_increment,
    `address_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `carrier` varchar(255) default NULL,
    `carrier_title` varchar(255) default NULL,
    `code` varchar(255) default NULL,
    `method` varchar(255) default NULL,
    `method_description` text,
    `price` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`rate_id`),
    KEY `FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS` (`address_id`),
    CONSTRAINT `FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS` FOREIGN KEY (`address_id`) REFERENCES `{$installer->getTable('sales_flat_quote_address')}` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_order')}`;
CREATE TABLE `{$installer->getTable('sales_order')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `entity_type_id` smallint(5) unsigned NOT NULL default '0',
    `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
    `increment_id` varchar(50) NOT NULL default '',
    `parent_id` int(10) unsigned NOT NULL default '0',
    `store_id` smallint(5) unsigned default NULL,
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `is_active` tinyint(1) unsigned NOT NULL default '1',
    `customer_id` int(11) default NULL,
    `tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `subtotal` decimal(12,4) NOT NULL default '0.0000',
    `grand_total` decimal(12,4) NOT NULL default '0.0000',
    `total_paid` decimal(12,4) NOT NULL default '0.0000',
    `total_refunded` decimal(12,4) NOT NULL default '0.0000',
    `total_qty_ordered` decimal(12,4) NOT NULL default '0.0000',
    `total_canceled` decimal(12,4) NOT NULL default '0.0000',
    `total_invoiced` decimal(12,4) NOT NULL default '0.0000',
    `total_online_refunded` decimal(12,4) NOT NULL default '0.0000',
    `total_offline_refunded` decimal(12,4) NOT NULL default '0.0000',
    `base_tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal` decimal(12,4) NOT NULL default '0.0000',
    `base_grand_total` decimal(12,4) NOT NULL default '0.0000',
    `base_total_paid` decimal(12,4) NOT NULL default '0.0000',
    `base_total_refunded` decimal(12,4) NOT NULL default '0.0000',
    `base_total_qty_ordered` decimal(12,4) NOT NULL default '0.0000',
    `base_total_canceled` decimal(12,4) NOT NULL default '0.0000',
    `base_total_invoiced` decimal(12,4) NOT NULL default '0.0000',
    `base_total_online_refunded` decimal(12,4) NOT NULL default '0.0000',
    `base_total_offline_refunded` decimal(12,4) NOT NULL default '0.0000',
    `subtotal_refunded` decimal(12,4) default NULL,
    `subtotal_canceled` decimal(12,4) default NULL,
    `tax_refunded` decimal(12,4) default NULL,
    `tax_canceled` decimal(12,4) default NULL,
    `shipping_refunded` decimal(12,4) default NULL,
    `shipping_canceled` decimal(12,4) default NULL,
    `base_subtotal_refunded` decimal(12,4) default NULL,
    `base_subtotal_canceled` decimal(12,4) default NULL,
    `base_tax_refunded` decimal(12,4) default NULL,
    `base_tax_canceled` decimal(12,4) default NULL,
    `base_shipping_refunded` decimal(12,4) default NULL,
    `base_shipping_canceled` decimal(12,4) default NULL,
    `subtotal_invoiced` decimal(12,4) default NULL,
    `tax_invoiced` decimal(12,4) default NULL,
    `shipping_invoiced` decimal(12,4) default NULL,
    `base_subtotal_invoiced` decimal(12,4) default NULL,
    `base_tax_invoiced` decimal(12,4) default NULL,
    `base_shipping_invoiced` decimal(12,4) default NULL,
    `shipping_tax_amount` decimal(12,4) default NULL,
    `base_shipping_tax_amount` decimal(12,4) default NULL,
    PRIMARY KEY  (`entity_id`),
    KEY `FK_SALES_ORDER_TYPE` (`entity_type_id`),
    KEY `FK_SALES_ORDER_STORE` (`store_id`),
    KEY `IDX_CUSTOMER` (`customer_id`),
    CONSTRAINT `FK_SALE_ORDER_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_SALE_ORDER_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_order')}_datetime`;
CREATE TABLE `{$this->getTable('sales_order')}_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_DATETIME` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_DATETIME` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_order')}_decimal`;
CREATE TABLE `{$this->getTable('sales_order')}_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_DECIMAL` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_DECIMAL` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_order')}_int`;
CREATE TABLE `{$this->getTable('sales_order')}_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_INT` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_INT` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_order')}_text`;
CREATE TABLE `{$this->getTable('sales_order')}_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_TEXT` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_TEXT` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `{$installer->getTable('sales_order')}_varchar`;
CREATE TABLE `{$this->getTable('sales_order')}_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_VARCHAR` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_VARCHAR` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity')}`;
CREATE TABLE `{$installer->getTable('sales_order_entity')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
    `increment_id` varchar(50) NOT NULL default '',
    `parent_id` int(10) unsigned NOT NULL default '0',
    `store_id` smallint(5) unsigned default NULL,
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `is_active` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY  (`entity_id`),
    KEY `FK_SALES_ORDER_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_ORDER_ENTITY_STORE` (`store_id`),
    CONSTRAINT `FK_SALES_ORDER_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALE_ORDER_ENTITY_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `{$this->getTable('sales_order_entity')}_datetime`;
CREATE TABLE `{$this->getTable('sales_order_entity')}_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_ENTITY_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_ENTITY_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_ENTITY_DATETIME` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_ENTITY_DATETIME` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('sales_order_entity')}_decimal`;
CREATE TABLE `{$installer->getTable('sales_order_entity')}_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_ENTITY_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_ENTITY_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_ENTITY_DECIMAL` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_ENTITY_DECIMAL` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('sales_order_entity')}_int`;
CREATE TABLE `{$installer->getTable('sales_order_entity')}_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_ENTITY_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_ENTITY_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_ENTITY_INT` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_ENTITY_INT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('sales_order_entity')}_text`;
CREATE TABLE `{$installer->getTable('sales_order_entity')}_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_ENTITY_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_ENTITY_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_ENTITY_TEXT` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_ENTITY_TEXT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('sales_order_entity')}_varchar`;
CREATE TABLE `{$installer->getTable('sales_order_entity')}_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SALES_ORDER_ENTITY_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SALES_ORDER_ENTITY_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SALES_ORDER_ENTITY_VARCHAR` (`entity_id`),
  CONSTRAINT `FK_SALES_ORDER_ENTITY_VARCHAR` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_ENTITY_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->installEntities();
$installer->endSetup();
