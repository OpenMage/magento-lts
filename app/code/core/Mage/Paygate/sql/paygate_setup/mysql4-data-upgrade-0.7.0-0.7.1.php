<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$connection->beginTransaction();

try {
    $paymentMethodCode = 'authorizenet';
    $transactionTable = $installer->getTable('sales/payment_transaction');
    $paymentTable = $installer->getTable('sales/order_payment');

    /**
     * Update payments
     */
    $payments = $connection->fetchAll(
        $connection->select()
            ->from($paymentTable)
            ->joinLeft(
                $transactionTable,
                "$transactionTable.txn_id = $paymentTable.last_trans_id",
                [
                   'last_transaction_id' => 'transaction_id',
                   'last_transaction_type' => 'txn_type',
                   'last_transaction_is_closed' => 'is_closed'
                ]
            )
            ->where('method=?', $paymentMethodCode)
    );

    $paymentsIds = [];
    $transactionsShouldBeOpened = [];
    foreach ($payments as $payment) {
        $paymentId = $payment['entity_id'];
        $card = [
            'last_trans_id' => $payment['last_trans_id'],
            'cc_type' => $payment['cc_type'],
            'cc_owner' => $payment['cc_owner'],
            'cc_last4' => $payment['cc_last4'],
            'cc_exp_month' => $payment['cc_exp_month'],
            'cc_exp_year' => $payment['cc_exp_year'],
            'cc_ss_issue' => $payment['cc_ss_issue'],
            'cc_ss_start_month' => $payment['cc_ss_start_month'],
            'cc_ss_start_year' => $payment['cc_ss_start_year'],
            'requested_amount' => $payment['base_amount_ordered'],
            'processed_amount' => $payment['base_amount_ordered'],
            'captured_amount' => $payment['base_amount_paid_online'],
            'refunded_amount' => $payment['base_amount_refunded_online']
        ];
        $additionalInformation = unserialize($payment['additional_information'], ['allowed_classes' => false]);
        if (isset($additionalInformation['authorize_cards'])) {
            continue;
        }
        $additionalInformation['authorize_cards'] = [
            (string) md5(microtime(1)) => $card
        ];
        $additionalInformation = serialize($additionalInformation);

        $bind  = [
            'additional_information' => $additionalInformation,
            'last_trans_id' => null,
            'cc_type' => null,
            'cc_owner' => null,
            'cc_last4' => null,
            'cc_exp_month' => null,
            'cc_exp_year' => null,
            'cc_ss_issue' => null,
            'cc_ss_start_month' => null,
            'cc_ss_start_year' => null
        ];
        $where = $this->getConnection()->quoteInto('entity_id=?', $paymentId);
        $this->getConnection()->update($paymentTable, $bind, $where);

        /**
         * Collect information for update last transactions of updated payments
         */
        $paymentsIds[] = $paymentId;
        if (($payment['last_transaction_type'] == 'authorization' || $payment['last_transaction_type'] == 'capture')
            && $payment['last_transaction_is_closed'] == '1') {
            $transactionsShouldBeOpened[] = $payment['last_transaction_id'];
        }
    }

    /**
     * Update transactions
     */
    $transactions = $installer->getConnection()->fetchAll(
        $installer->getConnection()->select()
            ->from(
                $transactionTable,
                ['transaction_id', 'txn_id', 'txn_type', 'is_closed', 'additional_information']
            )
            ->where('payment_id IN (?)', $paymentsIds)
    );
    foreach ($transactions as $transaction) {
        $transactionId = $transaction['transaction_id'];

        $parts = explode('-', $transaction['txn_id']);
        $realTransactionId = array_shift($parts);
        $additionalInformation = unserialize($transaction['additional_information'], ['allowed_classes' => false]);
        $additionalInformation['real_transaction_id'] = $realTransactionId;
        $additionalInformation = serialize($additionalInformation);

        $isClosed = $transaction['is_closed'];
        if (in_array($transactionId, $transactionsShouldBeOpened)) {
            $isClosed = '0';
        }

        $bind  = [
            'additional_information' => $additionalInformation,
            'is_closed' => $isClosed
        ];
        $where = $this->getConnection()->quoteInto('transaction_id=?', $transactionId);
        $this->getConnection()->update($transactionTable, $bind, $where);
    }

    $installer->endSetup();
    $connection->commit();
} catch (Exception $e) {
    $connection->rollBack();
    throw $e;
}
