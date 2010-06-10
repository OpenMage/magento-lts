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
 * @package     Mage_Widget
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('widget/widget')}` (
  `widget_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `parameters` text,
  PRIMARY KEY  (`widget_id`),
  KEY `IDX_CODE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preconfigured Widgets';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('widget/widget_instance')}` (
  `instance_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `package_theme` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `store_ids` VARCHAR(255) NOT NULL DEFAULT '0',
  `widget_parameters` TEXT,
  `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('widget/widget_instance_page')}` (
  `page_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `group` VARCHAR(25) NOT NULL DEFAULT '',
  `layout_handle` VARCHAR(255) NOT NULL DEFAULT '',
  `block_reference` VARCHAR(255) NOT NULL DEFAULT '',
  `for` VARCHAR(25) NOT NULL DEFAULT '',
  `entities` TEXT,
  `template` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`page_id`),
  KEY `IDX_WIDGET_WIDGET_INSTANCE_ID` (`instance_id`),
  CONSTRAINT `FK_WIDGET_WIDGET_INSTANCE_ID` FOREIGN KEY (`instance_id`) REFERENCES `{$installer->getTable('widget/widget_instance')}` (`instance_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('widget/widget_instance_page_layout')}` (
    `page_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
    `layout_update_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
    UNIQUE KEY `page_id` (`page_id`,`layout_update_id`),
    KEY `IDX_WIDGET_WIDGET_INSTANCE_PAGE_ID` (`page_id`),
    KEY `IDX_WIDGET_WIDGET_INSTANCE_LAYOUT_UPDATE_ID` (`layout_update_id`),
    CONSTRAINT `FK_WIDGET_WIDGET_INSTANCE_LAYOUT_UPDATE_ID` FOREIGN KEY (`layout_update_id`) REFERENCES `{$installer->getTable('core/layout_update')}` (`layout_update_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_WIDGET_WIDGET_INSTANCE_PAGE_ID` FOREIGN KEY (`page_id`) REFERENCES `{$installer->getTable('widget/widget_instance_page')}` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
