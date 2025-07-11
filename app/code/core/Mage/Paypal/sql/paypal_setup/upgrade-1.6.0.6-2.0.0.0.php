<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal installation script
 *
 * @category    Mage
 * @package     Mage_Paypal
 */
$installer = $this;
/* @var $installer Mage_Paypal_Model_Resource_Setup */

$installer->startSetup();
$debugTableName = 'paypal/debug';

$installer->getConnection()->dropTable($installer->getTable($debugTableName));

$debugTable = $installer->getConnection()
    ->newTable($installer->getTable($debugTableName))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => true,
    ], 'Quote ID')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, [
        'nullable'  => true,
    ], 'Quote Increment ID')
    ->addColumn('action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, [
        'nullable'  => false,
    ], 'Action')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable'  => true,
    ], 'Transaction ID')
    ->addColumn('request_body', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable'  => true,
    ], 'Request Body')
    ->addColumn('response_body', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable'  => true,
    ], 'Response Body')
    ->addColumn('exception_message', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable'  => true,
    ], 'Exception Message')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName($debugTableName, ['quote_id']),
        ['quote_id'],
    )
    ->addIndex(
        $installer->getIdxName($debugTableName, ['increment_id']),
        ['increment_id'],
    )->addIndex(
        $installer->getIdxName($debugTableName, ['transaction_id']),
        ['transaction_id'],
    )
    ->addForeignKey(
        $installer->getFkName($debugTableName, 'quote_id', 'sales/quote', 'entity_id'),
        'quote_id',
        $installer->getTable('sales/quote'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName($debugTableName, 'increment_id', 'sales/order', 'increment_id'),
        'increment_id',
        $installer->getTable('sales/order'),
        'increment_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    )
    ->setComment('PayPal Debug Table');
$installer->getConnection()->createTable($debugTable);

$installer->getConnection()->delete(
    $installer->getTable('core_resource'),
    $installer->getConnection()->quoteInto('code = ?', 'paypaluk_setup'),
);
$status = 'paypal_auth_expired';
$statusLabel = 'PayPal Authorization Expired';

$installer->getConnection()->insert(
    $installer->getTable('sales/order_status'),
    [
        'status' => $status,
        'label'  => $statusLabel,
    ],
);

$installer->getConnection()->insert(
    $installer->getTable('sales/order_status_state'),
    [
        'status'     => $status,
        'state'      => Mage_Sales_Model_Order::STATE_HOLDED,
        'is_default' => 0,
    ],
);

$installer->getConnection()->delete(
    $installer->getTable('core/config_data'),
    $installer->getConnection()->quoteInto('path IN (?)', [
        'payment/paypal_billing_agreement/active',
        'payment/paypal_billing_agreement/sort_order',
        'payment/paypal_billing_agreement/payment_action',
        'payment/paypal_billing_agreement/allowspecific',
        'payment/paypal_billing_agreement/debug',
        'payment/paypal_billing_agreement/verify_peer',
        'payment/paypal_billing_agreement/line_items_enabled',
        'payment/paypal_billing_agreement/allow_billing_agreement_wizard',
        'payment/paypal_express/sort_order',
        'payment/paypal_express/payment_action',
        'payment/paypal_express/visible_on_cart',
        'payment/paypal_express/visible_on_product',
        'payment/paypal_express/allowspecific',
        'payment/paypal_express/debug',
        'payment/paypal_express/verify_peer',
        'payment/paypal_express/line_items_enabled',
        'payment/paypal_express/transfer_shipping_options',
        'payment/paypal_express/solution_type',
        'payment/paypal_express/require_billing_address',
        'payment/paypal_express/allow_ba_signup',
        'payment/paypal_direct/using_pbridge',
        'payment/paypal_direct/active',
        'payment/paypal_direct/sort_order',
        'payment/paypal_direct/payment_action',
        'payment/paypal_direct/cctypes',
        'payment/paypal_direct/allowspecific',
        'payment/paypal_direct/debug',
        'payment/paypal_direct/verify_peer',
        'payment/paypal_direct/line_items_enabled',
        'payment/paypal_direct/useccv',
        'payment/paypal_direct/centinel',
        'payment/paypal_standard/active',
        'payment/paypal_standard/sort_order',
        'payment/paypal_standard/payment_action',
        'payment/paypal_standard/allowspecific',
        'payment/paypal_standard/sandbox_flag',
        'payment/paypal_standard/line_items_enabled',
        'payment/paypal_standard/debug',
        'payment/paypal_standard/verify_peer',
        'payment/paypaluk_express/active',
        'payment/paypaluk_express/sort_order',
        'payment/paypaluk_express/payment_action',
        'payment/paypaluk_express/visible_on_cart',
        'payment/paypaluk_express/visible_on_product',
        'payment/paypaluk_express/allowspecific',
        'payment/paypaluk_express/debug',
        'payment/paypaluk_express/verify_peer',
        'payment/paypaluk_express/line_items_enabled',
        'payment/paypal_express/active',
        'payment/paypal_express/skip_order_review_step',
        'payment/paypal_express_bml/active',
        'payment/paypal_express_bml/publisher_id',
        'payment/paypal_express_bml/homepage_display',
        'payment/paypal_express_bml/homepage_position',
        'payment/paypal_express_bml/homepage_size',
        'payment/paypal_express_bml/categorypage_display',
        'payment/paypal_express_bml/categorypage_position',
        'payment/paypal_express_bml/categorypage_size',
        'payment/paypal_express_bml/productpage_display',
        'payment/paypal_express_bml/productpage_position',
        'payment/paypal_express_bml/productpage_size',
        'payment/paypal_express_bml/checkout_display',
        'payment/paypal_express_bml/checkout_position',
        'payment/paypal_express_bml/checkout_size',
        'payment/paypaluk_express_bml/active',
        'payment/paypal_standard/was_active',
        'payment/paypal_wps_express/active',
        'paypal/wpp/api_authentication',
        'paypal/wpp/api_username',
        'paypal/wpp/api_password',
        'paypal/wpp/api_signature',
        'paypal/wpp/sandbox_flag',
        'paypal/wpp/use_proxy',
        'paypal/fetch_reports/ftp_login',
        'paypal/fetch_reports/ftp_password',
        'paypal/fetch_reports/ftp_sandbox',
        'paypal/fetch_reports/ftp_ip',
        'paypal/fetch_reports/ftp_path',
        'paypal/fetch_reports/active',
        'paypal/fetch_reports/schedule',
        'paypal/fetch_reports/time',
        'paypal/style/logo',
        'paypal/style/page_style',
        'paypal/style/paypal_hdrimg',
        'paypal/style/paypal_hdrbackcolor',
        'paypal/style/paypal_hdrbordercolor',
        'paypal/style/paypal_payflowcolor',
        'paypal/wpp/button_flavor',
        'paypal/general/business_account',
    ]),
);

$installer->endSetup();
