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

$installer->addAttribute('quote', 'subtotal', ['type' => 'decimal']);
$installer->addAttribute('quote', 'base_subtotal', ['type' => 'decimal']);

$installer->addAttribute('quote', 'subtotal_with_discount', ['type' => 'decimal']);
$installer->addAttribute('quote', 'base_subtotal_with_discount', ['type' => 'decimal']);
