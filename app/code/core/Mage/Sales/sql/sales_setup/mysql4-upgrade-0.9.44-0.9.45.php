<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$orderEntityType = $installer->getEntityType('order');
$orderEntityTypeId = $orderEntityType['entity_type_id'];

$attributes = [
    $installer->getAttribute($orderEntityTypeId, 'store_to_base_rate'),
    $installer->getAttribute($orderEntityTypeId, 'store_to_order_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_global_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_order_rate'),
];

foreach ($attributes as $attribute) {
    $installer->getConnection()->addColumn($this->getTable('sales_order'), $attribute['attribute_code'], 'decimal(12,4) NULL');
}

try {
    $installer->getConnection()->beginTransaction();

    foreach ($attributes as $attribute) {
        $installer->run("
            UPDATE {$this->getTable('sales_order')} AS o, {$this->getTable('sales_order')}_{$attribute['backend_type']} AS od
            SET o.{$attribute['attribute_code']} = od.value
            WHERE od.entity_id = o.entity_id
                AND od.attribute_id = {$attribute['attribute_id']}
                AND od.entity_type_id = {$orderEntityTypeId}
        ");

        $installer->run("
            DELETE FROM {$this->getTable('sales_order')}_{$attribute['backend_type']}
            WHERE attribute_id = {$attribute['attribute_id']}
                AND entity_type_id = {$orderEntityTypeId}
        ");
    }

    foreach ($attributes as $attribute) {
        $installer->updateAttribute($orderEntityTypeId, $attribute['attribute_code'], ['backend_type' => 'static']);
    }

    $installer->getConnection()->commit();
} catch (Exception $e) {
    $installer->getConnection()->rollBack();
    foreach ($attributes as $attribute) {
        $installer->getConnection()->dropColumn($this->getTable('sales_order'), $attribute['attribute_code']);
    }

    throw $e;
}
