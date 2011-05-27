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
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_ImportExport_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('importexport_importdata')}` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity` VARCHAR(50) NOT NULL,
    `behavior` SET('" . Mage_ImportExport_Model_Import::BEHAVIOR_APPEND. "','"
    . Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE . "','" .
    Mage_ImportExport_Model_Import::BEHAVIOR_DELETE . "') NOT NULL DEFAULT '" .
    Mage_ImportExport_Model_Import::BEHAVIOR_APPEND . "',
    `data` MEDIUMTEXT NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
");

// add unique key for parent-child pairs which makes easier configurable products import
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_super_link'),
    'UNQ_product_id_parent_id',
    array('product_id', 'parent_id'),
    'unique'
);

// add unique key for product-attribute pairs
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_super_attribute'),
    'UNQ_product_id_attribute_id',
    array('product_id', 'attribute_id'),
    'unique'
);

// add unique key for product-value-website
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'UNQ_product_super_attribute_id_value_index_website_id',
    array('product_super_attribute_id', 'value_index', 'website_id'),
    'unique'
);

$installer->getConnection()->addConstraint(
    'FK_INT_PRODUCT_LINK',
    $installer->getTable('catalog/product_link_attribute_int'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id'
);

$installer->getConnection()->addConstraint(
    'FK_INT_PRODUCT_LINK_ATTRIBUTE',
    $installer->getTable('catalog/product_link_attribute_int'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id'
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_link_attribute_int'),
    'UNQ_product_link_attribute_id_link_id',
    array('product_link_attribute_id', 'link_id'),
    'unique'
);

$installer->endSetup();
