<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->addAttribute('invoice', 'store_id', ['type' => 'static']);
$installer->addAttribute('creditmemo', 'store_id', ['type' => 'static']);
$installer->addAttribute('shipment', 'store_id', ['type' => 'static']);
