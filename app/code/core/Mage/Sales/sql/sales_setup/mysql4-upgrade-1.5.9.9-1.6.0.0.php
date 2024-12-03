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
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'FK_PRODUCT_ORDERED_AGGREGATED_DAILY_PRODUCT_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'FK_PRODUCT_ORDERED_AGGREGATED_DAILY_STORE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'FK_PRODUCT_ORDERED_AGGREGATED_MONTHLY_PRODUCT_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'FK_PRODUCT_ORDERED_AGGREGATED_MONTHLY_STORE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'FK_PRODUCT_ORDERED_AGGREGATED_YEARLY_PRODUCT_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'FK_PRODUCT_ORDERED_AGGREGATED_YEARLY_STORE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/billing_agreement'),
    'FK_BILLING_AGREEMENT_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/billing_agreement'),
    'FK_BILLING_AGREEMENT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/billing_agreement_order'),
    'FK_BILLING_AGREEMENT_ORDER_AGREEMENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/billing_agreement_order'),
    'FK_BILLING_AGREEMENT_ORDER_ORDER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/creditmemo'),
    'FK_SALES_FLAT_CREDITMEMO_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/creditmemo'),
    'FK_SALES_FLAT_CREDITMEMO_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/creditmemo_comment'),
    'FK_SALES_FLAT_CREDITMEMO_COMMENT_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/creditmemo_grid'),
    'FK_SALES_FLAT_CREDITMEMO_GRID_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/creditmemo_grid'),
    'FK_SALES_FLAT_CREDITMEMO_GRID_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/creditmemo_item'),
    'FK_SALES_FLAT_CREDITMEMO_ITEM_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoice'),
    'FK_SALES_FLAT_INVOICE_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoice'),
    'FK_SALES_FLAT_INVOICE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoice_comment'),
    'FK_SALES_FLAT_INVOICE_COMMENT_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoice_grid'),
    'FK_SALES_FLAT_INVOICE_GRID_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoice_grid'),
    'FK_SALES_FLAT_INVOICE_GRID_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoice_item'),
    'FK_SALES_FLAT_INVOICE_ITEM_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order'),
    'FK_SALES_FLAT_ORDER_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order'),
    'FK_SALES_FLAT_ORDER_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_address'),
    'FK_SALES_FLAT_ORDER_ADDRESS_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_grid'),
    'FK_SALES_FLAT_ORDER_GRID_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_grid'),
    'FK_SALES_FLAT_ORDER_GRID_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_grid'),
    'FK_SALES_FLAT_ORDER_GRID_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_item'),
    'FK_SALES_FLAT_ORDER_ITEM_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_item'),
    'FK_SALES_FLAT_ORDER_ITEM_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_payment'),
    'FK_SALES_FLAT_ORDER_PAYMENT_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_status_history'),
    'FK_SALES_FLAT_ORDER_STATUS_HISTORY_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote'),
    'FK_SALES_QUOTE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_address'),
    'FK_SALES_QUOTE_ADDRESS_SALES_QUOTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_address_item'),
    'FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_address_item'),
    'FK_SALES_FLAT_QUOTE_ADDRESS_ITEM_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_address_item'),
    'FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_FLAT_QUOTE_ITEM_PARENT_ITEM'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_QUOTE_ITEM_CATALOG_PRODUCT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_QUOTE_ITEM_SALES_QUOTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_QUOTE_ITEM_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_item_option'),
    'FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_payment'),
    'FK_SALES_QUOTE_PAYMENT_SALES_QUOTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/quote_address_shipping_rate'),
    'FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment'),
    'FK_SALES_FLAT_SHIPMENT_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment'),
    'FK_SALES_FLAT_SHIPMENT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment_comment'),
    'FK_SALES_FLAT_SHIPMENT_COMMENT_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment_grid'),
    'FK_SALES_FLAT_SHIPMENT_GRID_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment_grid'),
    'FK_SALES_FLAT_SHIPMENT_GRID_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment_item'),
    'FK_SALES_FLAT_SHIPMENT_ITEM_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipment_track'),
    'FK_SALES_FLAT_SHIPMENT_TRACK_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoiced_aggregated'),
    'FK_SALES_INVOICED_AGGREGATED_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/invoiced_aggregated_order'),
    'FK_SALES_INVOICED_AGGREGATED_ORDER_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_aggregated_created'),
    'FK_SALES_ORDER_AGGREGATED_CREATED'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_status_label'),
    'FK_SALES_ORDER_STATUS_LABEL_STATUS'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_status_label'),
    'FK_SALES_ORDER_STATUS_LABEL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_status_state'),
    'FK_SALES_ORDER_STATUS_STATE_STATUS'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/order_tax'),
    'FK_SALES_ORDER_TAX_ORDER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/payment_transaction'),
    'FK_SALES_PAYMENT_TRANSACTION_ORDER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/payment_transaction'),
    'FK_SALES_PAYMENT_TRANSACTION_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/payment_transaction'),
    'FK_SALES_PAYMENT_TRANSACTION_PAYMENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/recurring_profile'),
    'FK_RECURRING_PROFILE_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/recurring_profile'),
    'FK_RECURRING_PROFILE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/recurring_profile_order'),
    'FK_RECURRING_PROFILE_ORDER_ORDER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/recurring_profile_order'),
    'FK_RECURRING_PROFILE_ORDER_PROFILE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/refunded_aggregated'),
    'FK_SALES_REFUNDED_AGGREGATED_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/refunded_aggregated_order'),
    'FK_SALES_REFUNDED_AGGREGATED_ORDER_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipping_aggregated'),
    'FK_SALES_SHIPPING_AGGREGATED_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('sales/shipping_aggregated_order'),
    'FK_SALES_SHIPPING_AGGREGATED_ORDER_STORE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'UNQ_PERIOD_STORE_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'IDX_PRODUCT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'UNQ_PERIOD_STORE_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'IDX_PRODUCT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'UNQ_PERIOD_STORE_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'IDX_PRODUCT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/billing_agreement'),
    'IDX_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/billing_agreement'),
    'FK_BILLING_AGREEMENT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/billing_agreement_order'),
    'UNQ_BILLING_AGREEMENT_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/billing_agreement_order'),
    'FK_BILLING_AGREEMENT_ORDER_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo'),
    'IDX_CREDITMEMO_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo'),
    'IDX_STATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_comment'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_comment'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_GRAND_TOTAL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_BASE_GRAND_TOTAL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_CREDITMEMO_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_STATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_ORDER_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_ORDER_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_grid'),
    'IDX_BILLING_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/creditmemo_item'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice'),
    'IDX_GRAND_TOTAL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice'),
    'IDX_STATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_comment'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_comment'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_GRAND_TOTAL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_STATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_ORDER_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_ORDER_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_grid'),
    'IDX_BILLING_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoice_item'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_STATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_CUSTOMER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_EXT_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_UPDATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order'),
    'IDX_QUOTE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_address'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_BASE_GRAND_TOTAL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_BASE_TOTAL_PAID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_GRAND_TOTAL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_TOTAL_PAID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_SHIPPING_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_BILLING_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_CUSTOMER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_grid'),
    'IDX_UPDATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_item'),
    'IDX_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_item'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_item'),
    'IDX_PRODUCT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_payment'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_status_history'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_status_history'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote'),
    'FK_SALES_QUOTE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote'),
    'IDX_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote'),
    'IDX_IS_ACTIVE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_address'),
    'FK_SALES_QUOTE_ADDRESS_SALES_QUOTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_address_item'),
    'FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_address_item'),
    'FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_address_item'),
    'FK_SALES_FLAT_QUOTE_ADDRESS_ITEM_PARENT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_QUOTE_ITEM_SALES_QUOTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_FLAT_QUOTE_ITEM_PARENT_ITEM'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_QUOTE_ITEM_CATALOG_PRODUCT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_item'),
    'FK_SALES_QUOTE_ITEM_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_item_option'),
    'FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_payment'),
    'FK_SALES_QUOTE_PAYMENT_SALES_QUOTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/quote_address_shipping_rate'),
    'FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment'),
    'IDX_TOTAL_QTY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment'),
    'IDX_UPDATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_comment'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_comment'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'UNQ_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_TOTAL_QTY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_SHIPMENT_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_ORDER_INCREMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_ORDER_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_grid'),
    'IDX_SHIPPING_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_item'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_track'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_track'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipment_track'),
    'IDX_CREATED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoiced_aggregated'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoiced_aggregated'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoiced_aggregated_order'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/invoiced_aggregated_order'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_aggregated_created'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_aggregated_created'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_status_label'),
    'FK_SALES_ORDER_STATUS_LABEL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/order_tax'),
    'IDX_ORDER_TAX'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/payment_transaction'),
    'UNQ_ORDER_PAYMENT_TXN'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/payment_transaction'),
    'IDX_ORDER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/payment_transaction'),
    'IDX_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/payment_transaction'),
    'IDX_PAYMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/recurring_profile'),
    'UNQ_INTERNAL_REF_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/recurring_profile'),
    'IDX_RECURRING_PROFILE_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/recurring_profile'),
    'IDX_RECURRING_PROFILE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/recurring_profile_order'),
    'UNQ_PROFILE_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/recurring_profile_order'),
    'IDX_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/refunded_aggregated'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/refunded_aggregated'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/refunded_aggregated_order'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/refunded_aggregated_order'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipping_aggregated'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipping_aggregated'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipping_aggregated_order'),
    'UNQ_PERIOD_STORE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sales/shipping_aggregated_order'),
    'IDX_STORE_ID'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('sales/quote') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'converted_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Converted At'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '1',
                'comment'   => 'Is Active'
            ],
            'is_virtual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Virtual'
            ],
            'is_multi_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Multi Shipping'
            ],
            'items_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Items Count'
            ],
            'items_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Items Qty'
            ],
            'orig_order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Orig Order Id'
            ],
            'store_to_base_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Store To Base Rate'
            ],
            'store_to_quote_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Store To Quote Rate'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Base Currency Code'
            ],
            'store_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Store Currency Code'
            ],
            'quote_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Quote Currency Code'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Grand Total'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Grand Total'
            ],
            'checkout_method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Checkout Method'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Id'
            ],
            'customer_tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Tax Class Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Group Id'
            ],
            'customer_email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Email'
            ],
            'customer_prefix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Customer Prefix'
            ],
            'customer_firstname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Firstname'
            ],
            'customer_middlename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Customer Middlename'
            ],
            'customer_lastname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Lastname'
            ],
            'customer_suffix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Customer Suffix'
            ],
            'customer_dob' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Customer Dob'
            ],
            'customer_note' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Note'
            ],
            'customer_note_notify' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '1',
                'comment'   => 'Customer Note Notify'
            ],
            'customer_is_guest' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Is Guest'
            ],
            'remote_ip' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Remote Ip'
            ],
            'applied_rule_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Applied Rule Ids'
            ],
            'reserved_order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Reserved Order Id'
            ],
            'password_hash' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Password Hash'
            ],
            'coupon_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Coupon Code'
            ],
            'global_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Global Currency Code'
            ],
            'base_to_global_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ],
            'base_to_quote_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Quote Rate'
            ],
            'customer_taxvat' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Taxvat'
            ],
            'customer_gender' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Gender'
            ],
            'subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ],
            'base_subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ],
            'subtotal_with_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal With Discount'
            ],
            'base_subtotal_with_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal With Discount'
            ],
            'is_changed' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Is Changed'
            ],
            'trigger_recollect' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Trigger Recollect'
            ],
            'ext_shipping_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Ext Shipping Info'
            ]
        ],
        'comment' => 'Sales Flat Quote'
    ],
    $installer->getTable('sales/quote_item') => [
        'columns' => [
            'item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item Id'
            ],
            'quote_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'parent_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Item Id'
            ],
            'is_virtual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Virtual'
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'applied_rule_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Rule Ids'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'free_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Free Shipping'
            ],
            'is_qty_decimal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Qty Decimal'
            ],
            'no_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'No Discount'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ],
            'base_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Price'
            ],
            'custom_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Custom Price'
            ],
            'discount_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Percent'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ],
            'tax_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Percent'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ],
            'row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Total'
            ],
            'base_row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Total'
            ],
            'row_total_with_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Total With Discount'
            ],
            'row_weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Weight'
            ],
            'product_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Type'
            ],
            'base_tax_before_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Before Discount'
            ],
            'tax_before_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Before Discount'
            ],
            'original_custom_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Custom Price'
            ],
            'redirect_url' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Redirect Url'
            ],
            'base_cost' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ],
            'price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ],
            'base_price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ],
            'row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ],
            'base_row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ]
        ],
        'comment' => 'Sales Flat Quote Item'
    ],
    $installer->getTable('sales/quote_address') => [
        'columns' => [
            'address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Address Id'
            ],
            'quote_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ],
            'save_in_address_book' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '0',
                'comment'   => 'Save In Address Book'
            ],
            'customer_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Address Id'
            ],
            'address_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Address Type'
            ],
            'email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Email'
            ],
            'prefix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Prefix'
            ],
            'firstname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Firstname'
            ],
            'middlename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Middlename'
            ],
            'lastname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Lastname'
            ],
            'suffix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Suffix'
            ],
            'company' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Company'
            ],
            'street' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Street'
            ],
            'city' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'City'
            ],
            'region' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Region'
            ],
            'region_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Region Id'
            ],
            'postcode' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Postcode'
            ],
            'country_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Country Id'
            ],
            'telephone' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Telephone'
            ],
            'fax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Fax'
            ],
            'same_as_billing' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Same As Billing'
            ],
            'free_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Free Shipping'
            ],
            'collect_shipping_rates' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Collect Shipping Rates'
            ],
            'shipping_method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Method'
            ],
            'shipping_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ],
            'subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal'
            ],
            'base_subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Subtotal'
            ],
            'subtotal_with_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal With Discount'
            ],
            'base_subtotal_with_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Subtotal With Discount'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ],
            'shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Shipping Amount'
            ],
            'base_shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Shipping Amount'
            ],
            'shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ],
            'base_shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Grand Total'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Grand Total'
            ],
            'customer_notes' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Customer Notes'
            ],
            'applied_taxes' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Taxes'
            ],
            'discount_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Discount Description'
            ],
            'shipping_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Discount Amount'
            ],
            'base_shipping_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Discount Amount'
            ],
            'subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ],
            'base_subtotal_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Total Incl Tax'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ],
            'shipping_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ],
            'shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ],
            'base_shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            ]
        ],
        'comment' => 'Sales Flat Quote Address'
    ],
    $installer->getTable('sales/quote_address_item') => [
        'columns' => [
            'address_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Address Item Id'
            ],
            'parent_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Item Id'
            ],
            'quote_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Address Id'
            ],
            'quote_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Item Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'applied_rule_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Rule Ids'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ],
            'row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Total'
            ],
            'base_row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Total'
            ],
            'row_total_with_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Total With Discount'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ],
            'row_weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Weight'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ],
            'super_product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Super Product Id'
            ],
            'parent_product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Product Id'
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ],
            'image' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Image'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'free_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Free Shipping'
            ],
            'is_qty_decimal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Is Qty Decimal'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'discount_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Percent'
            ],
            'no_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'No Discount'
            ],
            'tax_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Percent'
            ],
            'base_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price'
            ],
            'base_cost' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ],
            'price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ],
            'base_price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ],
            'row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ],
            'base_row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ]
        ],
        'comment' => 'Sales Flat Quote Address Item'
    ],
    $installer->getTable('sales/quote_item_option') => [
        'columns' => [
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id'
            ],
            'item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Item Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value'
            ]
        ],
        'comment' => 'Sales Flat Quote Item Option'
    ],
    $installer->getTable('sales/quote_payment') => [
        'columns' => [
            'payment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Payment Id'
            ],
            'quote_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Method'
            ],
            'cc_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Type'
            ],
            'cc_number_enc' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Number Enc'
            ],
            'cc_last4' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Last4'
            ],
            'cc_cid_enc' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Cid Enc'
            ],
            'cc_owner' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Owner'
            ],
            'cc_exp_month' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Exp Month'
            ],
            'cc_exp_year' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Exp Year'
            ],
            'cc_ss_owner' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Owner'
            ],
            'cc_ss_start_month' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Ss Start Month'
            ],
            'cc_ss_start_year' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Ss Start Year'
            ],
            'po_number' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Po Number'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'cc_ss_issue' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Issue'
            ],
            'additional_information' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Information'
            ]
        ],
        'comment' => 'Sales Flat Quote Payment'
    ],
    $installer->getTable('sales/quote_address_shipping_rate') => [
        'columns' => [
            'rate_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rate Id'
            ],
            'address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Address Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'carrier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Carrier'
            ],
            'carrier_title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Carrier Title'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Code'
            ],
            'method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Method'
            ],
            'method_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Method Description'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ],
            'error_message' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Error Message'
            ],
            'method_title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Method Title'
            ]
        ],
        'comment' => 'Sales Flat Quote Shipping Rate'
    ],
    $installer->getTable('sales/order') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'State'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Status'
            ],
            'coupon_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Coupon Code'
            ],
            'protect_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Protect Code'
            ],
            'shipping_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ],
            'is_virtual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Virtual'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ],
            'base_discount_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Canceled'
            ],
            'base_discount_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Invoiced'
            ],
            'base_discount_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Refunded'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ],
            'base_shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ],
            'base_shipping_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Canceled'
            ],
            'base_shipping_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Invoiced'
            ],
            'base_shipping_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Refunded'
            ],
            'base_shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ],
            'base_shipping_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Refunded'
            ],
            'base_subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ],
            'base_subtotal_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Canceled'
            ],
            'base_subtotal_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Invoiced'
            ],
            'base_subtotal_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Refunded'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ],
            'base_tax_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Canceled'
            ],
            'base_tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Invoiced'
            ],
            'base_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Refunded'
            ],
            'base_to_global_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ],
            'base_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ],
            'base_total_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Canceled'
            ],
            'base_total_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Invoiced'
            ],
            'base_total_invoiced_cost' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Invoiced Cost'
            ],
            'base_total_offline_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Offline Refunded'
            ],
            'base_total_online_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Online Refunded'
            ],
            'base_total_paid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Paid'
            ],
            'base_total_qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Qty Ordered'
            ],
            'base_total_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Refunded'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ],
            'discount_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Canceled'
            ],
            'discount_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Invoiced'
            ],
            'discount_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Refunded'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ],
            'shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ],
            'shipping_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Canceled'
            ],
            'shipping_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Invoiced'
            ],
            'shipping_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Refunded'
            ],
            'shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ],
            'shipping_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Refunded'
            ],
            'store_to_base_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ],
            'store_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ],
            'subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ],
            'subtotal_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Canceled'
            ],
            'subtotal_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Invoiced'
            ],
            'subtotal_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Refunded'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ],
            'tax_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Canceled'
            ],
            'tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Invoiced'
            ],
            'tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Refunded'
            ],
            'total_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Canceled'
            ],
            'total_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Invoiced'
            ],
            'total_offline_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Offline Refunded'
            ],
            'total_online_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Online Refunded'
            ],
            'total_paid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Paid'
            ],
            'total_qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty Ordered'
            ],
            'total_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Refunded'
            ],
            'can_ship_partially' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Can Ship Partially'
            ],
            'can_ship_partially_item' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Can Ship Partially Item'
            ],
            'customer_is_guest' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Customer Is Guest'
            ],
            'customer_note_notify' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Customer Note Notify'
            ],
            'billing_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment'   => 'Customer Group Id'
            ],
            'edit_increment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Edit Increment'
            ],
            'email_sent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ],
            'quote_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Address Id'
            ],
            'quote_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Id'
            ],
            'shipping_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ],
            'adjustment_negative' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Negative'
            ],
            'adjustment_positive' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Positive'
            ],
            'base_adjustment_negative' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Negative'
            ],
            'base_adjustment_positive' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Positive'
            ],
            'base_shipping_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Discount Amount'
            ],
            'base_subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Incl Tax'
            ],
            'base_total_due' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Due'
            ],
            'payment_authorization_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Payment Authorization Amount'
            ],
            'shipping_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Discount Amount'
            ],
            'subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ],
            'total_due' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Due'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weight'
            ],
            'customer_dob' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Customer Dob'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'applied_rule_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Applied Rule Ids'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ],
            'customer_email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Email'
            ],
            'customer_firstname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Firstname'
            ],
            'customer_lastname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Lastname'
            ],
            'customer_middlename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Middlename'
            ],
            'customer_prefix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Prefix'
            ],
            'customer_suffix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Suffix'
            ],
            'customer_taxvat' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Taxvat'
            ],
            'discount_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Discount Description'
            ],
            'ext_customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Ext Customer Id'
            ],
            'ext_order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Ext Order Id'
            ],
            'global_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ],
            'hold_before_state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Hold Before State'
            ],
            'hold_before_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Hold Before Status'
            ],
            'order_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Order Currency Code'
            ],
            'original_increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Original Increment Id'
            ],
            'relation_child_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Child Id'
            ],
            'relation_child_real_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Child Real Id'
            ],
            'relation_parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Parent Id'
            ],
            'relation_parent_real_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Parent Real Id'
            ],
            'remote_ip' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Remote Ip'
            ],
            'shipping_method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Method'
            ],
            'store_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ],
            'store_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Store Name'
            ],
            'x_forwarded_for' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'X Forwarded For'
            ],
            'customer_note' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Customer Note'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ],
            'total_item_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Total Item Count'
            ],
            'customer_gender' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Gender'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ],
            'shipping_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ],
            /* TODO: remove comment
            'base_shipping_hidden_tax_amnt' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Hidden Tax Amount'
            ),
            */
            'hidden_tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Invoiced'
            ],
            'base_hidden_tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Invoiced'
            ],
            'hidden_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Refunded'
            ],
            'base_hidden_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Refunded'
            ],
            'shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ],
            'base_shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            ]
        ],
        'comment' => 'Sales Flat Order'
    ],
    $installer->getTable('sales/order_grid') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Status'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'store_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Store Name'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ],
            'base_total_paid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Paid'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ],
            'total_paid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Paid'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ],
            'order_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Order Currency Code'
            ],
            'shipping_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Name'
            ],
            'billing_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Billing Name'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ]
        ],
        'comment' => 'Sales Flat Order Grid'
    ],
    $installer->getTable('sales/order_item') => [
        'columns' => [
            'item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item Id'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'parent_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Item Id'
            ],
            'quote_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Quote Item Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ],
            'product_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Type'
            ],
            'product_options' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Product Options'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ],
            'is_virtual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Virtual'
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'applied_rule_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Rule Ids'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'free_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Free Shipping'
            ],
            'is_qty_decimal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Qty Decimal'
            ],
            'no_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'No Discount'
            ],
            'qty_backordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Backordered'
            ],
            'qty_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Canceled'
            ],
            'qty_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Invoiced'
            ],
            'qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ],
            'qty_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Refunded'
            ],
            'qty_shipped' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Shipped'
            ],
            'base_cost' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Cost'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ],
            'base_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Price'
            ],
            'original_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Price'
            ],
            'base_original_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Original Price'
            ],
            'tax_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Percent'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ],
            'tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Invoiced'
            ],
            'base_tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Invoiced'
            ],
            'discount_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Percent'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ],
            'discount_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Invoiced'
            ],
            'base_discount_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Invoiced'
            ],
            'amount_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Amount Refunded'
            ],
            'base_amount_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Amount Refunded'
            ],
            'row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Total'
            ],
            'base_row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Total'
            ],
            'row_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Invoiced'
            ],
            'base_row_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Invoiced'
            ],
            'row_weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Weight'
            ],
            'base_tax_before_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Before Discount'
            ],
            'tax_before_discount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Before Discount'
            ],
            'ext_order_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Ext Order Item Id'
            ],
            'locked_do_invoice' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Locked Do Invoice'
            ],
            'locked_do_ship' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Locked Do Ship'
            ],
            'price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ],
            'base_price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ],
            'row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ],
            'base_row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ],
            'hidden_tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Invoiced'
            ],
            'base_hidden_tax_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Invoiced'
            ],
            'hidden_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Refunded'
            ],
            'base_hidden_tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Refunded'
            ],
            'is_nominal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Is Nominal'
            ],
            'tax_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Canceled'
            ],
            'hidden_tax_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Canceled'
            ],
            'tax_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Refunded'
            ]
        ],
        'comment' => 'Sales Flat Order Item'
    ],
    $installer->getTable('sales/order_address') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Id'
            ],
            'customer_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Address Id'
            ],
            'quote_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Address Id'
            ],
            'region_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Region Id'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Id'
            ],
            'fax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Fax'
            ],
            'region' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Region'
            ],
            'postcode' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Postcode'
            ],
            'lastname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Lastname'
            ],
            'street' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Street'
            ],
            'city' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'City'
            ],
            'email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Email'
            ],
            'telephone' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Telephone'
            ],
            'country_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'comment'   => 'Country Id'
            ],
            'firstname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Firstname'
            ],
            'address_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Address Type'
            ],
            'prefix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Prefix'
            ],
            'middlename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Middlename'
            ],
            'suffix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Suffix'
            ],
            'company' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Company'
            ]
        ],
        'comment' => 'Sales Flat Order Address'
    ],
    $installer->getTable('sales/order_payment') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'base_shipping_captured' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Captured'
            ],
            'shipping_captured' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Captured'
            ],
            'amount_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Refunded'
            ],
            'base_amount_paid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Paid'
            ],
            'amount_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Canceled'
            ],
            'base_amount_authorized' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Authorized'
            ],
            'base_amount_paid_online' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Paid Online'
            ],
            'base_amount_refunded_online' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Refunded Online'
            ],
            'base_shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ],
            'shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ],
            'amount_paid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Paid'
            ],
            'amount_authorized' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Authorized'
            ],
            'base_amount_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Ordered'
            ],
            'base_shipping_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Refunded'
            ],
            'shipping_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Refunded'
            ],
            'base_amount_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Refunded'
            ],
            'amount_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Ordered'
            ],
            'base_amount_canceled' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Canceled'
            ],
            'quote_payment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Payment Id'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'cc_exp_month' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Exp Month'
            ],
            'cc_ss_start_year' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Start Year'
            ],
            'echeck_bank_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Bank Name'
            ],
            'method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Method'
            ],
            'cc_debug_request_body' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Debug Request Body'
            ],
            'cc_secure_verify' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Secure Verify'
            ],
            'protection_eligibility' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Protection Eligibility'
            ],
            'cc_approval' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Approval'
            ],
            'cc_last4' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Last4'
            ],
            'cc_status_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Status Description'
            ],
            'echeck_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Type'
            ],
            'cc_debug_response_serialized' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Debug Response Serialized'
            ],
            'cc_ss_start_month' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Start Month'
            ],
            'echeck_account_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Account Type'
            ],
            'last_trans_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Last Trans Id'
            ],
            'cc_cid_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Cid Status'
            ],
            'cc_owner' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Owner'
            ],
            'cc_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Type'
            ],
            'po_number' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Po Number'
            ],
            'cc_exp_year' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Exp Year'
            ],
            'cc_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Status'
            ],
            'echeck_routing_number' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Routing Number'
            ],
            'account_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Account Status'
            ],
            'anet_trans_method' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Anet Trans Method'
            ],
            'cc_debug_response_body' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Debug Response Body'
            ],
            'cc_ss_issue' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Issue'
            ],
            'echeck_account_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Account Name'
            ],
            'cc_avs_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Avs Status'
            ],
            'cc_number_enc' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Number Enc'
            ],
            'cc_trans_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Trans Id'
            ],
            'paybox_request_number' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Paybox Request Number'
            ],
            'address_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Address Status'
            ],
            'additional_information' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Information'
            ]
        ],
        'comment' => 'Sales Flat Order Payment'
    ],
    $installer->getTable('sales/order_status_history') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'is_customer_notified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Is Customer Notified'
            ],
            'is_visible_on_front' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ],
            'comment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Status'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ]
        ],
        'comment' => 'Sales Flat Order Status History'
    ],
    $installer->getTable('sales/order_status') => [
        'columns' => [
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status'
            ],
            'label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'nullable'  => false,
                'comment'   => 'Label'
            ]
        ],
        'comment' => 'Sales Order Status Table'
    ],
    $installer->getTable('sales/order_status_state') => [
        'columns' => [
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Label'
            ],
            'is_default' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Default'
            ]
        ],
        'comment' => 'Sales Order Status Table'
    ],
    $installer->getTable('sales/order_status_label') => [
        'columns' => [
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store Id'
            ],
            'label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'nullable'  => false,
                'comment'   => 'Label'
            ]
        ],
        'comment' => 'Sales Order Status Label Table'
    ],
    $installer->getTable('sales/invoice') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ],
            'shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ],
            'store_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ],
            'base_shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ],
            'base_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ],
            'shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ],
            'subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ],
            'base_subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Incl Tax'
            ],
            'store_to_base_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ],
            'base_shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ],
            'total_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty'
            ],
            'base_to_global_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ],
            'subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ],
            'base_subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ],
            'billing_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ],
            'is_used_for_refund' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Used For Refund'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'email_sent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ],
            'can_void_flag' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Can Void Flag'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ],
            'shipping_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ],
            'store_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ],
            'transaction_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Transaction Id'
            ],
            'order_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ],
            'global_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ],
            'shipping_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ],
            /* TODO: remove comment
            'base_shipping_hidden_tax_amnt' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Hidden Tax Amount'
            ),
            */
            'shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ],
            'base_shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            ],
            'base_total_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Refunded'
            ]
        ],
        'comment' => 'Sales Flat Invoice'
    ],
    $installer->getTable('sales/invoice_grid') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ],
            'store_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ],
            'order_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ],
            'global_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'order_increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'order_created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Order Created At'
            ],
            'billing_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Billing Name'
            ]
        ],
        'comment' => 'Sales Flat Invoice Grid'
    ],
    $installer->getTable('sales/invoice_item') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'base_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ],
            'base_row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ],
            'row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ],
            'price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ],
            'base_price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ],
            'base_cost' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'base_row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ],
            'row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Product Id'
            ],
            'order_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Order Item Id'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ]
        ],
        'comment' => 'Sales Flat Invoice Item'
    ],
    $installer->getTable('sales/invoice_comment') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'is_customer_notified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Customer Notified'
            ],
            'is_visible_on_front' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ],
            'comment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ]
        ],
        'comment' => 'Sales Flat Invoice Comment'
    ],
    $installer->getTable('sales/shipment') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'total_weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Weight'
            ],
            'total_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty'
            ],
            'email_sent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Id'
            ],
            'shipping_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ],
            'billing_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ],
            'shipment_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipment Status'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ]
        ],
        'comment' => 'Sales Flat Shipment'
    ],
    $installer->getTable('sales/shipment_grid') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'total_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'shipment_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipment Status'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'order_increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'order_created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Order Created At'
            ],
            'shipping_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Name'
            ]
        ],
        'comment' => 'Sales Flat Shipment Grid'
    ],
    $installer->getTable('sales/shipment_item') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weight'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Product Id'
            ],
            'order_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Order Item Id'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ]
        ],
        'comment' => 'Sales Flat Shipment Item'
    ],
    $installer->getTable('sales/shipment_comment') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'is_customer_notified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Is Customer Notified'
            ],
            'is_visible_on_front' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ],
            'comment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ]
        ],
        'comment' => 'Sales Flat Shipment Comment'
    ],
    $installer->getTable('sales/shipment_track') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'weight' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weight'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Title'
            ],
            'carrier_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Carrier Code'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ]
        ],
        'comment' => 'Sales Flat Shipment Track'
    ],
    $installer->getTable('sales/creditmemo') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'adjustment_positive' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Positive'
            ],
            'base_shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ],
            'store_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ],
            'base_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ],
            'base_adjustment_negative' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Negative'
            ],
            'base_subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Incl Tax'
            ],
            'shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ],
            'subtotal_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ],
            'adjustment_negative' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Negative'
            ],
            'base_shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ],
            'store_to_base_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ],
            'base_to_global_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ],
            'base_adjustment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment'
            ],
            'base_subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ],
            'subtotal' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ],
            'adjustment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ],
            'base_adjustment_positive' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Positive'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ],
            'shipping_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'email_sent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ],
            'creditmemo_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Creditmemo Status'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ],
            'shipping_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ],
            'billing_address_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ],
            'invoice_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Invoice Id'
            ],
            'store_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ],
            'order_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ],
            'global_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ],
            'transaction_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Transaction Id'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ],
            'shipping_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ],
            /* TODO: remove comment
            'base_shipping_hidden_tax_amnt' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Hidden Tax Amount'
            ),
            */
            'shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ],
            'base_shipping_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            ]
        ],
        'comment' => 'Sales Flat Creditmemo'
    ],
    $installer->getTable('sales/creditmemo_grid') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'store_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ],
            'base_to_order_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ],
            'grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ],
            'store_to_base_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ],
            'base_to_global_rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ],
            'base_grand_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'creditmemo_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Creditmemo Status'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ],
            'invoice_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Invoice Id'
            ],
            'store_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ],
            'order_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ],
            'base_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ],
            'global_currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ],
            'order_increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment Id'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ],
            'order_created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Order Created At'
            ],
            'billing_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Billing Name'
            ]
        ],
        'comment' => 'Sales Flat Creditmemo Grid'
    ],
    $installer->getTable('sales/creditmemo_item') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'base_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ],
            'base_row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ],
            'row_total' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total'
            ],
            'base_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ],
            'price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ],
            'base_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ],
            'base_price_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ],
            'qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ],
            'base_cost' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'base_row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ],
            'row_total_incl_tax' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Product Id'
            ],
            'order_item_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Order Item Id'
            ],
            'additional_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'sku' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ],
            'base_hidden_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ]
        ],
        'comment' => 'Sales Flat Creditmemo Item'
    ],
    $installer->getTable('sales/creditmemo_comment') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'is_customer_notified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Is Customer Notified'
            ],
            'is_visible_on_front' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ],
            'comment' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ]
        ],
        'comment' => 'Sales Flat Creditmemo Comment'
    ],
    $installer->getTable('sales/recurring_profile') => [
        'columns' => [
            'profile_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Profile Id'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'State'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'method_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Method Code'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ],
            'reference_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Reference Id'
            ],
            'subscriber_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 150,
                'comment'   => 'Subscriber Name'
            ],
            'start_datetime' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Start Datetime'
            ],
            'internal_reference_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 42,
                'nullable'  => false,
                'comment'   => 'Internal Reference Id'
            ],
            'schedule_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Schedule Description'
            ],
            'suspension_threshold' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Suspension Threshold'
            ],
            'bill_failed_later' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Bill Failed Later'
            ],
            'period_unit' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Period Unit'
            ],
            'period_frequency' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Period Frequency'
            ],
            'period_max_cycles' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Period Max Cycles'
            ],
            'billing_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Billing Amount'
            ],
            'trial_period_unit' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Trial Period Unit'
            ],
            'trial_period_frequency' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Trial Period Frequency'
            ],
            'trial_period_max_cycles' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Trial Period Max Cycles'
            ],
            'trial_billing_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Trial Billing Amount'
            ],
            'currency_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'comment'   => 'Currency Code'
            ],
            'shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ],
            'tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ],
            'init_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Init Amount'
            ],
            'init_may_fail' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Init May Fail'
            ],
            'order_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Order Info'
            ],
            'order_item_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Order Item Info'
            ],
            'billing_address_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Billing Address Info'
            ],
            'shipping_address_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Shipping Address Info'
            ],
            'profile_vendor_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Profile Vendor Info'
            ],
            'additional_info' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Info'
            ]
        ],
        'comment' => 'Sales Recurring Profile'
    ],
    $installer->getTable('sales/recurring_profile_order') => [
        'columns' => [
            'link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link Id'
            ],
            'profile_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Profile Id'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ]
        ],
        'comment' => 'Sales Recurring Profile Order'
    ],
    $installer->getTable('sales/order_tax') => [
        'columns' => [
            'tax_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Id'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Code'
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Title'
            ],
            'percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Percent'
            ],
            'amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount'
            ],
            'priority' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Priority'
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Position'
            ],
            'base_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount'
            ],
            'process' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Process'
            ],
            'base_real_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Real Amount'
            ],
            'hidden' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Hidden'
            ]
        ],
        'comment' => 'Sales Order Tax Table'
    ],
    $installer->getTable('sales/order_aggregated_created') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'total_qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Qty Ordered'
            ],
            'total_qty_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Qty Invoiced'
            ],
            'total_income_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Income Amount'
            ],
            'total_revenue_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Revenue Amount'
            ],
            'total_profit_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Profit Amount'
            ],
            'total_invoiced_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Invoiced Amount'
            ],
            'total_canceled_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Canceled Amount'
            ],
            'total_paid_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Paid Amount'
            ],
            'total_refunded_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Refunded Amount'
            ],
            'total_tax_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Tax Amount'
            ],
            'total_tax_amount_actual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Tax Amount Actual'
            ],
            'total_shipping_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Shipping Amount'
            ],
            'total_shipping_amount_actual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Shipping Amount Actual'
            ],
            'total_discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Discount Amount'
            ],
            'total_discount_amount_actual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Discount Amount Actual'
            ]
        ],
        'comment' => 'Sales Order Aggregated Created'
    ],
    $installer->getTable('sales/shipping_aggregated') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ],
            'shipping_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'total_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping'
            ],
            'total_shipping_actual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping Actual'
            ]
        ],
        'comment' => 'Sales Shipping Aggregated'
    ],
    $installer->getTable('sales/shipping_aggregated_order') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ],
            'shipping_description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'total_shipping' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping'
            ],
            'total_shipping_actual' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping Actual'
            ]
        ],
        'comment' => 'Sales Shipping Aggregated Order'
    ],
    $installer->getTable('sales/invoiced_aggregated') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'orders_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Orders Invoiced'
            ],
            'invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced'
            ],
            'invoiced_captured' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Captured'
            ],
            'invoiced_not_captured' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Not Captured'
            ]
        ],
        'comment' => 'Sales Invoiced Aggregated'
    ],
    $installer->getTable('sales/invoiced_aggregated_order') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'orders_invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Orders Invoiced'
            ],
            'invoiced' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced'
            ],
            'invoiced_captured' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Captured'
            ],
            'invoiced_not_captured' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Not Captured'
            ]
        ],
        'comment' => 'Sales Invoiced Aggregated Order'
    ],
    $installer->getTable('sales/refunded_aggregated') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Refunded'
            ],
            'online_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Online Refunded'
            ],
            'offline_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Offline Refunded'
            ]
        ],
        'comment' => 'Sales Refunded Aggregated'
    ],
    $installer->getTable('sales/refunded_aggregated_order') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ],
            'refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Refunded'
            ],
            'online_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Online Refunded'
            ],
            'offline_refunded' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Offline Refunded'
            ]
        ],
        'comment' => 'Sales Refunded Aggregated Order'
    ],
    $installer->getTable('sales/payment_transaction') => [
        'columns' => [
            'transaction_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Transaction Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Id'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ],
            'payment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Payment Id'
            ],
            'txn_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'comment'   => 'Txn Id'
            ],
            'parent_txn_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'comment'   => 'Parent Txn Id'
            ],
            'txn_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 15,
                'comment'   => 'Txn Type'
            ],
            'is_closed' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Closed'
            ],
            'additional_information' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '64K',
                'comment'   => 'Additional Information'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ]
        ],
        'comment' => 'Sales Payment Transaction'
    ],
    $installer->getTable('sales/bestsellers_aggregated_daily') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ],
            'product_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Name'
            ],
            'product_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Product Price'
            ],
            'qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ],
            'rating_pos' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rating Pos'
            ]
        ],
        'comment' => 'Sales Bestsellers Aggregated Daily'
    ],
    $installer->getTable('sales/bestsellers_aggregated_monthly') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ],
            'product_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Name'
            ],
            'product_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Product Price'
            ],
            'qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ],
            'rating_pos' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rating Pos'
            ]
        ],
        'comment' => 'Sales Bestsellers Aggregated Monthly'
    ],
    $installer->getTable('sales/bestsellers_aggregated_yearly') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ],
            'product_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Name'
            ],
            'product_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Product Price'
            ],
            'qty_ordered' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ],
            'rating_pos' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rating Pos'
            ]
        ],
        'comment' => 'Sales Bestsellers Aggregated Yearly'
    ],
    $installer->getTable('sales/billing_agreement') => [
        'columns' => [
            'agreement_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Customer Id'
            ],
            'method_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Method Code'
            ],
            'reference_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Reference Id'
            ],
            'status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Status'
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'agreement_label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Agreement Label'
            ]
        ],
        'comment' => 'Sales Billing Agreement'
    ],
    $installer->getTable('sales/billing_agreement_order') => [
        'columns' => [
            'agreement_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ],
            'order_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Order Id'
            ]
        ],
        'comment' => 'Sales Billing Agreement Order'
    ]
];

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/creditmemo'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/invoice'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'forced_do_shipment_with_invoice',
    'forced_shipment_with_invoice',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'comment'   => 'Forced Do Shipment With Invoice'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'payment_authorization_expiration',
    'payment_auth_expiration',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Payment Authorization Expiration'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/quote_address'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/shipment_track'),
    'number',
    'track_number',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '64K',
        'comment'   => 'Number'
    ]
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    $installer->getIdxName(
        'sales/bestsellers_aggregated_daily',
        ['period', 'store_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    $installer->getIdxName('sales/bestsellers_aggregated_daily', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    $installer->getIdxName('sales/bestsellers_aggregated_daily', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    $installer->getIdxName(
        'sales/bestsellers_aggregated_monthly',
        ['period', 'store_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    $installer->getIdxName('sales/bestsellers_aggregated_monthly', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    $installer->getIdxName('sales/bestsellers_aggregated_monthly', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    $installer->getIdxName(
        'sales/bestsellers_aggregated_yearly',
        ['period', 'store_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    $installer->getIdxName('sales/bestsellers_aggregated_yearly', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    $installer->getIdxName('sales/bestsellers_aggregated_yearly', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement'),
    $installer->getIdxName('sales/billing_agreement', ['customer_id']),
    ['customer_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement'),
    $installer->getIdxName('sales/billing_agreement', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement_order'),
    'PRIMARY',
    ['agreement_id', 'order_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement_order'),
    $installer->getIdxName('sales/billing_agreement_order', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName(
        'sales/creditmemo',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', ['creditmemo_status']),
    ['creditmemo_status']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', ['state']),
    ['state']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_comment'),
    $installer->getIdxName('sales/creditmemo_comment', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_comment'),
    $installer->getIdxName('sales/creditmemo_comment', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName(
        'sales/creditmemo_grid',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['grand_total']),
    ['grand_total']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['base_grand_total']),
    ['base_grand_total']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['creditmemo_status']),
    ['creditmemo_status']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['state']),
    ['state']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['order_increment_id']),
    ['order_increment_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['order_created_at']),
    ['order_created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', ['billing_name']),
    ['billing_name']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_item'),
    $installer->getIdxName('sales/creditmemo_item', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName(
        'sales/invoice',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', ['grand_total']),
    ['grand_total']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', ['state']),
    ['state']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_comment'),
    $installer->getIdxName('sales/invoice_comment', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_comment'),
    $installer->getIdxName('sales/invoice_comment', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName(
        'sales/invoice_grid',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['grand_total']),
    ['grand_total']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['state']),
    ['state']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['order_increment_id']),
    ['order_increment_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['order_created_at']),
    ['order_created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', ['billing_name']),
    ['billing_name']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_item'),
    $installer->getIdxName('sales/invoice_item', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName(
        'sales/order',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['status']),
    ['status']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['state']),
    ['state']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['customer_id']),
    ['customer_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['ext_order_id']),
    ['ext_order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['quote_id']),
    ['quote_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', ['updated_at']),
    ['updated_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_address'),
    $installer->getIdxName('sales/order_address', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName(
        'sales/order_grid',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['status']),
    ['status']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['base_grand_total']),
    ['base_grand_total']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['base_total_paid']),
    ['base_total_paid']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['grand_total']),
    ['grand_total']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['total_paid']),
    ['total_paid']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['shipping_name']),
    ['shipping_name']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['billing_name']),
    ['billing_name']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['customer_id']),
    ['customer_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', ['updated_at']),
    ['updated_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_item'),
    $installer->getIdxName('sales/order_item', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_item'),
    $installer->getIdxName('sales/order_item', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_payment'),
    $installer->getIdxName('sales/order_payment', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_status_history'),
    $installer->getIdxName('sales/order_status_history', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_status_history'),
    $installer->getIdxName('sales/order_status_history', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote'),
    $installer->getIdxName('sales/quote', ['customer_id', 'store_id', 'is_active']),
    ['customer_id', 'store_id', 'is_active']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote'),
    $installer->getIdxName('sales/quote', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address'),
    $installer->getIdxName('sales/quote_address', ['quote_id']),
    ['quote_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_item'),
    $installer->getIdxName('sales/quote_address_item', ['quote_address_id']),
    ['quote_address_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_item'),
    $installer->getIdxName('sales/quote_address_item', ['parent_item_id']),
    ['parent_item_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_item'),
    $installer->getIdxName('sales/quote_address_item', ['quote_item_id']),
    ['quote_item_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', ['parent_item_id']),
    ['parent_item_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', ['quote_id']),
    ['quote_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item_option'),
    $installer->getIdxName('sales/quote_item_option', ['item_id']),
    ['item_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_payment'),
    $installer->getIdxName('sales/quote_payment', ['quote_id']),
    ['quote_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_shipping_rate'),
    $installer->getIdxName('sales/quote_address_shipping_rate', ['address_id']),
    ['address_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName(
        'sales/shipment',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', ['total_qty']),
    ['total_qty']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', ['updated_at']),
    ['updated_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_comment'),
    $installer->getIdxName('sales/shipment_comment', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_comment'),
    $installer->getIdxName('sales/shipment_comment', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName(
        'sales/shipment_grid',
        ['increment_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['increment_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['total_qty']),
    ['total_qty']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['shipment_status']),
    ['shipment_status']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['order_increment_id']),
    ['order_increment_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['order_created_at']),
    ['order_created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', ['shipping_name']),
    ['shipping_name']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_item'),
    $installer->getIdxName('sales/shipment_item', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_track'),
    $installer->getIdxName('sales/shipment_track', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_track'),
    $installer->getIdxName('sales/shipment_track', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_track'),
    $installer->getIdxName('sales/shipment_track', ['created_at']),
    ['created_at']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated'),
    $installer->getIdxName(
        'sales/invoiced_aggregated',
        ['period', 'store_id', 'order_status'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated'),
    $installer->getIdxName('sales/invoiced_aggregated', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated_order'),
    $installer->getIdxName(
        'sales/invoiced_aggregated_order',
        ['period', 'store_id', 'order_status'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated_order'),
    $installer->getIdxName('sales/invoiced_aggregated_order', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_aggregated_created'),
    $installer->getIdxName(
        'sales/order_aggregated_created',
        ['period', 'store_id', 'order_status'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_aggregated_created'),
    $installer->getIdxName('sales/order_aggregated_created', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_status_label'),
    $installer->getIdxName('sales/order_status_label', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_tax'),
    $installer->getIdxName('sales/order_tax', ['order_id', 'priority', 'position']),
    ['order_id', 'priority', 'position']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName(
        'sales/payment_transaction',
        ['order_id', 'payment_id', 'txn_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['order_id', 'payment_id', 'txn_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName('sales/payment_transaction', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName('sales/payment_transaction', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName('sales/payment_transaction', ['payment_id']),
    ['payment_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile'),
    $installer->getIdxName(
        'sales/recurring_profile',
        ['internal_reference_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['internal_reference_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile'),
    $installer->getIdxName('sales/recurring_profile', ['customer_id']),
    ['customer_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile'),
    $installer->getIdxName('sales/recurring_profile', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile_order'),
    $installer->getIdxName(
        'sales/recurring_profile_order',
        ['profile_id', 'order_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['profile_id', 'order_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile_order'),
    $installer->getIdxName('sales/recurring_profile_order', ['order_id']),
    ['order_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated'),
    $installer->getIdxName(
        'sales/refunded_aggregated',
        ['period', 'store_id', 'order_status'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated'),
    $installer->getIdxName('sales/refunded_aggregated', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated_order'),
    $installer->getIdxName(
        'sales/refunded_aggregated_order',
        ['period', 'store_id', 'order_status'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated_order'),
    $installer->getIdxName('sales/refunded_aggregated_order', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated'),
    $installer->getIdxName(
        'sales/shipping_aggregated',
        ['period', 'store_id', 'order_status', 'shipping_description'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status', 'shipping_description'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated'),
    $installer->getIdxName('sales/shipping_aggregated', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated_order'),
    $installer->getIdxName(
        'sales/shipping_aggregated_order',
        ['period', 'store_id', 'order_status', 'shipping_description'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'order_status', 'shipping_description'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated_order'),
    $installer->getIdxName('sales/shipping_aggregated_order', ['store_id']),
    ['store_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/bestsellers_aggregated_daily', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/bestsellers_aggregated_daily', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/bestsellers_aggregated_monthly', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/bestsellers_aggregated_monthly', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/bestsellers_aggregated_yearly', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/bestsellers_aggregated_yearly', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/billing_agreement', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/billing_agreement'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/billing_agreement', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/billing_agreement'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/billing_agreement_order', 'agreement_id', 'sales/billing_agreement', 'agreement_id'),
    $installer->getTable('sales/billing_agreement_order'),
    'agreement_id',
    $installer->getTable('sales/billing_agreement'),
    'agreement_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/billing_agreement_order', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/billing_agreement_order'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/creditmemo', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/creditmemo'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/creditmemo', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/creditmemo'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/creditmemo_comment', 'parent_id', 'sales/creditmemo', 'entity_id'),
    $installer->getTable('sales/creditmemo_comment'),
    'parent_id',
    $installer->getTable('sales/creditmemo'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/creditmemo_grid', 'entity_id', 'sales/creditmemo', 'entity_id'),
    $installer->getTable('sales/creditmemo_grid'),
    'entity_id',
    $installer->getTable('sales/creditmemo'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/creditmemo_grid', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/creditmemo_grid'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/creditmemo_item', 'parent_id', 'sales/creditmemo', 'entity_id'),
    $installer->getTable('sales/creditmemo_item'),
    'parent_id',
    $installer->getTable('sales/creditmemo'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoice', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/invoice'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoice', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/invoice'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoice_comment', 'parent_id', 'sales/invoice', 'entity_id'),
    $installer->getTable('sales/invoice_comment'),
    'parent_id',
    $installer->getTable('sales/invoice'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoice_grid', 'entity_id', 'sales/invoice', 'entity_id'),
    $installer->getTable('sales/invoice_grid'),
    'entity_id',
    $installer->getTable('sales/invoice'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoice_grid', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/invoice_grid'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoice_item', 'parent_id', 'sales/invoice', 'entity_id'),
    $installer->getTable('sales/invoice_item'),
    'parent_id',
    $installer->getTable('sales/invoice'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/order'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_address', 'parent_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/order_address'),
    'parent_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_grid', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/order_grid'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_grid', 'entity_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/order_grid'),
    'entity_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_grid', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/order_grid'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_item', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/order_item'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_item', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/order_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_payment', 'parent_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/order_payment'),
    'parent_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_status_history', 'parent_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/order_status_history'),
    'parent_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/quote'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_address', 'quote_id', 'sales/quote', 'entity_id'),
    $installer->getTable('sales/quote_address'),
    'quote_id',
    $installer->getTable('sales/quote'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_address_item', 'quote_item_id', 'sales/quote_item', 'item_id'),
    $installer->getTable('sales/quote_address_item'),
    'quote_item_id',
    $installer->getTable('sales/quote_item'),
    'item_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_address_item', 'parent_item_id', 'sales/quote_address_item', 'address_item_id'),
    $installer->getTable('sales/quote_address_item'),
    'parent_item_id',
    $installer->getTable('sales/quote_address_item'),
    'address_item_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_address_item', 'quote_address_id', 'sales/quote_address', 'address_id'),
    $installer->getTable('sales/quote_address_item'),
    'quote_address_id',
    $installer->getTable('sales/quote_address'),
    'address_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_item', 'parent_item_id', 'sales/quote_item', 'item_id'),
    $installer->getTable('sales/quote_item'),
    'parent_item_id',
    $installer->getTable('sales/quote_item'),
    'item_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_item', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('sales/quote_item'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_item', 'quote_id', 'sales/quote', 'entity_id'),
    $installer->getTable('sales/quote_item'),
    'quote_id',
    $installer->getTable('sales/quote'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_item', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/quote_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_item_option', 'item_id', 'sales/quote_item', 'item_id'),
    $installer->getTable('sales/quote_item_option'),
    'item_id',
    $installer->getTable('sales/quote_item'),
    'item_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_payment', 'quote_id', 'sales/quote', 'entity_id'),
    $installer->getTable('sales/quote_payment'),
    'quote_id',
    $installer->getTable('sales/quote'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/quote_address_shipping_rate', 'address_id', 'sales/quote_address', 'address_id'),
    $installer->getTable('sales/quote_address_shipping_rate'),
    'address_id',
    $installer->getTable('sales/quote_address'),
    'address_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/shipment'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/shipment'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment_comment', 'parent_id', 'sales/shipment', 'entity_id'),
    $installer->getTable('sales/shipment_comment'),
    'parent_id',
    $installer->getTable('sales/shipment'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment_grid', 'entity_id', 'sales/shipment', 'entity_id'),
    $installer->getTable('sales/shipment_grid'),
    'entity_id',
    $installer->getTable('sales/shipment'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment_grid', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/shipment_grid'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment_item', 'parent_id', 'sales/shipment', 'entity_id'),
    $installer->getTable('sales/shipment_item'),
    'parent_id',
    $installer->getTable('sales/shipment'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipment_track', 'parent_id', 'sales/shipment', 'entity_id'),
    $installer->getTable('sales/shipment_track'),
    'parent_id',
    $installer->getTable('sales/shipment'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoiced_aggregated', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/invoiced_aggregated'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/invoiced_aggregated_order', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/invoiced_aggregated_order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_aggregated_created', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/order_aggregated_created'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_status_label', 'status', 'sales/order_status', 'status'),
    $installer->getTable('sales/order_status_label'),
    'status',
    $installer->getTable('sales/order_status'),
    'status'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_status_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/order_status_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/order_status_state', 'status', 'sales/order_status', 'status'),
    $installer->getTable('sales/order_status_state'),
    'status',
    $installer->getTable('sales/order_status'),
    'status'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/payment_transaction', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/payment_transaction'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/payment_transaction', 'parent_id', 'sales/payment_transaction', 'transaction_id'),
    $installer->getTable('sales/payment_transaction'),
    'parent_id',
    $installer->getTable('sales/payment_transaction'),
    'transaction_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/payment_transaction', 'payment_id', 'sales/order_payment', 'entity_id'),
    $installer->getTable('sales/payment_transaction'),
    'payment_id',
    $installer->getTable('sales/order_payment'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/recurring_profile', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/recurring_profile'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/recurring_profile', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/recurring_profile'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/recurring_profile_order', 'order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/recurring_profile_order'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/recurring_profile_order', 'profile_id', 'sales/recurring_profile', 'profile_id'),
    $installer->getTable('sales/recurring_profile_order'),
    'profile_id',
    $installer->getTable('sales/recurring_profile'),
    'profile_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/refunded_aggregated', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/refunded_aggregated'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/refunded_aggregated_order', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/refunded_aggregated_order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipping_aggregated', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/shipping_aggregated'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('sales/shipping_aggregated_order', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sales/shipping_aggregated_order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->endSetup();
