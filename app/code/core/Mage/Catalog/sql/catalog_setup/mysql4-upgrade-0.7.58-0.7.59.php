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

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('catalog/category_flat')}`;
CREATE TABLE `{$installer->getTable('catalog/category_flat')}` (
    `entity_id` int(10) unsigned not null,
    `store_id` smallint(5) unsigned not null default '0',
    `parent_id` int(10) unsigned not null default '0',
    `path` varchar(255) not null default '',
    `level` int(11) not null default '0',
    `position` int(11) not null default '0',
    `children_count` int(11) not null,
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    KEY `CATEGORY_FLAT_CATEGORY_ID` (`entity_id`),
    KEY `CATEGORY_FLAT_STORE_ID` (`store_id`),
    KEY `path` (`path`),
    KEY `IDX_LEVEL` (`level`),
    CONSTRAINT `FK_CATEGORY_FLAT_CATEGORY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('catalog/category')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_CATEGORY_FLAT_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Flat Category'
");

$installer->endSetup();
