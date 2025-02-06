<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->addAttribute('quote_item', 'base_tax_before_discount', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'tax_before_discount', ['type' => 'decimal']);

$installer->addAttribute('order_item', 'base_tax_before_discount', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'tax_before_discount', ['type' => 'decimal']);
