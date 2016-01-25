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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

/*Table structure for table `sales_counter` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_counter')};
CREATE TABLE {$this->getTable('sales_counter')} (
  `counter_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` int(10) unsigned NOT NULL default '0',
  `counter_type` varchar(50) NOT NULL default '',
  `counter_value` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`counter_id`),
  UNIQUE KEY `store_id` (`store_id`,`counter_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_counter` */

/*Table structure for table `sales_discount_coupon` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_discount_coupon')};

CREATE TABLE {$this->getTable('sales_discount_coupon')} (
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

/*Data for the table `sales_discount_coupon` */

insert  into {$this->getTable('sales_discount_coupon')}(`coupon_id`,`coupon_code`,`discount_percent`,`discount_fixed`,
    `is_active`,`from_date`,`to_date`,`min_subtotal`,`limit_products`,`limit_categories`,`limit_attributes`)
values (1,'test',10.0000,0.0000,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0.0000,'','','');

/*Table structure for table `sales_invoice_entity` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity')};

CREATE TABLE {$this->getTable('sales_invoice_entity')} (
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
  KEY `FK_sales_invoice_entity_type` (`entity_type_id`),
  KEY `FK_sales_invoice_entity_store` (`store_id`),
  CONSTRAINT `FK_sales_invoice_entity_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_entity` */

/*Table structure for table `sales_invoice_entity_datetime` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_datetime')};
CREATE TABLE {$this->getTable('sales_invoice_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_invoice_entity_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_invoice_entity_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_invoice_entity_datetime_store` (`store_id`),
  KEY `FK_sales_invoice_entity_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_invoice_entity_datetime` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_invoice_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_datetime_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_datetime_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_datetime_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_entity_datetime` */

/*Table structure for table `sales_invoice_entity_decimal` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_decimal')};

CREATE TABLE {$this->getTable('sales_invoice_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_invoice_entity_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_invoice_entity_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_invoice_entity_decimal_store` (`store_id`),
  KEY `FK_sales_invoice_entity_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_invoice_entity_decimal` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_invoice_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_decimal_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_decimal_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_decimal_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_entity_decimal` */

/*Table structure for table `sales_invoice_entity_int` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_int')};

CREATE TABLE {$this->getTable('sales_invoice_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_invoice_entity_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_invoice_entity_int_attribute` (`attribute_id`),
  KEY `FK_sales_invoice_entity_int_store` (`store_id`),
  KEY `FK_sales_invoice_entity_int` (`entity_id`),
  CONSTRAINT `FK_sales_invoice_entity_int` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_invoice_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_int_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_int_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_int_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_entity_int` */

/*Table structure for table `sales_invoice_entity_text` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_text')};
CREATE TABLE {$this->getTable('sales_invoice_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_invoice_entity_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_invoice_entity_text_attribute` (`attribute_id`),
  KEY `FK_sales_invoice_entity_text_store` (`store_id`),
  KEY `FK_sales_invoice_entity_text` (`entity_id`),
  CONSTRAINT `FK_sales_invoice_entity_text` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_invoice_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_text_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_text_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_text_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_entity_text` */

/*Table structure for table `sales_invoice_entity_varchar` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_varchar')};
CREATE TABLE {$this->getTable('sales_invoice_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_invoice_entity_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_invoice_entity_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_invoice_entity_varchar_store` (`store_id`),
  KEY `FK_sales_invoice_entity_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_invoice_entity_varchar` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_invoice_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_varchar_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_varchar_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_invoice_entity_varchar_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_entity_varchar` */

/*Table structure for table `sales_order_entity` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_order_entity')};
CREATE TABLE {$this->getTable('sales_order_entity')} (
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
  KEY `FK_sales_order_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_store` (`store_id`),
  CONSTRAINT `FK_sales_order_entity_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `sales_order_entity` */

/*Table structure for table `sales_order_entity_datetime` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_order_entity_datetime')};
CREATE TABLE {$this->getTable('sales_order_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_datetime_store` (`store_id`),
  KEY `FK_sales_order_entity_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_datetime` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_order_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_datetime_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_datetime_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_datetime_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_order_entity_datetime` */

/*Table structure for table `sales_order_entity_decimal` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_order_entity_decimal')};
CREATE TABLE {$this->getTable('sales_order_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_decimal_store` (`store_id`),
  KEY `FK_sales_order_entity_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_decimal` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_order_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_decimal_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_decimal_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_decimal_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_order_entity_decimal` */

/*Table structure for table `sales_order_entity_int` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_order_entity_int')};
CREATE TABLE {$this->getTable('sales_order_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_int_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_int_store` (`store_id`),
  KEY `FK_sales_order_entity_int` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_int` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_order_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_int_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_int_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_int_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_order_entity_int` */

