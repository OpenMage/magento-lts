<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'password_created_at', [
    'label'    => 'Password created at',
    'visible'  => false,
    'required' => false,
    'type'     => 'int',
]);

$installer->endSetup();
