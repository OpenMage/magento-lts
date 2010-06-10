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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('catalog_product_bundle_option')};
CREATE TABLE {$this->getTable('catalog_product_bundle_option')} (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL,
  `required` tinyint(1) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bundle Options';

DROP TABLE IF EXISTS {$this->getTable('catalog_product_bundle_option_link')};
DROP TABLE IF EXISTS {$this->getTable('catalog_product_bundle_selection')};
CREATE TABLE {$this->getTable('catalog_product_bundle_selection')} (
  `selection_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL default '0',
  `is_default` tinyint(1) unsigned NOT NULL default '0',
  `selection_price_type` tinyint(1) unsigned NOT NULL default '0',
  `selection_price_value` decimal(12,4) NOT NULL default '0.0000',
  `selection_qty` decimal(12,4) NOT NULL default '0.0000',
  `selection_can_change_qty` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`selection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bundle Selections';

DROP TABLE IF EXISTS {$this->getTable('catalog_product_bundle_option_value')};
CREATE TABLE {$this->getTable('catalog_product_bundle_option_value')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bundle Selections';

");

$installer->addAttribute('catalog_product', 'price_type', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => '',
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
        'apply_to'          => 'bundle',
        'is_configurable'   => false
    ));

$installer->addAttribute('catalog_product', 'sku_type', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => '',
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
        'apply_to'          => 'bundle',
        'is_configurable'   => false
    ));

$installer->addAttribute('catalog_product', 'weight_type', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => '',
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
        'apply_to'          => 'bundle',
        'is_configurable'   => false
    ));

$installer->addAttribute('catalog_product', 'price_view', array(
        'group'             => 'Prices',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Price View',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'bundle/product_attribute_source_price_view',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => true,
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'bundle',
        'is_configurable'   => false
    ));

$fieldList = array('price','special_price','special_from_date','special_to_date',
    'minimal_price','cost','tier_price','weight','tax_class_id');
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array('bundle', $applyTo)) {
        $applyTo[] = 'bundle';
        $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}

$installer->endSetup();
