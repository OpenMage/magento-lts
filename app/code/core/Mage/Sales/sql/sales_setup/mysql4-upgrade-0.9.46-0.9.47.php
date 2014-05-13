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

$attributes = array(
    $installer->getAttribute($orderEntityTypeId, 'is_virtual'),
    $installer->getAttribute($orderEntityTypeId, 'shipping_description')
);

$installer->getConnection()->addColumn($this->getTable('sales_order'), $attributes[0]['attribute_code'], "tinyint(1) UNSIGNED NOT NULL DEFAULT 0");
$installer->getConnection()->addColumn($this->getTable('sales_order'), $attributes[1]['attribute_code'], "varchar(255) NOT NULL DEFAULT ''");


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
        $installer->updateAttribute($orderEntityTypeId, $attribute['attribute_code'], array('backend_type' => 'static'));
    }

    $installer->getConnection()->commit();

} catch (Exception $e) {
    $installer->getConnection()->rollback();
    foreach ($attributes as $attribute) {
        $installer->getConnection()->dropColumn($this->getTable('sales_order'), $attribute['attribute_code']);
    }
    throw $e;
}

$installer->run("
    CREATE TABLE `{$installer->getTable('sales/shipping_aggregated')}`
    (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `shipping_description`      varchar(255) NOT NULL default '',
        `orders_count`              int(11) NOT NULL DEFAULT '0',
        `total_shipping`            decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`, `order_status`, `shipping_description`),
        KEY `IDX_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `{$installer->getTable('sales/shipping_aggregated_order')}`
    (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `shipping_description`      varchar(255) NOT NULL default '',
        `orders_count`              int(11) NOT NULL DEFAULT '0',
        `total_shipping`            decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`, `order_status`, `shipping_description`),
        KEY `IDX_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'SALES_SHIPPING_AGGREGATED_STORE',
    $installer->getTable('sales/shipping_aggregated'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'SET NULL'
);

$installer->getConnection()->addConstraint(
    'SALES_SHIPPING_AGGREGATED_ORDER_STORE',
    $installer->getTable('sales/shipping_aggregated_order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'SET NULL'
);

$this->endSetup();
