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

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_price'),
    'IDX_CATALOG_PRODUCT_OPTION_PRICE_SI_OI',
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_price'),
    'UNQ_OPTION_STORE',
    ['option_id', 'store_id'],
    'unique',
);

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TITLE_SI_OI',
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_title'),
    'UNQ_OPTION_STORE',
    ['option_id', 'store_id'],
    'unique',
);

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_type_price'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_SI_OTI',
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_type_price'),
    'UNQ_OPTION_TYPE_STORE',
    ['option_type_id', 'store_id'],
    'unique',
);

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_type_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_SI_OTI',
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_type_title'),
    'UNQ_OPTION_TYPE_STORE',
    ['option_type_id', 'store_id'],
    'unique',
);

$installer->endSetup();
