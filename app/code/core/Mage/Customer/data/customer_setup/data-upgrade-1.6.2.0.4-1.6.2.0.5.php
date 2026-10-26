<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;
$installer->startSetup();

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
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
