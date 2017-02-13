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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cron
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

$installer->startSetup();

$installer->run("

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `cron_schedule` */

-- DROP TABLE IF EXISTS {$this->getTable('cron_schedule')};

CREATE TABLE {$this->getTable('cron_schedule')} (
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `task_name` int(10) unsigned NOT NULL default '0',
  `schedule_status` tinyint(4) NOT NULL default '0',
  `schedule_type` tinyint(4) NOT NULL default '0',
  `schedule_cmd` text NOT NULL,
  `schedule_comments` text NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `scheduled_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `executed_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`schedule_id`),
  KEY `task_name` (`task_name`),
  KEY `scheduled_at` (`scheduled_at`,`schedule_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cron_schedule` */

SET SQL_MODE=@OLD_SQL_MODE;


    ");

$installer->endSetup();
