<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;

$connection = $installer->getConnection();
$table      = $installer->getTable('catalog/product_super_attribute_pricing');
$connection->changeColumn($table, 'pricing_value', 'pricing_value', 'DECIMAL(12, 4) NULL DEFAULT NULL');
