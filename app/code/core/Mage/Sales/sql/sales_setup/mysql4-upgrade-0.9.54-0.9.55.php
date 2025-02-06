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

$installer->addAttribute('order_payment', 'base_amount_paid_online', ['type' => 'decimal']);
$installer->addAttribute('order_payment', 'base_amount_refunded_online', ['type' => 'decimal']);
