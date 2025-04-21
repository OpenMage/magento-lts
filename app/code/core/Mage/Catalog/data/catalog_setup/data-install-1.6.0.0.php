<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

// Create Root Catalog Node
Mage::getModel('catalog/category')
    ->load(1)
    ->setId(1)
    ->setStoreId(0)
    ->setPath(1)
    ->setLevel(0)
    ->setPosition(0)
    ->setChildrenCount(0)
    ->setName('Root Catalog')
    ->setInitialSetupFlag(true)
    ->save();

/** @var Mage_Catalog_Model_Category $category */
$category = Mage::getModel('catalog/category');

$category->setStoreId(0)
    ->setName('Default Category')
    ->setDisplayMode('PRODUCTS')
    ->setAttributeSetId($category->getDefaultAttributeSetId())
    ->setIsActive(1)
    ->setPath('1')
    ->setInitialSetupFlag(true)
    ->save();

$installer->setConfigData(Mage_Catalog_Helper_Category::XML_PATH_CATEGORY_ROOT_ID, $category->getId());

$installer->addAttributeGroup(Mage_Catalog_Model_Product::ENTITY, 'Default', 'Design', 6);

$entityTypeId     = $installer->getEntityTypeId(Mage_Catalog_Model_Category::ENTITY);
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// update General Group
//$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name', 'General Information');
$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'sort_order', '10');

$groups = [
    'display'   => [
        'name'  => 'Display Settings',
        'sort'  => 20,
        'id'    => null,
    ],
    'design'    => [
        'name'  => 'Custom Design',
        'sort'  => 30,
        'id'    => null,
    ],
];

foreach ($groups as $k => $groupProp) {
    $installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupProp['name'], $groupProp['sort']);
    $groups[$k]['id'] = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupProp['name']);
}

// update attributes group and sort
$attributes = [
    'custom_design'         => [
        'group' => 'design',
        'sort'  => 10,
    ],
    'custom_design_from'    => [
        'group' => 'design',
        'sort'  => 30,
    ],
    'custom_design_to'      => [
        'group' => 'design',
        'sort'  => 40,
    ],
    'page_layout'           => [
        'group' => 'design',
        'sort'  => 50,
    ],
    'custom_layout_update'  => [
        'group' => 'design',
        'sort'  => 60,
    ],
    'display_mode'          => [
        'group' => 'display',
        'sort'  => 10,
    ],
    'landing_page'          => [
        'group' => 'display',
        'sort'  => 20,
    ],
    'is_anchor'             => [
        'group' => 'display',
        'sort'  => 30,
    ],
    'available_sort_by'     => [
        'group' => 'display',
        'sort'  => 40,
    ],
    'default_sort_by'       => [
        'group' => 'display',
        'sort'  => 50,
    ],
];

foreach ($attributes as $attributeCode => $attributeProp) {
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groups[$attributeProp['group']]['id'],
        $attributeCode,
        $attributeProp['sort'],
    );
}

/**
 * Install product link types
 */
$data = [
    [
        'link_type_id'  => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        'code'          => 'relation',
    ],
    [
        'link_type_id'  => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
        'code'  => 'super',
    ],
    [
        'link_type_id'  => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
        'code'  => 'up_sell',
    ],
    [
        'link_type_id'  => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
        'code'  => 'cross_sell',
    ],
];

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('catalog/product_link_type'), $bind);
}

/**
 * install product link attributes
 */
$data = [
    [
        'link_type_id'                  => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int',
    ],
    [
        'link_type_id'                  => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int',
    ],
    [
        'link_type_id'                  => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
        'product_link_attribute_code'   => 'qty',
        'data_type'                     => 'decimal',
    ],
    [
        'link_type_id'                  => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int',
    ],
    [
        'link_type_id'                  => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int',
    ],
];

$installer->getConnection()->insertMultiple($installer->getTable('catalog/product_link_attribute'), $data);

/**
 * Remove Catalog specified attribute options (columns) from eav/attribute table
 *
 */
$describe = $installer->getConnection()->describeTable($installer->getTable('catalog/eav_attribute'));
foreach ($describe as $columnData) {
    if ($columnData['COLUMN_NAME'] == 'attribute_id') {
        continue;
    }
    $installer->getConnection()->dropColumn($installer->getTable('eav/attribute'), $columnData['COLUMN_NAME']);
}
