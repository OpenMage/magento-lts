<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Sales_Model_Resource_Setup $installer */

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
