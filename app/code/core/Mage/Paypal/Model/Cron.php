<?php

declare(strict_types=1);

/**
 * Simplified cron job for PayPal authorization management.
 *
 * This class handles three main scenarios:
 * 1. Auto-reauthorize after 3-day honor period
 * 2. Email alerts when approaching 29-day expiration
 * 3. Close transactions after 29-day expiration
 *
 * PHP version 8.4
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2005-2024 Magento Inc. (https://www.magentocommerce.com)
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link        https://magento.com
 */
class Mage_Paypal_Model_Cron
{
    /**
     * @var array
     */
    protected $_emailAlerts = [];

    /**
     * Main method to process PayPal authorization lifecycle
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return void
     */
    public function processAuthorizationLifecycle($schedule)
    {
        $this->_emailAlerts = [];

        $transactions = $this->_getActivePayPalAuthorizations();

        foreach ($transactions as $transaction) {
            $this->_processTransaction($transaction);
        }
        if (!empty($this->_emailAlerts)) {
            $this->_sendEmailAlerts();
        }
    }

    /**
     * Get active PayPal authorization transactions
     */
    protected function _getActivePayPalAuthorizations(): Mage_Core_Model_Resource_Db_Collection_Abstract
    {
        return Mage::getModel('sales/order_payment_transaction')->getCollection()
            ->join(
                ['payment' => 'sales/order_payment'],
                'main_table.payment_id = payment.entity_id',
                ['method', 'parent_id'],
            )
            ->join(
                ['order' => 'sales/order'],
                'payment.parent_id = order.entity_id',
                ['increment_id', 'state', 'status', 'store_id'],
            )
            ->addFieldToFilter('main_table.txn_type', Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
            ->addFieldToFilter('main_table.is_closed', 0)
            ->addFieldToFilter('payment.method', 'paypal')
            ->addFieldToFilter('order.state', [
                'in' => [
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                ],
            ]);
    }

    /**
     * Process individual transaction based on its age
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @return void
     */
    protected function _processTransaction($transaction)
    {
        $order = Mage::getModel('sales/order')->load($transaction->getData('parent_id'));
        $payment = $order->getPayment();

        $hoursFromAuth = $this->_calculateHoursFromAuthorization($transaction);
        $hoursUntilExpiry = $this->_calculateHoursUntilExpiry($transaction, $order->getStoreId());

        if ($hoursUntilExpiry <= 0) {
            // Authorization expired (29+ days) - close transaction
            $this->_handleExpiredAuthorization($payment);
        } elseif ($hoursUntilExpiry <= 72) {
            // Within 3 days of expiration - send email alert
            $this->_addExpirationAlert($order, $transaction, $hoursUntilExpiry);
        } elseif ($hoursFromAuth >= 72 && !$this->_hasBeenReauthorized($transaction)) {
            // Past honor period (3+ days) - attempt reauthorization
            $this->_attemptReauthorization($order, $transaction);
        }
    }

    /**
     * Calculate hours from authorization creation
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @return float
     */
    protected function _calculateHoursFromAuthorization($transaction)
    {
        $authCreated = $transaction->getCreatedAt();
        $createdTimestamp = strtotime((string) $authCreated);
        $nowTimestamp = time();

        return ($nowTimestamp - $createdTimestamp) / 3600;
    }

    /**
     * Calculate hours until expiry with timezone conversion
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @param int $storeId
     * @return float
     */
    protected function _calculateHoursUntilExpiry($transaction, $storeId)
    {
        $additionalInfo = $transaction->getAdditionalInformation();
        $authExpiry = $additionalInfo[Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME] ?? null;

        if (!$authExpiry) {
            $createdTimestamp = strtotime((string) $transaction->getCreatedAt());
            $expiryTimestamp = $createdTimestamp + (29 * 24 * 3600);
            return ($expiryTimestamp - time()) / 3600;
        }
        $storeTimezone = Mage::app()->getStore($storeId)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        $expiryDateTime = new DateTime($authExpiry, new DateTimeZone('UTC'));
        $expiryDateTime->setTimezone(new DateTimeZone($storeTimezone));
        $nowDateTime = new DateTime('now', new DateTimeZone($storeTimezone));

        return ($expiryDateTime->getTimestamp() - $nowDateTime->getTimestamp()) / 3600;
    }

    /**
     * Check if transaction has been reauthorized
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @return bool
     */
    protected function _hasBeenReauthorized($transaction)
    {
        $additionalInfo = $transaction->getAdditionalInformation();
        return isset($additionalInfo[Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_ID]) && $additionalInfo[Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_ID];
    }

    /**
     * Attempt to reauthorize payment after honor period
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @return void
     */
    protected function _attemptReauthorization($order, $transaction)
    {
        try {
            $authorizationId = $transaction->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID);
            if (!$authorizationId) {
                return;
            }

            $paypalModel = Mage::getModel('paypal/paypal');
            $result = $paypalModel->reauthorizePayment($authorizationId, $order);

            Mage::log([
                'order_id' => $order->getIncrementId(),
                'old_auth_id' => $authorizationId,
                'new_auth_id' => $result,
                'message' => 'Auto-reauthorization successful',
            ], Zend_Log::INFO, 'paypal_auto_reauth.log');
        } catch (Exception $e) {
            Mage::logException($e);
            $order->addStatusHistoryComment(
                'PayPal auto-reauthorization failed due to system error. Please check log and go to PayPal site to reauthorize manually.',
                false,
            )->save();
        }
    }

