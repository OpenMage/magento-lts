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
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
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
