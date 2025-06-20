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

try {
    $installer->run("
    ALTER TABLE {$this->getTable('sales_order_entity_varchar')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_order_entity_varchar_store`, DROP FOREIGN KEY `FK_sales_order_entity_varchar_store`;
    ALTER TABLE {$this->getTable('sales_order_entity_text')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_order_entity_text_store`, DROP FOREIGN KEY `FK_sales_order_entity_text_store`;
    ALTER TABLE {$this->getTable('sales_order_entity_int')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_order_entity_int_store`, DROP FOREIGN KEY `FK_sales_order_entity_int_store`;
    ALTER TABLE {$this->getTable('sales_order_entity_decimal')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_order_entity_decimal_store`, DROP FOREIGN KEY `FK_sales_order_entity_decimal_store`;
    ALTER TABLE {$this->getTable('sales_order_entity_datetime')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_order_entity_datetime_store`, DROP FOREIGN KEY `FK_sales_order_entity_datetime_store`;

    ALTER TABLE {$this->getTable('sales_quote_entity_varchar')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_entity_varchar_store`, DROP FOREIGN KEY `FK_sales_quote_entity_varchar_store`;
    ALTER TABLE {$this->getTable('sales_quote_entity_text')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_entity_text_store`, DROP FOREIGN KEY `FK_sales_quote_entity_text_store`;
    ALTER TABLE {$this->getTable('sales_quote_entity_int')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_entity_int_store`, DROP FOREIGN KEY `FK_sales_quote_entity_int_store`;
    ALTER TABLE {$this->getTable('sales_quote_entity_decimal')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_entity_decimal_store`, DROP FOREIGN KEY `FK_sales_quote_entity_decimal_store`;
    ALTER TABLE {$this->getTable('sales_quote_entity_datetime')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_entity_datetime_store`, DROP FOREIGN KEY `FK_sales_quote_entity_datetime_store`;

    ALTER TABLE {$this->getTable('sales_quote_temp_varchar')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_temp_varchar_store`, DROP FOREIGN KEY `FK_sales_quote_temp_varchar_store`;
    ALTER TABLE {$this->getTable('sales_quote_temp_text')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_temp_text_store`, DROP FOREIGN KEY `FK_sales_quote_temp_text_store`;
    ALTER TABLE {$this->getTable('sales_quote_temp_int')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_temp_int_store`, DROP FOREIGN KEY `FK_sales_quote_temp_int_store`;
    ALTER TABLE {$this->getTable('sales_quote_temp_decimal')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_temp_decimal_store`, DROP FOREIGN KEY `FK_sales_quote_temp_decimal_store`;
    ALTER TABLE {$this->getTable('sales_quote_temp_datetime')} DROP COLUMN `store_id`, DROP INDEX `FK_sales_quote_temp_datetime_store`, DROP FOREIGN KEY `FK_sales_quote_temp_datetime_store`;
    ");
} catch (Exception) {
}

$installer->installEntities();

$installer->endSetup();
