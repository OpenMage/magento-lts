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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

/*Table structure for table `tax_class` */

-- DROP TABLE IF EXISTS {$this->getTable('tax_class')};
CREATE TABLE {$this->getTable('tax_class')} (
  `class_id` smallint(6) NOT NULL auto_increment,
  `class_name` varchar(255) NOT NULL default '',
  `class_type` enum('CUSTOMER','PRODUCT') NOT NULL default 'CUSTOMER',
  PRIMARY KEY  (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_class` */

insert  into {$this->getTable('tax_class')}(`class_id`,`class_name`,`class_type`) values (2,'Taxable Goods','PRODUCT'),(3,'Retail Customer','CUSTOMER');

/*Table structure for table `tax_rate` */

-- DROP TABLE IF EXISTS {$this->getTable('tax_rate')};

CREATE TABLE {$this->getTable('tax_rate')} (
  `tax_rate_id` tinyint(4) NOT NULL auto_increment,
  `tax_county_id` smallint(6) default NULL,
  `tax_region_id` mediumint(9) unsigned default NULL,
  `tax_postcode` varchar(12) default NULL,
  PRIMARY KEY  (`tax_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base tax rates';

/*Data for the table `tax_rate` */

insert  into {$this->getTable('tax_rate')}(`tax_rate_id`,`tax_county_id`,`tax_region_id`,`tax_postcode`) values (1,0,12,NULL),(2,0,43,NULL);

/*Table structure for table `tax_rate_data` */

-- DROP TABLE IF EXISTS {$this->getTable('tax_rate_data')};

CREATE TABLE {$this->getTable('tax_rate_data')} (
  `tax_rate_data_id` tinyint(4) NOT NULL auto_increment,
  `tax_rate_id` tinyint(4) NOT NULL default '0',
  `rate_value` decimal(12,4) NOT NULL default '0.0000',
  `rate_type_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`tax_rate_data_id`),
  KEY `rate_id` (`tax_rate_id`),
  KEY `rate_type_id` (`rate_type_id`),
  UNIQUE idx_rate_rate_type (tax_rate_id, rate_type_id),
  CONSTRAINT `FK_TAX_RATE_DATA_TAX_RATE` FOREIGN KEY (`tax_rate_id`) REFERENCES {$this->getTable('tax_rate')} (`tax_rate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TAX_RATE_DATE_TAX_RATE_TYPE` FOREIGN KEY (`rate_type_id`) REFERENCES {$this->getTable('tax_rate_type')} (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_rate_data` */

insert  into {$this->getTable('tax_rate_data')}(`tax_rate_data_id`,`tax_rate_id`,`rate_value`,`rate_type_id`) values (6,2,8.3750,1),(7,2,0.0000,2),(8,2,0.0000,3),(9,2,0.0000,4),(10,2,0.0000,5),(31,1,8.2500,1),(32,1,0.0000,2),(33,1,0.0000,3),(34,1,0.0000,4),(35,1,0.0000,5);

/*Table structure for table `tax_rate_type` */

-- DROP TABLE IF EXISTS {$this->getTable('tax_rate_type')};
CREATE TABLE {$this->getTable('tax_rate_type')} (
  `type_id` tinyint(4) NOT NULL auto_increment,
  `type_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_rate_type` */

insert  into {$this->getTable('tax_rate_type')}(`type_id`,`type_name`) values (1,'Rate 1'),(2,'Rate 2'),(3,'Rate 3'),(4,'Rate 4'),(5,'Rate 5');

/*Table structure for table `tax_rule` */

-- DROP TABLE IF EXISTS {$this->getTable('tax_rule')};
CREATE TABLE {$this->getTable('tax_rule')} (
  `tax_rule_id` tinyint(4) NOT NULL auto_increment,
  `tax_customer_class_id` smallint(6) NOT NULL default '0',
  `tax_product_class_id` smallint(6) NOT NULL default '0',
  `tax_rate_type_id` tinyint(4) NOT NULL default '0',
  `tax_shipping` tinyint (1)  NOT NULL default '0',
  PRIMARY KEY  (`tax_rule_id`),
  KEY `tax_customer_class_id` (`tax_customer_class_id`,`tax_product_class_id`),
  KEY `tax_customer_class_id_2` (`tax_customer_class_id`),
  KEY `tax_product_class_id` (`tax_product_class_id`),
  KEY `tax_rate_id` (`tax_rate_type_id`),
  CONSTRAINT `FK_TAX_RULE_TAX_CLASS_CUSTOMER` FOREIGN KEY (`tax_customer_class_id`) REFERENCES {$this->getTable('tax_class')} (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TAX_RULE_TAX_CLASS_PRODUCT` FOREIGN KEY (`tax_product_class_id`) REFERENCES {$this->getTable('tax_class')} (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_rule` */

insert  into {$this->getTable('tax_rule')}(`tax_rule_id`,`tax_customer_class_id`,`tax_product_class_id`,`tax_rate_type_id`) values (1,3,2,1);

    ");

$installer->endSetup();
