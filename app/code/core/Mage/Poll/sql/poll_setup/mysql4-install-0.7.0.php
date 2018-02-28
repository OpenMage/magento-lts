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
 * @package     Mage_Poll
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('poll')};
CREATE TABLE {$this->getTable('poll')} (
  `poll_id` int(10) unsigned NOT NULL auto_increment,
  `poll_title` varchar(255) NOT NULL default '',
  `votes_count` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(6) NOT NULL default '0',
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_closed` datetime default NULL,
  `active` smallint(6) NOT NULL default '1',
  `closed` tinyint(1) NOT NULL default '0',
  `answers_display` smallint(6) default NULL,
  PRIMARY KEY  (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into {$this->getTable('poll')}(`poll_id`,`poll_title`,`votes_count`,`store_id`,`date_posted`,`date_closed`,`active`,`closed`,`answers_display`) values (1,'What is your favorite color',5,1, NOW(),NULL,1,0,NULL);

-- DROP TABLE IF EXISTS {$this->getTable('poll_answer')};
CREATE TABLE {$this->getTable('poll_answer')} (
  `answer_id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(10) unsigned NOT NULL default '0',
  `answer_title` varchar(255) NOT NULL default '',
  `votes_count` int(10) unsigned NOT NULL default '0',
  `answer_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`answer_id`),
  KEY `FK_POLL_PARENT` (`poll_id`),
  CONSTRAINT `FK_POLL_PARENT` FOREIGN KEY (`poll_id`) REFERENCES {$this->getTable('poll')} (`poll_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into {$this->getTable('poll_answer')}(`answer_id`,`poll_id`,`answer_title`,`votes_count`,`answer_order`) values (1,1,'Green',4,0),(2,1,'Red',1,0),(3,1,'Black',0,0),(4,1,'Magenta',0,0);

-- DROP TABLE IF EXISTS {$this->getTable('poll_store')};
CREATE TABLE {$this->getTable('poll_store')} (
  `poll_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`poll_id`,`store_id`),
  CONSTRAINT `FK_POLL_STORE` FOREIGN KEY (`poll_id`) REFERENCES {$this->getTable('poll')} (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('poll_store')} (`poll_id`,`store_id`) VALUES(1,1);
-- DROP TABLE IF EXISTS {$this->getTable('poll_vote')};
CREATE TABLE {$this->getTable('poll_vote')} (
  `vote_id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(10) unsigned NOT NULL default '0',
  `poll_answer_id` int(10) unsigned NOT NULL default '0',
  `ip_address` bigint(20) default NULL,
  `customer_id` int(11) default NULL,
  `vote_time` timestamp NOT NULL,
  PRIMARY KEY  (`vote_id`),
  KEY `FK_POLL_ANSWER` (`poll_answer_id`),
  CONSTRAINT `FK_POLL_ANSWER` FOREIGN KEY (`poll_answer_id`) REFERENCES {$this->getTable('poll_answer')} (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
