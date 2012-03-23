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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('xmlconnect/application')}` (
  `application_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(32) NOT NULL,
  `type` varchar(32) DEFAULT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `active_from` date DEFAULT NULL,
  `active_to` date DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `configuration` blob,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `browsing_mode` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `UNQ_XMLCONNECT_APPLICATION_CODE` (`code`),
  KEY `FK_XMLCONNECT_APPLICAION_STORE` (`store_id`),
  CONSTRAINT `FK_XMLCONNECT_APPLICAION_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('xmlconnect_history')}` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `params` blob,
  `title` varchar(200) DEFAULT NULL,
  `activation_key` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`history_id`),
  KEY `FK_XMLCONNECT_HISTORY_APPLICATION` (`application_id`),
  CONSTRAINT `FK_XMLCONNECT_HISTORY_APPLICATION` FOREIGN KEY (`application_id`) REFERENCES `{$installer->getTable('xmlconnect/application')}` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
");

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'thumbnail', array(
    'type'              => 'varchar',
    'backend'           => 'catalog/category_attribute_backend_image',
    'frontend'          => '',
    'label'             => 'Thumbnail Image',
    'input'             => 'image',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'thumbnail',
    '4'
);

$installer->endSetup();
