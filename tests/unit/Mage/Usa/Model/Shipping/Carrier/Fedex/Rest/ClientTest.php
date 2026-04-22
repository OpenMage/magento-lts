<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Carbon\CarbonImmutable;
use Varien_Object;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client as Client;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder as RequestBuilder;
use OpenMage\Tests\Unit\OpenMageTest;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use ShipStream\FedEx\Api\RatesAndTransitTimesV1\Requests\RateAndTransitTimes;
use ShipStream\FedEx\Api\ShipV1\Requests\CancelShipment;
use ShipStream\FedEx\Api\ShipV1\Requests\CreateShipment;
use ShipStream\FedEx\Api\TrackV1\Requests\TrackByTrackingNumber;
use ShipStream\FedEx\Auth\MemoryCache;
use ShipStream\FedEx\Enums\Endpoint;
use ShipStream\FedEx\FedEx;

final class ClientTest extends OpenMageTest
{
    protected function setUp(): void
    {
        parent::setUp();
        MemoryCache::set('test-id.sandbox', new AccessTokenAuthenticator(
            'test-access-token',
            null,
            CarbonImmutable::now()->addHours(1),
        ));
    }

    public function testGetRatesReturnsDecodedBody(): void
    {
        $expected = ['output' => ['rateReplyDetails' => [['serviceType' => 'FEDEX_GROUND']]]];
        $client = $this->clientWithMocks([
            RateAndTransitTimes::class => MockResponse::make($expected, 200),
        ]);

        $response = $client->getRates($this->validRatePayload());

        self::assertSame($expected, $response);
    }

    public function testGetRatesHydratesInternationalCustomsClearanceDetail(): void
    {
        $expected = ['output' => ['rateReplyDetails' => [['serviceType' => 'INTERNATIONAL_ECONOMY']]]];
        $client = $this->clientWithMocks([
            RateAndTransitTimes::class => MockResponse::make($expected, 200),
        ]);

        $raw = (new Varien_Object())
            ->setAccount('510510510')
            ->setDropoffType('REGULAR_PICKUP')
            ->setPackaging('YOUR_PACKAGING')
            ->setOrigPostal('38116')
            ->setOrigCountry('US')
            ->setDestPostal('SW1A 1AA')
            ->setDestCountry('GB')
            ->setWeight(1.36)
            ->setValue(24.99)
            ->setUnitOfMeasure('LB')
            ->setResidenceDelivery(true);

        $payload = (new RequestBuilder())->buildRatePayload($raw, 'USD');

        $response = $client->getRates($payload);

        self::assertSame($expected, $response);
    }

    public function testTrackReturnsDecodedBody(): void
    {
        $expected = ['output' => ['completeTrackResults' => [['trackingNumber' => '794644746111']]]];
        $client = $this->clientWithMocks([
            TrackByTrackingNumber::class => MockResponse::make($expected, 200),
        ]);

        $response = $client->track([
            'includeDetailedScans' => true,
            'trackingInfo' => [['trackingNumberInfo' => ['trackingNumber' => '794644746111']]],
        ]);

        self::assertSame($expected, $response);
    }

    public function testProcessShipmentReturnsDecodedBody(): void
    {
        $expected = ['output' => ['transactionShipments' => [['masterTrackingNumber' => '111']]]];
        $client = $this->clientWithMocks([
            CreateShipment::class => MockResponse::make($expected, 200),
        ]);

        $response = $client->processShipment($this->validShipmentPayload());

        self::assertSame($expected, $response);
    }

    public function testDeleteShipmentReturnsDecodedBody(): void
    {
        $expected = ['output' => ['cancelledShipment' => true]];
        $client = $this->clientWithMocks([
            CancelShipment::class => MockResponse::make($expected, 200),
        ]);

        $response = $client->deleteShipment([
            'accountNumber' => ['value' => '510510510'],
            'trackingNumber' => '794644746111',
            'deletionControl' => 'DELETE_ONE_PACKAGE',
        ]);

        self::assertSame($expected, $response);
    }

    public function testRestExceptionReturnsErrorBody(): void
    {
        $errorBody = ['errors' => [['code' => 'INVALID', 'message' => 'Missing field']]];
        $client = $this->clientWithMocks([
            RateAndTransitTimes::class => MockResponse::make($errorBody, 400),
        ]);

        $response = $client->getRates($this->validRatePayload());

        self::assertSame($errorBody, $response);
    }

