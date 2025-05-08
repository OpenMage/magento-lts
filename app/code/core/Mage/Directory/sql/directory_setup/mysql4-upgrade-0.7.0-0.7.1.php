<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup()
    ->run("
DROP TABLE IF EXISTS {$this->getTable('directory_country_format')};
CREATE TABLE {$this->getTable('directory_country_format')} (
    `country_format_id` int(10) unsigned NOT NULL auto_increment,
    `country_id` char(2) NOT NULL default '',
    `type` varchar(30) NOT NULL default '',
    `format` text NOT NULL,
    PRIMARY KEY  (`country_format_id`),
    UNIQUE KEY `country_type` (`country_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries format';

ALTER TABLE {$this->getTable('directory_country')},
  DROP COLUMN `address_template_plain`, DROP COLUMN `address_template_html`;
");
$installer->endSetup();
