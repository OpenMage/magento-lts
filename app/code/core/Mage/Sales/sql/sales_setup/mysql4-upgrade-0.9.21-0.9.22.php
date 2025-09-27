<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'payment_authorization_amount', ['type' => 'decimal']);
$installer->addAttribute('order', 'payment_authorization_expiration', ['type' => 'int']);

$installer->endSetup();
