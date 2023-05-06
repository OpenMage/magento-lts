<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('dataflow_session')}`;
CREATE TABLE `{$this->getTable('dataflow_session')}` (
  `session_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `created_date` datetime default NULL,
  `file` varchar(255) character set utf8 default NULL,
  `type` varchar(32) character set utf8 default NULL,
  `direction` varchar(32) character set utf8 default NULL,
  `comment` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('dataflow_import_data')}`;
CREATE TABLE `{$this->getTable('dataflow_import_data')}` (
  `import_id` int(11) NOT NULL auto_increment,
  `session_id` int(11) default NULL,
  `serial_number` int(11) NOT NULL default '0',
  `value` text character set utf8,
  `status` int(1) NOT NULL default '0',
  PRIMARY KEY  (`import_id`),
  KEY `FK_dataflow_import_data` (`session_id`),
  CONSTRAINT `FK_dataflow_import_data` FOREIGN KEY (`session_id`) REFERENCES `{$this->getTable('dataflow_session')}` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
