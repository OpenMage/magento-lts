<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$eavConfig = Mage::getSingleton('eav/config');
$customerEntityTypeId = $eavConfig->getEntityType('customer')->getEntityTypeId();
$customerAddressEntityTypeId = $eavConfig->getEntityType('customer_address')->getEntityTypeId();

$entityTypeIds = [$customerAddressEntityTypeId, $customerEntityTypeId];

$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
    ->addFieldToFilter('frontend_input', 'multiselect')
    ->addFieldToFilter('entity_type_id', ['in' => $entityTypeIds])
    ->getItems();

foreach ($attributes as $attribute) {
    $entityTypeId = $attribute->getEntityTypeId();
    $attributeId = $attribute->getId();
    $attributeTableOld = $installer->getAttributeTable($entityTypeId, $attributeId);

    $installer->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'text');

    $attributeTableNew = $installer->getAttributeTable($entityTypeId, $attributeId);

    if ($attributeTableOld != $attributeTableNew) {
        $connection->disableTableKeys($attributeTableOld)
            ->disableTableKeys($attributeTableNew);

        $select = $connection->select()
            ->from($attributeTableOld, ['entity_type_id', 'attribute_id', 'entity_id', 'value'])
            ->where('entity_type_id = ?', $entityTypeId)
            ->where('attribute_id = ?', $attributeId);

        $query = $select->insertFromSelect(
            $attributeTableNew,
            ['entity_type_id', 'attribute_id', 'entity_id', 'value'],
        );

        $connection->query($query);

        $connection->delete(
            $attributeTableOld,
            $connection->quoteInto('entity_type_id = ?', $entityTypeId)
            . $connection->quoteInto(' AND attribute_id = ?', $attributeId),
        );

        $connection->enableTableKeys($attributeTableOld)
            ->enableTableKeys($attributeTableNew);
    }
}

$installer->endSetup();
