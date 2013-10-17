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
 * @package     Mage_Log
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('log_customer')};
CREATE TABLE {$this->getTable('log_customer')} (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `visitor_id` bigint(20) unsigned default NULL,
  `customer_id` int(11) NOT NULL default '0',
  `login_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `logout_at` datetime default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Customers log information';

-- DROP TABLE IF EXISTS {$this->getTable('log_quote')};
CREATE TABLE {$this->getTable('log_quote')} (
  `quote_id` int(10) unsigned NOT NULL default '0',
  `visitor_id` bigint(20) unsigned default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`quote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Quote log data';

-- DROP TABLE IF EXISTS {$this->getTable('log_summary')};
CREATE TABLE {$this->getTable('log_summary')} (
  `summary_id` bigint(20) unsigned NOT NULL auto_increment,
  `type_id` smallint(5) unsigned default NULL,
  `visitor_count` int(11) NOT NULL default '0',
  `customer_count` int(11) NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`summary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Summary log information';

-- DROP TABLE IF EXISTS {$this->getTable('log_summary_type')};
CREATE TABLE {$this->getTable('log_summary_type')} (
  `type_id` smallint(5) unsigned NOT NULL auto_increment,
  `type_code` varchar(64) NOT NULL default '',
  `period` smallint(5) unsigned NOT NULL default '0',
  `period_type` enum('MINUTE','HOUR','DAY','WEEK','MONTH') NOT NULL default 'MINUTE',
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Type of summary information';

insert  into {$this->getTable('log_summary_type')} (`type_id`,`type_code`,`period`,`period_type`) values
    (1,'hour',1,'HOUR'),(2,'day',1,'DAY')
/* ,(3,'week',1,'WEEK'),(4,'month',1,'MONTH') */;

-- DROP TABLE IF EXISTS {$this->getTable('log_url')};
CREATE TABLE {$this->getTable('log_url')} (
  `url_id` bigint(20) unsigned NOT NULL default '0',
  `visitor_id` bigint(20) unsigned default NULL,
  `visit_time` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='URL visiting history';

-- DROP TABLE IF EXISTS {$this->getTable('log_url_info')};
CREATE TABLE {$this->getTable('log_url_info')} (
  `url_id` bigint(20) unsigned NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  `referer` varchar(255) default NULL,
  PRIMARY KEY  (`url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Detale information about url visit';

-- DROP TABLE IF EXISTS {$this->getTable('log_visitor')};
CREATE TABLE {$this->getTable('log_visitor')} (
  `visitor_id` bigint(20) unsigned NOT NULL auto_increment,
  `session_id` char(64) NOT NULL default '',
  `first_visit_at` datetime default NULL,
  `last_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_url_id` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`visitor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='System visitors log';

-- DROP TABLE IF EXISTS {$this->getTable('log_visitor_info')};
CREATE TABLE {$this->getTable('log_visitor_info')} (
  `visitor_id` bigint(20) unsigned NOT NULL default '0',
  `http_referer` varchar(255) default NULL,
  `http_user_agent` varchar(255) default NULL,
  `http_accept_charset` varchar(255) default NULL,
  `http_accept_language` varchar(255) default NULL,
  `server_addr` bigint(20) default NULL,
  `remote_addr` bigint(20) default NULL,
  PRIMARY KEY  (`visitor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Additional information by visitor';

    ");

$installer->endSetup();
