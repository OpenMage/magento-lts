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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

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
