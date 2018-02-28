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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales install
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */

$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('sales_counter')}`;
CREATE TABLE `{$installer->getTable('sales_counter')}` (
  `counter_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `counter_type` varchar(50) NOT NULL default '',
  `counter_value` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`counter_id`),
  UNIQUE KEY `store_id` (`store_id`,`counter_type`),
  CONSTRAINT `FK_SALES_COUNTER_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_discount_coupon')}`;
CREATE TABLE `{$installer->getTable('sales_discount_coupon')}` (
  `coupon_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_code` varchar(50) NOT NULL default '',
  `discount_percent` decimal(10,4) NOT NULL default '0.0000',
  `discount_fixed` decimal(10,4) NOT NULL default '0.0000',
  `is_active` tinyint(1) NOT NULL default '1',
  `from_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `to_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `min_subtotal` decimal(12,4) NOT NULL default '0.0000',
  `limit_products` text NOT NULL,
  `limit_categories` text NOT NULL,
  `limit_attributes` text NOT NULL,
  PRIMARY KEY  (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity')}`;
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
  KEY `FK_sales_order_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_store` (`store_id`),
  CONSTRAINT `FK_SALE_ORDER_ENTITY_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity_datetime')}`;
CREATE TABLE `{$installer->getTable('sales_order_entity_datetime')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_datetime` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_datetime_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_datetime_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity_decimal')}`;
CREATE TABLE `{$installer->getTable('sales_order_entity_decimal')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_decimal` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_decimal_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_decimal_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity_int')}`;
CREATE TABLE `{$installer->getTable('sales_order_entity_int')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_int_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_int` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_int` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_int_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_int_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity_text')}`;
CREATE TABLE `{$installer->getTable('sales_order_entity_text')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_text_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_text` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_text` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_text_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_text_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_order_entity_varchar')}`;
CREATE TABLE `{$installer->getTable('sales_order_entity_varchar')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_varchar` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_order_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_varchar_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_varchar_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_entity')}`;
CREATE TABLE `{$installer->getTable('sales_quote_entity')}` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `increment_id` varchar(50) NOT NULL default '',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_sales_quote_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_store` (`store_id`),
  CONSTRAINT `FK_sales_quote_entity_store` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_entity_datetime')}`;
CREATE TABLE `{$installer->getTable('sales_quote_entity_datetime')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_datetime` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_datetime_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_datetime_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_entity_decimal')}`;
CREATE TABLE `{$installer->getTable('sales_quote_entity_decimal')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_decimal` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_decimal_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_decimal_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_entity_int')}`;
CREATE TABLE `{$installer->getTable('sales_quote_entity_int')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_int_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_int` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_int` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_int_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_int_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_entity_text')}`;
CREATE TABLE `{$installer->getTable('sales_quote_entity_text')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_text_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_text` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_text` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_text_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_text_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_entity_varchar')}`;
CREATE TABLE `{$installer->getTable('sales_quote_entity_varchar')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_varchar` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_varchar_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_varchar_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_rule')}`;
CREATE TABLE `{$installer->getTable('sales_quote_rule')}` (
  `quote_rule_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `is_active` tinyint(4) NOT NULL default '0',
  `start_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `expire_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `coupon_code` varchar(50) NOT NULL default '',
  `customer_registered` tinyint(1) NOT NULL default '2',
  `customer_new_buyer` tinyint(1) NOT NULL default '2',
  `show_in_catalog` tinyint(1) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `conditions_serialized` text NOT NULL,
  `actions_serialized` text NOT NULL,
  PRIMARY KEY  (`quote_rule_id`),
  KEY `rule_name` (`name`),
  KEY `is_active` (`is_active`,`start_at`,`expire_at`,`coupon_code`,`customer_registered`,`customer_new_buyer`,`show_in_catalog`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp')}`;
CREATE TABLE `{$installer->getTable('sales_quote_temp')}` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `increment_id` varchar(50) NOT NULL default '',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_sales_quote_temp_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_store` (`store_id`),
  CONSTRAINT `FK_sales_quote_temp_store` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_datetime')}`;
CREATE TABLE `{$installer->getTable('sales_quote_temp_datetime')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_datetime` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_temp')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_datetime_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_datetime_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_decimal')}`;
CREATE TABLE `{$installer->getTable('sales_quote_temp_decimal')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_decimal` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_temp')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_decimal_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_decimal_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_int')}`;
CREATE TABLE `{$installer->getTable('sales_quote_temp_int')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_int_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_int` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_int` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_temp')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_int_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_int_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_text')}`;
CREATE TABLE `{$installer->getTable('sales_quote_temp_text')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_text_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_text` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_text` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_temp')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_text_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_text_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('sales_quote_temp_varchar')}`;
CREATE TABLE `{$installer->getTable('sales_quote_temp_varchar')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_varchar` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_quote_temp')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_varchar_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_varchar_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();

$installer->installEntities();
