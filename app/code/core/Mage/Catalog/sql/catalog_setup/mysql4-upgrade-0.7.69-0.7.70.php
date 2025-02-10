<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
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
