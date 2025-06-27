<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->addAttribute('order', 'paypal_ipn_customer_notified', ['type' => 'int', 'visible' => false, 'default' => 0]);
$installer->endSetup();
