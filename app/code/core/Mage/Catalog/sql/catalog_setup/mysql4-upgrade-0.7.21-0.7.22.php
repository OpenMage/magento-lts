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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

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