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
ALTER TABLE `{$this->getTable('catalog_product_entity_tier_price')}` MODIFY COLUMN `qty` DECIMAL(12,4) NOT NULL DEFAULT 1;
DELETE FROM `{$this->getTable('catalog_product_entity_tier_price')}` WHERE store_id>0;
ALTER TABLE `{$this->getTable('catalog_product_entity_tier_price')}` DROP COLUMN `store_id`,
 ADD COLUMN `website_id` SMALLINT(5) UNSIGNED NOT NULL AFTER `value`
, DROP INDEX `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE`,
 DROP FOREIGN KEY `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE`,
 ADD CONSTRAINT `FK_CATALOG_PRODUCT_TIER_WEBSITE` FOREIGN KEY `FK_CATALOG_PRODUCT_TIER_WEBSITE` (`website_id`)
    REFERENCES `{$this->getTable('core_website')}` (`website_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
");
