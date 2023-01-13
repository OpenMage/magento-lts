<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$orderEntityType = $installer->getEntityType('order');
$orderEntityTypeId = $orderEntityType['entity_type_id'];

$attributes = [
    $installer->getAttribute($orderEntityTypeId, 'store_to_base_rate'),
    $installer->getAttribute($orderEntityTypeId, 'store_to_order_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_global_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_order_rate')
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
