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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$groupPriceAttrId = $installer->getAttribute('catalog_product', 'group_price', 'attribute_id');
$priceAttrId = $installer->getAttribute('catalog_product', 'price', 'attribute_id');
$connection = $installer->getConnection();

// update sort_order of Group Price attribute to be after Price
$select = $connection->select()
    ->join(
        ['t2' => $installer->getTable('eav/entity_attribute')],
        't1.attribute_group_id = t2.attribute_group_id',
        ['sort_order' => new Zend_Db_Expr('t2.sort_order + 1')]
    )->where('t1.attribute_id = ?', $groupPriceAttrId)
    ->where('t2.attribute_id = ?', $priceAttrId);
$query = $select->crossUpdateFromSelect(['t1' => $installer->getTable('eav/entity_attribute')]);
$connection->query($query);

// update sort_order of all other attributes to be after Group Price
$select = $connection->select()
    ->join(
        ['t2' => $installer->getTable('eav/entity_attribute')],
        't1.attribute_group_id = t2.attribute_group_id',
        ['sort_order' => new Zend_Db_Expr('t1.sort_order + 1')]
    )->where('t1.attribute_id != ?', $groupPriceAttrId)
    ->where('t1.sort_order >= t2.sort_order')
    ->where('t2.attribute_id = ?', $groupPriceAttrId);
$query = $select->crossUpdateFromSelect(['t1' => $installer->getTable('eav/entity_attribute')]);
$connection->query($query);
