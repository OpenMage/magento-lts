<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
