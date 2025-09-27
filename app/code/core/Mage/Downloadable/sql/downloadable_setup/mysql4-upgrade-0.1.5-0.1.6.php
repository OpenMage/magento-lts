<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
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
    'FK_DOWNLOADABLE_ORDER_ITEM_ID',
    $installer->getTable('downloadable/link_purchased'),
    'order_item_id',
    $installer->getTable('sales/order_item'),
    'item_id',
);
$conn->addConstraint(
    'FK_DOWNLOADABLE_ORDER_ID',
    $installer->getTable('downloadable/link_purchased'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id',
);

$installer->endSetup();
