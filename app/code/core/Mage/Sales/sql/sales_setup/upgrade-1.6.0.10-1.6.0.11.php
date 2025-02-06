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
    ['product_id'],
);

$installer->endSetup();
