<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
