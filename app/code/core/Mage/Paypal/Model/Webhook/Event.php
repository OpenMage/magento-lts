<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Webhook_Event extends Mage_Core_Model_Abstract
{
    public const STATUS_RECEIVED = 'received';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_DEFERRED = 'deferred';

    public const STATUS_PROCESSED = 'processed';

    public const STATUS_IGNORED = 'ignored';

    public const STATUS_FAILED = 'failed';

    public const STATUS_DUPLICATE = 'duplicate';

    private const REDACTED_VALUE = '[redacted]';

    private const SENSITIVE_KEYS = [
        'access_token',
        'account_number',
        'address',
        'authorization',
        'birth_date',
        'cert_url',
        'client_secret',
        'email',
        'email_address',
        'given_name',
        'id_token',
        'name',
        'payer',
        'phone',
        'refresh_token',
        'shipping',
        'surname',
        'token',
        'transmission_sig',
    ];

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _construct(): void
    {
        $this->_init('paypal/webhook_event');
    }

    /**
     * Populate this event from a verified PayPal webhook payload.
     *
     * @param array<string, mixed>  $payload
     * @param array<string, string> $headers
     */
    public function populateFromPayload(array $payload, array $headers): self
    {
        $resource = $this->getResourcePayload($payload);
        $relatedIds = $this->extractRelatedIds($resource);

        $eventType = (string) ($payload['event_type'] ?? '');
        $resourceId = (string) ($resource['id'] ?? '');
        $webhookEventId = (string) ($payload['id'] ?? '');

        $this->addData([
            'webhook_event_id'       => $webhookEventId,
            'transmission_id'        => $headers['PAYPAL-TRANSMISSION-ID'] ?? null,
            'event_type'             => $eventType,
            'resource_type'          => $payload['resource_type'] ?? ($resource['resource_type'] ?? null),
            'resource_id'            => $resourceId !== '' ? $resourceId : null,
            'paypal_order_id'        => $this->extractOrderId($eventType, $resource, $relatedIds),
            'paypal_capture_id'      => $this->extractCaptureId($eventType, $resource, $relatedIds),
            'paypal_authorization_id' => $this->extractAuthorizationId($eventType, $resource, $relatedIds),
            'paypal_refund_id'       => $this->extractRefundId($eventType, $resource, $relatedIds),
            'status'                 => self::STATUS_VERIFIED,
            'processing_attempts'    => 0,
            'headers_json'           => $this->encodeJson($this->redactPayload($headers)),
            'payload_json'           => $this->encodeJson($this->redactPayload($payload)),
            'event_time'             => $this->normalizeDate($payload['create_time'] ?? $payload['event_time'] ?? null),
            'updated_at'             => Varien_Date::now(),
        ]);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        $payloadJson = (string) $this->getData('payload_json');
        if ($payloadJson === '') {
            return [];
        }

        $payload = json_decode($payloadJson, true);
        return is_array($payload) ? $payload : [];
    }

    public function markProcessed(?string $message = null): self
    {
        return $this->markStatus(self::STATUS_PROCESSED, $message, true);
    }

    public function markIgnored(?string $message = null): self
    {
        return $this->markStatus(self::STATUS_IGNORED, $message, true);
    }

    public function markDeferred(string $message): self
    {
        return $this->markStatus(self::STATUS_DEFERRED, $message, false);
    }

    public function markFailed(string $message): self
    {
        return $this->markStatus(self::STATUS_FAILED, $message, false);
    }

    public function incrementProcessingAttempts(): self
    {
        $this->setData('processing_attempts', ((int) $this->getData('processing_attempts')) + 1);
        $this->setData('updated_at', Varien_Date::now());
        return $this;
    }

    public function isTerminal(): bool
    {
        return in_array(
            (string) $this->getData('status'),
            [self::STATUS_PROCESSED, self::STATUS_IGNORED, self::STATUS_DUPLICATE],
            true,
        );
    }

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function getResourcePayload(array $payload): array
    {
        $resource = $payload['resource'] ?? [];
        return is_array($resource) ? $resource : [];
    }

    /**
     * @param  array<string, mixed>  $resource
     * @return array<string, string>
     */
    public function extractRelatedIds(array $resource): array
    {
        $relatedIds = $resource['supplementary_data']['related_ids'] ?? [];
        if (!is_array($relatedIds)) {
            return [];
        }

        return array_filter(
            array_map(strval(...), $relatedIds),
            static fn(string $value): bool => $value !== '',
        );
    }

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function redactPayload(array $payload): array
    {
        $redacted = [];
        foreach ($payload as $key => $value) {
            $normalizedKey = strtolower((string) $key);
            if ($this->isSensitiveKey($normalizedKey)) {
                $redacted[$key] = self::REDACTED_VALUE;
                continue;
            }

            if (is_array($value)) {
                $redacted[$key] = $this->redactPayload($value);
                continue;
            }

            $redacted[$key] = $value;
        }

        return $redacted;
    }

    private function markStatus(string $status, ?string $message, bool $processed): self
    {
        $this->setData('status', $status);
        $this->setData('last_error', $message);
        $this->setData('updated_at', Varien_Date::now());
        if ($processed) {
            $this->setData('processed_at', Varien_Date::now());
        }

        return $this;
    }

    /**
     * @param array<string, mixed>  $resource
     * @param array<string, string> $relatedIds
     */
    private function extractOrderId(string $eventType, array $resource, array $relatedIds): ?string
    {
        if (($relatedIds['order_id'] ?? '') !== '') {
            return $relatedIds['order_id'];
        }

        $resourceId = (string) ($resource['id'] ?? '');
        return str_starts_with($eventType, 'CHECKOUT.ORDER.') && $resourceId !== '' ? $resourceId : null;
    }

    /**
     * @param array<string, mixed>  $resource
     * @param array<string, string> $relatedIds
     */
    private function extractCaptureId(string $eventType, array $resource, array $relatedIds): ?string
    {
        if (($relatedIds['capture_id'] ?? '') !== '') {
            return $relatedIds['capture_id'];
        }

        $resourceId = (string) ($resource['id'] ?? '');
        if ($resourceId === '') {
            return null;
        }

        return str_starts_with($eventType, 'PAYMENT.CAPTURE.')
            && $eventType !== 'PAYMENT.CAPTURE.REFUNDED'
                ? $resourceId
                : null;
    }

    /**
     * @param array<string, mixed>  $resource
     * @param array<string, string> $relatedIds
     */
    private function extractAuthorizationId(string $eventType, array $resource, array $relatedIds): ?string
    {
        if (($relatedIds['authorization_id'] ?? '') !== '') {
            return $relatedIds['authorization_id'];
        }

        $resourceId = (string) ($resource['id'] ?? '');
        return str_starts_with($eventType, 'PAYMENT.AUTHORIZATION.') && $resourceId !== '' ? $resourceId : null;
    }

    /**
     * @param array<string, mixed>  $resource
     * @param array<string, string> $relatedIds
     */
    private function extractRefundId(string $eventType, array $resource, array $relatedIds): ?string
    {
        if (($relatedIds['refund_id'] ?? '') !== '') {
            return $relatedIds['refund_id'];
        }

        $resourceId = (string) ($resource['id'] ?? '');
        return $eventType === 'PAYMENT.CAPTURE.REFUNDED' && $resourceId !== '' ? $resourceId : null;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return (new DateTime($value))->format(Varien_Date::DATETIME_PHP_FORMAT);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array<mixed> $value
     */
    private function encodeJson(array $value): string
    {
        $json = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return is_string($json) ? $json : '{}';
    }

    private function isSensitiveKey(string $key): bool
    {
        foreach (self::SENSITIVE_KEYS as $sensitiveKey) {
            if ($key === $sensitiveKey || str_contains($key, $sensitiveKey)) {
                return true;
            }
        }

        return false;
    }
}
