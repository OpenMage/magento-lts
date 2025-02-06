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

$installer->addAttribute('customer', 'password_created_at', [
    'label'    => 'Password created at',
    'visible'  => false,
    'required' => false,
    'type'     => 'int',
]);

$installer->endSetup();
