<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Customer_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$this->updateAttribute('customer', 'default_billing', 'frontend_label', 'Default Billing Address');
$this->updateAttribute('customer', 'default_shipping', 'frontend_label', 'Default Shipping Address');

$installer->endSetup();
