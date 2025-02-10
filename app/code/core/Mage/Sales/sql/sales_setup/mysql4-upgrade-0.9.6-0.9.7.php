<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->addAttribute('quote_item', 'base_tax_before_discount', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'tax_before_discount', ['type' => 'decimal']);

$installer->addAttribute('order_item', 'base_tax_before_discount', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'tax_before_discount', ['type' => 'decimal']);
