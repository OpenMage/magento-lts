<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_bundle`;

CREATE TABLE `{$installer->getTable('bundle/price_indexer_idx')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `tax_class_id` SMALLINT(5) UNSIGNED DEFAULT '0',
    `price_type` TINYINT(1) UNSIGNED NOT NULL,
    `special_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_percent` DECIMAL(12,4) DEFAULT NULL,
    `orig_price` DECIMAL(12,4) DEFAULT NULL,
    `price` DECIMAL(12,4) DEFAULT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    `base_tier` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('bundle/price_indexer_tmp')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `tax_class_id` SMALLINT(5) UNSIGNED DEFAULT '0',
    `price_type` TINYINT(1) UNSIGNED NOT NULL,
    `special_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_percent` DECIMAL(12,4) DEFAULT NULL,
    `orig_price` DECIMAL(12,4) DEFAULT NULL,
    `price` DECIMAL(12,4) DEFAULT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    `base_tier` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_bndl_sel`;

CREATE TABLE `{$installer->getTable('bundle/selection_indexer_idx')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `option_id` INT(10) UNSIGNED DEFAULT '0',
    `selection_id` INT(10) UNSIGNED DEFAULT '0',
    `group_type` TINYINT(1) UNSIGNED DEFAULT '0',
    `is_required` TINYINT(1) UNSIGNED DEFAULT '0',
    `price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`, `selection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('bundle/selection_indexer_tmp')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `option_id` INT(10) UNSIGNED DEFAULT '0',
    `selection_id` INT(10) UNSIGNED DEFAULT '0',
    `group_type` TINYINT(1) UNSIGNED DEFAULT '0',
    `is_required` TINYINT(1) UNSIGNED DEFAULT '0',
    `price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`, `selection_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_index_price')}_bndl_opt`;

CREATE TABLE `{$installer->getTable('bundle/option_indexer_idx')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `option_id` INT(10) UNSIGNED DEFAULT '0',
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `alt_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    `alt_tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('bundle/option_indexer_tmp')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `option_id` INT(10) UNSIGNED DEFAULT '0',
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    `alt_price` DECIMAL(12,4) DEFAULT NULL,
    `max_price` DECIMAL(12,4) DEFAULT NULL,
    `tier_price` DECIMAL(12,4) DEFAULT NULL,
    `alt_tier_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

");
$installer->endSetup();
