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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$this->startSetup();
$this->run("
DROP TABLE IF EXISTS `{$installer->getTable('sales_quote')}`;
CREATE TABLE `{$installer->getTable('sales_quote')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
    `parent_id` int(10) unsigned NOT NULL default '0',
    `store_id` smallint(5) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `converted_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `is_active` tinyint(1) unsigned NOT NULL default '1',
    `is_virtual` tinyint(1) unsigned NOT NULL default '0',
    `is_multi_shipping` tinyint(1) unsigned NOT NULL default '0',
    `is_multi_payment` tinyint(1) unsigned NOT NULL default '0',
    `customer_note_notify` tinyint(1) unsigned NOT NULL default '1',
    `customer_is_guest` tinyint(1) unsigned NOT NULL default '0',

    `quote_status_id` int(10) unsigned NOT NULL,
    `billing_address_id` int(10) unsigned NOT NULL,
    `orig_order_id` int(10) unsigned NOT NULL,
    `customer_id` int(10) unsigned NOT NULL,
    `customer_tax_class_id` int(10) unsigned NOT NULL,
    `customer_group_id` int(10) unsigned NOT NULL,
    `items_count` int(10) unsigned NOT NULL,

    `items_qty` decimal(12,4) NOT NULL default '0.0000',
    `store_to_base_rate` decimal(12,4) NOT NULL default '0.0000',
    `store_to_quote_rate` decimal(12,4) NOT NULL default '0.0000',
    `grand_total` decimal(12,4) NOT NULL default '0.0000',
    `base_grand_total` decimal(12,4) NOT NULL default '0.0000',
    `custbalance_amount` decimal(12,4) NOT NULL default '0.0000',

    `checkout_method` varchar(255) NOT NULL default '',
    `password_hash` varchar(255) NOT NULL default '',
    `coupon_code` varchar(255) NOT NULL default '',
    `base_currency_code` varchar(255) NOT NULL default '',
    `store_currency_code` varchar(255) NOT NULL default '',
    `quote_currency_code` varchar(255) NOT NULL default '',
    `customer_email` varchar(255) NOT NULL default '',
    `customer_firstname` varchar(255) NOT NULL default '',
    `customer_lastname` varchar(255) NOT NULL default '',
    `customer_note` varchar(255) NOT NULL default '',
    `remote_ip` varchar(255) NOT NULL default '',
    `applied_rule_ids` varchar(255) NOT NULL default '',



    PRIMARY KEY  (`entity_id`),
    KEY `FK_SALES_QUOTE_STORE` (`store_id`),
    CONSTRAINT `FK_SALES_QUOTE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_decimal')}`;
CREATE TABLE `{$installer->getTable('sales_quote_decimal')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_DECIMAL_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_DECIMAL` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_DECIMAL` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_int')}`;
CREATE TABLE `{$installer->getTable('sales_quote_int')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` int(11) NOT NULL default '0',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_INT_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_INT_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_INT` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_INT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_text')}`;
CREATE TABLE `{$installer->getTable('sales_quote_text')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` text NOT NULL,
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_TEXT_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_TEXT_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_TEXT` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_TEXT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_varchar')}`;
CREATE TABLE `{$installer->getTable('sales_quote_varchar')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_VARCHAR_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_VARCHAR` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_VARCHAR` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_item')}`;
CREATE TABLE `{$installer->getTable('sales_quote_item')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
    `parent_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `product_id` int(10) unsigned,
    `super_product_id` int(10) unsigned,
    `parent_product_id` int(10) unsigned,

    `sku` varchar(255) NOT NULL default '',
    `name` varchar(255),

    `description` text,
    `applied_rule_ids` text,
    `additional_data` text,

    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `is_qty_decimal` tinyint(1) unsigned,
    `no_discount` tinyint(1) unsigned default '0',

    `weight` decimal(12,4) NOT NULL default '0.0000',
    `qty` decimal(12,4) NOT NULL default '0.0000',
    `price` decimal(12,4) NOT NULL default '0.0000',
    `discount_percent` decimal(12,4) NOT NULL default '0.0000',
    `discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `tax_percent` decimal(12,4) NOT NULL default '0.0000',
    `tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_total_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `base_price` decimal(12,4) NOT NULL default '0.0000',
    `base_discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_weight` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_QUOTE` FOREIGN KEY (`parent_id`) REFERENCES `{$installer->getTable('sales_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_item_decimal')}`;
CREATE TABLE `{$installer->getTable('sales_quote_item_decimal')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ITEM_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ITEM_DECIMAL_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ITEM_DECIMAL` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_DECIMAL` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_item')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_item_int')}`;
CREATE TABLE `{$installer->getTable('sales_quote_item_int')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` int(11) NOT NULL default '0',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ITEM_INT_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ITEM_INT_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ITEM_INT` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_INT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_item')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_item_text')}`;
CREATE TABLE `{$installer->getTable('sales_quote_item_text')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` text NOT NULL,
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ITEM_TEXT_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ITEM_TEXT_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ITEM_TEXT` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_TEXT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_item')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_item_varchar')}`;
CREATE TABLE `{$installer->getTable('sales_quote_item_varchar')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ITEM_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ITEM_VARCHAR_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ITEM_VARCHAR` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_VARCHAR` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_item')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ITEM_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;











DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_address')}`;
CREATE TABLE `{$installer->getTable('sales_quote_address')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
    `parent_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `customer_id` int(10) unsigned,
    `customer_address_id` int(10) unsigned,

    `address_type` varchar(255),
    `email` varchar(255),
    `firstname` varchar(255),
    `lastname` varchar(255),
    `company` varchar(255),
    `street` varchar(255),
    `city` varchar(255),
    `region` varchar(255),
    `region_id` int(10) unsigned,
    `postcode` varchar(255),
    `country_id` varchar(255),
    `telephone` varchar(255),
    `fax` varchar(255),

    `same_as_billing` tinyint(1) unsigned NOT NULL default '0',
    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `collect_shipping_rates` tinyint(1) unsigned NOT NULL default '0',

    `shipping_method` varchar(255) NOT NULL default '',
    `shipping_description` varchar(255) NOT NULL default '',

    `weight` decimal(12,4) NOT NULL default '0.0000',
    `subtotal` decimal(12,4) NOT NULL default '0.0000',
    `subtotal_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `custbalance_amount` decimal(12,4) NOT NULL default '0.0000',
    `grand_total` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `base_tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_custbalance_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_grand_total` decimal(12,4) NOT NULL default '0.0000',

    `customer_notes` text,

    PRIMARY KEY  (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_QUOTE` FOREIGN KEY (`parent_id`) REFERENCES `{$installer->getTable('sales_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_address_decimal')}`;
CREATE TABLE `{$installer->getTable('sales_quote_address_decimal')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_DECIMAL_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_DECIMAL` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_DECIMAL` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_address')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_address_int')}`;
CREATE TABLE `{$installer->getTable('sales_quote_address_int')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` int(11) NOT NULL default '0',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_INT_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_INT_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_INT` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_INT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_address')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_address_text')}`;
CREATE TABLE `{$installer->getTable('sales_quote_address_text')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` text NOT NULL,
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_TEXT_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_TEXT_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_TEXT` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_TEXT` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_address')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_address_varchar')}`;
CREATE TABLE `{$installer->getTable('sales_quote_address_varchar')}` (
    `value_id` int(11) NOT NULL auto_increment,
    `entity_type_id` smallint(8) unsigned NOT NULL default '0',
    `attribute_id` smallint(5) unsigned NOT NULL default '0',
    `entity_id` int(10) unsigned NOT NULL default '0',
    `value` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`value_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_VARCHAR_ATTRIBUTE` (`attribute_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_VARCHAR` (`entity_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_VARCHAR` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_address')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp')}`;
DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_datetime')}`;
DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_decimal')}`;
DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_int')}`;
DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_text')}`;
DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_varchar')}`;
");
$this->endSetup();
$this->installEntities();
