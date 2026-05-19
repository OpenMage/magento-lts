<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook;

use Mage_Paypal_Model_Webhook_Verifier as Subject;

trait VerifierTrait
{
    /**
     * @return array<string, array{0: array<string, null|string>, 1: array<string, string>}>
     */
    public function provideRequiredHeadersData(): array
    {
        return [
            'lowercase names and trimmed values' => [
                [
                    'paypal-auth-algo'         => ' SHA256withRSA ',
                    'paypal-cert-url'          => ' https://api-m.paypal.com/certs/test ',
                    'paypal-transmission-id'   => ' transmission-id ',
                    'paypal-transmission-sig'  => ' signature ',
                    'paypal-transmission-time' => ' 2026-05-19T12:00:00Z ',
                ],
                [
                    Subject::HEADER_AUTH_ALGO         => 'SHA256withRSA',
                    Subject::HEADER_CERT_URL          => 'https://api-m.paypal.com/certs/test',
                    Subject::HEADER_TRANSMISSION_ID   => 'transmission-id',
                    Subject::HEADER_TRANSMISSION_SIG  => 'signature',
                    Subject::HEADER_TRANSMISSION_TIME => '2026-05-19T12:00:00Z',
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{0: bool, 1: array<string, mixed>}>
     */
    public function provideVerificationResponses(): array
    {
        return [
            'success' => [true, ['verification_status' => Subject::VERIFICATION_SUCCESS]],
            'failure' => [false, ['verification_status' => 'FAILURE']],
            'missing status' => [false, []],
        ];
    }
}
