<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Customer_Model_Entity_Setup $installer */

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
