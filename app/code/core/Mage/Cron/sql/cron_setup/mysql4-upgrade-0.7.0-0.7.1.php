<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Cron
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cron_schedule')};
CREATE TABLE {$this->getTable('cron_schedule')} (
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `job_code` varchar(255) NOT NULL default '0',
  `status` enum('pending','running','success','missed','error') NOT NULL default 'pending',
  `messages` text,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `scheduled_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `executed_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`schedule_id`),
  KEY `task_name` (`job_code`),
  KEY `scheduled_at` (`scheduled_at`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

    ");

$installer->endSetup();
