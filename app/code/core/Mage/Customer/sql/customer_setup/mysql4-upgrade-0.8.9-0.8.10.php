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

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$tableCustomer =
$tableCustomerAddress = $this->getTable('customer/address_entity');

$types = ['datetime', 'decimal', 'int', 'text', 'varchar'];

$tables = [$this->getTable('customer/entity'),
    $this->getTable('customer/address_entity')];

foreach ($tables as $table) {
    foreach ($types as $type) {
        $tableName = $table . '_' . $type;

        $select = $installer->getConnection()->select()
            ->from($tableName, [
                'entity_id'         => 'entity_id',
                'attribute_id'      => 'attribute_id',
                'rows_count'        => 'COUNT(*)'])
            ->group(['entity_id', 'attribute_id'])
            ->having('rows_count > 1');
        $query = $installer->getConnection()->query($select);

        while ($row = $query->fetch()) {
            $sql = 'DELETE FROM `' . $tableName . '`'
                . ' WHERE entity_id=? AND attribute_id=?'
                . ' LIMIT ' . ($row['rows_count'] - 1);
            $installer->getConnection()->query($sql, [
                $row['entity_id'],
                $row['attribute_id']
            ]);
        }

        $installer->getConnection()->addKey($tableName, 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id'], 'unique');
    }
}
