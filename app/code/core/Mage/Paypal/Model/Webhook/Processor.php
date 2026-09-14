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

class Mage_Paypal_Model_Webhook_Processor
{
    public const ACTION_ORDER_APPROVED = 'order_approved';

    public const ACTION_ORDER_COMPLETED = 'order_completed';

    public const ACTION_CAPTURE_COMPLETED = 'capture_completed';

    public const ACTION_CAPTURE_DENIED = 'capture_denied';

    public const ACTION_CAPTURE_REFUNDED = 'capture_refunded';

    public const ACTION_CAPTURE_REVERSED = 'capture_reversed';

    public const ACTION_AUTHORIZATION_VOIDED = 'authorization_voided';

    public const ACTION_DISPUTE = 'dispute';

    public const ACTION_SUBSCRIPTION = 'subscription';

    public const ACTION_IGNORE = 'ignore';

    /**
     * How long an approved in-checkout order is left to the buyer's browser before the webhook charges it.
     */
    public const APPROVAL_GRACE_SECONDS = 300;

    private readonly Mage_Paypal_Model_Webhook_Event_Resolver $resolver;

    private ?Mage_Paypal_Model_Checkout_Placement $placement = null;

    /**
     * Mage::getModel() always passes its (empty) arguments array as the first constructor argument.
     *
     * @param null|array<mixed>|Mage_Paypal_Model_Webhook_Event_Resolver $resolver
     */
    public function __construct(null|array|Mage_Paypal_Model_Webhook_Event_Resolver $resolver = null)
    {
        $this->resolver = $resolver instanceof Mage_Paypal_Model_Webhook_Event_Resolver
            ? $resolver
            : Mage::getModel('paypal/webhook_event_resolver');
    }

    public function process(Mage_Paypal_Model_Webhook_Event $event): void
    {
        if ($event->isTerminal()) {
            return;
        }

        // Outside the event lock's transaction: placing an order charges the buyer and commits on its own. Inside
        // it, a failure later in this method would roll the order back while the PayPal charge stays.
        $this->recoverMissingOrder($event);

        $connection = null;

        try {
            $connection = $this->beginEventLock($event);
            if ($event->getId()) {
                $event = Mage::getModel('paypal/webhook_event')->load($event->getId());
            }

            if ($event->isTerminal()) {
                $connection?->commit();
                return;
            }

            $action = $this->getActionForEventType((string) $event->getData('event_type'));
            $awaitsCheckout = $action === self::ACTION_ORDER_APPROVED && !$this->isPastApprovalGrace($event);
            if (!$awaitsCheckout) {
                $event->incrementProcessingAttempts();
            }

            if ($action === self::ACTION_IGNORE) {
                $event->markIgnored('Unsupported PayPal webhook event type.')->save();
                $connection?->commit();
                return;
            }

            if ($action === self::ACTION_SUBSCRIPTION) {
                $event->markIgnored('Subscription webhook stored; subscription processing is handled separately.')->save();
                $connection?->commit();
                return;
            }

            $resolution = $this->resolver->resolve($event);
            $this->applyResolution($event, $resolution);

            $order = $resolution->getData('order');
            $payment = $resolution->getData('payment');
            if (!$order instanceof Mage_Sales_Model_Order || !$payment instanceof Mage_Sales_Model_Order_Payment) {
                $this->deferUnresolvedEvent($event, $action, $awaitsCheckout);
                $connection?->commit();
                return;
            }

            switch ($action) {
                case self::ACTION_ORDER_APPROVED:
                case self::ACTION_ORDER_COMPLETED:
                    // Nothing left to do once the order exists.
                    break;
                case self::ACTION_CAPTURE_COMPLETED:
                    $this->processCaptureCompleted($event, $order, $payment);
                    break;
                case self::ACTION_CAPTURE_DENIED:
                    $this->processCaptureDenied($event, $order);
                    break;
                case self::ACTION_CAPTURE_REFUNDED:
                    $this->processCaptureRefunded($event, $order, $payment);
                    break;
                case self::ACTION_CAPTURE_REVERSED:
                    $this->processCaptureReversed($event, $order, $payment);
                    break;
                case self::ACTION_AUTHORIZATION_VOIDED:
                    $this->processAuthorizationVoided($event, $order, $payment);
                    break;
                case self::ACTION_DISPUTE:
                    $this->processDispute($event, $order);
                    break;
            }

            $event->markProcessed()->save();
            $connection?->commit();
        } catch (Exception $exception) {
            if ($connection instanceof Varien_Db_Adapter_Interface && $connection->getTransactionLevel() > 0) {
                $connection->rollBack();
            }

            $event->markFailed($exception->getMessage())->save();
            throw $exception;
        }
    }

