<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'hidden_tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_hidden_tax_refunded', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_hidden_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_hidden_tax_refunded', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'shipping_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'base_shipping_hidden_tax_amount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'hidden_tax_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'base_hidden_tax_amount', 'decimal(12,4) NULL');

