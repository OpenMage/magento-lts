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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
$installer->addAttribute('catalog_product', 'media_gallery',  array(
        'group'         => 'Images',
        'label'         => 'Media Gallery',
        'input'         => 'gallery',
        'backend'       => 'catalog/product_attribute_backend_media',
        'class'         => '',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'visible_on_front' => false
    )
);
$installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('catalog_product_entity_media_gallery')}`;
    CREATE TABLE `{$this->getTable('catalog_product_entity_media_gallery')}` (
        `value_id` int(11) unsigned NOT NULL auto_increment,
        `entity_type_id` smallint(5) unsigned NOT NULL default '0',
        `attribute_id` smallint(5) unsigned NOT NULL default '0',
        `entity_id` int(10) unsigned NOT NULL default '0',
        `value` varchar(255) default NULL,
        PRIMARY KEY  (`value_id`),
        KEY `FK_CATALOG_PRODUCT_MEDIA_GALLERY_ATTRIBUTE` (`attribute_id`),
        KEY `FK_CATALOG_PRODUCT_MEDIA_GALLERY_ENTITY` (`entity_id`),
        KEY `FK_CATALOG_PRODUCT_MEDIA_GALLERY_ENTITY_TYPE` (`entity_type_id`),
        CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE,
        CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE,
        CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog product media gallery';

    DROP TABLE IF EXISTS `{$this->getTable('catalog_product_entity_media_gallery_value')}`;
    CREATE TABLE `{$this->getTable('catalog_product_entity_media_gallery_value')}` (
      `value_id` int(11) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `label` varchar(255) default NULL,
      `position` int(11) unsigned default NULL,
      `disabled` tinyint(1) unsigned NOT NULL default '0',
      PRIMARY KEY  (`value_id`,`store_id`),
      KEY `FK_CATALOG_PRODUCT_MEDIA_GALLERY_VALUE_STORE` (`store_id`),
      CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_VALUE_GALLERY` FOREIGN KEY (`value_id`) REFERENCES `{$this->getTable('catalog_product_entity_media_gallery')}` (`value_id`) ON DELETE CASCADE,
      CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_VALUE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog product media gallery values';

    DROP TABLE IF EXISTS `{$this->getTable('catalog_product_entity_media_gallery_image')}`;
    CREATE TABLE `{$this->getTable('catalog_product_entity_media_gallery_image')}` (
      `value_id` int(11) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `type` varchar(50) NOT NULL default '',
      PRIMARY KEY  (`value_id`,`store_id`,`type`),
      CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_IMAGE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE,
      CONSTRAINT `FK_CATALOG_PRODUCT_MEDIA_GALLERY_IMAGE_GALLERY` FOREIGN KEY (`value_id`) REFERENCES `{$this->getTable('catalog_product_entity_media_gallery')}` (`value_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Catalog product media gallery images';
");

$installer->endSetup();
