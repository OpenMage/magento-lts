<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('sitemap')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('sitemap')}` (
  `sitemap_id` int(11) NOT NULL auto_increment,
  `sitemap_type` varchar(32) default NULL,
  `sitemap_filename` varchar(32) default NULL,
  `sitemap_path` tinytext,
  `sitemap_time` timestamp NULL default NULL,
  `store_id` int(11) default NULL,
  PRIMARY KEY  (`sitemap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
