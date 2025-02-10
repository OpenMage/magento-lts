<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'tax_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'tax_refunded', 'decimal(12,4) NULL');
