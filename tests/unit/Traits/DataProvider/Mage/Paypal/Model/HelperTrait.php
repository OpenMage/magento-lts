<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model;

trait HelperTrait
{
    /**
     * Scenarios for extractCaptureId() / extractCaptureAmount().
     *
     * `kind` selects the response shape the test builds; `amount` is the
     * capture amount string when one is present.
     *
     * @return array<string, array{string, ?string}>
     */
    public static function provideCaptureResultShapes(): array
    {
        return [
            'null result'              => ['null', null],
            'scalar result'            => ['string', null],
            'object without getters'   => ['plain', null],
            'no purchase units'        => ['emptyUnits', null],
            'purchase unit no payments' => ['noPayments', null],
            'no captures'              => ['emptyCaptures', null],
            'capture without amount'   => ['noAmount', null],
            'complete capture'         => ['ok', '42.17'],
        ];
    }

    /**
     * Scenarios for prepareRawDetails().
     *
     * Each case pairs a raw PayPal JSON response body with the whitelisted,
     * flattened result expected on the transaction record. Covers Orders v2
     * order responses and the standalone Payments v2 capture / refund /
     * authorization resources.
     *
     * @return array<string, array{string, array<string, string>}>
     */
    public static function provideRawDetails(): array
    {
        $captureOnOrder = (string) json_encode([
            'id' => 'ORDER-1',
            'intent' => 'CAPTURE',
            'status' => 'COMPLETED',
            'links' => [['href' => 'https://example.com', 'rel' => 'self']],
            'payer' => [
                'email_address' => 'buyer@example.com',
                'name' => ['given_name' => 'Jane', 'surname' => 'Doe'],
            ],
            'purchase_units' => [[
                'invoice_id' => 'INV-9',
                'amount' => ['currency_code' => 'EUR', 'value' => '45.93'],
                // A large item list must not leak into the result.
                'items' => array_fill(0, 100, ['name' => 'Widget', 'quantity' => '1']),
                'payments' => [
                    'captures' => [[
                        'id' => 'CAP-1',
                        'status' => 'COMPLETED',
                        'amount' => ['currency_code' => 'EUR', 'value' => '45.93'],
                        'seller_receivable_breakdown' => [
                            'paypal_fee' => ['currency_code' => 'EUR', 'value' => '1.91'],
                            'net_amount' => ['currency_code' => 'EUR', 'value' => '44.02'],
                        ],
                    ]],
                ],
            ]],
            'create_time' => '2026-05-19T12:15:29Z',
            'update_time' => '2026-05-19T12:16:10Z',
        ]);

        $standaloneCapture = (string) json_encode([
            'id' => 'CAP-2',
            'status' => 'COMPLETED',
            'amount' => ['currency_code' => 'USD', 'value' => '20.00'],
            'invoice_id' => 'INV-2',
            'seller_receivable_breakdown' => [
                'paypal_fee' => ['currency_code' => 'USD', 'value' => '0.88'],
                'net_amount' => ['currency_code' => 'USD', 'value' => '19.12'],
            ],
            'create_time' => '2026-05-19T13:00:00Z',
        ]);

        $standaloneRefund = (string) json_encode([
            'id' => 'REF-1',
            'status' => 'COMPLETED',
            'amount' => ['currency_code' => 'EUR', 'value' => '10.60'],
            'seller_payable_breakdown' => [
                'paypal_fee' => ['currency_code' => 'EUR', 'value' => '0.40'],
                'net_amount' => ['currency_code' => 'EUR', 'value' => '10.20'],
            ],
        ]);

        $orderAuthorization = (string) json_encode([
            'id' => 'ORDER-A',
            'intent' => 'AUTHORIZE',
            'status' => 'COMPLETED',
            // Newer responses carry the payer under payment_source.paypal.
            'payment_source' => [
                'paypal' => [
                    'email_address' => 'auth@example.com',
                    'name' => ['given_name' => 'Sam', 'surname' => 'Lee'],
                ],
            ],
            'purchase_units' => [[
                'amount' => ['currency_code' => 'USD', 'value' => '99.00'],
                'payments' => [
                    'authorizations' => [[
                        'id' => 'AUTH-9',
                        'status' => 'CREATED',
                        'amount' => ['currency_code' => 'USD', 'value' => '99.00'],
                        'expiration_time' => '2026-06-01T00:00:00Z',
                    ]],
                ],
            ]],
        ]);

        $standaloneAuthorization = (string) json_encode([
            'id' => 'AUTH-S',
            'status' => 'CREATED',
            'amount' => ['currency_code' => 'USD', 'value' => '55.00'],
            'expiration_time' => '2026-07-01T00:00:00Z',
        ]);

        $zeroAmountNoPayer = (string) json_encode([
            'id' => 'ORDER-Z',
            'status' => 'CREATED',
            'purchase_units' => [[
                'amount' => ['currency_code' => 'USD', 'value' => '0.00'],
            ]],
        ]);

        return [
            'invalid json'    => ['{not json', []],
            'non-array json'  => ['"just a string"', []],
            'null json'       => ['null', []],
            'capture on order' => [$captureOnOrder, [
                'id' => 'ORDER-1',
                'intent' => 'CAPTURE',
                'status' => 'COMPLETED',
                'payer_email' => 'buyer@example.com',
                'payer_name' => 'Jane Doe',
                'amount' => 'EUR 45.93',
                'invoice_id' => 'INV-9',
                'capture_id' => 'CAP-1',
                'capture_amount' => 'EUR 45.93',
                'paypal_fee' => 'EUR 1.91',
                'net_amount' => 'EUR 44.02',
                'create_time' => '2026-05-19T12:15:29Z',
                'update_time' => '2026-05-19T12:16:10Z',
            ]],
            'standalone capture' => [$standaloneCapture, [
                'id' => 'CAP-2',
                'status' => 'COMPLETED',
                'amount' => 'USD 20.00',
                'invoice_id' => 'INV-2',
                'capture_id' => 'CAP-2',
                'capture_amount' => 'USD 20.00',
                'paypal_fee' => 'USD 0.88',
                'net_amount' => 'USD 19.12',
                'create_time' => '2026-05-19T13:00:00Z',
            ]],
            'standalone refund' => [$standaloneRefund, [
                'id' => 'REF-1',
                'status' => 'COMPLETED',
                'amount' => 'EUR 10.60',
                'paypal_fee' => 'EUR 0.40',
                'net_amount' => 'EUR 10.20',
                'refund_id' => 'REF-1',
                'refund_amount' => 'EUR 10.60',
            ]],
            'order authorization' => [$orderAuthorization, [
                'id' => 'ORDER-A',
                'intent' => 'AUTHORIZE',
                'status' => 'COMPLETED',
                'payer_email' => 'auth@example.com',
                'payer_name' => 'Sam Lee',
                'amount' => 'USD 99.00',
                'authorization_id' => 'AUTH-9',
                'authorization_status' => 'CREATED',
                'authorization_amount' => 'USD 99.00',
                'expiration_time' => '2026-06-01T00:00:00Z',
            ]],
            'standalone authorization' => [$standaloneAuthorization, [
                'id' => 'AUTH-S',
                'status' => 'CREATED',
                'amount' => 'USD 55.00',
                'authorization_id' => 'AUTH-S',
                'authorization_status' => 'CREATED',
                'authorization_amount' => 'USD 55.00',
                'expiration_time' => '2026-07-01T00:00:00Z',
            ]],
            'zero amount, no payer' => [$zeroAmountNoPayer, [
                'id' => 'ORDER-Z',
                'status' => 'CREATED',
                'amount' => 'USD 0.00',
            ]],
        ];
    }
}