    public function getActionForEventType(string $eventType): string
    {
        return match ($eventType) {
            'CHECKOUT.ORDER.APPROVED' => self::ACTION_ORDER_APPROVED,
            'CHECKOUT.ORDER.COMPLETED' => self::ACTION_ORDER_COMPLETED,
            'PAYMENT.CAPTURE.COMPLETED' => self::ACTION_CAPTURE_COMPLETED,
            'PAYMENT.CAPTURE.DENIED' => self::ACTION_CAPTURE_DENIED,
            'PAYMENT.CAPTURE.REFUNDED' => self::ACTION_CAPTURE_REFUNDED,
            'PAYMENT.CAPTURE.REVERSED' => self::ACTION_CAPTURE_REVERSED,
            'PAYMENT.AUTHORIZATION.VOIDED' => self::ACTION_AUTHORIZATION_VOIDED,
            'RISK.DISPUTE.CREATED',
            'CUSTOMER.DISPUTE.CREATED',
            'CUSTOMER.DISPUTE.UPDATED',
            'CUSTOMER.DISPUTE.RESOLVED' => self::ACTION_DISPUTE,
            default => str_starts_with($eventType, 'BILLING.SUBSCRIPTION.')
                ? self::ACTION_SUBSCRIPTION
                : self::ACTION_IGNORE,
        };
    }

    /**
     * Place the order for a PayPal payment whose checkout never got that far.
     *
     * An approval is charged only after a grace period, so the buyer's own browser request wins whenever it
     * arrives. The attempt that reaches the retry limit notifies the store if placement fails.
     */
    public function recoverMissingOrder(Mage_Paypal_Model_Webhook_Event $event): void
    {
        $action = $this->getActionForEventType((string) $event->getData('event_type'));
        if (!in_array($action, [self::ACTION_ORDER_APPROVED, self::ACTION_ORDER_COMPLETED, self::ACTION_CAPTURE_COMPLETED], true)) {
            return;
        }

        $paypalOrderId = (string) $event->getData('paypal_order_id');
        if ($paypalOrderId === '') {
            return;
        }

        $retryLimit = $this->getRetryLimit();
        $attempt = (int) $event->getData('processing_attempts') + 1;
        if ($attempt > $retryLimit) {
            return;
        }

        $isApproval = $action === self::ACTION_ORDER_APPROVED;
        if ($isApproval && !$this->isPastApprovalGrace($event)) {
            return;
        }

        try {
            $this->getPlacement()->recoverOrder($paypalOrderId, $isApproval, $attempt === $retryLimit);
        } catch (Throwable $throwable) {
            Mage::logException($throwable);
        }
    }

