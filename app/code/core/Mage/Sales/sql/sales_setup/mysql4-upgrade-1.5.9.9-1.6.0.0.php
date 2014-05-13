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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
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
$tables = array(
    $installer->getTable('sales/quote') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'converted_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Converted At'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '1',
                'comment'   => 'Is Active'
            ),
            'is_virtual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Virtual'
            ),
            'is_multi_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Multi Shipping'
            ),
            'items_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Items Count'
            ),
            'items_qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Items Qty'
            ),
            'orig_order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Orig Order Id'
            ),
            'store_to_base_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Store To Base Rate'
            ),
            'store_to_quote_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Store To Quote Rate'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Base Currency Code'
            ),
            'store_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Store Currency Code'
            ),
            'quote_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Quote Currency Code'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Grand Total'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Grand Total'
            ),
            'checkout_method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Checkout Method'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Id'
            ),
            'customer_tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Tax Class Id'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Group Id'
            ),
            'customer_email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Email'
            ),
            'customer_prefix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Customer Prefix'
            ),
            'customer_firstname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Firstname'
            ),
            'customer_middlename' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Customer Middlename'
            ),
            'customer_lastname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Lastname'
            ),
            'customer_suffix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Customer Suffix'
            ),
            'customer_dob' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Customer Dob'
            ),
            'customer_note' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Note'
            ),
            'customer_note_notify' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '1',
                'comment'   => 'Customer Note Notify'
            ),
            'customer_is_guest' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Customer Is Guest'
            ),
            'remote_ip' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Remote Ip'
            ),
            'applied_rule_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Applied Rule Ids'
            ),
            'reserved_order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Reserved Order Id'
            ),
            'password_hash' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Password Hash'
            ),
            'coupon_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Coupon Code'
            ),
            'global_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Global Currency Code'
            ),
            'base_to_global_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ),
            'base_to_quote_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Quote Rate'
            ),
            'customer_taxvat' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Taxvat'
            ),
            'customer_gender' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Gender'
            ),
            'subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ),
            'base_subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ),
            'subtotal_with_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal With Discount'
            ),
            'base_subtotal_with_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal With Discount'
            ),
            'is_changed' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Is Changed'
            ),
            'trigger_recollect' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Trigger Recollect'
            ),
            'ext_shipping_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Ext Shipping Info'
            )
        ),
        'comment' => 'Sales Flat Quote'
    ),
    $installer->getTable('sales/quote_item') => array(
        'columns' => array(
            'item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item Id'
            ),
            'quote_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'parent_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Item Id'
            ),
            'is_virtual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Virtual'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'applied_rule_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Rule Ids'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'free_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Free Shipping'
            ),
            'is_qty_decimal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Qty Decimal'
            ),
            'no_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'No Discount'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ),
            'base_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Price'
            ),
            'custom_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Custom Price'
            ),
            'discount_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Percent'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ),
            'tax_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Percent'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ),
            'row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Total'
            ),
            'base_row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Total'
            ),
            'row_total_with_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Total With Discount'
            ),
            'row_weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Weight'
            ),
            'product_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Type'
            ),
            'base_tax_before_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Before Discount'
            ),
            'tax_before_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Before Discount'
            ),
            'original_custom_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Custom Price'
            ),
            'redirect_url' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Redirect Url'
            ),
            'base_cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ),
            'price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ),
            'base_price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ),
            'row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ),
            'base_row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            )
        ),
        'comment' => 'Sales Flat Quote Item'
    ),
    $installer->getTable('sales/quote_address') => array(
        'columns' => array(
            'address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Address Id'
            ),
            'quote_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'save_in_address_book' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '0',
                'comment'   => 'Save In Address Book'
            ),
            'customer_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Address Id'
            ),
            'address_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Address Type'
            ),
            'email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Email'
            ),
            'prefix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Prefix'
            ),
            'firstname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Firstname'
            ),
            'middlename' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Middlename'
            ),
            'lastname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Lastname'
            ),
            'suffix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Suffix'
            ),
            'company' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Company'
            ),
            'street' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Street'
            ),
            'city' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'City'
            ),
            'region' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Region'
            ),
            'region_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Region Id'
            ),
            'postcode' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Postcode'
            ),
            'country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Country Id'
            ),
            'telephone' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Telephone'
            ),
            'fax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Fax'
            ),
            'same_as_billing' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Same As Billing'
            ),
            'free_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Free Shipping'
            ),
            'collect_shipping_rates' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Collect Shipping Rates'
            ),
            'shipping_method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Method'
            ),
            'shipping_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ),
            'subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal'
            ),
            'base_subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Subtotal'
            ),
            'subtotal_with_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal With Discount'
            ),
            'base_subtotal_with_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Subtotal With Discount'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ),
            'shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Shipping Amount'
            ),
            'base_shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Shipping Amount'
            ),
            'shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ),
            'base_shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Grand Total'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Grand Total'
            ),
            'customer_notes' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Customer Notes'
            ),
            'applied_taxes' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Taxes'
            ),
            'discount_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Discount Description'
            ),
            'shipping_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Discount Amount'
            ),
            'base_shipping_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Discount Amount'
            ),
            'subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ),
            'base_subtotal_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Total Incl Tax'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ),
            'shipping_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ),
            'shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ),
            'base_shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            )
        ),
        'comment' => 'Sales Flat Quote Address'
    ),
    $installer->getTable('sales/quote_address_item') => array(
        'columns' => array(
            'address_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Address Item Id'
            ),
            'parent_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Item Id'
            ),
            'quote_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Address Id'
            ),
            'quote_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Item Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'applied_rule_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Rule Ids'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ),
            'row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Total'
            ),
            'base_row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Total'
            ),
            'row_total_with_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Total With Discount'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ),
            'row_weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Weight'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ),
            'super_product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Super Product Id'
            ),
            'parent_product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Product Id'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ),
            'image' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Image'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'free_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Free Shipping'
            ),
            'is_qty_decimal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Is Qty Decimal'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'discount_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Percent'
            ),
            'no_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'No Discount'
            ),
            'tax_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Percent'
            ),
            'base_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price'
            ),
            'base_cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ),
            'price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ),
            'base_price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ),
            'row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ),
            'base_row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            )
        ),
        'comment' => 'Sales Flat Quote Address Item'
    ),
    $installer->getTable('sales/quote_item_option') => array(
        'columns' => array(
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id'
            ),
            'item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Item Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Sales Flat Quote Item Option'
    ),
    $installer->getTable('sales/quote_payment') => array(
        'columns' => array(
            'payment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Payment Id'
            ),
            'quote_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Quote Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Method'
            ),
            'cc_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Type'
            ),
            'cc_number_enc' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Number Enc'
            ),
            'cc_last4' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Last4'
            ),
            'cc_cid_enc' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Cid Enc'
            ),
            'cc_owner' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Owner'
            ),
            'cc_exp_month' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Exp Month'
            ),
            'cc_exp_year' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Exp Year'
            ),
            'cc_ss_owner' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Owner'
            ),
            'cc_ss_start_month' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Ss Start Month'
            ),
            'cc_ss_start_year' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Cc Ss Start Year'
            ),
            'po_number' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Po Number'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'cc_ss_issue' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Issue'
            ),
            'additional_information' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Information'
            )
        ),
        'comment' => 'Sales Flat Quote Payment'
    ),
    $installer->getTable('sales/quote_address_shipping_rate') => array(
        'columns' => array(
            'rate_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rate Id'
            ),
            'address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Address Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'carrier' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Carrier'
            ),
            'carrier_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Carrier Title'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Code'
            ),
            'method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Method'
            ),
            'method_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Method Description'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ),
            'error_message' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Error Message'
            ),
            'method_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Method Title'
            )
        ),
        'comment' => 'Sales Flat Quote Shipping Rate'
    ),
    $installer->getTable('sales/order') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'State'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Status'
            ),
            'coupon_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Coupon Code'
            ),
            'protect_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Protect Code'
            ),
            'shipping_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ),
            'is_virtual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Virtual'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ),
            'base_discount_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Canceled'
            ),
            'base_discount_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Invoiced'
            ),
            'base_discount_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Refunded'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ),
            'base_shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ),
            'base_shipping_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Canceled'
            ),
            'base_shipping_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Invoiced'
            ),
            'base_shipping_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Refunded'
            ),
            'base_shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ),
            'base_shipping_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Refunded'
            ),
            'base_subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ),
            'base_subtotal_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Canceled'
            ),
            'base_subtotal_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Invoiced'
            ),
            'base_subtotal_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Refunded'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ),
            'base_tax_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Canceled'
            ),
            'base_tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Invoiced'
            ),
            'base_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Refunded'
            ),
            'base_to_global_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ),
            'base_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ),
            'base_total_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Canceled'
            ),
            'base_total_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Invoiced'
            ),
            'base_total_invoiced_cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Invoiced Cost'
            ),
            'base_total_offline_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Offline Refunded'
            ),
            'base_total_online_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Online Refunded'
            ),
            'base_total_paid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Paid'
            ),
            'base_total_qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Qty Ordered'
            ),
            'base_total_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Refunded'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ),
            'discount_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Canceled'
            ),
            'discount_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Invoiced'
            ),
            'discount_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Refunded'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ),
            'shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ),
            'shipping_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Canceled'
            ),
            'shipping_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Invoiced'
            ),
            'shipping_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Refunded'
            ),
            'shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ),
            'shipping_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Refunded'
            ),
            'store_to_base_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ),
            'store_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ),
            'subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ),
            'subtotal_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Canceled'
            ),
            'subtotal_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Invoiced'
            ),
            'subtotal_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Refunded'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ),
            'tax_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Canceled'
            ),
            'tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Invoiced'
            ),
            'tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Refunded'
            ),
            'total_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Canceled'
            ),
            'total_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Invoiced'
            ),
            'total_offline_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Offline Refunded'
            ),
            'total_online_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Online Refunded'
            ),
            'total_paid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Paid'
            ),
            'total_qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty Ordered'
            ),
            'total_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Refunded'
            ),
            'can_ship_partially' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Can Ship Partially'
            ),
            'can_ship_partially_item' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Can Ship Partially Item'
            ),
            'customer_is_guest' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Customer Is Guest'
            ),
            'customer_note_notify' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Customer Note Notify'
            ),
            'billing_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment'   => 'Customer Group Id'
            ),
            'edit_increment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Edit Increment'
            ),
            'email_sent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ),
            'quote_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Address Id'
            ),
            'quote_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Id'
            ),
            'shipping_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ),
            'adjustment_negative' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Negative'
            ),
            'adjustment_positive' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Positive'
            ),
            'base_adjustment_negative' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Negative'
            ),
            'base_adjustment_positive' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Positive'
            ),
            'base_shipping_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Discount Amount'
            ),
            'base_subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Incl Tax'
            ),
            'base_total_due' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Due'
            ),
            'payment_authorization_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Payment Authorization Amount'
            ),
            'shipping_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Discount Amount'
            ),
            'subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ),
            'total_due' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Due'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weight'
            ),
            'customer_dob' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Customer Dob'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'applied_rule_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Applied Rule Ids'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ),
            'customer_email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Email'
            ),
            'customer_firstname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Firstname'
            ),
            'customer_lastname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Lastname'
            ),
            'customer_middlename' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Middlename'
            ),
            'customer_prefix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Prefix'
            ),
            'customer_suffix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Suffix'
            ),
            'customer_taxvat' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Customer Taxvat'
            ),
            'discount_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Discount Description'
            ),
            'ext_customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Ext Customer Id'
            ),
            'ext_order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Ext Order Id'
            ),
            'global_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ),
            'hold_before_state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Hold Before State'
            ),
            'hold_before_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Hold Before Status'
            ),
            'order_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Order Currency Code'
            ),
            'original_increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Original Increment Id'
            ),
            'relation_child_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Child Id'
            ),
            'relation_child_real_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Child Real Id'
            ),
            'relation_parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Parent Id'
            ),
            'relation_parent_real_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Relation Parent Real Id'
            ),
            'remote_ip' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Remote Ip'
            ),
            'shipping_method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Method'
            ),
            'store_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ),
            'store_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Store Name'
            ),
            'x_forwarded_for' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'X Forwarded For'
            ),
            'customer_note' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Customer Note'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ),
            'total_item_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Total Item Count'
            ),
            'customer_gender' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Gender'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ),
            'shipping_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ),
            /* TODO: remove comment
            'base_shipping_hidden_tax_amnt' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Hidden Tax Amount'
            ),
            */
            'hidden_tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Invoiced'
            ),
            'base_hidden_tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Invoiced'
            ),
            'hidden_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Refunded'
            ),
            'base_hidden_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Refunded'
            ),
            'shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ),
            'base_shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            )
        ),
        'comment' => 'Sales Flat Order'
    ),
    $installer->getTable('sales/order_grid') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Status'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'store_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Store Name'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ),
            'base_total_paid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Paid'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ),
            'total_paid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Paid'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ),
            'order_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Order Currency Code'
            ),
            'shipping_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Name'
            ),
            'billing_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Billing Name'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            )
        ),
        'comment' => 'Sales Flat Order Grid'
    ),
    $installer->getTable('sales/order_item') => array(
        'columns' => array(
            'item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item Id'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'parent_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Item Id'
            ),
            'quote_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Quote Item Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ),
            'product_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Type'
            ),
            'product_options' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Product Options'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Weight'
            ),
            'is_virtual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Virtual'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'applied_rule_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Applied Rule Ids'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'free_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Free Shipping'
            ),
            'is_qty_decimal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Qty Decimal'
            ),
            'no_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'No Discount'
            ),
            'qty_backordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Backordered'
            ),
            'qty_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Canceled'
            ),
            'qty_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Invoiced'
            ),
            'qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ),
            'qty_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Refunded'
            ),
            'qty_shipped' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Qty Shipped'
            ),
            'base_cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Cost'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ),
            'base_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Price'
            ),
            'original_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Price'
            ),
            'base_original_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Original Price'
            ),
            'tax_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Percent'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Amount'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Amount'
            ),
            'tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Tax Invoiced'
            ),
            'base_tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Tax Invoiced'
            ),
            'discount_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Percent'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Amount'
            ),
            'discount_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Discount Invoiced'
            ),
            'base_discount_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Discount Invoiced'
            ),
            'amount_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Amount Refunded'
            ),
            'base_amount_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Base Amount Refunded'
            ),
            'row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Total'
            ),
            'base_row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Total'
            ),
            'row_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Row Invoiced'
            ),
            'base_row_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Base Row Invoiced'
            ),
            'row_weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'default'   => '0.0000',
                'comment'   => 'Row Weight'
            ),
            'base_tax_before_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Before Discount'
            ),
            'tax_before_discount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Before Discount'
            ),
            'ext_order_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Ext Order Item Id'
            ),
            'locked_do_invoice' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Locked Do Invoice'
            ),
            'locked_do_ship' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Locked Do Ship'
            ),
            'price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ),
            'base_price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ),
            'row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ),
            'base_row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ),
            'hidden_tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Invoiced'
            ),
            'base_hidden_tax_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Invoiced'
            ),
            'hidden_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Refunded'
            ),
            'base_hidden_tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Refunded'
            ),
            'is_nominal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Is Nominal'
            ),
            'tax_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Canceled'
            ),
            'hidden_tax_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Canceled'
            ),
            'tax_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Refunded'
            )
        ),
        'comment' => 'Sales Flat Order Item'
    ),
    $installer->getTable('sales/order_address') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Id'
            ),
            'customer_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Address Id'
            ),
            'quote_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Address Id'
            ),
            'region_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Region Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Id'
            ),
            'fax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Fax'
            ),
            'region' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Region'
            ),
            'postcode' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Postcode'
            ),
            'lastname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Lastname'
            ),
            'street' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Street'
            ),
            'city' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'City'
            ),
            'email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Email'
            ),
            'telephone' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Telephone'
            ),
            'country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'comment'   => 'Country Id'
            ),
            'firstname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Firstname'
            ),
            'address_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Address Type'
            ),
            'prefix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Prefix'
            ),
            'middlename' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Middlename'
            ),
            'suffix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Suffix'
            ),
            'company' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Company'
            )
        ),
        'comment' => 'Sales Flat Order Address'
    ),
    $installer->getTable('sales/order_payment') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'base_shipping_captured' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Captured'
            ),
            'shipping_captured' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Captured'
            ),
            'amount_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Refunded'
            ),
            'base_amount_paid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Paid'
            ),
            'amount_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Canceled'
            ),
            'base_amount_authorized' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Authorized'
            ),
            'base_amount_paid_online' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Paid Online'
            ),
            'base_amount_refunded_online' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Refunded Online'
            ),
            'base_shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ),
            'shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ),
            'amount_paid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Paid'
            ),
            'amount_authorized' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Authorized'
            ),
            'base_amount_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Ordered'
            ),
            'base_shipping_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Refunded'
            ),
            'shipping_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Refunded'
            ),
            'base_amount_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Refunded'
            ),
            'amount_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount Ordered'
            ),
            'base_amount_canceled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount Canceled'
            ),
            'quote_payment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Quote Payment Id'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'cc_exp_month' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Exp Month'
            ),
            'cc_ss_start_year' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Start Year'
            ),
            'echeck_bank_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Bank Name'
            ),
            'method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Method'
            ),
            'cc_debug_request_body' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Debug Request Body'
            ),
            'cc_secure_verify' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Secure Verify'
            ),
            'protection_eligibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Protection Eligibility'
            ),
            'cc_approval' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Approval'
            ),
            'cc_last4' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Last4'
            ),
            'cc_status_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Status Description'
            ),
            'echeck_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Type'
            ),
            'cc_debug_response_serialized' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Debug Response Serialized'
            ),
            'cc_ss_start_month' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Start Month'
            ),
            'echeck_account_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Account Type'
            ),
            'last_trans_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Last Trans Id'
            ),
            'cc_cid_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Cid Status'
            ),
            'cc_owner' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Owner'
            ),
            'cc_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Type'
            ),
            'po_number' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Po Number'
            ),
            'cc_exp_year' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Exp Year'
            ),
            'cc_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Status'
            ),
            'echeck_routing_number' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Routing Number'
            ),
            'account_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Account Status'
            ),
            'anet_trans_method' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Anet Trans Method'
            ),
            'cc_debug_response_body' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Debug Response Body'
            ),
            'cc_ss_issue' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Ss Issue'
            ),
            'echeck_account_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Echeck Account Name'
            ),
            'cc_avs_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Avs Status'
            ),
            'cc_number_enc' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Number Enc'
            ),
            'cc_trans_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Cc Trans Id'
            ),
            'paybox_request_number' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Paybox Request Number'
            ),
            'address_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Address Status'
            ),
            'additional_information' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Information'
            )
        ),
        'comment' => 'Sales Flat Order Payment'
    ),
    $installer->getTable('sales/order_status_history') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'is_customer_notified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Is Customer Notified'
            ),
            'is_visible_on_front' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ),
            'comment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Status'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Sales Flat Order Status History'
    ),
    $installer->getTable('sales/order_status') => array(
        'columns' => array(
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status'
            ),
            'label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'nullable'  => false,
                'comment'   => 'Label'
            )
        ),
        'comment' => 'Sales Order Status Table'
    ),
    $installer->getTable('sales/order_status_state') => array(
        'columns' => array(
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Label'
            ),
            'is_default' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Default'
            )
        ),
        'comment' => 'Sales Order Status Table'
    ),
    $installer->getTable('sales/order_status_label') => array(
        'columns' => array(
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store Id'
            ),
            'label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'nullable'  => false,
                'comment'   => 'Label'
            )
        ),
        'comment' => 'Sales Order Status Label Table'
    ),
    $installer->getTable('sales/invoice') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ),
            'shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ),
            'store_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ),
            'base_shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ),
            'base_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ),
            'shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ),
            'subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ),
            'base_subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Incl Tax'
            ),
            'store_to_base_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ),
            'base_shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ),
            'total_qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty'
            ),
            'base_to_global_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ),
            'subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ),
            'base_subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ),
            'billing_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ),
            'is_used_for_refund' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Used For Refund'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'email_sent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ),
            'can_void_flag' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Can Void Flag'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ),
            'shipping_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ),
            'store_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ),
            'transaction_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Transaction Id'
            ),
            'order_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ),
            'global_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ),
            'shipping_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ),
            /* TODO: remove comment
            'base_shipping_hidden_tax_amnt' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Hidden Tax Amount'
            ),
            */
            'shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ),
            'base_shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            ),
            'base_total_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Total Refunded'
            )
        ),
        'comment' => 'Sales Flat Invoice'
    ),
    $installer->getTable('sales/invoice_grid') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ),
            'store_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ),
            'order_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ),
            'global_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'order_increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'order_created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Order Created At'
            ),
            'billing_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Billing Name'
            )
        ),
        'comment' => 'Sales Flat Invoice Grid'
    ),
    $installer->getTable('sales/invoice_item') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'base_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ),
            'base_row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ),
            'row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ),
            'price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ),
            'base_price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ),
            'base_cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'base_row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ),
            'row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Product Id'
            ),
            'order_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Order Item Id'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            )
        ),
        'comment' => 'Sales Flat Invoice Item'
    ),
    $installer->getTable('sales/invoice_comment') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'is_customer_notified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Customer Notified'
            ),
            'is_visible_on_front' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ),
            'comment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Sales Flat Invoice Comment'
    ),
    $installer->getTable('sales/shipment') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'total_weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Weight'
            ),
            'total_qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty'
            ),
            'email_sent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Customer Id'
            ),
            'shipping_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ),
            'billing_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ),
            'shipment_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipment Status'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            )
        ),
        'comment' => 'Sales Flat Shipment'
    ),
    $installer->getTable('sales/shipment_grid') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'total_qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Qty'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'shipment_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipment Status'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'order_increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'order_created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Order Created At'
            ),
            'shipping_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Name'
            )
        ),
        'comment' => 'Sales Flat Shipment Grid'
    ),
    $installer->getTable('sales/shipment_item') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weight'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Product Id'
            ),
            'order_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Order Item Id'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            )
        ),
        'comment' => 'Sales Flat Shipment Item'
    ),
    $installer->getTable('sales/shipment_comment') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'is_customer_notified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Is Customer Notified'
            ),
            'is_visible_on_front' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ),
            'comment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Sales Flat Shipment Comment'
    ),
    $installer->getTable('sales/shipment_track') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'weight' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weight'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Title'
            ),
            'carrier_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Carrier Code'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            )
        ),
        'comment' => 'Sales Flat Shipment Track'
    ),
    $installer->getTable('sales/creditmemo') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'adjustment_positive' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Positive'
            ),
            'base_shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Tax Amount'
            ),
            'store_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ),
            'base_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ),
            'base_adjustment_negative' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Negative'
            ),
            'base_subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal Incl Tax'
            ),
            'shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ),
            'subtotal_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal Incl Tax'
            ),
            'adjustment_negative' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment Negative'
            ),
            'base_shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Amount'
            ),
            'store_to_base_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ),
            'base_to_global_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ),
            'base_adjustment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment'
            ),
            'base_subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Subtotal'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ),
            'subtotal' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Subtotal'
            ),
            'adjustment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Adjustment'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ),
            'base_adjustment_positive' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Adjustment Positive'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ),
            'shipping_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Tax Amount'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'email_sent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Email Sent'
            ),
            'creditmemo_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Creditmemo Status'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ),
            'shipping_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Shipping Address Id'
            ),
            'billing_address_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Billing Address Id'
            ),
            'invoice_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Invoice Id'
            ),
            'store_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ),
            'order_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ),
            'global_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ),
            'transaction_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Transaction Id'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            ),
            'shipping_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Hidden Tax Amount'
            ),
            /* TODO: remove comment
            'base_shipping_hidden_tax_amnt' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Hidden Tax Amount'
            ),
            */
            'shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Incl Tax'
            ),
            'base_shipping_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Shipping Incl Tax'
            )
        ),
        'comment' => 'Sales Flat Creditmemo'
    ),
    $installer->getTable('sales/creditmemo_grid') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'store_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Order Rate'
            ),
            'base_to_order_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Order Rate'
            ),
            'grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Grand Total'
            ),
            'store_to_base_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Store To Base Rate'
            ),
            'base_to_global_rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base To Global Rate'
            ),
            'base_grand_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Grand Total'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'creditmemo_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Creditmemo Status'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'State'
            ),
            'invoice_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Invoice Id'
            ),
            'store_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Store Currency Code'
            ),
            'order_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Order Currency Code'
            ),
            'base_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Base Currency Code'
            ),
            'global_currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'comment'   => 'Global Currency Code'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Increment Id'
            ),
            'order_increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Increment Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            ),
            'order_created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Order Created At'
            ),
            'billing_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Billing Name'
            )
        ),
        'comment' => 'Sales Flat Creditmemo Grid'
    ),
    $installer->getTable('sales/creditmemo_item') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'base_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ),
            'base_row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Amount'
            ),
            'row_total' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total'
            ),
            'base_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Discount Amount'
            ),
            'price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price Incl Tax'
            ),
            'base_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tax Amount'
            ),
            'base_price_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Price Incl Tax'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Qty'
            ),
            'base_cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Cost'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'base_row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Row Total Incl Tax'
            ),
            'row_total_incl_tax' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Row Total Incl Tax'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Product Id'
            ),
            'order_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Order Item Id'
            ),
            'additional_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Data'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sku'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Hidden Tax Amount'
            ),
            'base_hidden_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Hidden Tax Amount'
            )
        ),
        'comment' => 'Sales Flat Creditmemo Item'
    ),
    $installer->getTable('sales/creditmemo_comment') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ),
            'is_customer_notified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Is Customer Notified'
            ),
            'is_visible_on_front' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Is Visible On Front'
            ),
            'comment' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Comment'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Sales Flat Creditmemo Comment'
    ),
    $installer->getTable('sales/recurring_profile') => array(
        'columns' => array(
            'profile_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Profile Id'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'State'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'method_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Method Code'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ),
            'reference_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Reference Id'
            ),
            'subscriber_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 150,
                'comment'   => 'Subscriber Name'
            ),
            'start_datetime' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Start Datetime'
            ),
            'internal_reference_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 42,
                'nullable'  => false,
                'comment'   => 'Internal Reference Id'
            ),
            'schedule_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Schedule Description'
            ),
            'suspension_threshold' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Suspension Threshold'
            ),
            'bill_failed_later' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Bill Failed Later'
            ),
            'period_unit' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Period Unit'
            ),
            'period_frequency' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Period Frequency'
            ),
            'period_max_cycles' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Period Max Cycles'
            ),
            'billing_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Billing Amount'
            ),
            'trial_period_unit' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Trial Period Unit'
            ),
            'trial_period_frequency' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Trial Period Frequency'
            ),
            'trial_period_max_cycles' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Trial Period Max Cycles'
            ),
            'trial_billing_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Trial Billing Amount'
            ),
            'currency_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'comment'   => 'Currency Code'
            ),
            'shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Shipping Amount'
            ),
            'tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tax Amount'
            ),
            'init_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Init Amount'
            ),
            'init_may_fail' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Init May Fail'
            ),
            'order_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Order Info'
            ),
            'order_item_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Order Item Info'
            ),
            'billing_address_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Billing Address Info'
            ),
            'shipping_address_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Shipping Address Info'
            ),
            'profile_vendor_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Profile Vendor Info'
            ),
            'additional_info' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Additional Info'
            )
        ),
        'comment' => 'Sales Recurring Profile'
    ),
    $installer->getTable('sales/recurring_profile_order') => array(
        'columns' => array(
            'link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link Id'
            ),
            'profile_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Profile Id'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            )
        ),
        'comment' => 'Sales Recurring Profile Order'
    ),
    $installer->getTable('sales/order_tax') => array(
        'columns' => array(
            'tax_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Id'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Code'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Title'
            ),
            'percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Percent'
            ),
            'amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Amount'
            ),
            'priority' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Priority'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Position'
            ),
            'base_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Amount'
            ),
            'process' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Process'
            ),
            'base_real_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Real Amount'
            ),
            'hidden' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Hidden'
            )
        ),
        'comment' => 'Sales Order Tax Table'
    ),
    $installer->getTable('sales/order_aggregated_created') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'total_qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Qty Ordered'
            ),
            'total_qty_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Qty Invoiced'
            ),
            'total_income_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Income Amount'
            ),
            'total_revenue_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Revenue Amount'
            ),
            'total_profit_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Profit Amount'
            ),
            'total_invoiced_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Invoiced Amount'
            ),
            'total_canceled_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Canceled Amount'
            ),
            'total_paid_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Paid Amount'
            ),
            'total_refunded_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Refunded Amount'
            ),
            'total_tax_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Tax Amount'
            ),
            'total_tax_amount_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Tax Amount Actual'
            ),
            'total_shipping_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Shipping Amount'
            ),
            'total_shipping_amount_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Shipping Amount Actual'
            ),
            'total_discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Discount Amount'
            ),
            'total_discount_amount_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Discount Amount Actual'
            )
        ),
        'comment' => 'Sales Order Aggregated Created'
    ),
    $installer->getTable('sales/shipping_aggregated') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ),
            'shipping_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'total_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping'
            ),
            'total_shipping_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping Actual'
            )
        ),
        'comment' => 'Sales Shipping Aggregated'
    ),
    $installer->getTable('sales/shipping_aggregated_order') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ),
            'shipping_description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Shipping Description'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'total_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping'
            ),
            'total_shipping_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Total Shipping Actual'
            )
        ),
        'comment' => 'Sales Shipping Aggregated Order'
    ),
    $installer->getTable('sales/invoiced_aggregated') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'orders_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Orders Invoiced'
            ),
            'invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced'
            ),
            'invoiced_captured' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Captured'
            ),
            'invoiced_not_captured' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Not Captured'
            )
        ),
        'comment' => 'Sales Invoiced Aggregated'
    ),
    $installer->getTable('sales/invoiced_aggregated_order') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'orders_invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Orders Invoiced'
            ),
            'invoiced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced'
            ),
            'invoiced_captured' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Captured'
            ),
            'invoiced_not_captured' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Invoiced Not Captured'
            )
        ),
        'comment' => 'Sales Invoiced Aggregated Order'
    ),
    $installer->getTable('sales/refunded_aggregated') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Refunded'
            ),
            'online_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Online Refunded'
            ),
            'offline_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Offline Refunded'
            )
        ),
        'comment' => 'Sales Refunded Aggregated'
    ),
    $installer->getTable('sales/refunded_aggregated_order') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Orders Count'
            ),
            'refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Refunded'
            ),
            'online_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Online Refunded'
            ),
            'offline_refunded' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Offline Refunded'
            )
        ),
        'comment' => 'Sales Refunded Aggregated Order'
    ),
    $installer->getTable('sales/payment_transaction') => array(
        'columns' => array(
            'transaction_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Transaction Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Parent Id'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Order Id'
            ),
            'payment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Payment Id'
            ),
            'txn_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'comment'   => 'Txn Id'
            ),
            'parent_txn_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'comment'   => 'Parent Txn Id'
            ),
            'txn_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 15,
                'comment'   => 'Txn Type'
            ),
            'is_closed' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Closed'
            ),
            'additional_information' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '64K',
                'comment'   => 'Additional Information'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Sales Payment Transaction'
    ),
    $installer->getTable('sales/bestsellers_aggregated_daily') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ),
            'product_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Name'
            ),
            'product_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Product Price'
            ),
            'qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ),
            'rating_pos' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rating Pos'
            )
        ),
        'comment' => 'Sales Bestsellers Aggregated Daily'
    ),
    $installer->getTable('sales/bestsellers_aggregated_monthly') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ),
            'product_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Name'
            ),
            'product_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Product Price'
            ),
            'qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ),
            'rating_pos' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rating Pos'
            )
        ),
        'comment' => 'Sales Bestsellers Aggregated Monthly'
    ),
    $installer->getTable('sales/bestsellers_aggregated_yearly') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Product Id'
            ),
            'product_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Product Name'
            ),
            'product_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Product Price'
            ),
            'qty_ordered' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Qty Ordered'
            ),
            'rating_pos' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rating Pos'
            )
        ),
        'comment' => 'Sales Bestsellers Aggregated Yearly'
    ),
    $installer->getTable('sales/billing_agreement') => array(
        'columns' => array(
            'agreement_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Customer Id'
            ),
            'method_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Method Code'
            ),
            'reference_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Reference Id'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Status'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Updated At'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'agreement_label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Agreement Label'
            )
        ),
        'comment' => 'Sales Billing Agreement'
    ),
    $installer->getTable('sales/billing_agreement_order') => array(
        'columns' => array(
            'agreement_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ),
            'order_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Order Id'
            )
        ),
        'comment' => 'Sales Billing Agreement Order'
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/creditmemo'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/invoice'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'forced_do_shipment_with_invoice',
    'forced_shipment_with_invoice',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'comment'   => 'Forced Do Shipment With Invoice'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'payment_authorization_expiration',
    'payment_auth_expiration',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Payment Authorization Expiration'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/quote_address'),
    'base_shipping_hidden_tax_amount',
    'base_shipping_hidden_tax_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Shipping Hidden Tax Amount'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/shipment_track'),
    'number',
    'track_number',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '64K',
        'comment'   => 'Number'
    )
);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    $installer->getIdxName(
        'sales/bestsellers_aggregated_daily',
        array('period', 'store_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    $installer->getIdxName('sales/bestsellers_aggregated_daily', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_daily'),
    $installer->getIdxName('sales/bestsellers_aggregated_daily', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    $installer->getIdxName(
        'sales/bestsellers_aggregated_monthly',
        array('period', 'store_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    $installer->getIdxName('sales/bestsellers_aggregated_monthly', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_monthly'),
    $installer->getIdxName('sales/bestsellers_aggregated_monthly', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    $installer->getIdxName(
        'sales/bestsellers_aggregated_yearly',
        array('period', 'store_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    $installer->getIdxName('sales/bestsellers_aggregated_yearly', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/bestsellers_aggregated_yearly'),
    $installer->getIdxName('sales/bestsellers_aggregated_yearly', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement'),
    $installer->getIdxName('sales/billing_agreement', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement'),
    $installer->getIdxName('sales/billing_agreement', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement_order'),
    'PRIMARY',
    array('agreement_id', 'order_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/billing_agreement_order'),
    $installer->getIdxName('sales/billing_agreement_order', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName(
        'sales/creditmemo',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', array('creditmemo_status')),
    array('creditmemo_status')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', array('state')),
    array('state')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo'),
    $installer->getIdxName('sales/creditmemo', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_comment'),
    $installer->getIdxName('sales/creditmemo_comment', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_comment'),
    $installer->getIdxName('sales/creditmemo_comment', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName(
        'sales/creditmemo_grid',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('grand_total')),
    array('grand_total')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('base_grand_total')),
    array('base_grand_total')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('creditmemo_status')),
    array('creditmemo_status')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('state')),
    array('state')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('order_increment_id')),
    array('order_increment_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('order_created_at')),
    array('order_created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getIdxName('sales/creditmemo_grid', array('billing_name')),
    array('billing_name')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/creditmemo_item'),
    $installer->getIdxName('sales/creditmemo_item', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName(
        'sales/invoice',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', array('grand_total')),
    array('grand_total')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', array('state')),
    array('state')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice'),
    $installer->getIdxName('sales/invoice', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_comment'),
    $installer->getIdxName('sales/invoice_comment', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_comment'),
    $installer->getIdxName('sales/invoice_comment', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName(
        'sales/invoice_grid',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('grand_total')),
    array('grand_total')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('state')),
    array('state')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('order_increment_id')),
    array('order_increment_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('order_created_at')),
    array('order_created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_grid'),
    $installer->getIdxName('sales/invoice_grid', array('billing_name')),
    array('billing_name')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoice_item'),
    $installer->getIdxName('sales/invoice_item', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName(
        'sales/order',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('status')),
    array('status')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('state')),
    array('state')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('ext_order_id')),
    array('ext_order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('quote_id')),
    array('quote_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', array('updated_at')),
    array('updated_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_address'),
    $installer->getIdxName('sales/order_address', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName(
        'sales/order_grid',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('status')),
    array('status')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('base_grand_total')),
    array('base_grand_total')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('base_total_paid')),
    array('base_total_paid')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('grand_total')),
    array('grand_total')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('total_paid')),
    array('total_paid')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('shipping_name')),
    array('shipping_name')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('billing_name')),
    array('billing_name')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_grid'),
    $installer->getIdxName('sales/order_grid', array('updated_at')),
    array('updated_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_item'),
    $installer->getIdxName('sales/order_item', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_item'),
    $installer->getIdxName('sales/order_item', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_payment'),
    $installer->getIdxName('sales/order_payment', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_status_history'),
    $installer->getIdxName('sales/order_status_history', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_status_history'),
    $installer->getIdxName('sales/order_status_history', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote'),
    $installer->getIdxName('sales/quote', array('customer_id', 'store_id', 'is_active')),
    array('customer_id', 'store_id', 'is_active')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote'),
    $installer->getIdxName('sales/quote', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address'),
    $installer->getIdxName('sales/quote_address', array('quote_id')),
    array('quote_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_item'),
    $installer->getIdxName('sales/quote_address_item', array('quote_address_id')),
    array('quote_address_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_item'),
    $installer->getIdxName('sales/quote_address_item', array('parent_item_id')),
    array('parent_item_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_item'),
    $installer->getIdxName('sales/quote_address_item', array('quote_item_id')),
    array('quote_item_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', array('parent_item_id')),
    array('parent_item_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', array('quote_id')),
    array('quote_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item'),
    $installer->getIdxName('sales/quote_item', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_item_option'),
    $installer->getIdxName('sales/quote_item_option', array('item_id')),
    array('item_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_payment'),
    $installer->getIdxName('sales/quote_payment', array('quote_id')),
    array('quote_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/quote_address_shipping_rate'),
    $installer->getIdxName('sales/quote_address_shipping_rate', array('address_id')),
    array('address_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName(
        'sales/shipment',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', array('total_qty')),
    array('total_qty')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment'),
    $installer->getIdxName('sales/shipment', array('updated_at')),
    array('updated_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_comment'),
    $installer->getIdxName('sales/shipment_comment', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_comment'),
    $installer->getIdxName('sales/shipment_comment', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName(
        'sales/shipment_grid',
        array('increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('increment_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('total_qty')),
    array('total_qty')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('shipment_status')),
    array('shipment_status')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('order_increment_id')),
    array('order_increment_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('order_created_at')),
    array('order_created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_grid'),
    $installer->getIdxName('sales/shipment_grid', array('shipping_name')),
    array('shipping_name')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_item'),
    $installer->getIdxName('sales/shipment_item', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_track'),
    $installer->getIdxName('sales/shipment_track', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_track'),
    $installer->getIdxName('sales/shipment_track', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipment_track'),
    $installer->getIdxName('sales/shipment_track', array('created_at')),
    array('created_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated'),
    $installer->getIdxName(
        'sales/invoiced_aggregated',
        array('period', 'store_id', 'order_status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated'),
    $installer->getIdxName('sales/invoiced_aggregated', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated_order'),
    $installer->getIdxName(
        'sales/invoiced_aggregated_order',
        array('period', 'store_id', 'order_status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/invoiced_aggregated_order'),
    $installer->getIdxName('sales/invoiced_aggregated_order', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_aggregated_created'),
    $installer->getIdxName(
        'sales/order_aggregated_created',
        array('period', 'store_id', 'order_status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_aggregated_created'),
    $installer->getIdxName('sales/order_aggregated_created', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_status_label'),
    $installer->getIdxName('sales/order_status_label', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/order_tax'),
    $installer->getIdxName('sales/order_tax', array('order_id', 'priority', 'position')),
    array('order_id', 'priority', 'position')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName(
        'sales/payment_transaction',
        array('order_id', 'payment_id', 'txn_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('order_id', 'payment_id', 'txn_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName('sales/payment_transaction', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName('sales/payment_transaction', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/payment_transaction'),
    $installer->getIdxName('sales/payment_transaction', array('payment_id')),
    array('payment_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile'),
    $installer->getIdxName(
        'sales/recurring_profile',
        array('internal_reference_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('internal_reference_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile'),
    $installer->getIdxName('sales/recurring_profile', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile'),
    $installer->getIdxName('sales/recurring_profile', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile_order'),
    $installer->getIdxName(
        'sales/recurring_profile_order',
        array('profile_id', 'order_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('profile_id', 'order_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/recurring_profile_order'),
    $installer->getIdxName('sales/recurring_profile_order', array('order_id')),
    array('order_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated'),
    $installer->getIdxName(
        'sales/refunded_aggregated',
        array('period', 'store_id', 'order_status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated'),
    $installer->getIdxName('sales/refunded_aggregated', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated_order'),
    $installer->getIdxName(
        'sales/refunded_aggregated_order',
        array('period', 'store_id', 'order_status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/refunded_aggregated_order'),
    $installer->getIdxName('sales/refunded_aggregated_order', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated'),
    $installer->getIdxName(
        'sales/shipping_aggregated',
        array('period', 'store_id', 'order_status', 'shipping_description'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status', 'shipping_description'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated'),
    $installer->getIdxName('sales/shipping_aggregated', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated_order'),
    $installer->getIdxName(
        'sales/shipping_aggregated_order',
        array('period', 'store_id', 'order_status', 'shipping_description'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status', 'shipping_description'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('sales/shipping_aggregated_order'),
    $installer->getIdxName('sales/shipping_aggregated_order', array('store_id')),
    array('store_id')
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
