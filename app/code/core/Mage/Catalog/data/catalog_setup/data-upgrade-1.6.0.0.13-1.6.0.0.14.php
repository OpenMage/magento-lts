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
$connection = $installer->getConnection();

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId(Mage_Catalog_Model_Category::ENTITY);
$attributeId = $installer->getAttributeId($entityTypeId, 'filter_price_range');
$attributeTableOld = $installer->getAttributeTable($entityTypeId, $attributeId);

$installer->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'decimal');

$attributeTableNew = $installer->getAttributeTable($entityTypeId, $attributeId);

if ($attributeTableOld != $attributeTableNew) {
    $connection->disableTableKeys($attributeTableOld)
        ->disableTableKeys($attributeTableNew);

    $select = $connection->select()
        ->from($attributeTableOld, ['entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value'])
        ->where('entity_type_id = ?', $entityTypeId)
        ->where('attribute_id = ?', $attributeId);

    $query = $select->insertFromSelect(
        $attributeTableNew,
        ['entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value']
    );

    $connection->query($query);

    $connection->delete(
        $attributeTableOld,
        $connection->quoteInto('entity_type_id = ?', $entityTypeId)
            . $connection->quoteInto(' AND attribute_id = ?', $attributeId)
    );

    $connection->enableTableKeys($attributeTableOld)
        ->enableTableKeys($attributeTableNew);
}

Mage::getModel('index/indexer')
    ->getProcessByCode(Mage_Catalog_Helper_Category_Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

$installer->endSetup();
