<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
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
