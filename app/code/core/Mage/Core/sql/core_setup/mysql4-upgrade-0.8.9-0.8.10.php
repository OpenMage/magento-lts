<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('core_flag')} (
  `flag_id` smallint(5) unsigned NOT NULL auto_increment,
  `flag_code` varchar(255) NOT NULL,
  `state` smallint(5) unsigned NOT NULL default '0',
  `flag_data` text,
  `last_update` datetime NOT NULL,
  PRIMARY KEY  (`flag_id`),
  KEY (`last_update`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
