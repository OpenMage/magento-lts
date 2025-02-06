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

$installer->addAttribute('quote', 'customer_prefix', ['type' => 'static']);
$installer->addAttribute('quote', 'customer_middlename', ['type' => 'static']);
$installer->addAttribute('quote', 'customer_suffix', ['type' => 'static']);

$installer->addAttribute('quote_address', 'prefix', ['type' => 'static']);
$installer->addAttribute('quote_address', 'middlename', ['type' => 'static']);
$installer->addAttribute('quote_address', 'suffix', ['type' => 'static']);
