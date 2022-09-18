<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    'SET NULL'
);

$installer->installEntities();
$installer->endSetup();
