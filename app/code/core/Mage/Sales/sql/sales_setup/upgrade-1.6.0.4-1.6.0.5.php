<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $this */
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
