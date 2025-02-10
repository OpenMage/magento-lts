<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->installEntities();
$installer->removeEntityType('invoice_address');
$installer->removeEntityType('invoice_payment');
