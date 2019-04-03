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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
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
