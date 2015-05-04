<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$productTypes = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_enabled', array(
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
    'used_in_product_listing' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_display_actual_price_type', array(
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
    'used_in_product_listing' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp', array(
    'group'         => 'Prices',
    'backend'       => 'catalog/product_attribute_backend_price',
    'frontend'      => '',
    'label'         => 'Manufacturer\'s Suggested Retail Price',
    'type'          => 'decimal',
    'input'         => 'price',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true
));
