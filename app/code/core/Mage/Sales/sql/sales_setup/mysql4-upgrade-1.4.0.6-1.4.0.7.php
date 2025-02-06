<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$orderHistoryTable = $installer->getTable('sales_flat_order_status_history');
$installer->getConnection()->addColumn(
    $orderHistoryTable,
    'is_visible_on_front',
    "tinyint(1) UNSIGNED NOT NULL default '0' after `is_customer_notified`",
);
$installer->run("UPDATE {$orderHistoryTable} SET
    is_visible_on_front = (is_customer_notified = 1 AND comment IS NOT NULL AND comment <> '');");
