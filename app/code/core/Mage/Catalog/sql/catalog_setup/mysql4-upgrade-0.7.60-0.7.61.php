<?php

/**
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$tableCatalogProduct = $this->getTable('catalog/product');
$types = ['datetime', 'decimal', 'int', 'text', 'varchar'];
foreach ($types as $type) {
    $tableName = $tableCatalogProduct . '_' . $type;

    $select = $installer->getConnection()->select()
        ->from($tableName, [
            'entity_id'         => 'entity_id',
            'attribute_id'      => 'attribute_id',
            'store_id'          => 'store_id',
            'rows_count'        => 'COUNT(*)'])
        ->group(['entity_id', 'attribute_id', 'store_id'])
        ->having('rows_count > 1');
    $query = $installer->getConnection()->query($select);

    while ($row = $query->fetch()) {
        $sql = 'DELETE FROM `' . $tableName . '`'
            . ' WHERE entity_id=? AND attribute_id=? AND store_id=?'
            . ' LIMIT ' . ($row['rows_count'] - 1);
        $installer->getConnection()->query($sql, [
            $row['entity_id'],
            $row['attribute_id'],
            $row['store_id'],
        ]);
    }

    $installer->getConnection()->addKey($tableName, 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id', 'store_id'], 'unique');
}

// exception for gallery table

$tableName = $tableCatalogProduct . '_gallery';

$select = $installer->getConnection()->select()
    ->from($tableName, [
        'entity_type_id'    => 'entity_type_id',
        'entity_id'         => 'entity_id',
        'attribute_id'      => 'attribute_id',
        'store_id'          => 'store_id',
        'rows_count'        => 'COUNT(*)'])
    ->group(['entity_id', 'attribute_id', 'store_id'])
    ->having('rows_count > 1');
$query = $installer->getConnection()->query($select);

while ($row = $query->fetch()) {
    $sql = 'DELETE FROM `' . $tableName . '`'
        . ' WHERE entity_type_id=? AND entity_id=? AND attribute_id=? AND store_id=?'
        . ' LIMIT ' . ($row['rows_count'] - 1);
    $installer->getConnection()->query($sql, [
        $row['entity_type_id'],
        $row['entity_id'],
        $row['attribute_id'],
        $row['store_id'],
    ]);
}

$installer->getConnection()->addKey("{$tableCatalogProduct}_gallery", 'IDX_BASE', ['entity_type_id','entity_id','attribute_id','store_id'], 'unique');
