<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$this->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('sales/invoiced_aggregated')}`
    (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `orders_count`              int(11) NOT NULL DEFAULT '0',
        `orders_invoiced`           decimal(12,4) NOT NULL DEFAULT '0',
        `invoiced`                  decimal(12,4) NOT NULL DEFAULT '0',
        `invoiced_captured`         decimal(12,4) NOT NULL DEFAULT '0',
        `invoiced_not_captured`     decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`, `order_status`),
        KEY `IDX_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `{$installer->getTable('sales/invoiced_aggregated_order')}`
    (
        `id`                        int(11) unsigned NOT NULL auto_increment,
        `period`                    date NOT NULL DEFAULT '0000-00-00',
        `store_id`                  smallint(5) unsigned NULL DEFAULT NULL,
        `order_status`              varchar(50) NOT NULL default '',
        `orders_count`              int(11) NOT NULL DEFAULT '0',
        `orders_invoiced`           decimal(12,4) NOT NULL DEFAULT '0',
        `invoiced`                  decimal(12,4) NOT NULL DEFAULT '0',
        `invoiced_captured`         decimal(12,4) NOT NULL DEFAULT '0',
        `invoiced_not_captured`     decimal(12,4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_ORDER_STATUS` (`period`,`store_id`, `order_status`),
        KEY `IDX_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'SALES_INVOICED_AGGREGATED_STORE',
    $installer->getTable('sales/invoiced_aggregated'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'SET NULL',
);

$installer->getConnection()->addConstraint(
    'SALES_INVOICED_AGGREGATED_ORDER_STORE',
    $installer->getTable('sales/invoiced_aggregated_order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'SET NULL',
);

$this->endSetup();
