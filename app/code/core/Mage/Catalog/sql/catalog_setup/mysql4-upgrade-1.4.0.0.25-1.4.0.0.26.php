<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp')}`;
    CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp')}` (
        `parent_id` int(10) unsigned NOT NULL,
        `child_id` int(10) unsigned NOT NULL,
        `customer_group_id` smallint(5) unsigned NOT NULL,
        `website_id` smallint(5) unsigned NOT NULL,
        `price` decimal(12,4) default NULL,
        `tier_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`parent_id`,`child_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
");
