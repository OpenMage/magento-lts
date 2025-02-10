<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->addAttribute('invoice', 'email_sent', ['type' => 'int']);
$installer->addAttribute('shipment', 'email_sent', ['type' => 'int']);
