<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/product_index_price'),
    'final_price',
    'DECIMAL(12,4) DEFAULT NULL AFTER `price`',
);
$installer->getConnection()->addColumn(
    $installer->getTable('catalog/product_index_price'),
    'tier_price',
    'DECIMAL(12,4) DEFAULT NULL',
);

$installer->endSetup();
