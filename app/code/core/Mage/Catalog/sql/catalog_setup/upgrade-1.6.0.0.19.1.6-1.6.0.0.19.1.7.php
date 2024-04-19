<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Catalog_Model_Resource_Setup $installer */

$installer->startSetup();
$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'meta_robots', array(
    'type'          => 'int',
    'label'         => 'Robots',
    'input'         => 'select',
    'source'        => 'catalog/category_attribute_source_robots',
    'required'      => false,
    'sort_order'    => 9,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'group'         => 'General Information',
    'user_defined'  => false,
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'meta_robots', array(
    'type'          => 'int',
    'label'         => 'Robots',
    'input'         => 'select',
    'source'        => 'catalog/product_attribute_source_robots',
    'sort_order'    => 9,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'group'         => 'Meta Information',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'visible_in_advanced_search' => false,
));

$installer->endSetup();