    /**
     * Add expiration alert to email queue
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @param float $hoursUntilExpiry
     * @return void
     */
    protected function _addExpirationAlert($order, $transaction, $hoursUntilExpiry)
    {
        $daysUntilExpiry = round($hoursUntilExpiry / 24, 1);

        $this->_emailAlerts[] = [
            'order' => $order,
            'transaction' => $transaction,
            'days_until_expiry' => $daysUntilExpiry,
            'hours_until_expiry' => $hoursUntilExpiry,
        ];
    }

    /**
     * Handle expired authorization (29+ days)
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return void
     */
    protected function _handleExpiredAuthorization($payment)
    {
        try {
            $transactionModel = Mage::getSingleton('paypal/transaction');
            $transactionModel->updateExpiredTransaction($payment);

            $order = $payment->getOrder();
            $message = sprintf(
                'PayPal authorization %s has expired after 29 days. Transaction closed.',
                $payment->getLastTransId(),
            );

            $order->setState(
                Mage_Sales_Model_Order::STATE_HOLDED,
                'paypal_auth_expired',
                $message,
                false,
            )->save();

            Mage::log([
                'order_id' => $order->getIncrementId(),
                'message' => 'Authorization expired and closed',
            ], Zend_Log::WARN, 'paypal_expired_auth.log');
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Send email alerts for orders approaching expiration
     *
     * @return void
     */
    protected function _sendEmailAlerts()
    {
        $subject = 'PayPal Authorization Expiring Soon - Action Required';
        $body = $this->_buildAlertEmailBody();

        $mail = Mage::getModel('core/email')
            ->setToEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
            ->setToName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
            ->setFromName(Mage::getStoreConfig('general/store_information/name'))
            ->setSubject($subject)
            ->setBody($body)
            ->setType('text');

        try {
            $mail->send();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Build email body for expiration alerts
     *
     * @return string
     */
    protected function _buildAlertEmailBody()
    {
        $body = "PayPal Authorization Expiration Alert\n";
        $body .= 'Generated: ' . date('Y-m-d H:i:s') . "\n";
        $body .= str_repeat('=', 50) . "\n\n";

        $body .= "The following orders have PayPal authorizations expiring within 3 days:\n\n";
        foreach ($this->_emailAlerts as $alert) {
            $order = $alert['order'];
            $transaction = $alert['transaction'];
            $orderNumber = $order->getIncrementId();
            $customerName = $order->getCustomerName();

            $orderAmount = $order->getBaseCurrencyCode() . ' ' . number_format((float) $order->getGrandTotal(), 2);
            $transactionId = $transaction->getTxnId();
            $daysRemaining = $alert['days_until_expiry'];

            $body .= 'Order: #' . $orderNumber . "\n";
            $body .= 'Customer: ' . $customerName . "\n";
            $body .= 'Amount: ' . $orderAmount . "\n";
            $body .= 'Transaction ID: ' . $transactionId . "\n";
            $body .= 'Expires in: ' . $daysRemaining . " days\n";
            $body .= "Action: CAPTURE PAYMENT IMMEDIATELY\n\n";
        }
        $body .= str_repeat('=', 50) . "\n";
        $body .= "Note: Authorizations expire after 29 days and cannot be recovered.\n";
        $body .= "Capture payments as soon as possible to avoid losing the transaction.\n\n";

        return $body . 'This is an automated alert from your PayPal payment system.';
    }
}
