<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('adminnotification_inbox')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('adminnotification_inbox')}` (
  `notification_id` int(10) unsigned NOT NULL auto_increment,
  `severity` tinyint(3) unsigned NOT NULL default '0',
  `date_added` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `url` varchar(255) NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL default '0',
  `is_remove` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`notification_id`),
  KEY `IDX_SEVERITY` (`severity`),
  KEY `IDX_IS_READ` (`is_read`),
  KEY `IDX_IS_REMOVE` (`is_remove`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
