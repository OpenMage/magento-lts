<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->addAttribute('quote', 'customer_gender', ['type' => 'int', 'visible' => false]);

$installer->addAttribute('order', 'customer_gender', ['type' => 'int', 'visible' => false]);
