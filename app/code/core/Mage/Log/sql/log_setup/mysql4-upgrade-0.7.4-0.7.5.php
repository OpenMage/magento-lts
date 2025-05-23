<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('log_visitor_online')}` (
  `visitor_id` bigint(20) unsigned NOT NULL auto_increment,
  `visitor_type` char(1) NOT NULL,
  `remote_addr` int(11) NOT NULL,
  `first_visit_at` datetime default NULL,
  `last_visit_at` datetime default NULL,
  `customer_id` int(10) unsigned default NULL,
  `last_url` varchar(255) default NULL,
  PRIMARY KEY  (`visitor_id`),
  KEY `IDX_VISITOR_TYPE` (`visitor_type`),
  KEY `IDX_VISIT_TIME` (`first_visit_at`,`last_visit_at`),
  KEY `IDX_CUSTOMER` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
