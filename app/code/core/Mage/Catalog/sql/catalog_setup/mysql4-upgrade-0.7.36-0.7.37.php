<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

try {
    $installer->run("
        ALTER TABLE `{$installer->getTable('catalog_product_website')}` ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

        delete from `{$installer->getTable('catalog_product_website')}` where product_id not in (select entity_id from catalog_product_entity);
        delete from `{$installer->getTable('catalog_product_website')}` where website_id not in (select website_id from core_website);

        ALTER TABLE `{$installer->getTable('catalog_product_website')}` DROP INDEX `FK_CATALOG_PRODUCT_WEBSITE_WEBSITE`,
            ADD CONSTRAINT `FK_CATALOG_PRODUCT_WEBSITE_PRODUCT` FOREIGN KEY `FK_CATALOG_PRODUCT_WEBSITE_PRODUCT` (`product_id`)
             REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE` FOREIGN KEY `FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE` (`website_id`)
             REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ROW_FORMAT = DYNAMIC;
    ");
} catch (Exception $e) {
}

$installer->endSetup();
