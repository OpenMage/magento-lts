<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');
