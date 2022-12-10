<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->closeConnection();

// Add listing and sort attribute properties
$installer->getConnection()->addColumn(
    $installer->getTable('eav/attribute'),
    'used_in_product_listing',
    'tinyint(1) UNSIGNED NOT NULL DEFAULT 0'
);
$installer->getConnection()->addColumn(
    $installer->getTable('eav/attribute'),
    'used_for_sort_by',
    'tinyint(1) UNSIGNED NOT NULL DEFAULT 0'
);

$entityTypeId   = $installer->getEntityTypeId('catalog_product');
$sqlAttributes  = $installer->getConnection()->quoteInto(
    '?',
    Mage::getSingleton('catalog/config')->getProductCollectionAttributes()
);
$installer->run("
UPDATE `{$installer->getTable('eav/attribute')}`
    SET `used_for_sort_by`='1'
    WHERE `entity_type_id`='{$entityTypeId}'
        AND `attribute_code` IN('name', 'price');
UPDATE `{$installer->getTable('eav/attribute')}`
    SET `used_in_product_listing`='1'
    WHERE `entity_type_id`='{$entityTypeId}'
        AND `attribute_code` IN($sqlAttributes);
");

$installer->getConnection()->addKey(
    $installer->getTable('eav/attribute'),
    'IDX_USED_FOR_SORT_BY',
    ['entity_type_id','used_for_sort_by']
);
$installer->getConnection()->addKey(
    $installer->getTable('eav/attribute'),
    'IDX_USED_IN_PRODUCT_LISTING',
    ['entity_type_id','used_in_product_listing']
);

// Add frontent input renderer
$installer->getConnection()->addColumn(
    $installer->getTable('eav/attribute'),
    'frontend_input_renderer',
    'varchar(255) DEFAULT NULL AFTER `frontend_input`'
);

// Modify Groups and Attributes for Category
$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// update General Group
$installer->updateAttributeGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'attribute_group_name',
    'General Information'
);
$installer->updateAttributeGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'sort_order',
    '10'
);

// Add groups
$groups = [
    'display'   => [
        'name'  => 'Display Settings',
        'sort'  => 20,
        'id'    => null
    ],
    'design'    => [
        'name'  => 'Custom Design',
        'sort'  => 30,
        'id'    => null
    ]
];

foreach ($groups as $k => $groupProp) {
    $installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupProp['name'], $groupProp['sort']);
    $groups[$k]['id'] = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupProp['name']);
}

// Add Catalog Default Sort Attributes
$installer->addAttribute($entityTypeId, 'available_sort_by', [
    'input'         => 'multiselect',
    'type'          => 'text',
    'label'         => 'Available Product Listing Sort By',
    'source'        => 'catalog/category_attribute_source_sortby',
    'backend'       => 'catalog/category_attribute_backend_sortby',
    'required'      => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => 1,
    'input_renderer' => 'adminhtml/catalog_category_helper_sortby_available',
]);
$installer->addAttribute($entityTypeId, 'default_sort_by', [
    'input'         => 'select',
    'label'         => 'Default Product Listing Sort By',
    'source'        => 'catalog/category_attribute_source_sortby',
    'backend'       => 'catalog/category_attribute_backend_sortby',
    'required'      => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => 1,
    'input_renderer' => 'adminhtml/catalog_category_helper_sortby_default',
]);

// update attributes group and sort
$attributes = [
    'custom_design'         => [
        'group' => 'design',
        'sort'  => 10
    ],
    'custom_design_apply'   => [
        'group' => 'design',
        'sort'  => 20
    ],
    'custom_design_from'    => [
        'group' => 'design',
        'sort'  => 30
    ],
    'custom_design_to'      => [
        'group' => 'design',
        'sort'  => 40
    ],
    'page_layout'           => [
        'group' => 'design',
        'sort'  => 50
    ],
    'custom_layout_update'  => [
        'group' => 'design',
        'sort'  => 60
    ],
    'display_mode'          => [
        'group' => 'display',
        'sort'  => 10
    ],
    'landing_page'          => [
        'group' => 'display',
        'sort'  => 20
    ],
    'is_anchor'             => [
        'group' => 'display',
        'sort'  => 30
    ],
    'available_sort_by'     => [
        'group' => 'display',
        'sort'  => 40
    ],
    'default_sort_by'       => [
        'group' => 'display',
        'sort'  => 50
    ],
];

foreach ($attributes as $attributeCode => $attributeProp) {
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groups[$attributeProp['group']]['id'],
        $attributeCode,
        $attributeProp['sort']
    );
}

$installer->endSetup();
