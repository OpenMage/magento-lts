<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;

$this->startSetup();

$orderEntityType = $installer->getEntityType('order');
$orderEntityTypeId = $orderEntityType['entity_type_id'];

$attributesToModify = array(
    $installer->getAttribute($orderEntityTypeId, 'store_to_base_rate'),
    $installer->getAttribute($orderEntityTypeId, 'store_to_order_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_global_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_order_rate')
);

$attributesToMove = array(
    $installer->getAttribute($orderEntityTypeId, 'status'),
    $installer->getAttribute($orderEntityTypeId, 'state')
);

// modify existing attributes in sales/order table
foreach ($attributesToModify as $attribute) {
    $installer->getConnection()->modifyColumn($this->getTable('sales_order'), $attribute['attribute_code'], "decimal(12,4) NOT NULL DEFAULT '0'");
}


foreach ($attributesToMove as $attribute) {
    $installer->getConnection()->addColumn($this->getTable('sales_order'), $attribute['attribute_code'], 'varchar(50) NULL');
}

foreach ($attributesToMove as $attribute) {
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

foreach ($attributesToMove as $attribute) {
    $installer->updateAttribute($orderEntityTypeId, $attribute['attribute_code'], array('backend_type' => 'static'));
}

$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('sales/order_aggregated_created')}`
    (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `orders_count`              int(11) NOT NULL DEFAULT '0',
        `total_qty_ordered`         decimal(12,4) NOT NULL DEFAULT '0',
        `base_profit_amount`        decimal(12,4) NOT NULL DEFAULT '0',
        `base_subtotal_amount`      decimal(12,4) NOT NULL DEFAULT '0',
        `base_tax_amount`           decimal(12,4) NOT NULL DEFAULT '0',
        `base_shipping_amount`      decimal(12,4) NOT NULL DEFAULT '0',
        `base_discount_amount`      decimal(12,4) NOT NULL DEFAULT '0',
        `base_grand_total_amount`   decimal(12,4) NOT NULL DEFAULT '0',
        `base_invoiced_amount`      decimal(12,4) NOT NULL DEFAULT '0',
        `base_refunded_amount`      decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`, `order_status`),
        KEY `IDX_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'SALES_ORDER_AGGREGATED_CREATED',
    $installer->getTable('sales/order_aggregated_created'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'SET NULL'
);


$this->endSetup();
