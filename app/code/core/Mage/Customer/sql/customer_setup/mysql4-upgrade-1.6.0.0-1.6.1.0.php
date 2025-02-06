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

// Add reset password link token attribute
$installer->addAttribute('customer', 'rp_token', [
    'type'     => 'varchar',
    'input'    => 'hidden',
    'visible'  => false,
    'required' => false,
]);

// Add reset password link token creation date attribute
$installer->addAttribute('customer', 'rp_token_created_at', [
    'type'           => 'datetime',
    'input'          => 'date',
    'validate_rules' => 'a:1:{s:16:"input_validation";s:4:"date";}',
    'visible'        => false,
    'required'       => false,
]);

$installer->endSetup();
