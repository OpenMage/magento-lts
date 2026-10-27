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

    /**
     * Mage::getModel() always passes its (empty) arguments array as the first constructor argument.
     *
     * @param null|array<mixed>|Mage_Paypal_Model_Api $api
     */
    public function __construct(null|array|Mage_Paypal_Model_Api $api = null, ?Mage_Paypal_Model_Config $config = null)
    {
        $this->api = $api instanceof Mage_Paypal_Model_Api ? $api : Mage::getSingleton('paypal/api');
        $this->config = $config ?? Mage::getSingleton('paypal/config');
    }

    /**
     * Verify a PayPal webhook signature through PayPal's REST verification endpoint. $rawBody is the request body
     * exactly as PayPal sent it.
     *
     * @param array<string, null|string> $headers
     */
    public function verify(array $headers, string $rawBody): bool
    {
        $normalizedHeaders = $this->extractRequiredHeaders($headers);
        $webhookId = $this->config->getWebhookId();
        if ($webhookId === '') {
            throw new InvalidArgumentException('PayPal webhook ID is not configured.');
        }

        $response = $this->api->postPaypalRest(
            self::VERIFY_ENDPOINT,
            $this->buildVerificationRequest($normalizedHeaders, $webhookId, $rawBody),
        );

        return ($response['verification_status'] ?? null) === self::VERIFICATION_SUCCESS;
    }

    /**
     * Build the verification request with the webhook event embedded verbatim.
     *
     * PayPal verifies the signature against the event as it was sent. A json_decode()/json_encode() round trip
     * turns empty objects into empty arrays, which makes an authentic event fail verification.
     *
     * @param array<string, string> $normalizedHeaders
     */
    public function buildVerificationRequest(array $normalizedHeaders, string $webhookId, string $rawBody): string
    {
        $rawBody = trim($rawBody);
        if (!str_starts_with($rawBody, '{') || !is_array(json_decode($rawBody, true))) {
            throw new InvalidArgumentException('PayPal webhook body is not a JSON object.');
        }

        $request = json_encode([
            'auth_algo'         => $normalizedHeaders[self::HEADER_AUTH_ALGO],
            'cert_url'          => $normalizedHeaders[self::HEADER_CERT_URL],
            'transmission_id'   => $normalizedHeaders[self::HEADER_TRANSMISSION_ID],
            'transmission_sig'  => $normalizedHeaders[self::HEADER_TRANSMISSION_SIG],
            'transmission_time' => $normalizedHeaders[self::HEADER_TRANSMISSION_TIME],
            'webhook_id'        => $webhookId,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($request)) {
            throw new InvalidArgumentException('Failed to encode the PayPal webhook verification request.');
        }

        return substr($request, 0, -1) . ',"webhook_event":' . $rawBody . '}';
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
