<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('quote', 'is_changed', ['type' => 'int']);

$installer->endSetup();
