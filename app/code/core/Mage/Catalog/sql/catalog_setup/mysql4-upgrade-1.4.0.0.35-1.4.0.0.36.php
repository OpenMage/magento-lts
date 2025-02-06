<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL_ATTRIBUTE',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_super_attribute_label'),
    'FK_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL_STORE',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_PROD_SUPER_ATTR_LABEL_ATTR',
    $installer->getTable('catalog/product_super_attribute_label'),
    'product_super_attribute_id',
    $installer->getTable('catalog/product_super_attribute'),
    'product_super_attribute_id',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_PROD_SUPER_ATTR_LABEL_STORE',
    $installer->getTable('catalog/product_super_attribute_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);
$installer->endSetup();
