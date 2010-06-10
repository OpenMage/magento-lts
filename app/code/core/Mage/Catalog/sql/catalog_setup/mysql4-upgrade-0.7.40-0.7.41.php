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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$defaultValue = 'container2';

$installer->addAttribute('catalog_product', 'options_container', array(
    'group'             => 'Design',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Display product options in',
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
    )
);

$newAttributeId = $installer->getAttributeId('catalog_product', 'options_container');

$installer->run("
INSERT INTO {$this->getTable('catalog_product_entity_varchar')}
    (entity_id, entity_type_id, attribute_id, value)
    SELECT entity_id, entity_type_id, {$newAttributeId}, '{$defaultValue}' FROM {$this->getTable('catalog_product_entity')}
");

$installer->endSetup();
