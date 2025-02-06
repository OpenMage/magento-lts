<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$this->updateAttribute('customer', 'default_billing', 'frontend_label', 'Default Billing Address');
$this->updateAttribute('customer', 'default_shipping', 'frontend_label', 'Default Shipping Address');

$installer->endSetup();
