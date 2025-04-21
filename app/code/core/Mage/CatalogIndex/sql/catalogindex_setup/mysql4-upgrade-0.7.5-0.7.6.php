<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_PRODUCT_PRODUCT',
    $installer->getTable('catalogrule_product'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);

$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_PRODUCT_PRICE_PRODUCT',
    $installer->getTable('catalogrule_product_price'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
);

$installer->endSetup();
