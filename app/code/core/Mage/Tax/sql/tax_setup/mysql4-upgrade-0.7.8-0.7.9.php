<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/** @var Mage_Tax_Model_Resource_Setup $installer */
$installer  = $this;

$installer->addAttribute('invoice', 'shipping_tax_amount', ['type' => 'decimal']);
$installer->addAttribute('invoice', 'base_shipping_tax_amount', ['type' => 'decimal']);

$installer->addAttribute('creditmemo', 'shipping_tax_amount', ['type' => 'decimal']);
$installer->addAttribute('creditmemo', 'base_shipping_tax_amount', ['type' => 'decimal']);

$installer->addAttribute('quote_item', 'price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'base_price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'base_row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_address_item', 'price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_address_item', 'base_price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_address_item', 'row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_address_item', 'base_row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_address', 'subtotal_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('quote_address', 'base_subtotal_total_incl_tax', ['type' => 'decimal']);

$installer->addAttribute('order_item', 'price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'base_price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'base_row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('order', 'subtotal_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('order', 'base_subtotal_incl_tax', ['type' => 'decimal']);

$installer->addAttribute('invoice_item', 'price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'base_price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'base_row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('invoice', 'subtotal_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('invoice', 'base_subtotal_incl_tax', ['type' => 'decimal']);

$installer->addAttribute('creditmemo_item', 'price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'base_price_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'base_row_total_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('creditmemo', 'subtotal_incl_tax', ['type' => 'decimal']);
$installer->addAttribute('creditmemo', 'base_subtotal_incl_tax', ['type' => 'decimal']);
