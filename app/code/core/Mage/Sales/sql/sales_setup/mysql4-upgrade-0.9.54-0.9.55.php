<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->addAttribute('order_payment', 'base_amount_paid_online', ['type' => 'decimal']);
$installer->addAttribute('order_payment', 'base_amount_refunded_online', ['type' => 'decimal']);