    public function isPastApprovalGrace(Mage_Paypal_Model_Webhook_Event $event): bool
    {
        $eventTime = (string) $event->getData('event_time');
        if ($eventTime === '') {
            $eventTime = (string) $event->getData('created_at');
        }

        if ($eventTime === '') {
            return false;
        }

        try {
            return Carbon::parse($eventTime, 'UTC')->addSeconds(self::APPROVAL_GRACE_SECONDS)->lte(Carbon::now('UTC'));
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Retry an event whose order does not exist yet, up to the retry limit, then give it up so it stops taking a
     * slot in every cron batch. An approval still inside its grace period has not used up an attempt.
     */
    private function deferUnresolvedEvent(Mage_Paypal_Model_Webhook_Event $event, string $action, bool $awaitsCheckout): void
    {
        if ($awaitsCheckout) {
            $event->markDeferred("Waiting for the buyer's checkout to place the order.")->save();
            return;
        }

        $attempts = (int) $event->getData('processing_attempts');
        if ($attempts < $this->getRetryLimit()) {
            $event->markDeferred('No matching Magento order/payment was found yet.')->save();
            return;
        }

        $event->markFailed(sprintf('No matching Magento order/payment was found after %d attempts.', $attempts))->save();
        if ($action === self::ACTION_CAPTURE_COMPLETED) {
            $this->alertOrphanedCapture($event);
        }
    }

    /**
     * PayPal kept the money but there is neither an order nor an open cart to place one from, e.g. the cart was
     * merged away when the buyer logged in. Placement has already notified the store when the cart still exists.
     */
    private function alertOrphanedCapture(Mage_Paypal_Model_Webhook_Event $event): void
    {
        $placement = $this->getPlacement();
        $paypalOrderId = (string) $event->getData('paypal_order_id');
        if ($paypalOrderId !== '' && $placement->findQuoteByPaypalOrderId($paypalOrderId) instanceof Mage_Sales_Model_Quote) {
            return;
        }

        $captureId = (string) $event->getData('paypal_capture_id');
        if ($captureId === '') {
            $captureId = (string) $event->getData('resource_id');
        }

        $message = sprintf(
            'PayPal capture %s (PayPal order %s) has no Magento order and no open cart to place it from. '
            . 'Place the order manually or refund the capture in PayPal.',
            $captureId,
            $paypalOrderId !== '' ? $paypalOrderId : 'unknown',
        );
        Mage::log($message, Level::Critical, 'paypal.log', true);
        $placement->notifyStore(null, $message);
    }

    private function getRetryLimit(): int
    {
        return max(1, Mage::getSingleton('paypal/config')->getWebhookRetryLimit());
    }

    public function setPlacement(Mage_Paypal_Model_Checkout_Placement $placement): self
    {
        $this->placement = $placement;
        return $this;
    }

    private function getPlacement(): Mage_Paypal_Model_Checkout_Placement
    {
        return $this->placement ??= Mage::getSingleton('paypal/checkout_placement');
    }

    private function beginEventLock(Mage_Paypal_Model_Webhook_Event $event): ?Varien_Db_Adapter_Interface
    {
        $eventId = (int) $event->getId();
        if ($eventId === 0) {
            return null;
        }

        /** @var Mage_Paypal_Model_Resource_Webhook_Event $resource */
        $resource = $event->getResource();

        return $resource->lockEvent($eventId);
    }

    private function applyResolution(Mage_Paypal_Model_Webhook_Event $event, Varien_Object $resolution): void
    {
        foreach (['order_id', 'increment_id', 'payment_id'] as $field) {
            if ($resolution->getData($field) !== null) {
                $event->setData($field, $resolution->getData($field));
            }
        }
    }

    private function processCaptureCompleted(
        Mage_Paypal_Model_Webhook_Event $event,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Payment $payment
    ): void {
        $captureId = (string) ($event->getData('paypal_capture_id') ?: $event->getData('resource_id'));
        if ($captureId === '') {
            throw new Mage_Paypal_Model_Exception('Capture webhook does not include a capture ID.');
        }

        $this->createOrUpdateTransaction(
            $payment,
            $captureId,
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            null,
            $event,
        );

        // Only auto-invoice when the webhook reports a capture for the full
        // order total. prepareInvoice() with no quantities invoices every
        // remaining item, which would overstate paid/invoiced totals for a
        // partial capture - those are left for the merchant to invoice.
        $captureAmount = $this->extractCaptureAmount($event);
        $isFullCapture = $captureAmount !== null
            && abs($captureAmount - (float) $order->getGrandTotal()) < 0.01;

        $message = Mage::helper('paypal')->__('PayPal webhook confirmed capture. Capture ID: %s', $captureId);

        if ($isFullCapture && !$this->hasInvoiceForTransaction($order, $captureId) && $order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
            $invoice->setTransactionId($captureId);
            $invoice->register();

            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($order)
                ->save();
        } elseif ($captureAmount !== null && !$isFullCapture) {
            $message = Mage::helper('paypal')->__(
                'PayPal webhook confirmed a partial capture. Capture ID: %s. '
                . 'Create the invoice manually for the captured items.',
                $captureId,
            );
        }

        $order->addStatusHistoryComment($message, false)->save();
    }

    /**
     * Read the capture amount from a PAYMENT.CAPTURE.* webhook payload.
     */
    private function extractCaptureAmount(Mage_Paypal_Model_Webhook_Event $event): ?float
    {
        $amount = $event->getPayload()['resource']['amount']['value'] ?? null;

        return is_numeric($amount) ? (float) $amount : null;
    }

    private function processCaptureDenied(Mage_Paypal_Model_Webhook_Event $event, Mage_Sales_Model_Order $order): void
    {
        $reason = $this->extractReason($event);
        $message = Mage::helper('paypal')->__(
            'PayPal capture was denied by webhook. %s',
            $reason !== '' ? 'Reason: ' . $reason : '',
        );

        if ($order->canHold()) {
            $order->hold()->addStatusHistoryComment($message, false)->save();
            return;
        }

        $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, $message, false)->save();
    }

    private function processCaptureRefunded(
        Mage_Paypal_Model_Webhook_Event $event,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Payment $payment
    ): void {
        $refundId = (string) ($event->getData('paypal_refund_id') ?: $event->getData('resource_id'));
        if ($refundId === '') {
            throw new Mage_Paypal_Model_Exception('Refund webhook does not include a refund ID.');
        }

        $this->createOrUpdateTransaction(
            $payment,
            $refundId,
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND,
            (string) $event->getData('paypal_capture_id') ?: null,
            $event,
        );

        $order->addStatusHistoryComment(
            Mage::helper('paypal')->__(
                'PayPal webhook recorded an off-platform refund. Refund ID: %s. No Magento credit memo was created.',
                $refundId,
            ),
            false,
        )->save();
    }

    private function processCaptureReversed(
        Mage_Paypal_Model_Webhook_Event $event,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Payment $payment
    ): void {
        $reversalId = (string) ($event->getData('resource_id') ?: $event->getData('paypal_capture_id'));
        if ($reversalId !== '') {
            $this->createOrUpdateTransaction(
                $payment,
                $reversalId . '-reversal',
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID,
                (string) $event->getData('paypal_capture_id') ?: null,
                $event,
            );
        }

        $message = Mage::helper('paypal')->__(
            'PayPal webhook reported a capture reversal. Review the PayPal account before releasing this order.',
        );
        if ($order->canHold()) {
            $order->hold()->addStatusHistoryComment($message, false)->save();
            return;
        }

        $order->addStatusHistoryComment($message, false)->save();
    }

    private function processAuthorizationVoided(
        Mage_Paypal_Model_Webhook_Event $event,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Payment $payment
    ): void {
        $authorizationId = (string) ($event->getData('paypal_authorization_id') ?: $event->getData('resource_id'));
        if ($authorizationId === '') {
            throw new Mage_Paypal_Model_Exception('Authorization void webhook does not include an authorization ID.');
        }

        // loadByTxnId() refuses to run without the payment it is scoped to.
        $transaction = Mage::getModel('sales/order_payment_transaction')
            ->setOrderPaymentObject($payment)
            ->loadByTxnId($authorizationId);
        if ($transaction->getId()) {
            $transaction->setIsClosed(1)->save();
        }

        $additionalInformation = $payment->getAdditionalInformation();
        unset($additionalInformation[Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_STATUS]);
        unset($additionalInformation[Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID]);
        unset($additionalInformation[Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME]);
        $payment->setAdditionalInformation($additionalInformation)->save();

        $order->addStatusHistoryComment(
            Mage::helper('paypal')->__('PayPal authorization was voided. Authorization ID: %s', $authorizationId),
            false,
        )->save();
    }

    private function processDispute(Mage_Paypal_Model_Webhook_Event $event, Mage_Sales_Model_Order $order): void
    {
        $message = Mage::helper('paypal')->__(
            'PayPal webhook reported a dispute event (%s). Review and resolve manually.',
            (string) $event->getData('event_type'),
        );

        if ($order->canHold()) {
            $order->hold()->addStatusHistoryComment($message, false)->save();
            return;
        }

        $order->addStatusHistoryComment($message, false)->save();
    }

    private function createOrUpdateTransaction(
        Mage_Sales_Model_Order_Payment $payment,
        string $transactionId,
        string $transactionType,
        ?string $parentTransactionId,
        Mage_Paypal_Model_Webhook_Event $event
    ): void {
        // loadByTxnId() refuses to run without the payment it is scoped to.
        $transaction = Mage::getModel('sales/order_payment_transaction')
            ->setOrderPaymentObject($payment)
            ->loadByTxnId($transactionId);
        if (!$transaction->getId()) {
            $transaction->setOrderPaymentObject($payment)
                ->setTxnId($transactionId)
                ->setTxnType($transactionType);

            if ($parentTransactionId !== null && $parentTransactionId !== '') {
                $transaction->setParentTxnId($parentTransactionId);
            }
        }

        $transaction->setIsClosed(1)
            ->setAdditionalInformation('paypal_webhook_event_id', $event->getData('webhook_event_id'))
            ->save();
    }

    private function hasInvoiceForTransaction(Mage_Sales_Model_Order $order, string $transactionId): bool
    {
        foreach ($order->getInvoiceCollection() as $invoice) {
            if ((string) $invoice->getTransactionId() === $transactionId) {
                return true;
            }

            if ((int) $invoice->getState() === Mage_Sales_Model_Order_Invoice::STATE_PAID) {
                return true;
            }
        }

        return false;
    }

    private function extractReason(Mage_Paypal_Model_Webhook_Event $event): string
    {
        $payload = $event->getPayload();
        $resource = is_array($payload['resource'] ?? null) ? $payload['resource'] : [];
        $reason = $resource['status_details']['reason'] ?? $resource['status_details']['description'] ?? '';
        return is_scalar($reason) ? (string) $reason : '';
    }
}
