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

$defaultValue = 'container2';

$installer->addAttribute('catalog_product', 'options_container', [
    'group'             => 'Design',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Display Product Options In',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'catalog/entity_product_attribute_design_options_container',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => $defaultValue,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'visible_in_advanced_search' => false,
    'unique'            => false,
]);

$newAttributeId = $installer->getAttributeId('catalog_product', 'options_container');

$installer->run("
INSERT INTO {$this->getTable('catalog_product_entity_varchar')}
    (entity_id, entity_type_id, attribute_id, value)
    SELECT entity_id, entity_type_id, {$newAttributeId}, '{$defaultValue}'
    FROM {$this->getTable('catalog_product_entity')}
");

$installer->endSetup();
