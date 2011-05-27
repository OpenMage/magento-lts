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
 * @package     Mage_Admin
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('admin_assert')};
CREATE TABLE {$this->getTable('admin_assert')} (
  `assert_id` int(10) unsigned NOT NULL auto_increment,
  `assert_type` varchar(20) character set utf8 NOT NULL default '',
  `assert_data` text character set utf8,
  PRIMARY KEY  (`assert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ACL Asserts';

-- DROP TABLE IF EXISTS {$this->getTable('admin_role')};
CREATE TABLE {$this->getTable('admin_role')} (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `tree_level` tinyint(3) unsigned NOT NULL default '0',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `role_type` char(1) character set utf8 NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `role_name` varchar(50) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`role_id`),
  KEY `parent_id` (`parent_id`,`sort_order`),
  KEY `tree_level` (`tree_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ACL Roles';

insert  into {$this->getTable('admin_role')}(`role_id`,`parent_id`,`tree_level`,`sort_order`,`role_type`,`user_id`,`role_name`) values (1,0,1,1,'G',0,'Administrators'),(2,1,2,1,'U',1,'Administrator');

-- DROP TABLE IF EXISTS {$this->getTable('admin_rule')};
CREATE TABLE {$this->getTable('admin_rule')} (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `role_id` int(10) unsigned NOT NULL default '0',
  `resource_id` varchar(255) character set utf8 NOT NULL default '',
  `privileges` varchar(20) character set utf8 NOT NULL default '',
  `assert_id` int(10) unsigned NOT NULL default '0',
  `role_type` char(1) default NULL,
  `permission` varchar(10) default NULL,
  PRIMARY KEY  (`rule_id`),
  KEY `resource` (`resource_id`,`role_id`),
  KEY `role_id` (`role_id`,`resource_id`),
  CONSTRAINT `FK_admin_rule` FOREIGN KEY (`role_id`) REFERENCES {$this->getTable('admin_role')} (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ACL Rules';

insert into {$this->getTable('admin_rule')}(`rule_id`, `role_id`, `resource_id`, `privileges`, `assert_id`, `role_type`, `permission`) values (1,1,'all','',0,'G','allow');

-- DROP TABLE IF EXISTS {$this->getTable('admin_user')};
CREATE TABLE {$this->getTable('admin_user')} (
  `user_id` mediumint(9) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) character set utf8 NOT NULL default '',
  `lastname` varchar(32) character set utf8 NOT NULL default '',
  `email` varchar(128) character set utf8 NOT NULL default '',
  `username` varchar(40) character set utf8 NOT NULL default '',
  `password` varchar(40) character set utf8 NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default NULL,
  `logdate` datetime default NULL,
  `lognum` smallint(5) unsigned NOT NULL default '0',
  `reload_acl_flag` tinyint(1) NOT NULL default '0',
  `is_active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users';
");

$installer->endSetup();
