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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$groupedLinkType = Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED;
$installer->run("

CREATE TABLE `{$installer->getTable('catalog/product_relation')}` (
  `parent_id` INT(10) UNSIGNED NOT NULL,
  `child_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`parent_id`,`child_id`),
  KEY `IDX_CHILD` (`child_id`),
  CONSTRAINT `FK_CATALOG_PRODUCT_RELATION_CHILD` FOREIGN KEY (`child_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_RELATION_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

INSERT IGNORE INTO `{$installer->getTable('catalog/product_relation')}`
SELECT
  `product_id`,
  `linked_product_id`
FROM `{$installer->getTable('catalog/product_link')}`
WHERE `link_type_id`={$groupedLinkType};

INSERT IGNORE INTO `{$installer->getTable('catalog/product_relation')}`
SELECT
  `parent_id`,
  `product_id`
FROM `{$installer->getTable('catalog/product_super_link')}`;

CREATE TABLE `{$installer->getTable('catalog/product_index_eav')}` (
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` smallint(5) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`attribute_id`,`store_id`,`value`),
  KEY `IDX_ENTITY` (`entity_id`),
  KEY `IDX_ATTRIBUTE` (`attribute_id`),
  KEY `IDX_STORE` (`store_id`),
  KEY `IDX_VALUE` (`value`),
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_EAV_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_EAV_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
