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

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'base_tax_refunded', [
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Base Tax Refunded',
        'scale'     => 4,
        'precision' => 12,
    ]);
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'discount_refunded', [
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Discount Refunded',
        'scale'     => 4,
        'precision' => 12,
    ]);
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'base_discount_refunded', [
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Base Discount Refunded',
        'scale'     => 4,
        'precision' => 12,
    ]);
