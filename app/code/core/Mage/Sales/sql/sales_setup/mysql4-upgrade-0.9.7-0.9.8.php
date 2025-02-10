<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->run("
ALTER TABLE `{$installer->getTable('sales_order_tax')}` ADD `base_amount` DECIMAL( 12, 4 ) NOT NULL, ADD `process` SMALLINT NOT NULL;
");
