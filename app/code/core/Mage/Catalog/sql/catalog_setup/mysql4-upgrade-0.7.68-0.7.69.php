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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
// fix for sample data 1.2.0
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_PRODUCT'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE'
);
$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE'
);
$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_website'),
    'FK_CATALOG_PRODUCT_WEBSITE_WEBSITE'
);
$installer->getConnection()->addConstraint('FK_SUPER_PRODUCT_ATTRIBUTE_LABEL',
    $installer->getTable('catalog/product_super_attribute_label'), 'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'), 'product_super_attribute_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SUPER_PRODUCT_ATTRIBUTE_PRICING',
    $installer->getTable('catalog/product_super_attribute_pricing'), 'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'), 'product_super_attribute_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SUPER_PRODUCT_LINK_ENTITY',
    $installer->getTable('catalog/product_super_link'), 'product_id',
    $installer->getTable('catalog/product'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SUPER_PRODUCT_LINK_PARENT',
    $installer->getTable('catalog/product_super_link'), 'parent_id',
    $installer->getTable('catalog/product'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_CATALOG_PRODUCT_WEBSITE_WEBSITE',
    $installer->getTable('catalog/product_website'), 'website_id',
    $installer->getTable('core/website'), 'website_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_CATALOG_WEBSITE_PRODUCT_PRODUCT',
    $installer->getTable('catalog/product_website'), 'product_id',
    $installer->getTable('catalog/product'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->endSetup();
