<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('catalogsearch_query')};
CREATE TABLE {$this->getTable('catalogsearch_query')} (
    `query_id` int(10) unsigned NOT NULL auto_increment,
    `query_text` varchar(255) NOT NULL default '',
    `num_results` int(10) unsigned NOT NULL default '0',
    `popularity` int(10) unsigned NOT NULL default '0',
    `redirect` varchar(255) NOT NULL default '',
    `synonim_for` varchar(255) NOT NULL default '',
    `store_id` smallint (5) unsigned NOT NULL,
    PRIMARY KEY  (`query_id`),
    KEY `search_query` (`query_text`,`popularity`),
    KEY `FK_catalogsearch_query` (`store_id`),
    CONSTRAINT `FK_catalogsearch_query` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
