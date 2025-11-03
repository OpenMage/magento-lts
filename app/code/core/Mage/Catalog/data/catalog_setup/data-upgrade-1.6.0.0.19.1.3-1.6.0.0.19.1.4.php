<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$catalogProductEntityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getEntityTypeId();

$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
    ->addFieldToFilter('frontend_input', 'multiselect')
    ->addFieldToFilter('entity_type_id', $catalogProductEntityTypeId)
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
            ->from($attributeTableOld, ['entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value'])
            ->where('entity_type_id = ?', $entityTypeId)
            ->where('attribute_id = ?', $attributeId);

        $query = $select->insertFromSelect(
            $attributeTableNew,
            ['entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value'],
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

Mage::getModel('index/indexer')
    ->getProcessByCode(Mage_Catalog_Helper_Product_Flat::CATALOG_FLAT_PROCESS_CODE)
    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

$installer->endSetup();
