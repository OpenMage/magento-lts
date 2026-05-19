<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/** @var Mage_Paypal_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$tableName = 'paypal/webhook_event';
$table = $installer->getConnection()
    ->newTable($installer->getTable($tableName))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ], 'Entity ID')
    ->addColumn('webhook_event_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, [
        'nullable' => false,
    ], 'PayPal Webhook Event ID')
    ->addColumn('transmission_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => true,
    ], 'PayPal Transmission ID')
    ->addColumn('event_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => false,
    ], 'PayPal Event Type')
    ->addColumn('resource_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, [
        'nullable' => true,
    ], 'Resource Type')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => true,
    ], 'PayPal Resource ID')
    ->addColumn('paypal_order_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => true,
    ], 'PayPal Order ID')
    ->addColumn('paypal_capture_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => true,
    ], 'PayPal Capture ID')
    ->addColumn('paypal_authorization_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => true,
    ], 'PayPal Authorization ID')
    ->addColumn('paypal_refund_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, [
        'nullable' => true,
    ], 'PayPal Refund ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Magento Order Entity ID')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, [
        'nullable' => true,
    ], 'Magento Order Increment ID')
    ->addColumn('payment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Magento Payment Entity ID')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, [
        'nullable' => false,
        'default'  => 'received',
    ], 'Processing Status')
    ->addColumn('processing_attempts', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
        'default'  => 0,
    ], 'Processing Attempts')
    ->addColumn('last_error', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => true,
    ], 'Last Processing Error')
    ->addColumn('headers_json', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => true,
    ], 'Webhook Headers JSON')
    ->addColumn('payload_json', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
        'nullable' => false,
    ], 'Webhook Payload JSON')
    ->addColumn('event_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => true,
    ], 'PayPal Event Time')
    ->addColumn('processed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => true,
    ], 'Processed At')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => true,
    ], 'Updated At')
    ->addIndex(
        $installer->getIdxName($tableName, ['webhook_event_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['webhook_event_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['transmission_id']),
        ['transmission_id'],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['event_type']),
        ['event_type'],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['resource_id']),
        ['resource_id'],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['paypal_capture_id']),
        ['paypal_capture_id'],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['paypal_refund_id']),
        ['paypal_refund_id'],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['status', 'created_at']),
        ['status', 'created_at'],
    )
    ->addIndex(
        $installer->getIdxName($tableName, ['order_id']),
        ['order_id'],
    )
    ->addForeignKey(
        $installer->getFkName($tableName, 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName($tableName, 'payment_id', 'sales/order_payment', 'entity_id'),
        'payment_id',
        $installer->getTable('sales/order_payment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('PayPal Webhook Event Log');

$installer->getConnection()->createTable($table);
$installer->endSetup();
