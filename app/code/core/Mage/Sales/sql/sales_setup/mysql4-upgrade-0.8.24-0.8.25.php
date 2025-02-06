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

$installer->getConnection()->addColumn($this->getTable('sales_quote_address'), 'shipping_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_quote_address'), 'base_shipping_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($this->getTable('sales_order'), 'shipping_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_shipping_tax_amount', 'decimal(12,4) NULL');

$installer->addAttribute('quote_address', 'shipping_tax_amount', ['type' => 'static']);
$installer->addAttribute('quote_address', 'base_shipping_tax_amount', ['type' => 'static']);

$installer->addAttribute('order', 'shipping_tax_amount', ['type' => 'static']);
$installer->addAttribute('order', 'base_shipping_tax_amount', ['type' => 'static']);

$installer->addAttribute('invoice', 'shipping_tax_amount', ['type' => 'decimal']);
$installer->addAttribute('invoice', 'base_shipping_tax_amount', ['type' => 'decimal']);

$installer->addAttribute('creditmemo', 'shipping_tax_amount', ['type' => 'decimal']);
$installer->addAttribute('creditmemo', 'base_shipping_tax_amount', ['type' => 'decimal']);
