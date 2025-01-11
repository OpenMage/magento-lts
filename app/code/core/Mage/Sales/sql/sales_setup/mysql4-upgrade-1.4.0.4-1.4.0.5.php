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

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'shipping_incl_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'base_shipping_incl_tax', 'decimal(12,4) NULL');
