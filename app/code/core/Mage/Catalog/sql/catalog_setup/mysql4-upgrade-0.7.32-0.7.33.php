<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$attributeId    = $installer->getAttributeId('catalog_category', 'is_active');
$entityTypeId   = $installer->getEntityTypeId('catalog_category');
$installer->updateAttribute('catalog_category', 'is_active', 'backend_type', 'int');

$categoryIntTable = $installer->getTable('catalog_category_entity_int');
$categoryTable = $installer->getTable('catalog_category_entity');
$attributesCount = $installer->getConnection()->fetchOne("SELECT count(*) FROM `{$categoryIntTable}` WHERE attribute_id='{$attributeId}'");
$valueId = $installer->getConnection()->fetchOne("SELECT MAX(value_id) FROM `{$categoryIntTable}`");
if (!$attributesCount) {
    $data = $installer->getConnection()->fetchAll("SELECT {$entityTypeId} as entity_type_id, {$attributeId} as attribute_id, 0 as store_id, entity_id, is_active as value FROM `{$categoryTable}`");
    foreach ($data as $row) {
        $row['value_id'] = ++$valueId;
        $data = $installer->getConnection()->insert($categoryIntTable, $row);
    }
}
