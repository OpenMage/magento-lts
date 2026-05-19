<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Webhook_Verifier
{
    public const HEADER_AUTH_ALGO = 'PAYPAL-AUTH-ALGO';

    public const HEADER_CERT_URL = 'PAYPAL-CERT-URL';

    public const HEADER_TRANSMISSION_ID = 'PAYPAL-TRANSMISSION-ID';

    public const HEADER_TRANSMISSION_SIG = 'PAYPAL-TRANSMISSION-SIG';

    public const HEADER_TRANSMISSION_TIME = 'PAYPAL-TRANSMISSION-TIME';

    public const VERIFY_ENDPOINT = '/v1/notifications/verify-webhook-signature';

    public const VERIFICATION_SUCCESS = 'SUCCESS';

    /**
     * @var string[]
     */
    public const REQUIRED_HEADERS = [
        self::HEADER_AUTH_ALGO,
        self::HEADER_CERT_URL,
        self::HEADER_TRANSMISSION_ID,
        self::HEADER_TRANSMISSION_SIG,
        self::HEADER_TRANSMISSION_TIME,
    ];

    private readonly Mage_Paypal_Model_Api $api;

    private readonly Mage_Paypal_Model_Config $config;

    public function __construct(?Mage_Paypal_Model_Api $api = null, ?Mage_Paypal_Model_Config $config = null)
    {
        $this->api = $api ?? Mage::getSingleton('paypal/api');
        $this->config = $config ?? Mage::getSingleton('paypal/config');
    }

    /**
     * Verify a PayPal webhook signature through PayPal's REST verification endpoint.
     *
     * @param array<string, null|string> $headers
     * @param array<string, mixed>       $payload
     */
    public function verify(array $headers, array $payload): bool
    {
        $normalizedHeaders = $this->extractRequiredHeaders($headers);
        $webhookId = $this->config->getWebhookId();
        if ($webhookId === '') {
            throw new InvalidArgumentException('PayPal webhook ID is not configured.');
        }

        $response = $this->api->postPaypalRest(self::VERIFY_ENDPOINT, [
            'auth_algo'         => $normalizedHeaders[self::HEADER_AUTH_ALGO],
            'cert_url'          => $normalizedHeaders[self::HEADER_CERT_URL],
            'transmission_id'   => $normalizedHeaders[self::HEADER_TRANSMISSION_ID],
            'transmission_sig'  => $normalizedHeaders[self::HEADER_TRANSMISSION_SIG],
            'transmission_time' => $normalizedHeaders[self::HEADER_TRANSMISSION_TIME],
            'webhook_id'        => $webhookId,
            'webhook_event'     => $payload,
        ]);

        return ($response['verification_status'] ?? null) === self::VERIFICATION_SUCCESS;
    }

    /**
     * @param  array<string, null|string> $headers
     * @return array<string, string>
     */
    public function extractRequiredHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $value) {
            $normalized[strtoupper((string) $name)] = is_string($value) ? trim($value) : '';
        }

        $required = [];
        foreach (self::REQUIRED_HEADERS as $header) {
            $value = $normalized[$header] ?? '';
            if ($value === '') {
                throw new InvalidArgumentException(sprintf('Missing required PayPal webhook header: %s', $header));
            }

            $required[$header] = $value;
        }

        return $required;
    }
}
