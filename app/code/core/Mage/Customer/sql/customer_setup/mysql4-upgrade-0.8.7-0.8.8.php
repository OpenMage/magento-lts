<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Customer_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'confirmation', [
    'label'    => 'Is confirmed',
    'visible'  => false,
    'required' => false,
]);

$installer->endSetup();
