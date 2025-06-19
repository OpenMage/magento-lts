<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report events SQL
 *
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('report_event')} CHANGE `sybtype` `subtype` tinyint(3) unsigned NOT NULL default '0' AFTER `subject_id`;
ALTER TABLE {$this->getTable('report_event')} DROP INDEX `sybtype`, ADD INDEX (`subtype`);
");

$installer->endSetup();
