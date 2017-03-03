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
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('downloadable/sample'), 'sample_type', "varchar(20) NOT NULL default '' AFTER `sample_file`");
$installer->getConnection()->addColumn($installer->getTable('downloadable/link'), 'link_type', "varchar(20) NOT NULL default '' AFTER `link_file`");
$installer->getConnection()->addColumn($installer->getTable('downloadable/link'), 'sample_type', "varchar(20) NOT NULL default '' AFTER `sample_file`");

$conn->dropForeignKey($installer->getTable('downloadable/link_purchased'), 'FK_DOWNLOADABLE_ORDER_ITEM_ID');
$conn->dropKey($installer->getTable('downloadable/link_purchased'), 'DOWNLOADABLE_ORDER_ITEM_ID');


$installer->run("
CREATE TABLE `{$installer->getTable('downloadable/link_purchased_item')}`(
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `purchased_id` int(10) unsigned NOT NULL default '0',
  `order_item_id` int(10) unsigned NOT NULL default '0',
  `number_of_downloads_bought` int(10) unsigned NOT NULL default '0',
  `number_of_downloads_used` int(10) unsigned NOT NULL default '0',
  `link_id` int(20) unsigned NOT NULL default '0',
  `link_title` varchar(255) NOT NULL default '',
  `is_shareable` smallint(1) unsigned NOT NULL default '0',
  `link_url` varchar(255) NOT NULL default '',
  `link_file` varchar(255) NOT NULL default '',
  `link_type` varchar(255) NOT NULL default '',
  `status` varchar(50) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`item_id`),
  KEY `DOWNLOADABLE_LINK_PURCHASED_ID` (`purchased_id`),
  KEY `DOWNLOADABLE_ORDER_ITEM_ID` (`order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DOWNLOADABLE_LINK_PURCHASED_ID', $installer->getTable('downloadable/link_purchased_item'), 'purchased_id', $installer->getTable('downloadable/link_purchased'), 'purchased_id'
);
$conn->addConstraint(
    'FK_DOWNLOADABLE_ORDER_ITEM_ID', $installer->getTable('downloadable/link_purchased_item'), 'order_item_id', $installer->getTable('sales/order_item'), 'item_id'
);

$installer->run("
    INSERT INTO `{$installer->getTable('downloadable/link_purchased_item')}`
        (`purchased_id`, `order_item_id`, `number_of_downloads_bought`, `number_of_downloads_used`, `link_id`, `link_title`, `is_shareable`, `link_url`, `link_file`, `status`, `created_at`, `updated_at`)
    SELECT
        `purchased_id`, `order_item_id`, `number_of_downloads_bought`, `number_of_downloads_used`, `link_id`, `link_title`, `is_shareable`, `link_url`, `link_file`, `status`, `created_at`, `updated_at`
        FROM `{$installer->getTable('downloadable/link_purchased')}`

");

$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'order_item_id');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'number_of_downloads_bought');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'number_of_downloads_used');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'link_id');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'link_title');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'is_shareable');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'link_url');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'link_file');
$conn->dropColumn($installer->getTable('downloadable/link_purchased'), 'status');

$installer->getConnection()->addColumn($installer->getTable('downloadable/link_purchased'), 'link_section_title', "varchar(255) NOT NULL default '' AFTER `product_sku`");

$installer->endSetup();
