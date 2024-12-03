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
