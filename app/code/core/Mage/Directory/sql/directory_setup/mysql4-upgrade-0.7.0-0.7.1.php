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
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup()
    ->run("
DROP TABLE IF EXISTS {$this->getTable('directory_country_format')};
CREATE TABLE {$this->getTable('directory_country_format')} (
    `country_format_id` int(10) unsigned NOT NULL auto_increment,
    `country_id` char(2) NOT NULL default '',
    `type` varchar(30) NOT NULL default '',
    `format` text NOT NULL,
    PRIMARY KEY  (`country_format_id`),
    UNIQUE KEY `country_type` (`country_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries format';

ALTER TABLE {$this->getTable('directory_country')},
  DROP COLUMN `address_template_plain`, DROP COLUMN `address_template_html`;
");
$installer->endSetup();
