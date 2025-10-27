<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'msrp_enabled',
    'source_model',
    'catalog/product_attribute_source_msrp_type_enabled',
);

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'msrp_enabled',
    'default_value',
    Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_USE_CONFIG,
);

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'msrp_display_actual_price_type',
    'source_model',
    'catalog/product_attribute_source_msrp_type_price',
);

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'msrp_display_actual_price_type',
    'default_value',
    Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG,
);
