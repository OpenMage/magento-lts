<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/*
 * @deprecated since 1.4 Sales no more eav, moving attributes from eav to flat table,
 *             already done in sales upgrade
 *

$orderEntityType = $installer->getEntityType('order');
$orderEntityTypeId = $orderEntityType['entity_type_id'];


$attribute = $installer->getAttribute($orderEntityTypeId, 'coupon_code');

$installer->getConnection()->addColumn($this->getTable('sales/order'), $attribute['attribute_code'], "varchar(255) NULL DEFAULT NULL");

try {
    $installer->getConnection()->beginTransaction();

    $installer->run("
        UPDATE {$this->getTable('sales/order')} AS o, {$this->getTable('sales/order')}_varchar AS od
        SET o.{$attribute['attribute_code']} = od.value
        WHERE od.entity_id = o.entity_id
            AND od.attribute_id = {$attribute['attribute_id']}
            AND od.entity_type_id = {$orderEntityTypeId}
    ");

    $installer->run("
        DELETE FROM {$this->getTable('sales/order')}_{$attribute['backend_type']}
        WHERE attribute_id = {$attribute['attribute_id']}
            AND entity_type_id = {$orderEntityTypeId}
    ");

    $installer->updateAttribute($orderEntityTypeId, $attribute['attribute_code'], array('backend_type' => 'static'));

    $installer->getConnection()->commit();
} catch (Exception $e) {
    $installer->getConnection()->rollback();
    $installer->getConnection()->dropColumn($this->getTable('sales/order'), $attribute['attribute_code']);
    throw $e;
}
*/

$installer->run("
    CREATE TABLE `{$installer->getTable('salesrule/coupon_aggregated')}` (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `coupon_code`               varchar(50) NOT NULL default '',
        `coupon_uses`               int(11) NOT NULL DEFAULT '0',
        `subtotal_amount`           decimal(12,4) NOT NULL DEFAULT '0',
        `discount_amount`           decimal(12,4) NOT NULL DEFAULT '0',
        `total_amount`              decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_COUPON_AGGREGATED_PSOC` (`period`,`store_id`, `order_status`, `coupon_code`),
        KEY `IDX_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `{$installer->getTable('salesrule/coupon_aggregated_order')}` (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `coupon_code`               varchar(50) NOT NULL default '',
        `coupon_uses`               int(11) NOT NULL DEFAULT '0',
        `subtotal_amount`           decimal(12,4) NOT NULL DEFAULT '0',
        `discount_amount`           decimal(12,4) NOT NULL DEFAULT '0',
        `total_amount`              decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_COUPON_AGGREGATED_ORDER_PSOC` (`period`,`store_id`, `order_status`,`coupon_code`),
        KEY `IDX_STORE_ID` (`store_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_SALESTRULE_COUPON_AGGREGATED_ORDER_STORE',
    $this->getTable('salesrule/coupon_aggregated_order'),
    'store_id',
    $this->getTable('core_store'),
    'store_id',
);

$installer->getConnection()->addConstraint(
    'FK_SALESTRULE_COUPON_AGGREGATED_STORE',
    $this->getTable('salesrule/coupon_aggregated'),
    'store_id',
    $this->getTable('core_store'),
    'store_id',
);

$installer->endSetup();
