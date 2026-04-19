<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper as ResponseMapper;
use OpenMage\Tests\Unit\OpenMageTest;

class ResponseMapperTest extends OpenMageTest
{
    private ResponseMapper $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = new ResponseMapper();
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

        $this->assertCount(2, $mapped['rates']);
        $this->assertSame('FEDEX_GROUND', $mapped['rates'][0]['service_type']);
        $this->assertSame('ACCOUNT', $mapped['rates'][0]['rated_type']);
        $this->assertSame(12.34, $mapped['rates'][0]['amount']);
        $this->assertSame('USD', $mapped['rates'][0]['currency']);
        $this->assertSame(ResponseMapper::SEVERITY_WARNING, $mapped['alerts'][0]['severity']);
        $this->assertSame([], $mapped['errors']);
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

        $this->assertSame(9.99, $mapped['rates'][0]['amount']);
        $this->assertSame('EUR', $mapped['rates'][0]['currency']);
    }

    public function testMapsRateReplyErrorsFromTopLevel(): void
    {
        $json = [
            'errors' => [
                ['code' => 'INVALID', 'message' => 'Bad input'],
            ],
        ];

        $mapped = $this->mapper->mapRateReply($json);

        $this->assertSame([], $mapped['rates']);
        $this->assertSame(ResponseMapper::SEVERITY_ERROR, $mapped['errors'][0]['severity']);
        $this->assertSame('Bad input', $mapped['errors'][0]['message']);
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

        $this->assertSame('Delivered', $mapped['status']);
        $this->assertSame('FedEx Ground', $mapped['service']);
        $this->assertSame('2026-04-17', $mapped['deliverydate']);
        $this->assertSame('2026-04-15', $mapped['shippeddate']);
        $this->assertSame('J. DOE', $mapped['signedby']);
        $this->assertSame('Beverly Hills, CA, US', $mapped['deliverylocation']);
        $this->assertCount(1, $mapped['progressdetail']);
        $this->assertSame('Delivered', $mapped['progressdetail'][0]['activity']);
    }

    public function testTrackReplyReturnsEmptyWhenNumberNotFound(): void
    {
        $json = ['output' => ['completeTrackResults' => []]];
        $mapped = $this->mapper->mapTrackReply($json, '000');

        $this->assertNull($mapped['status']);
        $this->assertSame([], $mapped['progressdetail']);
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

        $this->assertSame('794644746111', $mapped['tracking_number']);
        $this->assertSame('794644746111', $mapped['master_tracking_number']);
        $this->assertSame('LABEL_BYTES', $mapped['label_content']);
        $this->assertSame([], $mapped['errors']);
    }

    public function testMapsCancelReply(): void
    {
        $json = ['output' => ['cancelledShipment' => true, 'cancellationMessage' => 'OK']];
        $mapped = $this->mapper->mapCancelReply($json);

        $this->assertTrue($mapped['cancelled']);
        $this->assertSame('OK', $mapped['message']);
    }
}
