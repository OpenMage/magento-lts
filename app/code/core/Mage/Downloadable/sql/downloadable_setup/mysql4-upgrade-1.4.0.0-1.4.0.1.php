<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_downloadable_idx`;

CREATE TABLE `{$installer->getTable('downloadable/product_price_indexer_idx')}` (
    `entity_id` int(10) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `min_price` decimal(12,4) default NULL,
    `max_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable/product_price_indexer_tmp')}` (
     `entity_id` int(10) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `min_price` decimal(12,4) default NULL,
    `max_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

");

$installer->endSetup();