    public function testInvalidJsonOnSuccessReturnsParseError(): void
    {
        $client = $this->clientWithMocks([
            RateAndTransitTimes::class => MockResponse::make('{not json', 200),
        ]);

        $response = $client->getRates($this->validRatePayload());

        self::assertSame('Could not parse client response', $response['errors'][0]['message']);
        self::assertArrayHasKey('detail', $response['errors'][0]);
    }

    public function testInvalidJsonOnErrorReturnsParseError(): void
    {
        $client = $this->clientWithMocks([
            RateAndTransitTimes::class => MockResponse::make('{not json', 500),
        ]);

        $response = $client->getRates($this->validRatePayload());

        self::assertSame('Could not parse client response', $response['errors'][0]['message']);
        self::assertArrayHasKey('detail', $response['errors'][0]);
    }

    /**
     * @param array<string, MockResponse> $responses
     */
    private function clientWithMocks(array $responses): Client
    {
        $connector = new FedEx(
            clientId: 'test-id',
            clientSecret: 'test-secret',
            endpoint: Endpoint::SANDBOX,
        );
        $connector->withMockClient(new MockClient($responses));

        return new Client($connector);
    }

    /**
     * @return array<string, array<array<string, mixed>, mixed>>
     */
    private function validRatePayload(): array
    {
        return [
            'accountNumber' => ['value' => '510510510'],
            'requestedShipment' => [
                'shipper' => ['address' => ['postalCode' => '38116', 'countryCode' => 'US']],
                'recipient' => ['address' => ['postalCode' => '90210', 'countryCode' => 'US', 'residential' => false]],
                'pickupType' => 'USE_SCHEDULED_PICKUP',
                'packagingType' => 'YOUR_PACKAGING',
                'rateRequestType' => ['LIST', 'ACCOUNT'],
                'totalPackageCount' => 1,
                'shippingChargesPayment' => [
                    'paymentType' => 'SENDER',
                    'payor' => [
                        'responsibleParty' => [
                            'accountNumber' => ['value' => '510510510'],
                            'address' => ['countryCode' => 'US'],
                        ],
                    ],
                ],
                'requestedPackageLineItems' => [[
                    'groupPackageCount' => 1,
                    'weight' => ['units' => 'LB', 'value' => 10.0],
                ]],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validShipmentPayload(): array
    {
        return [
            'accountNumber' => ['value' => '510510510'],
            'labelResponseOptions' => 'LABEL',
            'mergeLabelDocOption' => 'LABELS_AND_DOCS',
            'requestedShipment' => [
                'shipper' => [
                    'contact' => ['personName' => 'Shipper', 'companyName' => 'Ship Co', 'phoneNumber' => '8005551212'],
                    'address' => [
                        'streetLines' => ['123 Ship St'],
                        'city' => 'Memphis',
                        'stateOrProvinceCode' => 'TN',
                        'postalCode' => '38116',
                        'countryCode' => 'US',
                    ],
                ],
                'recipients' => [[
                    'contact' => ['personName' => 'R', 'companyName' => 'R Co', 'phoneNumber' => '2125551212'],
                    'address' => [
                        'streetLines' => ['1 Test Ave'],
                        'city' => 'Beverly Hills',
                        'stateOrProvinceCode' => 'CA',
                        'postalCode' => '90210',
                        'countryCode' => 'US',
                        'residential' => false,
                    ],
                ]],
                'pickupType' => 'USE_SCHEDULED_PICKUP',
                'packagingType' => 'YOUR_PACKAGING',
                'serviceType' => 'FEDEX_GROUND',
                'shippingChargesPayment' => [
                    'paymentType' => 'SENDER',
                    'payor' => [
                        'responsibleParty' => [
                            'accountNumber' => ['value' => '510510510'],
                            'address' => ['countryCode' => 'US'],
                        ],
                    ],
                ],
                'labelSpecification' => [
                    'labelFormatType' => 'COMMON2D',
                    'imageType' => 'PNG',
                    'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL',
                ],
                'rateRequestType' => ['ACCOUNT'],
                'totalPackageCount' => 1,
                'requestedPackageLineItems' => [[
                    'sequenceNumber' => 1,
                    'weight' => ['units' => 'LB', 'value' => 15.0],
                ]],
            ],
        ];
    }
}
