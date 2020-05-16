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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

//$installer->getConnection()->addKey($installer->getTable('sales/order_item'), 'IDX_PRODUCT_ID', 'product_id');

foreach (array('daily', 'monthly', 'yearly') as $frequency) {
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
        'SET NULL'
    );

    $installer->getConnection()->addConstraint(
        'PRODUCT_ORDERED_AGGREGATED_' . strtoupper($frequency) . '_PRODUCT_ID',
        $tableName,
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        'SET NULL'
    );
}
