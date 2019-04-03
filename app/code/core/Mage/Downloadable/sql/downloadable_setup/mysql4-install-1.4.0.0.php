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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('downloadable_link')}` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `number_of_downloads` int(10) unsigned DEFAULT NULL,
  `is_shareable` smallint(1) unsigned NOT NULL DEFAULT '0',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_file` varchar(255) NOT NULL DEFAULT '',
  `link_type` varchar(20) NOT NULL DEFAULT '',
  `sample_url` varchar(255) NOT NULL DEFAULT '',
  `sample_file` varchar(255) NOT NULL DEFAULT '',
  `sample_type` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `DOWNLODABLE_LINK_PRODUCT` (`product_id`),
  KEY `DOWNLODABLE_LINK_PRODUCT_SORT_ORDER` (`product_id`,`sort_order`),
  CONSTRAINT `FK_DOWNLODABLE_LINK_PRODUCT` FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable_link_price')}` (
  `price_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link_id` int(10) unsigned NOT NULL DEFAULT '0',
  `website_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`price_id`),
  KEY `DOWNLOADABLE_LINK_PRICE_LINK` (`link_id`),
  KEY `DOWNLOADABLE_LINK_PRICE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_DOWNLOADABLE_LINK_PRICE_WEBSITE` FOREIGN KEY (`website_id`)
    REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DOWNLOADABLE_LINK_PRICE_LINK` FOREIGN KEY (`link_id`)
    REFERENCES `{$installer->getTable('downloadable_link')}` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable_link_purchased')}` (
  `purchased_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_increment_id` varchar(50) NOT NULL DEFAULT '',
  `order_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `customer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `product_sku` varchar(255) NOT NULL DEFAULT '',
  `link_section_title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`purchased_id`),
  KEY `DOWNLOADABLE_ORDER_ID` (`order_id`),
  KEY `DOWNLOADABLE_CUSTOMER_ID` (`customer_id`),
  KEY `KEY_DOWNLOADABLE_ORDER_ITEM_ID` (`order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable_link_purchased_item')}` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchased_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned DEFAULT '0',
  `link_hash` varchar(255) NOT NULL DEFAULT '',
  `number_of_downloads_bought` int(10) unsigned NOT NULL DEFAULT '0',
  `number_of_downloads_used` int(10) unsigned NOT NULL DEFAULT '0',
  `link_id` int(20) unsigned NOT NULL DEFAULT '0',
  `link_title` varchar(255) NOT NULL DEFAULT '',
  `is_shareable` smallint(1) unsigned NOT NULL DEFAULT '0',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_file` varchar(255) NOT NULL DEFAULT '',
  `link_type` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`item_id`),
  KEY `DOWNLOADABLE_LINK_PURCHASED_ID` (`purchased_id`),
  KEY `DOWNLOADABLE_ORDER_ITEM_ID` (`order_item_id`),
  KEY `DOWNLOADALBE_LINK_HASH` (`link_hash`),
  CONSTRAINT `FK_DOWNLOADABLE_LINK_PURCHASED_ID` FOREIGN KEY (`purchased_id`)
    REFERENCES `{$installer->getTable('downloadable_link_purchased')}` (`purchased_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable_link_title')}` (
  `title_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link_id` int(10) unsigned NOT NULL DEFAULT '0',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`title_id`),
  UNIQUE KEY `UNQ_LINK_TITLE_STORE` (`link_id`,`store_id`),
  KEY `DOWNLOADABLE_LINK_TITLE_LINK` (`link_id`),
  KEY `DOWNLOADABLE_LINK_TITLE_STORE` (`store_id`),
  CONSTRAINT `FK_DOWNLOADABLE_LINK_TITLE_LINK` FOREIGN KEY (`link_id`)
    REFERENCES `{$installer->getTable('downloadable_link')}` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DOWNLOADABLE_LINK_TITLE_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable_sample')}` (
  `sample_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sample_url` varchar(255) NOT NULL DEFAULT '',
  `sample_file` varchar(255) NOT NULL DEFAULT '',
  `sample_type` varchar(20) NOT NULL DEFAULT '',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sample_id`),
  KEY `DOWNLODABLE_SAMPLE_PRODUCT` (`product_id`),
  CONSTRAINT `FK_DOWNLODABLE_SAMPLE_PRODUCT` FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('downloadable_sample_title')}` (
  `title_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sample_id` int(10) unsigned NOT NULL DEFAULT '0',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`title_id`),
  UNIQUE KEY `UNQ_SAMPLE_TITLE_STORE` (`sample_id`,`store_id`),
  KEY `DOWNLOADABLE_SAMPLE_TITLE_SAMPLE` (`sample_id`),
  KEY `DOWNLOADABLE_SAMPLE_TITLE_STORE` (`store_id`),
  CONSTRAINT `FK_DOWNLOADABLE_SAMPLE_TITLE_SAMPLE` FOREIGN KEY (`sample_id`)
    REFERENCES `{$installer->getTable('downloadable_sample')}` (`sample_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DOWNLOADABLE_SAMPLE_TITLE_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$fieldList = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'cost',
    'tier_price',
    'tax_class_id'
);

// make these attributes applicable to downloadable products
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array('downloadable', $applyTo)) {
        $applyTo[] = 'downloadable';
        $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}

$installer->addAttribute('catalog_product', 'links_purchased_separately', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Links can be purchased separately',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'downloadable',
    'is_configurable'   => false
));

$installer->addAttribute('catalog_product', 'samples_title', array(
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Samples title',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'downloadable',
    'is_configurable'   => false
));

$installer->addAttribute('catalog_product', 'links_title', array(
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Links title',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'downloadable',
    'is_configurable'   => false
));

$installer->addAttribute('catalog_product', 'links_exist', array(
    'type'                      => 'int',
    'backend'                   => '',
    'frontend'                  => '',
    'label'                     => '',
    'input'                     => '',
    'class'                     => '',
    'source'                    => '',
    'global'                    => true,
    'visible'                   => false,
    'required'                  => false,
    'user_defined'              => false,
    'default'                   => '0',
    'searchable'                => false,
    'filterable'                => false,
    'comparable'                => false,
    'visible_on_front'          => false,
    'unique'                    => false,
    'apply_to'                  => 'downloadable',
    'is_configurable'           => false,
    'used_in_product_listing'   => 1
));

$installer->endSetup();
