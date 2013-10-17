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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('tax_class')}` (
  `class_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL DEFAULT '',
  `class_type` enum('CUSTOMER','PRODUCT') NOT NULL DEFAULT 'CUSTOMER',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('tax_calculation')}` (
  `tax_calculation_rate_id` int(11) NOT NULL,
  `tax_calculation_rule_id` int(11) NOT NULL,
  `customer_tax_class_id` smallint(6) NOT NULL,
  `product_tax_class_id` smallint(6) NOT NULL,
  KEY `FK_TAX_CALCULATION_RULE` (`tax_calculation_rule_id`),
  KEY `FK_TAX_CALCULATION_RATE` (`tax_calculation_rate_id`),
  KEY `FK_TAX_CALCULATION_CTC` (`customer_tax_class_id`),
  KEY `FK_TAX_CALCULATION_PTC` (`product_tax_class_id`),
  KEY `IDX_TAX_CALCULATION` (`tax_calculation_rate_id`,`customer_tax_class_id`,`product_tax_class_id`),
  CONSTRAINT `FK_TAX_CALCULATION_PTC` FOREIGN KEY (`product_tax_class_id`)
    REFERENCES `{$installer->getTable('tax_class')}` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TAX_CALCULATION_CTC` FOREIGN KEY (`customer_tax_class_id`)
    REFERENCES `{$installer->getTable('tax_class')}` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TAX_CALCULATION_RATE` FOREIGN KEY (`tax_calculation_rate_id`)
    REFERENCES `{$installer->getTable('tax_calculation_rate')}` (`tax_calculation_rate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TAX_CALCULATION_RULE` FOREIGN KEY (`tax_calculation_rule_id`)
    REFERENCES `{$installer->getTable('tax_calculation_rule')}` (`tax_calculation_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('tax_calculation_rate')}` (
  `tax_calculation_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_country_id` char(2) NOT NULL,
  `tax_region_id` mediumint(9) NOT NULL,
  `tax_postcode` varchar(21) NOT NULL,
  `code` varchar(255) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `zip_is_range` tinyint(1) DEFAULT NULL,
  `zip_from` int(11) unsigned DEFAULT NULL,
  `zip_to` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`tax_calculation_rate_id`),
  KEY `IDX_TAX_CALCULATION_RATE` (`tax_country_id`,`tax_region_id`,`tax_postcode`),
  KEY `IDX_TAX_CALCULATION_RATE_CODE` (`code`),
  KEY `IDX_TAX_CALCULATION_RATE_RANGE` (`tax_calculation_rate_id`,`tax_country_id`,`tax_region_id`,`zip_is_range`,`tax_postcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('tax_calculation_rate_title')}` (
  `tax_calculation_rate_title_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_calculation_rate_id` int(11) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`tax_calculation_rate_title_id`),
  KEY `IDX_TAX_CALCULATION_RATE_TITLE` (`tax_calculation_rate_id`,`store_id`),
  KEY `FK_TAX_CALCULATION_RATE_TITLE_RATE` (`tax_calculation_rate_id`),
  KEY `FK_TAX_CALCULATION_RATE_TITLE_STORE` (`store_id`),
  CONSTRAINT `FK_TAX_CALCULATION_RATE_TITLE_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TAX_CALCULATION_RATE_TITLE_RATE` FOREIGN KEY (`tax_calculation_rate_id`)
    REFERENCES `{$installer->getTable('tax_calculation_rate')}` (`tax_calculation_rate_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('tax_calculation_rule')}` (
  `tax_calculation_rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `priority` mediumint(9) NOT NULL,
  `position` mediumint(9) NOT NULL,
  PRIMARY KEY (`tax_calculation_rule_id`),
  KEY `IDX_TAX_CALCULATION_RULE` (`priority`,`position`,`tax_calculation_rule_id`),
  KEY `IDX_TAX_CALCULATION_RULE_CODE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('tax_order_aggregated_created')}` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `period` date NOT NULL DEFAULT '0000-00-00',
    `store_id` smallint(5) unsigned DEFAULT NULL,
    `code` varchar(255) NOT NULL DEFAULT '',
    `order_status` varchar(50) NOT NULL DEFAULT '',
    `percent` float(12,4) NOT NULL DEFAULT '0.0000',
    `orders_count` int(11) unsigned NOT NULL DEFAULT '0',
    `tax_base_amount_sum` float(12,4) NOT NULL DEFAULT '0.0000',
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNQ_PERIOD_STORE_CODE_ORDER_STATUS` (`period`,`store_id`,`code`,`order_status`),
    KEY `IDX_STORE_ID` (`store_id`),
    CONSTRAINT `FK_TAX_ORDER_AGGREGATED_CREATED_STORE` FOREIGN KEY (`store_id`)
        REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('tax/sales_order_tax')}` (
    `tax_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `order_id` int(10) unsigned NOT NULL,
    `code` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `percent` decimal(12,4) NOT NULL,
    `amount` decimal(12,4) NOT NULL,
    `priority` int(11) NOT NULL,
    `position` int(11) NOT NULL,
    `base_amount` decimal(12,4) NOT NULL,
    `process` smallint(6) NOT NULL,
    `base_real_amount` decimal(12,4) NOT NULL,
    `hidden` smallint(5) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`tax_id`),
    KEY `IDX_ORDER_TAX` (`order_id`,`priority`,`position`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;

/* Insert initial tax data */
insert  into `{$installer->getTable('tax_class')}`(`class_id`,`class_name`,`class_type`) values (2,'Taxable Goods','PRODUCT');
insert  into `{$installer->getTable('tax_class')}`(`class_id`,`class_name`,`class_type`) values (3,'Retail Customer','CUSTOMER');
insert  into `{$installer->getTable('tax_class')}`(`class_id`,`class_name`,`class_type`) values (4,'Shipping','PRODUCT');
insert  into `{$installer->getTable('tax_calculation')}`(`tax_calculation_rate_id`,`tax_calculation_rule_id`,`customer_tax_class_id`,`product_tax_class_id`) values (1,1,3,2);
insert  into `{$installer->getTable('tax_calculation')}`(`tax_calculation_rate_id`,`tax_calculation_rule_id`,`customer_tax_class_id`,`product_tax_class_id`) values (2,1,3,2);
insert  into `{$installer->getTable('tax_calculation_rate')}`(`tax_calculation_rate_id`,`tax_country_id`,`tax_region_id`,`tax_postcode`,`code`,`rate`,`zip_is_range`,`zip_from`,`zip_to`) values (1,'US',12,'*','US-CA-*-Rate 1','8.2500',NULL,NULL,NULL);
insert  into `{$installer->getTable('tax_calculation_rate')}`(`tax_calculation_rate_id`,`tax_country_id`,`tax_region_id`,`tax_postcode`,`code`,`rate`,`zip_is_range`,`zip_from`,`zip_to`) values (2,'US',43,'*','US-NY-*-Rate 1','8.3750',NULL,NULL,NULL);
insert  into `{$installer->getTable('tax_calculation_rule')}`(`tax_calculation_rule_id`,`code`,`priority`,`position`) values (1,'Retail Customer-Taxable Goods-Rate 1',1,1);

");

$installer->endSetup();
