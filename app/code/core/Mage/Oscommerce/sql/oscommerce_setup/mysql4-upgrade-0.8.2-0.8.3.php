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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_orders')}` (
    `osc_magento_id` int(11) NOT NULL auto_increment,
    `orders_id` int(11) NOT NULL,
    `customers_id` int(11) NOT NULL default '0',
    `magento_customers_id` int(11) NOT NULL default '0',
    `import_id` int(11) NOT NULL default '0',
    `website_id` int(11) NOT NULL default '0',
    `customers_name` varchar(64) NOT NULL default '',
    `customers_company` varchar(32) default NULL,
    `customers_street_address` varchar(64) NOT NULL default '',
    `customers_suburb` varchar(32) default NULL,
    `customers_city` varchar(32) NOT NULL default '',
    `customers_postcode` varchar(10) NOT NULL default '',
    `customers_state` varchar(32) default NULL,
    `customers_country` varchar(32) NOT NULL default '',
    `customers_telephone` varchar(32) NOT NULL default '',
    `customers_email_address` varchar(96) NOT NULL default '',
    `customers_address_format_id` int(5) NOT NULL default '0',
    `delivery_name` varchar(64) NOT NULL default '',
    `delivery_company` varchar(32) default NULL,
    `delivery_street_address` varchar(64) NOT NULL default '',
    `delivery_suburb` varchar(32) default NULL,
    `delivery_city` varchar(32) NOT NULL default '',
    `delivery_postcode` varchar(10) NOT NULL default '',
    `delivery_state` varchar(32) default NULL,
    `delivery_country` varchar(32) NOT NULL default '',
    `delivery_address_format_id` int(5) NOT NULL default '0',
    `billing_name` varchar(64) NOT NULL default '',
    `billing_company` varchar(32) default NULL,
    `billing_street_address` varchar(64) NOT NULL default '',
    `billing_suburb` varchar(32) default NULL,
    `billing_city` varchar(32) NOT NULL default '',
    `billing_postcode` varchar(10) NOT NULL default '',
    `billing_state` varchar(32) default NULL,
    `billing_country` varchar(32) NOT NULL default '',
    `billing_address_format_id` int(5) NOT NULL default '0',
    `payment_method` varchar(255) NOT NULL default '',
    `cc_type` varchar(20) default NULL,
    `cc_owner` varchar(64) default NULL,
    `cc_number` varchar(32) default NULL,
    `cc_expires` varchar(4) default NULL,
    `last_modified` datetime default NULL,
    `date_purchased` datetime default NULL,
    `orders_status` int(5) NOT NULL default '0',
    `orders_date_finished` datetime default NULL,
    `currency` char(3) default NULL,
    `currency_value` decimal(14,6) default NULL,
    `currency_symbol` char(3) default NULL,
    PRIMARY KEY  (`osc_magento_id`),
    KEY `idx_orders_customers_id` (`customers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_orders_products')}` (
    `orders_products_id` int(11) NOT NULL auto_increment,
    `osc_magento_id` int(11) NOT NULL default '0',
    `products_id` int(11) NOT NULL default '0',
    `products_model` varchar(12) default NULL,
    `products_name` varchar(64) NOT NULL default '',
    `products_price` decimal(15,4) NOT NULL default '0.0000',
    `final_price` decimal(15,4) NOT NULL default '0.0000',
    `products_tax` decimal(7,4) NOT NULL default '0.0000',
    `products_quantity` int(2) NOT NULL default '0',
    PRIMARY KEY  (`orders_products_id`),
    KEY `idx_orders_products_osc_magento_id` (`osc_magento_id`),
    KEY `idx_orders_products_products_id` (`products_id`)
) ENGINE=MyISAM CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_orders_total')}` (
    `orders_total_id` int(10) unsigned NOT NULL auto_increment,
    `osc_magento_id` int(11) NOT NULL default '0',
    `title` varchar(255) NOT NULL default '',
    `text` varchar(255) NOT NULL default '',
    `value` decimal(15,4) NOT NULL default '0.0000',
    `class` varchar(32) NOT NULL default '',
    `sort_order` int(11) NOT NULL default '0',
    PRIMARY KEY  (`orders_total_id`),
    KEY `idx_orders_total_osc_magento_id` (`osc_magento_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_orders_status_history')}`(
   `orders_status_history_id` int(11) NOT NULL auto_increment,
   `osc_magento_id` int(11) NOT NULL default '0',
   `orders_status_id` int(5) NOT NULL default '0',
   `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
   `customer_notified` int(1) default '0',
   `comments` text,
    PRIMARY KEY  (`orders_status_history_id`),
    KEY `idx_orders_status_history_osc_magento_id` (`osc_magento_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

");

$installer->endSetup();
