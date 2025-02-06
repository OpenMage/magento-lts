<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Report events SQL
 *
 * @category   Mage
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
