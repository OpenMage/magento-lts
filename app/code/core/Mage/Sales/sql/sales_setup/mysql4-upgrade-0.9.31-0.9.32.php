<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'global_currency_code', 'varchar(255)');
