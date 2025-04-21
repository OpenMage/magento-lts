<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Batch update
 *
 * @package    Mage_Dataflow
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$installer->getTable('dataflow_batch_import')}`;
-- DROP TABLE IF EXISTS `{$installer->getTable('dataflow_batch_export')}`;
-- DROP TABLE IF EXISTS `{$installer->getTable('dataflow_batch')}`;

CREATE TABLE `{$installer->getTable('dataflow_batch')}` (
  `batch_id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `adapter` varchar(128) default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`batch_id`),
  KEY `FK_DATAFLOW_BATCH_PROFILE` (`profile_id`),
  KEY `FK_DATAFLOW_BATCH_STORE` (`store_id`),
  KEY `IDX_CREATED_AT` (`created_at`),
  CONSTRAINT `FK_DATAFLOW_BATCH_PROFILE` FOREIGN KEY (`profile_id`) REFERENCES `{$installer->getTable('dataflow_profile')}` (`profile_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DATAFLOW_BATCH_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('dataflow_batch_export')}` (
  `batch_export_id` bigint(20) unsigned NOT NULL auto_increment,
  `batch_id` int(10) unsigned NOT NULL default '0',
  `batch_data` longtext,
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`batch_export_id`),
  KEY `FK_DATAFLOW_BATCH_EXPORT_BATCH` (`batch_id`),
  CONSTRAINT `FK_DATAFLOW_BATCH_EXPORT_BATCH` FOREIGN KEY (`batch_id`) REFERENCES `{$installer->getTable('dataflow_batch')}` (`batch_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `{$installer->getTable('dataflow_batch_import')}` (
  `batch_import_id` bigint(20) unsigned NOT NULL auto_increment,
  `batch_id` int(10) unsigned NOT NULL default '0',
  `batch_data` longtext,
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`batch_import_id`),
  KEY `FK_DATAFLOW_BATCH_IMPORT_BATCH` (`batch_id`),
  CONSTRAINT `FK_DATAFLOW_BATCH_IMPORT_BATCH` FOREIGN KEY (`batch_id`) REFERENCES `{$installer->getTable('dataflow_batch')}` (`batch_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
