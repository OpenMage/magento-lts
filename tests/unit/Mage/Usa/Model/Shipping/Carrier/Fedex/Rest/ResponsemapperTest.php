<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper as Responsemapper;
use OpenMage\Tests\Unit\OpenMageTest;

final class ResponsemapperTest extends OpenMageTest
{
    private Responsemapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new Responsemapper();
    }

    public function testMapsRateReply(): void
    {
        $json = [
            'output' => [
                'rateReplyDetails' => [
                    [
                        'serviceType' => 'FEDEX_GROUND',
                        'ratedShipmentDetails' => [
                            ['rateType' => 'ACCOUNT', 'totalNetCharge' => 12.34, 'currency' => 'USD'],
                            ['rateType' => 'LIST', 'totalNetCharge' => 15.0, 'currency' => 'USD'],
                        ],
                    ],
                ],
                'alerts' => [
                    ['alertType' => 'WARNING', 'code' => 'W001', 'message' => 'Heads up'],
                ],
            ],
        ];

        $mapped = $this->mapper->mapRateReply($json);

        self::assertCount(2, $mapped['rates']);
        self::assertSame('FEDEX_GROUND', $mapped['rates'][0]['service_type']);
        self::assertSame('ACCOUNT', $mapped['rates'][0]['rated_type']);
        self::assertSame(12.34, $mapped['rates'][0]['amount']);
        self::assertSame('USD', $mapped['rates'][0]['currency']);
        self::assertSame(Responsemapper::SEVERITY_WARNING, $mapped['alerts'][0]['severity']);
        self::assertSame([], $mapped['errors']);
    }

    public function testRateReplyHandlesStructuredTotalNetCharge(): void
    {
        $json = [
            'output' => [
                'rateReplyDetails' => [[
                    'serviceType' => 'FEDEX_GROUND',
                    'ratedShipmentDetails' => [[
                        'rateType' => 'ACCOUNT',
                        'totalNetCharge' => ['amount' => 9.99, 'currency' => 'EUR'],
                    ]],
                ]],
            ],
        ];

        $mapped = $this->mapper->mapRateReply($json);

        self::assertSame(9.99, $mapped['rates'][0]['amount']);
        self::assertSame('EUR', $mapped['rates'][0]['currency']);
    }

    public function testMapsRateReplyErrorsFromTopLevel(): void
    {
        $json = [
            'errors' => [
                ['code' => 'INVALID', 'message' => 'Bad input'],
            ],
        ];

        $mapped = $this->mapper->mapRateReply($json);

        self::assertSame([], $mapped['rates']);
        self::assertSame(Responsemapper::SEVERITY_ERROR, $mapped['errors'][0]['severity']);
        self::assertSame('Bad input', $mapped['errors'][0]['message']);
    }

    public function testMapsTrackReply(): void
    {
        $json = [
            'output' => [
                'completeTrackResults' => [[
                    'trackingNumber' => '794644746111',
                    'trackResults' => [[
                        'latestStatusDetail' => ['description' => 'Delivered'],
                        'serviceDetail' => ['description' => 'FedEx Ground'],
                        'dateAndTimes' => [
                            ['type' => 'ACTUAL_DELIVERY', 'dateTime' => '2026-04-17T12:34:56-05:00'],
                            ['type' => 'ACTUAL_PICKUP', 'dateTime' => '2026-04-15T08:00:00-05:00'],
                        ],
                        'deliveryDetails' => [
                            'receivedByName' => 'J. DOE',
                            'actualDeliveryAddress' => ['city' => 'Beverly Hills', 'stateOrProvinceCode' => 'CA', 'countryCode' => 'US'],
                        ],
                        'scanEvents' => [[
                            'date' => '2026-04-17T12:34:56-05:00',
                            'eventDescription' => 'Delivered',
                            'scanLocation' => ['city' => 'Beverly Hills', 'stateOrProvinceCode' => 'CA', 'countryCode' => 'US'],
                        ]],
                    ]],
                ]],
            ],
        ];

        $mapped = $this->mapper->mapTrackReply($json, '794644746111');

        self::assertSame('Delivered', $mapped['status']);
        self::assertSame('FedEx Ground', $mapped['service']);
        self::assertSame('2026-04-17', $mapped['deliverydate']);
        self::assertSame('2026-04-15', $mapped['shippeddate']);
        self::assertSame('J. DOE', $mapped['signedby']);
        self::assertSame('Beverly Hills, CA, US', $mapped['deliverylocation']);
        self::assertCount(1, $mapped['progressdetail']);
        self::assertSame('Delivered', $mapped['progressdetail'][0]['activity']);
    }

    public function testTrackReplyReturnsEmptyWhenNumberNotFound(): void
    {
        $json = ['output' => ['completeTrackResults' => []]];
        $mapped = $this->mapper->mapTrackReply($json, '000');

        self::assertNull($mapped['status']);
        self::assertSame([], $mapped['progressdetail']);
    }

    public function testMapsShipReply(): void
    {
        $json = [
            'output' => [
                'transactionShipments' => [[
                    'masterTrackingNumber' => '794644746111',
                    'pieceResponses' => [[
                        'trackingNumber' => '794644746111',
                        'packageDocuments' => [[
                            'contentType' => 'LABEL',
                            'encodedLabel' => base64_encode('LABEL_BYTES'),
                        ]],
                    ]],
                ]],
            ],
        ];

        $mapped = $this->mapper->mapShipReply($json);

        self::assertSame('794644746111', $mapped['tracking_number']);
        self::assertSame('794644746111', $mapped['master_tracking_number']);
        self::assertSame('LABEL_BYTES', $mapped['label_content']);
        self::assertSame([], $mapped['errors']);
    }

    public function testMapsCancelReply(): void
    {
        $json = ['output' => ['cancelledShipment' => true, 'cancellationMessage' => 'OK']];
        $mapped = $this->mapper->mapCancelReply($json);

        self::assertTrue($mapped['cancelled']);
        self::assertSame('OK', $mapped['message']);
    }
}
