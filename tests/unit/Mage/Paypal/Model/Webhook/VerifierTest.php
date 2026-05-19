<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Webhook;

use InvalidArgumentException;
use Mage_Paypal_Model_Api;
use Mage_Paypal_Model_Config;
use Mage_Paypal_Model_Webhook_Verifier as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook\VerifierTrait;

final class VerifierTest extends OpenMageTest
{
    use VerifierTrait;

    /**
     * @dataProvider provideRequiredHeadersData
     * @param array<string, null|string> $headers
     * @param array<string, string>      $expectedResult
     */
    public function testExtractRequiredHeadersNormalizesHeaderNames(array $headers, array $expectedResult): void
    {
        $subject = new Subject(
            $this->createMock(Mage_Paypal_Model_Api::class),
            $this->createMock(Mage_Paypal_Model_Config::class),
        );

        self::assertSame($expectedResult, $subject->extractRequiredHeaders($headers));
    }

    public function testExtractRequiredHeadersRejectsMissingHeader(): void
    {
        $subject = new Subject(
            $this->createMock(Mage_Paypal_Model_Api::class),
            $this->createMock(Mage_Paypal_Model_Config::class),
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Subject::HEADER_TRANSMISSION_SIG);

        $subject->extractRequiredHeaders([
            Subject::HEADER_AUTH_ALGO         => 'SHA256withRSA',
            Subject::HEADER_CERT_URL          => 'https://api-m.paypal.com/certs/test',
            Subject::HEADER_TRANSMISSION_ID   => 'transmission-id',
            Subject::HEADER_TRANSMISSION_TIME => '2026-05-19T12:00:00Z',
        ]);
    }

    /**
     * @dataProvider provideVerificationResponses
     * @param array<string, mixed> $response
     */
    public function testVerifyPostsPayPalSignatureVerificationRequest(bool $expectedResult, array $response): void
    {
        $headers = [
            Subject::HEADER_AUTH_ALGO         => 'SHA256withRSA',
            Subject::HEADER_CERT_URL          => 'https://api-m.paypal.com/certs/test',
            Subject::HEADER_TRANSMISSION_ID   => 'transmission-id',
            Subject::HEADER_TRANSMISSION_SIG  => 'signature',
            Subject::HEADER_TRANSMISSION_TIME => '2026-05-19T12:00:00Z',
        ];
        $payload = [
            'id'         => 'WH-EVENT-1',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        ];

        $api = $this->createMock(Mage_Paypal_Model_Api::class);
        $api->expects(self::once())
            ->method('postPaypalRest')
            ->with(Subject::VERIFY_ENDPOINT, [
                'auth_algo'         => 'SHA256withRSA',
                'cert_url'          => 'https://api-m.paypal.com/certs/test',
                'transmission_id'   => 'transmission-id',
                'transmission_sig'  => 'signature',
                'transmission_time' => '2026-05-19T12:00:00Z',
                'webhook_id'        => 'WH-123',
                'webhook_event'     => $payload,
            ])
            ->willReturn($response);

        $config = $this->createMock(Mage_Paypal_Model_Config::class);
        $config->expects(self::once())
            ->method('getWebhookId')
            ->willReturn('WH-123');

        $subject = new Subject($api, $config);

        self::assertSame($expectedResult, $subject->verify($headers, $payload));
    }

    public function testVerifyRejectsMissingWebhookId(): void
    {
        $api = $this->createMock(Mage_Paypal_Model_Api::class);
        $api->expects(self::never())->method('postPaypalRest');

        $config = $this->createMock(Mage_Paypal_Model_Config::class);
        $config->expects(self::once())
            ->method('getWebhookId')
            ->willReturn('');

        $subject = new Subject($api, $config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('PayPal webhook ID is not configured.');

        $subject->verify([
            Subject::HEADER_AUTH_ALGO         => 'SHA256withRSA',
            Subject::HEADER_CERT_URL          => 'https://api-m.paypal.com/certs/test',
            Subject::HEADER_TRANSMISSION_ID   => 'transmission-id',
            Subject::HEADER_TRANSMISSION_SIG  => 'signature',
            Subject::HEADER_TRANSMISSION_TIME => '2026-05-19T12:00:00Z',
        ], ['id' => 'WH-EVENT-1']);
    }
}
