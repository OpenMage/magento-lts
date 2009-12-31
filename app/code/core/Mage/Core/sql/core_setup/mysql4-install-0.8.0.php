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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('core_resource')}` (
  `code` varchar(50) NOT NULL default '',
  `version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Resource version registry';

CREATE TABLE `{$installer->getTable('core_website')}` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `default_group_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`website_id`),
  UNIQUE KEY `code` (`code`),
  KEY `sort_order` (`sort_order`),
  KEY `default_group_id` (`default_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

INSERT INTO `{$installer->getTable('core_website')}` VALUES
    (0, 'admin', 'Admin', 0, 0),
    (1, 'base', 'Main Website', 0, 1);

-- DROP TABLE IF EXISTS `{$installer->getTable('core_store_group')}`;
CREATE TABLE `{$installer->getTable('core_store_group')}` (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(32) NOT NULL default '',
  `root_category_id` int(10) unsigned NOT NULL default '0',
  `default_store_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `FK_STORE_GROUP_WEBSITE` (`website_id`),
  KEY `default_store_id` (`default_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$installer->getTable('core_store_group')}` VALUES
    (0, 0, 'Default', 0, 0),
    (1, 1, 'Main Website Store', 2, 1);

-- DROP TABLE IF EXISTS `{$installer->getTable('core_store')}`;
CREATE TABLE `{$installer->getTable('core_store')}` (
  `store_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `website_id` smallint(5) unsigned default '0',
  `group_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(32) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`store_id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_STORE_WEBSITE` (`website_id`),
  KEY `is_active` (`is_active`,`sort_order`),
  KEY `FK_STORE_GROUP` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores';

INSERT INTO `{$installer->getTable('core_store')}` VALUES
    (0, 'admin', 0, 0, 'Admin', 0, 1),
    (1, 'default', 1, 1, 'Default Store View', 0, 1);

CREATE TABLE `{$installer->getTable('core_config_data')}` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `scope` enum('default','websites','stores','config') NOT NULL default 'default',
  `scope_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default 'general',
  `value` text NOT NULL,
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`scope`,`scope_id`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('core_email_template')}` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) default NULL,
  `template_text` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `added_at` datetime default NULL,
  `modified_at` datetime default NULL,
  PRIMARY KEY  (`template_id`),
  UNIQUE KEY `template_code` (`template_code`),
  KEY `added_at` (`added_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Email templates';

CREATE TABLE `{$installer->getTable('core_layout_update')}` (
  `layout_update_id` int(10) unsigned NOT NULL auto_increment,
  `handle` varchar(255) default NULL,
  `xml` text,
  PRIMARY KEY  (`layout_update_id`),
  KEY `handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('core_layout_link')}` (
  `layout_link_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `package` varchar(64) NOT NULL default '',
  `theme` varchar(64) NOT NULL default '',
  `layout_update_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`layout_link_id`),
  UNIQUE KEY `store_id` (`store_id`,`package`,`theme`,`layout_update_id`),
  KEY `FK_core_layout_link_update` (`layout_update_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('core_session')}` (
  `session_id` varchar(255) NOT NULL default '',
  `website_id` smallint(5) unsigned default NULL,
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `FK_SESSION_WEBSITE` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session data store';

CREATE TABLE `{$installer->getTable('core_translate')}` (
  `key_id` int(10) unsigned NOT NULL auto_increment,
  `string` varchar(255) NOT NULL default '',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `translate` varchar(255) NOT NULL default '',
  `locale` varchar(20) NOT NULL default 'en_US',
  PRIMARY KEY  (`key_id`),
  UNIQUE KEY `IDX_CODE` (`store_id`,`locale`,`string`),
  KEY `FK_CORE_TRANSLATE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Translation data';

CREATE TABLE `{$installer->getTable('core_url_rewrite')}` (
  `url_rewrite_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `id_path` varchar(255) NOT NULL default '',
  `request_path` varchar(255) NOT NULL default '',
  `target_path` varchar(255) NOT NULL default '',
  `options` varchar(255) NOT NULL default '',
  `type` int(1) NOT NULL default '0',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`url_rewrite_id`),
  UNIQUE KEY `id_path` (`id_path`,`store_id`),
  UNIQUE KEY `request_path` (`request_path`,`store_id`),
  KEY `target_path` (`target_path`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('core_url_rewrite_tag')}` (
  `url_rewrite_tag_id` int(10) unsigned NOT NULL auto_increment,
  `url_rewrite_id` int(10) unsigned NOT NULL default '0',
  `tag` varchar(255) default NULL,
  PRIMARY KEY  (`url_rewrite_tag_id`),
  UNIQUE KEY `tag` (`tag`,`url_rewrite_id`),
  KEY `url_rewrite_id` (`url_rewrite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('design_change')}` (
  `design_change_id` int(11) NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `design` varchar(255) NOT NULL default '',
  `date_from` date NOT NULL default '0000-00-00',
  `date_to` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`design_change_id`),
  KEY `FK_DESIGN_CHANGE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->run("

ALTER TABLE `{$installer->getTable('core_store_group')}`
  ADD CONSTRAINT `FK_STORE_GROUP_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('core_store')}`
  ADD CONSTRAINT `FK_STORE_GROUP_STORE` FOREIGN KEY (`group_id`) REFERENCES `{$installer->getTable('core_store_group')}` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_STORE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('core_layout_link')}`
  ADD CONSTRAINT `FK_CORE_LAYOUT_LINK_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CORE_LAYOUT_LINK_UPDATE` FOREIGN KEY (`layout_update_id`) REFERENCES `{$installer->getTable('core_layout_update')}` (`layout_update_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('core_session')}`
  ADD CONSTRAINT `FK_SESSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('core_translate')}`
  ADD CONSTRAINT `FK_CORE_TRANSLATE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('core_url_rewrite')}`
  ADD CONSTRAINT `FK_CORE_URL_REWRITE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('core_url_rewrite_tag')}`
  ADD CONSTRAINT `FK_CORE_URL_REWRITE_TAG_URL_REWRITE` FOREIGN KEY (`url_rewrite_id`) REFERENCES `{$installer->getTable('core_url_rewrite')}` (`url_rewrite_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$installer->getTable('design_change')}`
  ADD CONSTRAINT `FK_DESIGN_CHANGE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->endSetup();
