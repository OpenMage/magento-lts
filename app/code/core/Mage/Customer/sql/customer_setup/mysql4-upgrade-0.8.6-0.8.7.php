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

$installer->addAttribute('customer', 'taxvat', [
    'label'        => 'Tax/VAT number',
    'visible'      => 1,
    'required'     => 0,
    'position'     => 1,
]);

$installer->endSetup();
