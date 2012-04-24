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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_idx_cfg_opt_aggregate`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx')}` (
    `parent_id` int(10) unsigned NOT NULL,
    `child_id` int(10) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `price` decimal(12,4) default NULL,
    `tier_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`parent_id`,`child_id`,`customer_group_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp')}` (
     `entity_id` int(10) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `min_price` decimal(12,4) default NULL,
    `max_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_idx_cfg_option`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_cfg_option_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_cfg_option_idx')}` (
    `entity_id` int(10) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `min_price` decimal(12,4) default NULL,
    `max_price` decimal(12,4) default NULL,
    `tier_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_cfg_option_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_cfg_option_tmp')}` (
    `entity_id` int(10) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `min_price` decimal(12,4) default NULL,
    `max_price` decimal(12,4) default NULL,
    `tier_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_final_idx`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_final_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_final_idx')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `tax_class_id` SMALLINT(5) UNSIGNED DEFAULT '0',
    `orig_price` DECIMAL(12,4) DEFAULT NULL,
    `price` DECIMAL(12,4) DEFAULT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    `base_tier` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_final_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_final_tmp')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `tax_class_id` SMALLINT(5) UNSIGNED DEFAULT '0',
    `orig_price` DECIMAL(12,4) DEFAULT NULL,
    `price` DECIMAL(12,4) DEFAULT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    `base_tier` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_idx_option`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_option_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_option_idx')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_option_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_option_tmp')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_idx_option_aggregate`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_option_aggregate_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_option_aggregate_idx')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `option_id` INT(10) UNSIGNED DEFAULT '0',
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_option_aggregate_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_option_aggregate_tmp')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `option_id` INT(10) UNSIGNED DEFAULT '0',
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_eav')}_idx`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_eav_indexer_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_eav_indexer_idx')}` (
    `entity_id` int(10) unsigned NOT NULL,
     `attribute_id` smallint(5) unsigned NOT NULL,
     `store_id` smallint(5) unsigned NOT NULL,
     `value` int(10) unsigned NOT NULL,
     PRIMARY KEY  (`entity_id`,`attribute_id`,`store_id`,`value`),
     KEY `IDX_ENTITY` (`entity_id`),
     KEY `IDX_ATTRIBUTE` (`attribute_id`),
     KEY `IDX_STORE` (`store_id`),
     KEY `IDX_VALUE` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_eav_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_eav_indexer_tmp')}` (
    `entity_id` int(10) unsigned NOT NULL,
     `attribute_id` smallint(5) unsigned NOT NULL,
     `store_id` smallint(5) unsigned NOT NULL,
     `value` int(10) unsigned NOT NULL,
     PRIMARY KEY  (`entity_id`,`attribute_id`,`store_id`,`value`),
     KEY `IDX_ENTITY` (`entity_id`),
     KEY `IDX_ATTRIBUTE` (`attribute_id`),
     KEY `IDX_STORE` (`store_id`),
     KEY `IDX_VALUE` (`value`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_eav_decimal')}_idx`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_eav_decimal_indexer_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_eav_decimal_indexer_idx')}` (
     `entity_id` int(10) unsigned NOT NULL,
     `attribute_id` smallint(5) unsigned NOT NULL,
     `store_id` smallint(5) unsigned NOT NULL,
     `value` decimal(12,4) NOT NULL,
     PRIMARY KEY  (`entity_id`,`attribute_id`,`store_id`,`value`),
     KEY `IDX_ENTITY` (`entity_id`),
     KEY `IDX_ATTRIBUTE` (`attribute_id`),
     KEY `IDX_STORE` (`store_id`),
     KEY `IDX_VALUE` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_eav_decimal_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_eav_decimal_indexer_tmp')}` (
     `entity_id` int(10) unsigned NOT NULL,
     `attribute_id` smallint(5) unsigned NOT NULL,
     `store_id` smallint(5) unsigned NOT NULL,
     `value` decimal(12,4) NOT NULL,
     PRIMARY KEY  (`entity_id`,`attribute_id`,`store_id`,`value`),
     KEY `IDX_ENTITY` (`entity_id`),
     KEY `IDX_ATTRIBUTE` (`attribute_id`),
     KEY `IDX_STORE` (`store_id`),
     KEY `IDX_VALUE` (`value`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_idx`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_idx')}` (
     `entity_id` int(10) unsigned NOT NULL,
     `customer_group_id` smallint(5) unsigned NOT NULL,
     `website_id` smallint(5) unsigned NOT NULL,
     `tax_class_id` smallint(5) unsigned default '0',
     `price` decimal(12,4) default NULL,
     `final_price` decimal(12,4) default NULL,
     `min_price` decimal(12,4) default NULL,
     `max_price` decimal(12,4) default NULL,
     `tier_price` decimal(12,4) default NULL,
     PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`),
     KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
     KEY `IDX_WEBSITE` (`website_id`),
     KEY `IDX_MIN_PRICE` (`min_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_tmp')}` (
     `entity_id` int(10) unsigned NOT NULL,
     `customer_group_id` smallint(5) unsigned NOT NULL,
     `website_id` smallint(5) unsigned NOT NULL,
     `tax_class_id` smallint(5) unsigned default '0',
     `price` decimal(12,4) default NULL,
     `final_price` decimal(12,4) default NULL,
     `min_price` decimal(12,4) default NULL,
     `max_price` decimal(12,4) default NULL,
     `tier_price` decimal(12,4) default NULL,
     PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`),
     KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
     KEY `IDX_WEBSITE` (`website_id`),
     KEY `IDX_MIN_PRICE` (`min_price`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_product_index')}_idx`;

CREATE TABLE `{$installer->getTable('catalog/category_product_indexer_idx')}` (
     `category_id` int(10) unsigned NOT NULL default '0',
     `product_id` int(10) unsigned NOT NULL default '0',
     `position` int(10) NOT NULL default '0',
     `is_parent` tinyint(1) unsigned NOT NULL default '0',
     `store_id` smallint(5) unsigned NOT NULL default '0',
     `visibility` tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_product_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/category_product_indexer_tmp')}` (
     `category_id` int(10) unsigned NOT NULL default '0',
     `product_id` int(10) unsigned NOT NULL default '0',
     `position` int(10) NOT NULL default '0',
     `is_parent` tinyint(1) unsigned NOT NULL default '0',
     `store_id` smallint(5) unsigned NOT NULL default '0',
     `visibility` tinyint(3) unsigned NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('tmp_category_index_enabled_products')}`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_product_enabled_indexer_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/category_product_enabled_indexer_idx')}` (
    `product_id` int(10) unsigned NOT NULL DEFAULT '0',
    `visibility` int(11) unsigned NOT NULL DEFAULT '0',
    KEY `IDX_PRODUCT` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_product_enabled_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/category_product_enabled_indexer_tmp')}` (
    `product_id` int(10) unsigned NOT NULL DEFAULT '0',
    `visibility` int(11) unsigned NOT NULL DEFAULT '0',
    KEY `IDX_PRODUCT` (`product_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('tmp_category_index_anchor_categories')}`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_anchor_indexer_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/category_anchor_indexer_idx')}` (
    `category_id` int(10) unsigned NOT NULL DEFAULT '0',
    `path` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
    KEY `IDX_CATEGORY` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_anchor_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/category_anchor_indexer_tmp')}` (
    `category_id` int(10) unsigned NOT NULL DEFAULT '0',
    `path` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
    KEY `IDX_CATEGORY` (`category_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('tmp_category_index_anchor_products')}`;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_anchor_products_indexer_idx')}`;
CREATE TABLE `{$installer->getTable('catalog/category_anchor_products_indexer_idx')}` (
    `category_id` int(10) unsigned NOT NULL DEFAULT '0',
    `product_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_anchor_products_indexer_tmp')}`;
CREATE TABLE `{$installer->getTable('catalog/category_anchor_products_indexer_tmp')}` (
    `category_id` int(10) unsigned NOT NULL DEFAULT '0',
    `product_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

");
$installer->endSetup();
