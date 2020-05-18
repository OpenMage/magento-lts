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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer install
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('customer_address_entity')}`;
CREATE TABLE `{$installer->getTable('customer_address_entity')}` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `increment_id` varchar(50) NOT NULL default '',
  `parent_id` int(10) unsigned default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_CUSTOMER_ADDRESS_CUSTOMER_ID` (`parent_id`),
  CONSTRAINT `FK_CUSTOMER_ADDRESS_CUSTOMER_ID` FOREIGN KEY (`parent_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Customer Address Entities';

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_address_entity_datetime')}`;
CREATE TABLE `{$installer->getTable('customer_address_entity_datetime')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ADDRESS_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_DATETIME_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_ADDRESS_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_address_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_address_entity_decimal')}`;
CREATE TABLE `{$installer->getTable('customer_address_entity_decimal')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ADDRESS_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_DECIMAL_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_ADDRESS_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_address_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_address_entity_int')}`;
CREATE TABLE `{$installer->getTable('customer_address_entity_int')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ADDRESS_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_INT_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_ADDRESS_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_address_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_address_entity_text')}`;
CREATE TABLE `{$installer->getTable('customer_address_entity_text')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ADDRESS_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_TEXT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CUSTOMER_ADDRESS_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_address_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_address_entity_varchar')}`;
CREATE TABLE `{$installer->getTable('customer_address_entity_varchar')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ADDRESS_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_ADDRESS_VARCHAR_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_ADDRESS_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_address_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_ADDRESS_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_entity')}`;
CREATE TABLE `{$installer->getTable('customer_entity')}` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `website_id` smallint(5) unsigned default NULL,
  `email` varchar(255) NOT NULL default '',
  `group_id` smallint(3) unsigned NOT NULL default '0',
  `increment_id` varchar(50) NOT NULL default '',
  `store_id` smallint(5) unsigned default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_CUSTOMER_ENTITY_STORE` (`store_id`),
  KEY `IDX_ENTITY_TYPE` (`entity_type_id`),
  KEY `IDX_AUTH` (`email`,`website_id`),
  KEY `FK_CUSTOMER_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CUSTOMER_ENTITY_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Customer Entityies';

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_entity_datetime')}`;
CREATE TABLE `{$installer->getTable('customer_entity_datetime')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_DATETIME_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_entity_decimal')}`;
CREATE TABLE `{$installer->getTable('customer_entity_decimal')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_DECIMAL_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_entity_int')}`;
CREATE TABLE `{$installer->getTable('customer_entity_int')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_INT_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_entity_text')}`;
CREATE TABLE `{$installer->getTable('customer_entity_text')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_TEXT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CUSTOMER_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_entity_varchar')}`;
CREATE TABLE `{$installer->getTable('customer_entity_varchar')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
  KEY `FK_CUSTOMER_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMER_VARCHAR_ENTITY` (`entity_id`),
  KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`),
  CONSTRAINT `FK_CUSTOMER_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$installer->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('customer_group')}`;
