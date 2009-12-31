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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ProductAlert Install
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('product_alert_price')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('product_alert_price')}` (
  `alert_price_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0',
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_send_date` datetime default NULL,
  `send_count` smallint(5) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`alert_price_id`),
  CONSTRAINT `FK_PRODUCT_ALERT_PRICE_CUSTOMER`
    FOREIGN KEY (`customer_id`)
    REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ALERT_PRICE_PRODUCT`
    FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ALERT_PRICE_WEBSITE`
    FOREIGN KEY (`website_id`)
    REFERENCES `{$installer->getTable('core_website')}` (`website_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('product_alert_stock')}`;
CREATE TABLE `{$installer->getTable('product_alert_stock')}` (
  `alert_stock_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `send_date` datetime default NULL,
  `send_count` smallint(5) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`alert_stock_id`),
  CONSTRAINT `FK_PRODUCT_ALERT_STOCK_CUSTOMER`
    FOREIGN KEY (`customer_id`)
    REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ALERT_STOCK_PRODUCT`
    FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ALERT_STOCK_WEBSITE`
    FOREIGN KEY (`website_id`)
    REFERENCES `{$installer->getTable('core_website')}` (`website_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
