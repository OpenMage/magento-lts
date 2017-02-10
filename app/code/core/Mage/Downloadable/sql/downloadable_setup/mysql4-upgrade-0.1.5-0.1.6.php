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

$installer->run("
CREATE TABLE `{$installer->getTable('downloadable/link_purchased')}` (
    `purchased_id` int(10) unsigned NOT NULL auto_increment,
    `order_item_id` int(10) unsigned NOT NULL default '0',
    `order_id` int(10) unsigned NOT NULL default '0',
    `number_of_downloads_bought` int(10) unsigned NOT NULL default '0',
    `number_of_downloads_used` int(10) unsigned NOT NULL default '0',
    `link_id` int(20) unsigned NOT NULL default '0',
    `link_title` varchar(255) NOT NULL default '',
    `link_url` varchar(255) NOT NULL default '',
    `link_file` varchar(255) NOT NULL default '',
    `status` varchar(50) NOT NULL default '',
    `product_name` varchar(255) NOT NULL default '',
    `product_sku` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`purchased_id`),
    KEY `DOWNLOADABLE_ORDER_ITEM_ID` (`order_item_id`),
    KEY `DOWNLOADABLE_ORDER_ID` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DOWNLOADABLE_ORDER_ITEM_ID', $installer->getTable('downloadable/link_purchased'), 'order_item_id', $installer->getTable('sales/order_item'), 'item_id'
);
$conn->addConstraint(
    'FK_DOWNLOADABLE_ORDER_ID', $installer->getTable('downloadable/link_purchased'), 'order_id', $installer->getTable('sales/order'), 'entity_id'
);

$installer->endSetup();
