<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->run("
-- Removing old index tables if exists
DROP TABLE IF EXISTS `{$installer->getTable('catalog/product')}_tier_price_idx`;
DROP TABLE IF EXISTS `{$installer->getTable('core/website')}_date_idx`;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('catalog/product_index_tier_price')}` (
    `entity_id` INT(10) UNSIGNED NOT NULL,
    `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `min_price` DECIMAL(12,4) DEFAULT NULL,
    PRIMARY KEY  (`entity_id`,`customer_group_id`,`website_id`),
    CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_ENTITY` FOREIGN KEY (`entity_id`)
    REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_CUSTOMER` FOREIGN KEY (`customer_group_id`)
    REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_WEBSITE` FOREIGN KEY (`website_id`)
    REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('catalog/product_index_website')}` (
    `website_id` SMALLINT(5) UNSIGNED NOT NULL,
    `date` DATE DEFAULT NULL,
    `rate` FLOAT(12, 4) UNSIGNED DEFAULT 1,
    PRIMARY KEY (`website_id`),
    KEY `IDX_DATE` (`date`),
    CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_WEBSITE` FOREIGN KEY (`website_id`)
    REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
");
