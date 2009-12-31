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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$installer->getConnection()->dropForeignKey($installer->getTable('catalog/product_super_attribute_label'),
    'FK_SUPER_PRODUCT_ATTRIBUTE_LABEL');
$installer->getConnection()->dropForeignKey($installer->getTable('catalog/product_super_attribute_label'),
    'catalog_product_super_attribute_label_ibfk_1');
$installer->getConnection()->dropKey($installer->getTable('catalog/product_super_attribute_label'),
    'IDX_CATALOG_PRODUCT_SUPER_ATTRIBUTE_STORE_PSAI_SI');
$installer->getConnection()->addColumn($installer->getTable('catalog/product_super_attribute_label'),
    'use_default', 'tinyint(1) UNSIGNED DEFAULT 0 AFTER store_id');
$installer->getConnection()->addConstraint('FK_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL_ATTRIBUTE',
    $installer->getTable('catalog/product_super_attribute_label'), 'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'), 'product_super_attribute_id',
    'cascade', 'cascade', true);
$installer->getConnection()->addConstraint('FK_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL_STORE',
    $installer->getTable('catalog/product_super_attribute_label'), 'store_id',
    $installer->getTable('core/store'), 'store_id',
    'cascade', 'cascade', true);
$installer->getConnection()->addKey($installer->getTable('catalog/product_super_attribute_label'),
    'UNQ_ATTRIBUTE_STORE', array('product_super_attribute_id', 'store_id'), 'unique');
$installer->endSetup();
