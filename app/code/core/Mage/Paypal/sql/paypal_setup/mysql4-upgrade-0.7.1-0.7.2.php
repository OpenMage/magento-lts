<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->addAttribute('order', 'paypal_ipn_customer_notified', ['type' => 'int', 'visible' => false, 'default' => 0]);
$installer->endSetup();
