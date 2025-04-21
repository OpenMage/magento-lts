<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$tableOrder         = $this->getTable('sales_order');
$tableOrderItem     = $this->getTable('sales_flat_order_item');

$select = $installer->getConnection()->select()
    ->from($tableOrderItem, [
        'total_qty_ordered'   => 'SUM(qty_ordered)',
        'entity_id'           => 'order_id'])
    ->group(['order_id']);

$installer->run('CREATE TEMPORARY TABLE `tmp_order_items` ' . $select->assemble());

$select->reset()
    ->join('tmp_order_items', 'tmp_order_items.entity_id = order.entity_id', ['total_qty_ordered', 'entity_id']);
$sqlQuery = $select->crossUpdateFromSelect(['order' => $tableOrder]);
$installer->getConnection()->query($sqlQuery);

$installer->run('DROP TEMPORARY TABLE `tmp_order_items`');
