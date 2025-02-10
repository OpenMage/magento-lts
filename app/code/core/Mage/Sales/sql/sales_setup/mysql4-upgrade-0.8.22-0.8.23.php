<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('sales_quote'), 'reserved_order_id', 'varchar(64) default \'\'');
$installer->addAttribute('quote', 'reserved_order_id', ['type' => 'static']);
