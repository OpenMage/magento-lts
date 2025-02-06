<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_item'),
    'store_id',
    'smallint(5) unsigned default null AFTER `product_id`',
);
$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_ITEM_STORE',
    $installer->getTable('sales_flat_quote_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'set null',
    'cascade',
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order_item'),
    'store_id',
    'smallint(5) unsigned default null AFTER `quote_item_id`',
);
$installer->getConnection()->addConstraint(
    'FK_SALES_ORDER_ITEM_STORE',
    $installer->getTable('sales_flat_order_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'set null',
    'cascade',
);
$installer->addAttribute('quote_item', 'redirect_url', [
    'type'  => 'varchar',
]);

$installer->endSetup();
