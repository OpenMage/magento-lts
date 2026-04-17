<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $this */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$subSelect = $connection->select()
    ->from(
        ['citem' => $installer->getTable('sales/creditmemo_item')],
        [
            'amount_refunded'        => 'SUM(citem.row_total)',
            'base_amount_refunded'   => 'SUM(citem.base_row_total)',
            'base_tax_refunded'      => 'SUM(citem.base_tax_amount)',
            'discount_refunded'      => 'SUM(citem.discount_amount)',
            'base_discount_refunded' => 'SUM(citem.base_discount_amount)',
        ],
    )
    ->joinLeft(
        ['c' => $installer->getTable('sales/creditmemo')],
        'c.entity_id = citem.parent_id',
        [],
    )
    ->joinLeft(
        ['o' => $installer->getTable('sales/order')],
        'o.entity_id = c.order_id',
        [],
    )
    ->joinLeft(
        ['oitem' => $installer->getTable('sales/order_item')],
        'oitem.order_id = o.entity_id AND oitem.product_id=citem.product_id',
        ['item_id'],
    )
    ->group('oitem.item_id');

$select = $connection->select()
    ->from(
        ['selected' => $subSelect],
        [
            'amount_refunded'        => 'amount_refunded',
            'base_amount_refunded'   => 'base_amount_refunded',
            'base_tax_refunded'      => 'base_tax_refunded',
            'discount_refunded'      => 'discount_refunded',
            'base_discount_refunded' => 'base_discount_refunded',
        ],
    )
    ->where('main.item_id = selected.item_id');

$updateQuery = $connection->updateFromSelect(
    $select,
    ['main' => $installer->getTable('sales/order_item')],
);

$connection->query($updateQuery);
