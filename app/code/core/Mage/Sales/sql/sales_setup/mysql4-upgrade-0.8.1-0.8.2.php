<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
} catch (Exception $e) {
}

$installer->installEntities();

$installer->endSetup();
