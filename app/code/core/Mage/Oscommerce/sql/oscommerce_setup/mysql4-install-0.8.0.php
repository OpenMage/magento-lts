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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('oscommerce_import')}`;

CREATE TABLE `{$this->getTable('oscommerce_import')}` (
  `import_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `db_name` varchar(255) default NULL,
  `db_user` varchar(255) default NULL,
  `db_password` varchar(255) default NULL,
  `db_type` varchar(32) default NULL,
  PRIMARY KEY  (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('oscommerce_import_type')}` (
  `type_id` int(2) unsigned NOT NULL auto_increment,
  `type_code` varchar(32) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

insert  into `{$this->getTable('oscommerce_import_type')}`(`type_id`,`type_code`,`type_name`) values (1,'store','Store'),(2,'category','Category'),(3,'product','Product'), (4,'customer','Customer'),(5,'order','Order');

CREATE TABLE `{$this->getTable('oscommerce_ref')}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `import_id` int(10) NOT NULL,
  `type_id` int(10) NOT NULL,
  `value` int(10) NOT NULL,
  `ref_id` int(10) NOT NULL,
  `created_at` datetime default NULL,
  `user_id` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
