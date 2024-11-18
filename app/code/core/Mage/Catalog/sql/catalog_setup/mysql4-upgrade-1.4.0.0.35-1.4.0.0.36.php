<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
