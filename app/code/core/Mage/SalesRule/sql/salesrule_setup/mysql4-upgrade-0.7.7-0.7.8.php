<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

/**
 * add attributes discount_description, shipping_discount_amount, base_shipping_discount_amount
 */
$installer->addAttribute('quote_address', 'discount_description', ['type' => 'varchar']);
$installer->addAttribute('quote_address', 'shipping_discount_amount', ['type' => 'decimal']);
$installer->addAttribute('quote_address', 'base_shipping_discount_amount', ['type' => 'decimal']);

$installer->addAttribute('order', 'discount_description', ['type' => 'varchar']);
$installer->addAttribute('order', 'shipping_discount_amount', ['type' => 'decimal']);
$installer->addAttribute('order', 'base_shipping_discount_amount', ['type' => 'decimal']);
