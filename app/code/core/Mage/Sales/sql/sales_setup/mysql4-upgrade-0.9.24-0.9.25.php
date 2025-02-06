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

$installer->getConnection()->addColumn($this->getTable('sales_order'), 'shipping_tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_shipping_tax_refunded', 'decimal(12,4) NULL');

$installer->addAttribute('order', 'shipping_tax_refunded', ['type' => 'static']);
$installer->addAttribute('order', 'base_shipping_tax_refunded', ['type' => 'static']);
