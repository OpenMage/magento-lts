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

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_item'),
    'store_id',
    'smallint(5) unsigned default null AFTER `product_id`'
);
$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_ITEM_STORE',
    $installer->getTable('sales_flat_quote_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'set null',
    'cascade'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order_item'),
    'store_id',
    'smallint(5) unsigned default null AFTER `quote_item_id`'
);
$installer->getConnection()->addConstraint(
    'FK_SALES_ORDER_ITEM_STORE',
    $installer->getTable('sales_flat_order_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'set null',
    'cascade'
);
$installer->addAttribute('quote_item', 'redirect_url', [
    'type'  => 'varchar',
]);

$installer->endSetup();
