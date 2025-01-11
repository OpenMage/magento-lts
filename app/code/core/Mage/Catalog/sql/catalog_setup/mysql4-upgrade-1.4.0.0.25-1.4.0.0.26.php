<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
