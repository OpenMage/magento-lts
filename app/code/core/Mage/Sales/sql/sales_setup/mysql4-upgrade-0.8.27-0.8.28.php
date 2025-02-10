<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->addAttribute('quote', 'customer_prefix', ['type' => 'static']);
$installer->addAttribute('quote', 'customer_middlename', ['type' => 'static']);
$installer->addAttribute('quote', 'customer_suffix', ['type' => 'static']);

$installer->addAttribute('quote_address', 'prefix', ['type' => 'static']);
$installer->addAttribute('quote_address', 'middlename', ['type' => 'static']);
$installer->addAttribute('quote_address', 'suffix', ['type' => 'static']);
