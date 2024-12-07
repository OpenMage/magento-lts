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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

// Add index to sales_flat_order on customer_email for fast lookup, only first 15 bytes
$keyList = $installer->getConnection()->getIndexList($installer->getTable('sales/order'));
if (!isset($keyList['IDX_SALES_FLAT_ORDER_CUSTOMER_EMAIL'])) {
    $installer->run("
        ALTER TABLE {$installer->getTable('sales/order')}
        ADD INDEX `IDX_SALES_FLAT_ORDER_CUSTOMER_EMAIL` (`customer_email` (15));
    ");
}

// Add index to sales_flat_order_item.product_id for fast join/lookup
$this->getConnection()->addIndex(
    $installer->getTable('sales/order_item'),
    'IDX_SALES_FLAT_ORDER_ITEM_PRODUCT_ID',
    ['product_id']
);

$installer->endSetup();
