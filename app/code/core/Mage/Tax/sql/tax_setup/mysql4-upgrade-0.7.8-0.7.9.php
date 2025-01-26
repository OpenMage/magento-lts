<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
