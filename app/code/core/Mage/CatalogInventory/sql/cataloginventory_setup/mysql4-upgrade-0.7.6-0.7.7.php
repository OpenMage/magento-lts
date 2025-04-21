<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('cataloginventory/stock_status')}_idx`;

CREATE TABLE `{$installer->getTable('cataloginventory/stock_status_indexer_idx')}` (
     `product_id` int(10) unsigned NOT NULL,
     `website_id` smallint(5) unsigned NOT NULL,
     `stock_id` smallint(4) unsigned NOT NULL,
     `qty` decimal(12,4) NOT NULL default '0.0000',
     `stock_status` tinyint(3) unsigned NOT NULL,
     PRIMARY KEY  (`product_id`,`website_id`,`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_STOCK` (`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('cataloginventory/stock_status_indexer_tmp')}` (
     `product_id` int(10) unsigned NOT NULL,
     `website_id` smallint(5) unsigned NOT NULL,
     `stock_id` smallint(4) unsigned NOT NULL,
     `qty` decimal(12,4) NOT NULL default '0.0000',
     `stock_status` tinyint(3) unsigned NOT NULL,
     PRIMARY KEY  (`product_id`,`website_id`,`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_STOCK` (`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE` (`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
");

$installer->endSetup();
