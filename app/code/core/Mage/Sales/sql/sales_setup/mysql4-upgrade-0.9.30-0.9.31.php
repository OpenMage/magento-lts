<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'original_custom_price', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'subtotal', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_subtotal', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'subtotal_with_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_subtotal_with_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'is_changed', 'int(10) unsigned');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_to_global_rate', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_to_quote_rate', 'decimal(12,4)');
