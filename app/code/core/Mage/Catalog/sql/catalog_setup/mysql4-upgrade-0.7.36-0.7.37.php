<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
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
            ROW_FORMAT = FIXED;
    ");
} catch (Exception $e) {
}

$installer->endSetup();
