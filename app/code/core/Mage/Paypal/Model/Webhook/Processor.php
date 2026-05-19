<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Webhook_Processor
{
    public const ACTION_CAPTURE_COMPLETED = 'capture_completed';

    public const ACTION_CAPTURE_DENIED = 'capture_denied';

    public const ACTION_CAPTURE_REFUNDED = 'capture_refunded';

    public const ACTION_CAPTURE_REVERSED = 'capture_reversed';

    public const ACTION_AUTHORIZATION_VOIDED = 'authorization_voided';

    public const ACTION_DISPUTE = 'dispute';

    public const ACTION_SUBSCRIPTION = 'subscription';

    public const ACTION_IGNORE = 'ignore';

    private readonly Mage_Paypal_Model_Webhook_Event_Resolver $resolver;

    public function __construct(?Mage_Paypal_Model_Webhook_Event_Resolver $resolver = null)
    {
        $this->resolver = $resolver ?? Mage::getModel('paypal/webhook_event_resolver');
    }

    public function process(Mage_Paypal_Model_Webhook_Event $event): void
    {
        if ($event->isTerminal()) {
            return;
        }

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

            $event->incrementProcessingAttempts();
            $action = $this->getActionForEventType((string) $event->getData('event_type'));

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
                $event->markDeferred('No matching Magento order/payment was found yet.')->save();
                $connection?->commit();
                return;
            }

            switch ($action) {
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

        $transaction = Mage::getModel('sales/order_payment_transaction')->loadByTxnId($authorizationId);
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
        $transaction = Mage::getModel('sales/order_payment_transaction')->loadByTxnId($transactionId);
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
