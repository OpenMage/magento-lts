<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sitemap
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
