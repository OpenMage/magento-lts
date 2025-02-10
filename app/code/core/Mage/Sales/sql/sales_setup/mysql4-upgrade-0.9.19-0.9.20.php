<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'can_ship_partially', ['type' => 'int']);
$installer->addAttribute('order', 'can_ship_partially_item', ['type' => 'int']);

$installer->endSetup();
