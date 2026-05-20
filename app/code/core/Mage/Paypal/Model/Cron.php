<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use Carbon\Carbon;
use Monolog\Level;

/**
 * Cron job for PayPal authorization management.
 *
 * Handles three scenarios:
 * 1. Auto-reauthorize after the 3-day honor period
 * 2. Email alerts when approaching 29-day expiration
 * 3. Close transactions after 29-day expiration
 */
class Mage_Paypal_Model_Cron
{
    /**
     * Maximum number of automatic reauthorization attempts before giving up.
     */
    private const MAX_REAUTH_ATTEMPTS = 3;

    /**
     * Payment additional_information key holding the reauthorization attempt count.
     */
    private const REAUTH_ATTEMPTS_KEY = 'paypal_reauth_attempts';

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $_emailAlerts = [];

    /**
     * Main entry point: process the PayPal authorization lifecycle.
     */
    public function processAuthorizationLifecycle(?Mage_Cron_Model_Schedule $schedule = null): void
    {
        $this->_emailAlerts = [];

        $transactions = $this->_getActivePayPalAuthorizations();

        foreach ($transactions as $transaction) {
            // Isolate each row so one malformed transaction cannot abort the batch.
            try {
                $this->_processTransaction($transaction);
            } catch (Exception $exception) {
                Mage::logException($exception);
            }
        }

        if ($this->_emailAlerts !== []) {
            $this->_sendEmailAlerts();
        }
    }

