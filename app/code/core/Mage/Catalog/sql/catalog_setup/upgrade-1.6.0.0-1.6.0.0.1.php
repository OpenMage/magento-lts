<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

$productTypes = [
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
];
$productTypes = implode(',', $productTypes);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_enabled', [
    'group'         => 'Prices',
    'backend'       => 'catalog/product_attribute_backend_msrp',
    'frontend'      => '',
    'label'         => 'Apply MAP',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'adminhtml/catalog_product_helper_form_msrp_enabled',
    'visible_on_front' => false,
    'used_in_product_listing' => true,
]);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_display_actual_price_type', [
    'group'         => 'Prices',
    'backend'       => 'catalog/product_attribute_backend_boolean',
    'frontend'      => '',
    'label'         => 'Display Actual Price',
    'input'         => 'select',
    'source'        => 'catalog/product_attribute_source_msrp_type',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'adminhtml/catalog_product_helper_form_msrp_price',
    'visible_on_front' => false,
    'used_in_product_listing' => true,
]);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp', [
    'group'         => 'Prices',
    'backend'       => 'catalog/product_attribute_backend_price',
    'frontend'      => '',
    'label'         => "Manufacturer's Suggested Retail Price",
    'type'          => 'decimal',
    'input'         => 'price',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true,
]);
