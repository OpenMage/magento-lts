<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

foreach (['daily', 'monthly', 'yearly'] as $frequency) {
    $tableName = $installer->getTable('sales/bestsellers_aggregated_' . $frequency);

    $installer->run("
    CREATE TABLE `{$tableName}` (
      `id` int(11) unsigned NOT NULL auto_increment,
      `period` date NOT NULL DEFAULT '0000-00-00',
      `store_id` smallint(5) unsigned NULL DEFAULT NULL,
      `product_id` int(10) unsigned NULL DEFAULT NULL,
      `product_name` varchar(255) NOT NULL DEFAULT '',
      `product_price` decimal(12,4) NOT NULL DEFAULT '0',
      `qty_ordered` decimal(12,4) NOT NULL DEFAULT '0.0000',
      `rating_pos` smallint(5) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY `UNQ_PERIOD_STORE_PRODUCT` (`period`, `store_id`, `product_id`),
      KEY `IDX_STORE_ID` (`store_id`),
      KEY `IDX_PRODUCT_ID` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    $installer->getConnection()->addConstraint(
        'PRODUCT_ORDERED_AGGREGATED_' . strtoupper($frequency) . '_STORE_ID',
        $tableName,
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        'SET NULL',
    );

    $installer->getConnection()->addConstraint(
        'PRODUCT_ORDERED_AGGREGATED_' . strtoupper($frequency) . '_PRODUCT_ID',
        $tableName,
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        'SET NULL',
    );
}