CREATE TABLE `{$installer->getTable('customer_group')}` (
  `customer_group_id` smallint(3) unsigned NOT NULL auto_increment,
  `customer_group_code` varchar(32) NOT NULL default '',
  `tax_class_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`customer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer groups';

INSERT INTO `{$installer->getTable('customer_group')}` VALUES(0, 'NOT LOGGED IN', 3), (1, 'General', 3), (2, 'Wholesale', 3), (3, 'Retailer', 3);

-- DROP TABLE IF EXISTS `{$installer->getTable('customer/eav_attribute')}`;
CREATE TABLE `{$installer->getTable('customer/eav_attribute')}` (
  `attribute_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_visible_on_front` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `input_filter` varchar(255) NOT NULL,
  `lines_to_divide_multiline` smallint(5) unsigned NOT NULL DEFAULT '0',
  `min_text_length` int(11) unsigned NOT NULL DEFAULT '0',
  `max_text_length` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attribute_id`),
  CONSTRAINT `FK_CUSTOMER_EAV_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
$installer->installEntities();


$setup = $installer->getConnection();

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/prefix_show')
    ->where('value!=?', '0');
$showPrefix = (bool)Mage::helper('customer/address')->getConfig('prefix_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/middlename_show')
    ->where('value!=?', '0');
$showMiddlename = (bool)Mage::helper('customer/address')->getConfig('middlename_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/suffix_show')
    ->where('value!=?', '0');
$showSuffix = (bool)Mage::helper('customer/address')->getConfig('suffix_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/dob_show')
    ->where('value!=?', '0');
$showDob = (bool)Mage::helper('customer/address')->getConfig('dob_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/taxvat_show')
    ->where('value!=?', '0');
$showTaxVat = (bool)Mage::helper('customer/address')->getConfig('taxvat_show')
    || $setup->fetchOne($select) > 0;

/**
 *****************************************************************************
 * customer/account/create/
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), array(
    'code'      => 'customer_account_create',
    'label'     => 'customer_account_create',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0
));
$formTypeId   = $setup->lastInsertId();
$entityTypeId = $installer->getEntityTypeId('customer');

$setup->insert($installer->getTable('eav/form_type_entity'), array(
    'type_id'        => $formTypeId,
    'entity_type_id' => $entityTypeId
));

$setup->insert($installer->getTable('eav/form_fieldset'), array(
    'type_id'    => $formTypeId,
    'code'       => 'general',
    'sort_order' => 1
));
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), array(
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Personal Information'
));

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'prefix'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'firstname'),
    'sort_order'    => $elementSort++
));
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'middlename'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'lastname'),
    'sort_order'    => $elementSort++
));
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'suffix'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'email'),
    'sort_order'    => $elementSort++
));
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'dob'),
        'sort_order'    => $elementSort++
    ));
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++
    ));
}

/**
 *****************************************************************************
 * customer/account/edit/
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), array(
    'code'      => 'customer_account_edit',
    'label'     => 'customer_account_edit',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0
));
$formTypeId   = $setup->lastInsertId();
$entityTypeId = $installer->getEntityTypeId('customer');

$setup->insert($installer->getTable('eav/form_type_entity'), array(
    'type_id'        => $formTypeId,
    'entity_type_id' => $entityTypeId
));

$setup->insert($installer->getTable('eav/form_fieldset'), array(
    'type_id'    => $formTypeId,
    'code'       => 'general',
    'sort_order' => 1
));
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), array(
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Account Information'
));

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'prefix'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'firstname'),
    'sort_order'    => $elementSort++
));
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'middlename'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'lastname'),
    'sort_order'    => $elementSort++
));
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'suffix'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'email'),
    'sort_order'    => $elementSort++
));
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'dob'),
        'sort_order'    => $elementSort++
    ));
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++
    ));
}

/**
 *****************************************************************************
 * customer/address/edit
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), array(
    'code'      => 'customer_address_edit',
    'label'     => 'customer_address_edit',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0
));
$formTypeId   = $setup->lastInsertId();
$entityTypeId = $installer->getEntityTypeId('customer_address');

$setup->insert($installer->getTable('eav/form_type_entity'), array(
    'type_id'        => $formTypeId,
    'entity_type_id' => $entityTypeId
));

$setup->insert($installer->getTable('eav/form_fieldset'), array(
    'type_id'    => $formTypeId,
    'code'       => 'contact',
    'sort_order' => 1
));
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), array(
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Contact Information'
));

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'prefix'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'firstname'),
    'sort_order'    => $elementSort++
));
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'middlename'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'lastname'),
    'sort_order'    => $elementSort++
));
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), array(
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'suffix'),
        'sort_order'    => $elementSort++
    ));
}
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'company'),
    'sort_order'    => $elementSort++
));
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'telephone'),
    'sort_order'    => $elementSort++
));
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'fax'),
    'sort_order'    => $elementSort++
));

$setup->insert($installer->getTable('eav/form_fieldset'), array(
    'type_id'    => $formTypeId,
    'code'       => 'address',
    'sort_order' => 2
));
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), array(
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Address'
));

$elementSort = 0;
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'street'),
    'sort_order'    => $elementSort++
));
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'city'),
    'sort_order'    => $elementSort++
));
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'region'),
    'sort_order'    => $elementSort++
));
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'postcode'),
    'sort_order'    => $elementSort++
));
$setup->insert($installer->getTable('eav/form_element'), array(
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'country_id'),
    'sort_order'    => $elementSort++
));
