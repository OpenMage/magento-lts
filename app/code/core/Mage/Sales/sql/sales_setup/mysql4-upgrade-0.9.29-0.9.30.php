<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$tableOrder       = $this->getTable('sales_order');
$tableOrderEntity = $this->getTable('sales_order_entity');

$cleanTables = [
    $tableOrder . '_datetime',
    $tableOrder . '_decimal',
    $tableOrder . '_int',
    $tableOrder . '_text',
    $tableOrder . '_varchar',
    $tableOrderEntity . '_decimal',
    $tableOrderEntity . '_datetime',
    $tableOrderEntity . '_int',
    $tableOrderEntity . '_text',
    $tableOrderEntity . '_varchar',
];
foreach ($cleanTables as $tableName) {
    $select = $installer->getConnection()->select()
        ->from($tableName, [
            'entity_id'         => 'entity_id',
            'attribute_id'      => 'attribute_id',
            'entity_type_id'    => 'entity_type_id',
            'rows_count'        => 'COUNT(*)'])
        ->group(['entity_id', 'attribute_id', 'entity_type_id'])
        ->having('rows_count > 1');
    $query = $installer->getConnection()->query($select);

    while ($row = $query->fetch()) {
        $sql = 'DELETE FROM `' . $tableName . '`'
            . ' WHERE entity_id=? AND attribute_id=? AND entity_type_id=?'
            . ' LIMIT ' . ($row['rows_count'] - 1);
        $installer->getConnection()->query($sql, [
            $row['entity_id'],
            $row['attribute_id'],
            $row['entity_type_id'],
        ]);
    }
}

$installer->getConnection()->addKey("{$tableOrder}_datetime", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrder}_decimal", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrder}_int", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrder}_text", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrder}_varchar", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');

$installer->getConnection()->addKey("{$tableOrderEntity}_datetime", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_decimal", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_int", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_text", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_varchar", 'UNQ_ENTITY_ATTRIBUTE_TYPE', ['entity_id', 'attribute_id', 'entity_type_id'], 'unique');

$installer->getConnection()->addKey($tableOrderEntity, 'IDX_SALES_ORDER_ENTITY_PARENT', 'parent_id');
