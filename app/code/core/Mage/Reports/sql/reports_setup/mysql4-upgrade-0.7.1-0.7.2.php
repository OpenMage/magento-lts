<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('report_event_types')} DROP INDEX `event_type_id`;
ALTER TABLE {$this->getTable('report_event_types')} CHANGE `event_name` `event_name` varchar(64) NOT NULL;
UPDATE {$this->getTable('report_event_types')} SET `event_name`='catalog_product_compare_add_product' WHERE `event_type_id`=3;
ALTER TABLE {$this->getTable('report_event')} ADD `sybtype` tinyint(3) unsigned NOT NULL default '0' AFTER `subject_id`;
ALTER TABLE {$this->getTable('report_event')} ADD INDEX (`event_type_id`);
ALTER TABLE {$this->getTable('report_event')} ADD INDEX (`sybtype`);
ALTER TABLE {$this->getTable('report_event')} ADD INDEX (`store_id`);
ALTER TABLE {$this->getTable('report_event_types')} ADD `customer_login` TINYINT UNSIGNED NOT NULL DEFAULT '0';
UPDATE {$this->getTable('report_event_types')} SET `customer_login`=1;
");

$installer->endSetup();
