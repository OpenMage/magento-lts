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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_category');
$designApplyAttributeId = $installer->getAttributeId($entityTypeId, 'custom_design_apply');
$designAttributeId = $installer->getAttributeId($entityTypeId, 'custom_design');
$catalogCategoryEntityIntTable = $installer->getAttributeTable($entityTypeId, $designApplyAttributeId);
$eavAttributeTable = $installer->getTable('eav/attribute');

$installer->addAttribute($entityTypeId, 'custom_use_parent_settings', array(
    'type'          => 'int',
    'input'         => 'select',
    'label'         => 'Use Parent Category Settings',
    'source'        => 'eav/entity_attribute_source_boolean',
    'required'      => 0,
    'group'         => 'Custom Design',
    'sort_order'    => '5',
    'global'        => 0
));
$installer->addAttribute($entityTypeId, 'custom_apply_to_products', array(
    'type'          => 'int',
    'input'         => 'select',
    'label'         => 'Apply To Products',
    'source'        => 'eav/entity_attribute_source_boolean',
    'required'      => 0,
    'group'         => 'Custom Design',
    'sort_order'    => '6',
    'global'        => 0
));
$useParentSettingsAttributeId = $installer->getAttributeId($entityTypeId, 'custom_use_parent_settings');
$applyToProductsAttributeId = $installer->getAttributeId($entityTypeId, 'custom_apply_to_products');



$attributeIdExpr = new Zend_Db_Expr(
    'IF (e_a.attribute_id = e.attribute_id,'.
    $useParentSettingsAttributeId.', '.
    $applyToProductsAttributeId .')');
$productValueExpr = new Zend_Db_Expr('IF (e.value IN (1,3), 1, 0)');
$valueExpr = new Zend_Db_Expr('IF (e_a.attribute_id = e.attribute_id, 1, '. $productValueExpr .')');
$select = $installer->getConnection()->select()
    ->from(
        array('e' => $catalogCategoryEntityIntTable),
        array(
            'entity_type_id',
            'attribute_id' => $attributeIdExpr,
            'store_id',
            'entity_id',
            'value' => $valueExpr
        )
    )
    ->joinCross(
        array('e_a' => $eavAttributeTable),
        array())
    ->where('e_a.attribute_id IN (?)', array($designApplyAttributeId, $designAttributeId))
    ->where('e.attribute_id = ?', $designApplyAttributeId)
    ->order(array('e.entity_id', 'attribute_id'));

$insertArray = array(
    'entity_type_id',
    'attribute_id',
    'store_id',
    'entity_id',
    'value'
);

$sqlQuery = $select->insertFromSelect($catalogCategoryEntityIntTable, $insertArray, false);
$installer->getConnection()->query($sqlQuery);

$installer->endSetup();
