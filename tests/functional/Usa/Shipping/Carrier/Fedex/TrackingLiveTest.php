<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Functional\Usa\Shipping\Carrier\Fedex;

use Override;
use Mage_Shipping_Model_Tracking_Result;
use Mage_Shipping_Model_Tracking_Result_Error;
use Mage_Shipping_Model_Tracking_Result_Status;
use OpenMage\Tests\Functional\Usa\Shipping\Carrier\FedexTestCase;

final class TrackingLiveTest extends FedexTestCase
{
    /**
     * @return array<int, string>
     */
    #[Override]
    protected static function requiredEnv(): array
    {
        return ['FEDEX_TRACKING_CLIENT_ID', 'FEDEX_TRACKING_CLIENT_SECRET'];
    }

    public function testLiveTrackingReturnsStatus(): void
    {
        $carrier = $this->buildFedexCarrier();

        $result = $carrier->getTracking($this->trackingNumber);

        self::assertInstanceOf(Mage_Shipping_Model_Tracking_Result::class, $result);

        $trackings = $result->getAllTrackings();
        self::assertNotEmpty($trackings, 'FedEx tracking returned no result rows.');

        $errors = array_values(array_filter(
            $trackings,
            static fn($entry): bool => $entry instanceof Mage_Shipping_Model_Tracking_Result_Error,
        ));
        $statuses = array_values(array_filter(
            $trackings,
            static fn($entry): bool => $entry instanceof Mage_Shipping_Model_Tracking_Result_Status,
        ));

        if ($statuses === []) {
            $messages = array_map(
                static fn($error): string => (string) $error->getErrorMessage(),
                $errors,
            );
            self::fail(sprintf(
                "FedEx tracking returned no status rows for %s. Errors:\n  - %s",
                $this->trackingNumber,
                implode("\n  - ", $messages !== [] ? $messages : ['(no error messages returned)']),
            ));
        }

        $status = $statuses[0];
        self::assertSame('fedex', $status->getCarrier());
        self::assertSame($this->trackingNumber, $status->getTracking());
        self::assertNotSame('', (string) $status->getStatus());
    }

    public function testLiveTrackingGetResponseIncludesStatusText(): void
    {
        $carrier = $this->buildFedexCarrier();

        $carrier->getTracking($this->trackingNumber);

        $response = (string) $carrier->getResponse();
        self::assertNotSame('', $response);
        self::assertNotSame('Empty response', $response, 'Carrier reported an empty tracking response.');
    }

    public function testLiveTrackingReturnsOneRowPerMultiPieceTrackingNumber(): void
    {
        $trackingNumbers = [$this->trackingNumber, $this->trackingNumberAlt];

        $carrier = $this->buildFedexCarrier();

        $result = $carrier->getTracking($trackingNumbers);

        self::assertInstanceOf(Mage_Shipping_Model_Tracking_Result::class, $result);

        $trackings = $result->getAllTrackings();
        self::assertCount(
            count($trackingNumbers),
            $trackings,
            sprintf(
                'Expected one result row per tracking number (%d), got %d.',
                count($trackingNumbers),
                count($trackings),
            ),
        );

        $statuses = array_values(array_filter(
            $trackings,
            static fn($entry): bool => $entry instanceof Mage_Shipping_Model_Tracking_Result_Status,
        ));
        $errors = array_values(array_filter(
            $trackings,
            static fn($entry): bool => $entry instanceof Mage_Shipping_Model_Tracking_Result_Error,
        ));

        if ($statuses === []) {
            $messages = array_map(
                static fn($error): string => (string) $error->getErrorMessage(),
                $errors,
            );
            self::fail(sprintf(
                "FedEx batch tracking returned no status rows for [%s]. Errors:\n  - %s",
                implode(',', $trackingNumbers),
                implode("\n  - ", $messages !== [] ? $messages : ['(no error messages returned)']),
            ));
        }

        $returnedNumbers = array_map(
            static fn($entry): string => (string) $entry->getTracking(),
            $trackings,
        );

        foreach ($trackingNumbers as $trackingNumber) {
            self::assertContains(
                $trackingNumber,
                $returnedNumbers,
                sprintf(
                    'Expected tracking result for %s; got results for [%s].',
                    $trackingNumber,
                    implode(',', $returnedNumbers),
                ),
            );
        }
    }
}
