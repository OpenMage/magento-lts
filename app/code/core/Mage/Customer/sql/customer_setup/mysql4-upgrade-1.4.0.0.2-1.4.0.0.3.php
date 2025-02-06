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

$this->addAttribute('customer', 'created_at', [
    'type'     => 'static',
    'label'    => 'Created At',
    'visible'  => false,
    'required' => false,
    'input'    => 'date',
]);

$installer->endSetup();
