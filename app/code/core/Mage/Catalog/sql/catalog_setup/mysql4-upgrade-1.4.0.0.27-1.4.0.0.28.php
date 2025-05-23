<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

// is product used for recurring payments
$installer->addAttribute('catalog_product', 'is_recurring', [
    'group'             => 'Recurring Profile',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Enable Recurring Profile',
    'note'              => 'Products with recurring profile participate in catalog as nominal items.',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,virtual',
    'is_configurable'   => false,
]);

// recurring payment profile
$installer->addAttribute('catalog_product', 'recurring_profile', [
    'group'             => 'Recurring Profile',
    'type'              => 'text',
    'backend'           => 'catalog/product_attribute_backend_recurring',
    'frontend'          => '',
    'label'             => 'Recurring Payment Profile',
    'input'             => 'text', // doesn't matter
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,virtual',
    'is_configurable'   => false,
]);
