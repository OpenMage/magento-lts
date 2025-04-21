<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
