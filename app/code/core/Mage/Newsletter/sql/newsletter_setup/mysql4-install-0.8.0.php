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
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Newsletter install
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('newsletter_problem')}`;
CREATE TABLE `{$installer->getTable('newsletter_problem')}` (
  `problem_id` int(7) unsigned NOT NULL auto_increment,
  `subscriber_id` int(7) unsigned default NULL,
  `queue_id` int(7) unsigned NOT NULL default '0',
  `problem_error_code` int(3) unsigned default '0',
  `problem_error_text` varchar(200) default NULL,
  PRIMARY KEY  (`problem_id`),
  KEY `FK_PROBLEM_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_PROBLEM_QUEUE` (`queue_id`),
  CONSTRAINT `FK_PROBLEM_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `{$installer->getTable('newsletter_queue')}` (`queue_id`),
  CONSTRAINT `FK_PROBLEM_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES `{$installer->getTable('newsletter_subscriber')}` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter problems';

-- DROP TABLE IF EXISTS `{$installer->getTable('newsletter_queue')}`;
CREATE TABLE `{$installer->getTable('newsletter_queue')}` (
  `queue_id` int(7) unsigned NOT NULL auto_increment,
  `template_id` int(7) unsigned NOT NULL default '0',
  `queue_status` int(3) unsigned NOT NULL default '0',
  `queue_start_at` datetime default NULL,
  `queue_finish_at` datetime default NULL,
  PRIMARY KEY  (`queue_id`),
  KEY `FK_QUEUE_TEMPLATE` (`template_id`),
  CONSTRAINT `FK_QUEUE_TEMPLATE` FOREIGN KEY (`template_id`) REFERENCES `{$installer->getTable('newsletter_template')}` (`template_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter queue';

-- DROP TABLE IF EXISTS `{$installer->getTable('newsletter_queue_link')}`;
CREATE TABLE `{$installer->getTable('newsletter_queue_link')}` (
  `queue_link_id` int(9) unsigned NOT NULL auto_increment,
  `queue_id` int(7) unsigned NOT NULL default '0',
  `subscriber_id` int(7) unsigned NOT NULL default '0',
  `letter_sent_at` datetime default NULL,
  PRIMARY KEY  (`queue_link_id`),
  KEY `FK_QUEUE_LINK_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_QUEUE_LINK_QUEUE` (`queue_id`),
  CONSTRAINT `FK_QUEUE_LINK_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `{$installer->getTable('newsletter_queue')}` (`queue_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_QUEUE_LINK_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES `{$installer->getTable('newsletter_subscriber')}` (`subscriber_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter queue to subscriber link';

-- DROP TABLE IF EXISTS `{$installer->getTable('newsletter_queue_store_link')}`;
CREATE TABLE `{$installer->getTable('newsletter_queue_store_link')}` (
  `queue_id` int(7) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`queue_id`,`store_id`),
  KEY `FK_NEWSLETTER_QUEUE_STORE_LINK_STORE` (`store_id`),
  CONSTRAINT `FK_NEWSLETTER_QUEUE_STORE_LINK_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `{$installer->getTable('newsletter_queue')}` (`queue_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('newsletter_subscriber')}`;
CREATE TABLE `{$installer->getTable('newsletter_subscriber')}` (
  `subscriber_id` int(7) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned default '0',
  `change_status_at` datetime default NULL,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `subscriber_email` varchar(150) character set latin1 collate latin1_general_ci NOT NULL default '',
  `subscriber_status` int(3) NOT NULL default '0',
  `subscriber_confirm_code` varchar(32) default 'NULL',
  PRIMARY KEY  (`subscriber_id`),
  KEY `FK_SUBSCRIBER_CUSTOMER` (`customer_id`),
  KEY `FK_NEWSLETTER_SUBSCRIBER_STORE` (`store_id`),
  CONSTRAINT `FK_NEWSLETTER_SUBSCRIBER_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter subscribers';

-- DROP TABLE IF EXISTS `{$installer->getTable('newsletter_template')}`;
CREATE TABLE `{$installer->getTable('newsletter_template')}` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) default NULL,
  `template_text` text,
  `template_text_preprocessed` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `template_actual` tinyint(1) unsigned default '1',
  `added_at` datetime default NULL,
  `modified_at` datetime default NULL,
  PRIMARY KEY  (`template_id`),
  KEY `template_actual` (`template_actual`),
  KEY `added_at` (`added_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter templates';
");
$installer->endSetup();
