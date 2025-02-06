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

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'base_tax_refunded', [
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Base Tax Refunded',
        'scale'     => 4,
        'precision' => 12,
    ]);
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'discount_refunded', [
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Discount Refunded',
        'scale'     => 4,
        'precision' => 12,
    ]);
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'base_discount_refunded', [
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Base Discount Refunded',
        'scale'     => 4,
        'precision' => 12,
    ]);
