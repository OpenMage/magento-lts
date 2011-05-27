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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
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
