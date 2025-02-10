<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $conn */
$conn = $installer->getConnection();

$installer->addAttribute('quote_item', 'product_type', []);
$installer->addAttribute('order_item', 'product_type', []);