    /**
     * Process queued PayPal webhook events and clean up retained terminal rows.
     */
    public function processWebhookEvents(?Mage_Cron_Model_Schedule $schedule = null): void
    {
        $config = Mage::getSingleton('paypal/config');
        $processor = Mage::getModel('paypal/webhook_processor');

        $processableEvents = Mage::getModel('paypal/webhook_event')->getCollection()
            ->addProcessableFilter($config->getWebhookRetryLimit())
            ->setPageSize(100);

        foreach ($processableEvents as $event) {
            if (!$event instanceof Mage_Paypal_Model_Webhook_Event) {
                continue;
            }

            try {
                $processor->process($event);
            } catch (Exception $exception) {
                Mage::logException($exception);
            }
        }

        $expiredEvents = Mage::getModel('paypal/webhook_event')->getCollection()
            ->addRetentionFilter($config->getWebhookRetentionDays())
            ->setPageSize(500);

        foreach ($expiredEvents as $event) {
            try {
                $event->delete();
            } catch (Exception $exception) {
                Mage::logException($exception);
            }
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
     * Process an individual transaction based on its age.
     */
    protected function _processTransaction(Mage_Sales_Model_Order_Payment_Transaction $transaction): void
    {
        $order = Mage::getModel('sales/order')->load($transaction->getData('parent_id'));
        $payment = $order->getPayment();

        $hoursFromAuth = $this->_calculateHoursFromAuthorization($transaction);
        $hoursUntilExpiry = $this->_calculateHoursUntilExpiry($transaction);

        if ($hoursUntilExpiry <= 0) {
            // Authorization expired (29+ days) - close transaction
            $this->_handleExpiredAuthorization($payment);
        } elseif ($hoursUntilExpiry <= 72) {
            // Within 3 days of expiration - send email alert
            $this->_addExpirationAlert($order, $transaction, $hoursUntilExpiry);
        } elseif ($hoursFromAuth >= 72 && !$this->_hasBeenReauthorized($payment)) {
            // Past honor period (3+ days) - attempt reauthorization
            $this->_attemptReauthorization($order, $transaction);
        }
    }

    /**
     * Calculate hours elapsed since the authorization was created.
     */
    protected function _calculateHoursFromAuthorization(Mage_Sales_Model_Order_Payment_Transaction $transaction): float
    {
        $authCreated = $transaction->getCreatedAt();
        $createdTimestamp = Carbon::parse((string) $authCreated)->getTimestamp();
        $nowTimestamp = Carbon::now()->getTimestamp();

        return ($nowTimestamp - $createdTimestamp) / 3600;
    }

    /**
     * Calculate hours remaining until the authorization expires.
     */
    protected function _calculateHoursUntilExpiry(Mage_Sales_Model_Order_Payment_Transaction $transaction): float
    {
        $additionalInfo = $transaction->getAdditionalInformation();
        $authExpiry = $additionalInfo[Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME] ?? null;

        if (is_string($authExpiry) && $authExpiry !== '') {
            try {
                $expiryDateTime = new DateTime($authExpiry, new DateTimeZone('UTC'));
                return ($expiryDateTime->getTimestamp() - Carbon::now()->getTimestamp()) / 3600;
            } catch (Exception $exception) {
                // Malformed expiry value - fall through to the 29-day estimate below.
                Mage::logException($exception);
            }
        }

        // No (or unparseable) expiry from PayPal: estimate 29 days from creation.
        $createdTimestamp = Carbon::parse((string) $transaction->getCreatedAt())->getTimestamp();
        $expiryTimestamp = $createdTimestamp + (29 * 24 * 3600);
        return ($expiryTimestamp - Carbon::now()->getTimestamp()) / 3600;
    }

    /**
     * Check whether the authorization has already been reauthorized.
     */
    protected function _hasBeenReauthorized(Mage_Sales_Model_Order_Payment $payment): bool
    {
        $additionalInfo = $payment->getAdditionalInformation();
        return isset($additionalInfo[Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_REAUTHORIZED]);
    }

    /**
     * Attempt to reauthorize a payment after the honor period.
     */
    protected function _attemptReauthorization(
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Payment_Transaction $transaction
    ): void {
        $payment = $order->getPayment();
        $attempts = (int) $payment->getAdditionalInformation(self::REAUTH_ATTEMPTS_KEY);

        // Already exhausted the retry budget - the final-failure comment was added once.
        if ($attempts >= self::MAX_REAUTH_ATTEMPTS) {
            return;
        }

        $authorizationId = $transaction->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID);
        if (!$authorizationId) {
            return;
        }

        try {
            $paypalModel = Mage::getModel('paypal/paypal');
            $response = $paypalModel->reauthorizePayment($authorizationId, $order);

            // Mark as reauthorized so subsequent cron runs skip this transaction.
            $payment->setAdditionalInformation(
                Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_REAUTHORIZED,
                Varien_Date::now(),
            )->save();

            Mage::log([
                'order_id' => $order->getIncrementId(),
                'auth_id' => $authorizationId,
                'message' => 'Auto-reauthorization successful',
                'response' => $response,
            ], Level::Info, 'paypal_auto_reauth.log');
        } catch (Exception $exception) {
            Mage::logException($exception);

            $attempts++;
            $payment->setAdditionalInformation(self::REAUTH_ATTEMPTS_KEY, $attempts)->save();

            // Add the merchant-facing comment only once, on the final failed attempt.
            if ($attempts >= self::MAX_REAUTH_ATTEMPTS) {
                $order->addStatusHistoryComment(
                    sprintf(
                        'PayPal auto-reauthorization failed after %d attempts. '
                        . 'Please check the log and reauthorize manually on the PayPal site.',
                        self::MAX_REAUTH_ATTEMPTS,
                    ),
                    false,
                )->save();
            }
        }
    }

    /**
     * Queue an expiration alert for the batched notification email.
     */
    protected function _addExpirationAlert(
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Payment_Transaction $transaction,
        float $hoursUntilExpiry
    ): void {
        $daysUntilExpiry = round($hoursUntilExpiry / 24, 1);

        $this->_emailAlerts[] = [
            'order' => $order,
            'transaction' => $transaction,
            'days_until_expiry' => $daysUntilExpiry,
            'hours_until_expiry' => $hoursUntilExpiry,
        ];
    }

    /**
     * Handle an expired authorization (29+ days): close it and hold the order.
     */
    protected function _handleExpiredAuthorization(Mage_Sales_Model_Order_Payment $payment): void
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
            ], Level::Warning, 'paypal_expired_auth.log');
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Send the batched email alert for orders approaching expiration.
     */
    protected function _sendEmailAlerts(): void
    {
        $subject = 'PayPal Authorization Expiring Soon - Action Required';
        $body = $this->_buildAlertEmailBody();

        $recipient = (string) Mage::getStoreConfig('payment/paypal/auth_alert_email')
            ?: (string) Mage::getStoreConfig('trans_email/ident_general/email');

        $mail = Mage::getModel('core/email')
            ->setToEmail($recipient)
            ->setToName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
            ->setFromName(Mage::getStoreConfig('general/store_information/name'))
            ->setSubject($subject)
            ->setBody($body)
            ->setType('text');

        try {
            $mail->send();
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Build the plain-text body for the expiration alert email.
     */
    protected function _buildAlertEmailBody(): string
    {
        $body = "PayPal Authorization Expiration Alert\n";
        $body .= 'Generated: ' . Carbon::now()->format('Y-m-d H:i:s') . "\n";
        $body .= str_repeat('=', 50) . "\n\n";

        $body .= "The following orders have PayPal authorizations expiring within 3 days:\n\n";
        foreach ($this->_emailAlerts as $alert) {
            $order = $alert['order'];
            $transaction = $alert['transaction'];
            $orderNumber = $order->getIncrementId();

            $orderAmount = $order->getBaseCurrencyCode() . ' ' . number_format((float) $order->getGrandTotal(), 2);
            $transactionId = $transaction->getTxnId();
            $daysRemaining = $alert['days_until_expiry'];

            $body .= 'Order: #' . $orderNumber . "\n";
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
