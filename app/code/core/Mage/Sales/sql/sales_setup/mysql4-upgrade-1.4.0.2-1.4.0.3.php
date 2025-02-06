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

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'hidden_tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_hidden_tax_refunded', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_hidden_tax_refunded', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
