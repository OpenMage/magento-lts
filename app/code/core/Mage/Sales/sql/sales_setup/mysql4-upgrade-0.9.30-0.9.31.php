<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'original_custom_price', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'subtotal', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_subtotal', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'subtotal_with_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_subtotal_with_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'is_changed', 'int(10) unsigned');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_to_global_rate', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_to_quote_rate', 'decimal(12,4)');
