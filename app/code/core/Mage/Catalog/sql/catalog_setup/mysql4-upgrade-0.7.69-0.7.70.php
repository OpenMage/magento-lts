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

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/compare_item'),
    'store_id',
    'smallint unsigned default null',
);
$installer->getConnection()->addConstraint(
    'FK_CATALOG_COMPARE_ITEM_STORE',
    $installer->getTable('catalog/compare_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'set null',
    'cascade',
);

$installer->endSetup();
