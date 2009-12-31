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
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('catalogindex_aggregation')}` (
    `aggregation_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned NOT NULL,
    `created_at` datetime NOT NULL,
    `key` varchar(255) default NULL,
    `data` mediumtext,
    PRIMARY KEY  (`aggregation_id`),
    UNIQUE KEY `IDX_STORE_KEY` (`store_id`,`key`),
    CONSTRAINT `FK_CATALOGINDEX_AGGREGATION_STORE` FOREIGN KEY (`store_id`)
        REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('catalogindex_aggregation_tag')}` (
    `tag_id` int(10) unsigned NOT NULL auto_increment,
    `tag_code` varchar(255) NOT NULL,
    PRIMARY KEY  (`tag_id`),
    UNIQUE KEY `IDX_CODE` (`tag_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('catalogindex_aggregation_to_tag')}` (
    `aggregation_id` int(10) unsigned NOT NULL,
    `tag_id` int(10) unsigned NOT NULL,
    UNIQUE KEY `IDX_AGGREGATION_TAG` (`aggregation_id`,`tag_id`),
    KEY `FK_CATALOGINDEX_AGGREGATION_TO_TAG_TAG` (`tag_id`),
    CONSTRAINT `FK_CATALOGINDEX_AGGREGATION_TO_TAG_AGGREGATION` FOREIGN KEY (`aggregation_id`)
        REFERENCES `{$installer->getTable('catalogindex_aggregation')}` (`aggregation_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_CATALOGINDEX_AGGREGATION_TO_TAG_TAG` FOREIGN KEY (`tag_id`)
        REFERENCES `{$installer->getTable('catalogindex_aggregation_tag')}` (`tag_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
