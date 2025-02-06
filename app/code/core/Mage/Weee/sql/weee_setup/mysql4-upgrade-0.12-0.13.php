<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'weee_tax_applied_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'weee_tax_applied_row_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'weee_tax_applied_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'weee_tax_applied_row_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_weee_tax_applied_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_weee_tax_applied_row_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_weee_tax_applied_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_weee_tax_applied_row_amount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'weee_tax_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'weee_tax_row_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_weee_tax_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_weee_tax_row_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'weee_tax_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'weee_tax_row_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_weee_tax_disposition', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_weee_tax_row_disposition', 'decimal(12,4)');