/*Table structure for table `sales_order_entity_text` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_order_entity_text')};
CREATE TABLE {$this->getTable('sales_order_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_text_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_text_store` (`store_id`),
  KEY `FK_sales_order_entity_text` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_text` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_order_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_text_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_text_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_text_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_order_entity_text` */

/*Table structure for table `sales_order_entity_varchar` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_order_entity_varchar')};
CREATE TABLE {$this->getTable('sales_order_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_entity_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_entity_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_order_entity_varchar_store` (`store_id`),
  KEY `FK_sales_order_entity_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_order_entity_varchar` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_order_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_varchar_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_varchar_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_entity_varchar_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_order_entity_varchar` */

/*Table structure for table `sales_quote_entity` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity')};
CREATE TABLE {$this->getTable('sales_quote_entity')} (
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
  CONSTRAINT `FK_sales_quote_entity_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `sales_quote_entity_datetime` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_datetime')};
CREATE TABLE {$this->getTable('sales_quote_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_datetime_store` (`store_id`),
  KEY `FK_sales_quote_entity_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_datetime` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_datetime_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_datetime_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_datetime_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_entity_datetime` */

/*Table structure for table `sales_quote_entity_decimal` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_decimal')};
CREATE TABLE {$this->getTable('sales_quote_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_decimal_store` (`store_id`),
  KEY `FK_sales_quote_entity_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_decimal` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_decimal_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_decimal_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_decimal_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*Table structure for table `sales_quote_entity_int` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_int')};
CREATE TABLE {$this->getTable('sales_quote_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_int_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_int_store` (`store_id`),
  KEY `FK_sales_quote_entity_int` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_int` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_int_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_int_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_int_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*Table structure for table `sales_quote_entity_text` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_text')};
CREATE TABLE {$this->getTable('sales_quote_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_text_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_text_store` (`store_id`),
  KEY `FK_sales_quote_entity_text` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_text` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_text_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_text_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_text_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_entity_text` */

/*Table structure for table `sales_quote_entity_varchar` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_varchar')};
CREATE TABLE {$this->getTable('sales_quote_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_entity_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_entity_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_quote_entity_varchar_store` (`store_id`),
  KEY `FK_sales_quote_entity_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_quote_entity_varchar` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_varchar_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_varchar_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_entity_varchar_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sales_quote_rule` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_rule')};
CREATE TABLE {$this->getTable('sales_quote_rule')} (
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
  KEY `is_active` (`is_active`,`start_at`,`expire_at`,`coupon_code`,`customer_registered`,
    `customer_new_buyer`,`show_in_catalog`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_rule` */

/*Table structure for table `sales_quote_temp` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_temp')};
CREATE TABLE {$this->getTable('sales_quote_temp')} (
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
  CONSTRAINT `FK_sales_quote_temp_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `sales_quote_temp` */

/*Table structure for table `sales_quote_temp_datetime` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_temp_datetime')};
CREATE TABLE {$this->getTable('sales_quote_temp_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_datetime_store` (`store_id`),
  KEY `FK_sales_quote_temp_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_datetime` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_temp')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_datetime_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_datetime_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_datetime_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_temp_datetime` */

/*Table structure for table `sales_quote_temp_decimal` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_temp_decimal')};
CREATE TABLE {$this->getTable('sales_quote_temp_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_decimal_store` (`store_id`),
  KEY `FK_sales_quote_temp_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_decimal` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_temp')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_decimal_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_decimal_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_decimal_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_temp_decimal` */

/*Table structure for table `sales_quote_temp_int` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_temp_int')};
CREATE TABLE {$this->getTable('sales_quote_temp_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_int_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_int_store` (`store_id`),
  KEY `FK_sales_quote_temp_int` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_int` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_temp')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_int_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_int_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_int_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_temp_int` */

/*Table structure for table `sales_quote_temp_text` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_temp_text')};
CREATE TABLE {$this->getTable('sales_quote_temp_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_text_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_text_store` (`store_id`),
  KEY `FK_sales_quote_temp_text` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_text` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_temp')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_text_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_text_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_text_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_temp_text` */

/*Table structure for table `sales_quote_temp_varchar` */

-- DROP TABLE IF EXISTS {$this->getTable('sales_quote_temp_varchar')};
CREATE TABLE {$this->getTable('sales_quote_temp_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_quote_temp_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_quote_temp_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_quote_temp_varchar_store` (`store_id`),
  KEY `FK_sales_quote_temp_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_quote_temp_varchar` FOREIGN KEY (`entity_id`)
  REFERENCES {$this->getTable('sales_quote_temp')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_varchar_attribute` FOREIGN KEY (`attribute_id`)
  REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_varchar_entity_type` FOREIGN KEY (`entity_type_id`)
  REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_quote_temp_varchar_store` FOREIGN KEY (`store_id`)
  REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_quote_temp_varchar` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
    ");

$installer->installEntities();

$installer->endSetup();
