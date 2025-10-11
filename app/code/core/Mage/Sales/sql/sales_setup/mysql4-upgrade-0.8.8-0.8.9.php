<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('sales_order_entity')} MODIFY COLUMN `store_id` SMALLINT(5) UNSIGNED;
");

$installer->getConnection()->dropForeignKey($this->getTable('sales_order_entity'), 'FK_sales_order_entity_store');
$installer->getConnection()->dropColumn($this->getTable('sales_order_entity_datetime'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_order_entity_decimal'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_order_entity_int'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_order_entity_text'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_order_entity_varchar'), 'store_id');

$installer->getConnection()->dropColumn($this->getTable('sales_quote_entity_datetime'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_entity_decimal'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_entity_int'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_entity_text'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_entity_varchar'), 'store_id');

$installer->getConnection()->dropColumn($this->getTable('sales_quote_temp_datetime'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_temp_decimal'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_temp_int'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_temp_text'), 'store_id');
$installer->getConnection()->dropColumn($this->getTable('sales_quote_temp_varchar'), 'store_id');

$installer->getConnection()->addConstraint(
    'SALE_ORDER_ENTITY_STORE',
    $this->getTable('sales_order_entity'),
    'store_id',
    $this->getTable('core_store'),
    'store_id',
    'SET NULL',
);

$installer->installEntities();
$installer->endSetup();
