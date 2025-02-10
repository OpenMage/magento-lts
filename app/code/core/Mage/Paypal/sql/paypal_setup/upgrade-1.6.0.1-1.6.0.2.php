<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Paypal_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('paypal/settlement_report_row'), 'payment_tracking_id', [
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Payment Tracking ID',
        'length'  => '255',
    ]);
