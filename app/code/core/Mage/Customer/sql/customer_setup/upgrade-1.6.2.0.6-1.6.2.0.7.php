<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Customer_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

// Add reset password link customer Id attribute
$installer->addAttribute('customer', 'rp_customer_id', [
    'type'     => 'varchar',
    'input'    => 'hidden',
    'visible'  => false,
    'required' => false,
]);

$installer->endSetup();
