<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;

$table = $installer->getTable('cms_widget');
$installer->run('
CREATE TABLE IF NOT EXISTS `' . $table . '` (
  `widget_id` int(10) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `parameters` text,
  PRIMARY KEY  (`widget_id`),
  KEY `IDX_CODE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT="CMS Preconfigured Widgets";');
