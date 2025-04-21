<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_super_attribute_label'),
    'IDX_CATALOG_PRODUCT_SUPER_ATTRIBUTE_STORE_PSAI_SI',
    ['product_super_attribute_id', 'store_id'],
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_price'),
    'IDX_CATALOG_PRODUCT_OPTION_PRICE_SI_OI',
    ['store_id', 'option_id'],
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TITLE_SI_OI',
    ['store_id', 'option_id'],
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_type_price'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_SI_OTI',
    ['store_id', 'option_type_id'],
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_type_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_SI_OTI',
    ['store_id', 'option_type_id'],
);

$installer->endSetup();
