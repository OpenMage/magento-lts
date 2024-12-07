<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$subSelect = $installer->getConnection()->select()
    ->from(
        ['citem' => $installer->getTable('sales/creditmemo_item')],
        [
             'amount_refunded'        => 'SUM(citem.row_total)',
             'base_amount_refunded'   => 'SUM(citem.base_row_total)',
             'base_tax_refunded'      => 'SUM(citem.base_tax_amount)',
             'discount_refunded'      => 'SUM(citem.discount_amount)',
             'base_discount_refunded' => 'SUM(citem.base_discount_amount)',
        ]
    )
    ->joinLeft(
        ['c' => $installer->getTable('sales/creditmemo')],
        'c.entity_id = citem.parent_id',
        []
    )
    ->joinLeft(
        ['o' => $installer->getTable('sales/order')],
        'o.entity_id = c.order_id',
        []
    )
    ->joinLeft(
        ['oitem' => $installer->getTable('sales/order_item')],
        'oitem.order_id = o.entity_id AND oitem.product_id=citem.product_id',
        ['item_id']
    )
    ->group('oitem.item_id');

$select = $installer->getConnection()->select()
    ->from(
        ['selected' => $subSelect],
        [
            'amount_refunded'        => 'amount_refunded',
            'base_amount_refunded'   => 'base_amount_refunded',
            'base_tax_refunded'      => 'base_tax_refunded',
            'discount_refunded'      => 'discount_refunded',
            'base_discount_refunded' => 'base_discount_refunded',
        ]
    )
    ->where('main.item_id = selected.item_id');

$updateQuery = $installer->getConnection()->updateFromSelect(
    $select,
    ['main' => $installer->getTable('sales/order_item')]
);

$installer->getConnection()->query($updateQuery);
