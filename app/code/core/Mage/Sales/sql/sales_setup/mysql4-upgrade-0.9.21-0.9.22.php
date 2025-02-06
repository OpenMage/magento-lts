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
$installer->startSetup();

$installer->addAttribute('order', 'payment_authorization_amount', ['type' => 'decimal']);
$installer->addAttribute('order', 'payment_authorization_expiration', ['type' => 'int']);

$installer->endSetup();
