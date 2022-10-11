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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'subtotal_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'subtotal_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'tax_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'shipping_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'shipping_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_subtotal_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_subtotal_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_tax_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_tax_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_shipping_refunded', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_shipping_canceled', 'decimal(12,4) NULL');

$installer->addAttribute('order', 'subtotal_refunded', ['type'=>'static']);
$installer->addAttribute('order', 'subtotal_canceled', ['type'=>'static']);
$installer->addAttribute('order', 'tax_refunded', ['type'=>'static']);
$installer->addAttribute('order', 'tax_canceled', ['type'=>'static']);
$installer->addAttribute('order', 'shipping_refunded', ['type'=>'static']);
$installer->addAttribute('order', 'shipping_canceled', ['type'=>'static']);
$installer->addAttribute('order', 'base_subtotal_refunded', ['type'=>'static']);
$installer->addAttribute('order', 'base_subtotal_canceled', ['type'=>'static']);
$installer->addAttribute('order', 'base_tax_refunded', ['type'=>'static']);
$installer->addAttribute('order', 'base_tax_canceled', ['type'=>'static']);
$installer->addAttribute('order', 'base_shipping_refunded', ['type'=>'static']);
$installer->addAttribute('order', 'base_shipping_canceled', ['type'=>'static']);
