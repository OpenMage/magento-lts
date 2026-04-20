<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier;

use Mage_Usa_Model_Shipping_Carrier_Fedex as Subject;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client as RestClient;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\FedexTrait;
use PHPUnit\Framework\MockObject\MockObject;

final class FedexTest extends OpenMageTest
{
    use FedexTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    public function testVersionInfoStaysByteForByteCompatibleWithSoapAncestor(): void
    {
        self::assertSame([
            'ServiceId'    => 'crs',
            'Major'        => '10',
            'Intermediate' => '0',
            'Minor'        => '0',
        ], self::$subject->getVersionInfo());
    }

    public function testContainerTypesMatchesCoreShape(): void
    {
        $all = self::$subject->getContainerTypesAll();
        self::assertArrayHasKey('YOUR_PACKAGING', $all);
        self::assertArrayHasKey('FEDEX_ENVELOPE', $all);

        $filters = self::$subject->getContainerTypesFilter();
        self::assertCount(4, $filters);
        self::assertSame(['FEDEX_ENVELOPE', 'FEDEX_PAK'], $filters[0]['containers']);
    }

    public function testCollectRatesReturnsFalseWhenDisabled(): void
    {
        $fedex = $this->fedexWithConfig(['active' => false]);
        self::assertFalse($fedex->collectRates($this->domesticRateRequest()));
    }

    public function testSetRequestUsesSandboxSmartPostHubIdWhenSandboxModeEnabled(): void
    {
        $fedex = $this->fedexWithConfig([
            'sandbox_mode' => '1',
            'smartpost_hubid' => '9999',
        ]);

        $fedex->setRequest($this->domesticRateRequest());

        self::assertSame('5531', $this->rawRequest($fedex)->getSmartpostHubid());
    }

    public function testSetRequestUsesConfiguredSmartPostHubIdOutsideSandbox(): void
    {
        $fedex = $this->fedexWithConfig([
            'sandbox_mode' => '0',
            'smartpost_hubid' => '9999',
        ]);

        $fedex->setRequest($this->domesticRateRequest());

        self::assertSame('9999', $this->rawRequest($fedex)->getSmartpostHubid());
    }

    public function testCollectRatesBuildsResultFromRestResponse(): void
    {
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('getRates')
            ->willReturn([
                'output' => [
                    'rateReplyDetails' => [[
                        'serviceType' => 'FEDEX_GROUND',
                        'ratedShipmentDetails' => [[
                            'rateType' => 'ACCOUNT',
                            'totalNetCharge' => ['amount' => 12.34, 'currency' => 'USD'],
                        ]],
                    ]],
                ],
            ]);

        $fedex = $this->fedexWithConfig([
            'active' => true,
            'allowed_methods' => 'FEDEX_GROUND',
            'title' => 'FedEx',
        ]);
        $fedex->setData('rest_client', $restClient);
        $fedex->setData('cache_enabled', false);

        $result = $fedex->collectRates($this->domesticRateRequest());

        self::assertInstanceOf(\Mage_Shipping_Model_Rate_Result::class, $result);
        $rates = $result->getAllRates();
        self::assertCount(1, $rates);
        self::assertSame('FEDEX_GROUND', $rates[0]->getMethod());
        self::assertSame(12.34, (float) $rates[0]->getCost());
    }

    public function testGetTrackingBuildsStatusFromRestResponse(): void
    {
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('track')
            ->willReturn([
                'output' => [
                    'completeTrackResults' => [[
                        'trackingNumber' => '794644746111',
                        'trackResults' => [[
                            'latestStatusDetail' => ['description' => 'Delivered'],
                            'serviceDetail' => ['description' => 'FedEx Ground'],
                            'scanEvents' => [],
                        ]],
                    ]],
                ],
            ]);

        $fedex = $this->fedexWithConfig([
            'title' => 'FedEx',
            'tracking_client_id' => 'track-id',
            'tracking_client_secret' => 'track-secret',
        ]);
        $fedex->setData('tracking_rest_client', $restClient);

        $result = $fedex->getTracking('794644746111');
        self::assertInstanceOf(\Mage_Shipping_Model_Tracking_Result::class, $result);
        $trackings = $result->getAllTrackings();
        self::assertCount(1, $trackings);
        self::assertSame('Delivered', $trackings[0]->getStatus());
    }

    public function testGetTrackingReturnsErrorResultWhenTrackingCredentialsMissing(): void
    {
        $fedex = $this->fedexWithConfig([
            'title' => 'FedEx',
            'tracking_client_id' => '',
            'tracking_client_secret' => '',
        ]);

        $result = $fedex->getTracking('794644746111');

        self::assertInstanceOf(\Mage_Shipping_Model_Tracking_Result::class, $result);
        $errors = array_filter(
            $result->getAllTrackings(),
            static fn($entry) => $entry instanceof \Mage_Shipping_Model_Tracking_Result_Error,
        );
        self::assertCount(1, $errors);
        $error = array_values($errors)[0];
        self::assertSame('794644746111', $error->getTracking());
    }

    /**
     * @return iterable<string, array{array<string,string>, string}>
     */
    public static function missingTrackingCredentialProvider(): iterable
    {
        yield 'blank client id' => [
            ['tracking_client_id' => '', 'tracking_client_secret' => 'secret'],
            'tracking_client_id',
        ];
        yield 'blank client secret' => [
            ['tracking_client_id' => 'id', 'tracking_client_secret' => ''],
            'tracking_client_secret',
        ];
    }

    /**
     * @dataProvider missingTrackingCredentialProvider
     * @param array<string,string> $config
     */
    public function testGetTrackingRestClientThrowsWhenEitherCredentialIsMissing(array $config, string $missingField): void
    {
        $fedex = $this->fedexWithConfig($config);

        $method = new \ReflectionMethod(Subject::class, '_getTrackingRestClient');

        try {
            $method->invoke($fedex);
            self::fail('Expected Mage_Core_Exception when tracking credentials are missing');
        } catch (\Mage_Core_Exception $e) {
            self::assertStringContainsString($missingField, $e->getMessage());
        }
    }

    public function testCollectRatesBuildsRestClientFromRatesShipCredentials(): void
    {
        $stubClient = $this->createMock(RestClient::class);
        $stubClient->method('getRates')->willReturn(['output' => ['rateReplyDetails' => []]]);

        $fedex = $this->fedexWithConfig([
            'active' => true,
            'allowed_methods' => 'FEDEX_GROUND',
            'title' => 'FedEx',
            'client_id' => 'rates-id',
            'client_secret' => 'rates-secret',
            'tracking_client_id' => 'track-id',
            'tracking_client_secret' => 'track-secret',
            'sandbox_mode' => '1',
        ]);
        $fedex->setData('cache_enabled', false);

        $calls = new \ArrayObject();
        $fedex->setData('rest_client_factory', $this->recordingFactory($calls, $stubClient));

        $fedex->collectRates($this->domesticRateRequest());

        self::assertSame(
            [['client_id' => 'rates-id', 'client_secret' => 'rates-secret', 'sandbox_mode' => true]],
            $calls->getArrayCopy(),
        );
    }

    public function testGetTrackingBuildsRestClientFromTrackingCredentials(): void
    {
        $stubClient = $this->createMock(RestClient::class);
        $stubClient->method('track')->willReturn(['output' => ['completeTrackResults' => []]]);

        $fedex = $this->fedexWithConfig([
            'title' => 'FedEx',
            'client_id' => 'rates-id',
            'client_secret' => 'rates-secret',
            'tracking_client_id' => 'track-id',
            'tracking_client_secret' => 'track-secret',
            'sandbox_mode' => '0',
        ]);

        $calls = new \ArrayObject();
        $fedex->setData('rest_client_factory', $this->recordingFactory($calls, $stubClient));

        $fedex->getTracking('794644746111');

        self::assertSame(
            [['client_id' => 'track-id', 'client_secret' => 'track-secret', 'sandbox_mode' => false]],
            $calls->getArrayCopy(),
        );
    }

    private function recordingFactory(\ArrayObject $calls, RestClient $stubClient): \Closure
    {
        return static function (string $clientId, string $clientSecret, bool $sandboxMode) use ($calls, $stubClient): RestClient {
            $calls[] = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'sandbox_mode' => $sandboxMode,
            ];
            return $stubClient;
        };
    }

    public function testDoShipmentRequestReturnsTrackingAndLabel(): void
    {
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('processShipment')
            ->willReturn([
                'output' => [
                    'transactionShipments' => [[
                        'masterTrackingNumber' => '794644746111',
                        'pieceResponses' => [[
                            'trackingNumber' => '794644746111',
                            'packageDocuments' => [[
                                'contentType' => 'LABEL',
                                'encodedLabel' => base64_encode('LBL'),
                            ]],
                        ]],
                    ]],
                ],
            ]);

        $fedex = $this->fedexWithConfig([
            'account' => '510510510',
            'dropoff' => 'REGULAR_PICKUP',
            'title' => 'FedEx',
        ]);
        $fedex->setData('rest_client', $restClient);

        $result = $this->invokeDoShipmentRequest($fedex, $this->shipmentRequestVarien());

        self::assertSame('794644746111', $result->getTrackingNumber());
        self::assertSame('LBL', $result->getShippingLabelContent());
    }

    public function testRollBackCancelsShipmentForEachTrackingNumber(): void
    {
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::exactly(2))
            ->method('deleteShipment')
            ->with(self::callback(fn($payload) => isset($payload['trackingNumber']) && $payload['deletionControl'] === 'DELETE_ONE_PACKAGE'));

        $fedex = $this->fedexWithConfig(['account' => '510510510']);
        $fedex->setData('rest_client', $restClient);

        self::assertTrue($fedex->rollBack([
            ['tracking_number' => '111', 'label_content' => 'L1'],
            ['tracking_number' => '222', 'label_content' => 'L2'],
        ]));
    }

    public function testSingleRequestIncludesSmartPostForUsDomestic(): void
    {
        $payloads = [];
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('getRates')
            ->willReturnCallback(function (array $payload) use (&$payloads): array {
                $payloads[] = $payload;
                return ['output' => ['rateReplyDetails' => []]];
            });

        $fedex = $this->fedexWithConfig([
            'allowed_methods' => 'FEDEX_GROUND,SMART_POST',
            'smartpost_hubid' => '5531',
        ]);
        $fedex->setData('rest_client', $restClient);
        $fedex->setData('cache_enabled', false);

        $fedex->collectRates($this->domesticRateRequest());

        self::assertCount(1, $payloads);
        $rs = $payloads[0]['requestedShipment'];
        self::assertArrayNotHasKey('serviceType', $rs);
        self::assertSame('5531', $rs['smartPostInfoDetail']['hubId']);
    }

    public function testParentClearsHubIdForInternationalDestinations(): void
    {
        $payloads = [];
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('getRates')
            ->willReturnCallback(function (array $payload) use (&$payloads): array {
                $payloads[] = $payload;
                return ['output' => ['rateReplyDetails' => []]];
            });

        $fedex = $this->fedexWithConfig([
            'allowed_methods' => 'FEDEX_GROUND,SMART_POST,INTERNATIONAL_PRIORITY',
            'smartpost_hubid' => '5531',
        ]);
        $fedex->setData('rest_client', $restClient);
        $fedex->setData('cache_enabled', false);

        $fedex->collectRates($this->domesticRateRequest()->setDestCountryId('CA'));

        self::assertArrayNotHasKey('smartPostInfoDetail', $payloads[0]['requestedShipment']);
    }

    public function testParentClearsHubIdWhenSmartPostNotInAllowedMethods(): void
    {
        $payloads = [];
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('getRates')
            ->willReturnCallback(function (array $payload) use (&$payloads): array {
                $payloads[] = $payload;
                return ['output' => ['rateReplyDetails' => []]];
            });

        $fedex = $this->fedexWithConfig([
            'allowed_methods' => 'FEDEX_GROUND,FEDEX_2_DAY',
            'smartpost_hubid' => '5531',
        ]);
        $fedex->setData('rest_client', $restClient);
        $fedex->setData('cache_enabled', false);

        $fedex->collectRates($this->domesticRateRequest());

        self::assertArrayNotHasKey('smartPostInfoDetail', $payloads[0]['requestedShipment']);
    }

    public function testRequestToShipmentRollsBackPriorPackagesOnError(): void
    {
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::exactly(2))
            ->method('processShipment')
            ->willReturnOnConsecutiveCalls(
                ['output' => ['transactionShipments' => [[
                    'masterTrackingNumber' => 'FIRST',
                    'pieceResponses' => [[
                        'trackingNumber' => 'FIRST',
                        'packageDocuments' => [['contentType' => 'LABEL', 'encodedLabel' => base64_encode('L1')]],
                    ]],
                ]]]],
                ['errors' => [['code' => 'FAIL', 'message' => 'Second package failed']]],
            );

        $restClient->expects(self::once())
            ->method('deleteShipment')
            ->with(self::callback(fn($p) => $p['trackingNumber'] === 'FIRST'));

        $fedex = $this->fedexWithConfig([
            'account' => '510510510',
            'dropoff' => 'REGULAR_PICKUP',
            'title' => 'FedEx',
        ]);
        $fedex->setData('rest_client', $restClient);

        $shipmentRequest = new \Mage_Shipping_Model_Shipment_Request();
        $this->populateShipmentRequest($shipmentRequest);
        $shipmentRequest->setPackages([
            1 => $this->packageFixture(),
            2 => $this->packageFixture(),
        ]);

        $response = $fedex->requestToShipment($shipmentRequest);
        $info = (array) $response->getData('info');
        self::assertSame('FIRST', $info[0]['tracking_number']);
        self::assertNotEmpty($response->getErrors());
    }

    /**
     * @param array<string,mixed> $config
     */
    private function fedexWithConfig(array $config = []): MockObject&Subject
    {
        $fedex = $this->getMockBuilder(Subject::class)
            ->onlyMethods(['getConfigData', 'getConfigFlag'])
            ->getMock();

        $defaults = [
            'active' => '1',
            'title' => 'FedEx',
            'account' => '510510510',
            'allowed_methods' => '',
            'packaging' => 'YOUR_PACKAGING',
            'dropoff' => 'REGULAR_PICKUP',
            'unit_of_measure' => 'LB',
            'residence_delivery' => '0',
            'specificerrmsg' => 'FedEx is unavailable.',
            'smartpost_hubid' => '',
        ];

        $configMap = array_map('strval', array_merge($defaults, $config));

        $fedex->method('getConfigData')->willReturnCallback(
            fn($field) => $configMap[$field] ?? false,
        );
        $fedex->method('getConfigFlag')->willReturnCallback(
            fn($field) => (bool) ((int) ($configMap[$field] ?? '0')),
        );

        return $fedex;
    }

    private function domesticRateRequest(): \Mage_Shipping_Model_Rate_Request
    {
        return (new \Mage_Shipping_Model_Rate_Request())
            ->setOrigPostcode('38116')
            ->setOrigCountryId('US')
            ->setDestPostcode('90210')
            ->setDestCountryId('US')
            ->setPackageWeight(10.0)
            ->setPackagePhysicalValue(100.0)
            ->setFreeMethodWeight(10.0);
    }

    private function rawRequest(Subject $fedex): \Varien_Object
    {
        return (new \ReflectionProperty(Subject::class, '_rawRequest'))->getValue($fedex);
    }

    private function shipmentRequestVarien(): \Varien_Object
    {
        $request = new \Varien_Object();
        $request->setPackageId(1);
        $request->setShippingMethod('FEDEX_GROUND');
        $request->setPackagingType('YOUR_PACKAGING');
        $request->setPackageWeight(15.0);
        $request->setShipperContactPersonName('Shipper');
        $request->setShipperContactCompanyName('Ship Co');
        $request->setShipperContactPhoneNumber('800-555-1212');
        $request->setShipperAddressStreet1('123 Ship St');
        $request->setShipperAddressCity('Memphis');
        $request->setShipperAddressStateOrProvinceCode('TN');
        $request->setShipperAddressPostalCode('38116');
        $request->setShipperAddressCountryCode('US');
        $request->setRecipientContactPersonName('Recipient');
        $request->setRecipientContactCompanyName('R Co');
        $request->setRecipientContactPhoneNumber('212-555-1212');
        $request->setRecipientAddressStreet1('1 Test Ave');
        $request->setRecipientAddressCity('Beverly Hills');
        $request->setRecipientAddressStateOrProvinceCode('CA');
        $request->setRecipientAddressPostalCode('90210');
        $request->setRecipientAddressCountryCode('US');
        $request->setPackageParams(new \Varien_Object([
            'weight_units' => \Zend_Measure_Weight::POUND,
            'dimension_units' => \Zend_Measure_Length::INCH,
            'length' => 10,
            'width' => 8,
            'height' => 4,
            'delivery_confirmation' => 'NO_SIGNATURE_REQUIRED',
        ]));
        $request->setPackageItems([]);

        return $request;
    }

    private function invokeDoShipmentRequest(Subject $fedex, \Varien_Object $request): \Varien_Object
    {
        return (new \ReflectionMethod(Subject::class, '_doShipmentRequest'))->invoke($fedex, $request);
    }

    private function populateShipmentRequest(\Mage_Shipping_Model_Shipment_Request $request): void
    {
        $request->setShipperContactPersonName('Shipper');
        $request->setShipperContactCompanyName('Ship Co');
        $request->setShipperContactPhoneNumber('800-555-1212');
        $request->setShipperAddressStreet1('123 Ship St');
        $request->setShipperAddressCity('Memphis');
        $request->setShipperAddressStateOrProvinceCode('TN');
        $request->setShipperAddressPostalCode('38116');
        $request->setShipperAddressCountryCode('US');
        $request->setRecipientContactPersonName('Recipient');
        $request->setRecipientContactCompanyName('R Co');
        $request->setRecipientContactPhoneNumber('212-555-1212');
        $request->setRecipientAddressStreet1('1 Test Ave');
        $request->setRecipientAddressCity('Beverly Hills');
        $request->setRecipientAddressStateOrProvinceCode('CA');
        $request->setRecipientAddressPostalCode('90210');
        $request->setRecipientAddressCountryCode('US');
        $request->setShippingMethod('FEDEX_GROUND');
        $request->setStoreId(0);
    }

    /**
     * @return array{params: array<string,mixed>, items: array<mixed>}
     */
    private function packageFixture(): array
    {
        return [
            'params' => [
                'container' => 'YOUR_PACKAGING',
                'weight' => 15.0,
                'customs_value' => 0,
                'length' => 10,
                'width' => 8,
                'height' => 4,
                'weight_units' => \Zend_Measure_Weight::POUND,
                'dimension_units' => \Zend_Measure_Length::INCH,
                'delivery_confirmation' => 'NO_SIGNATURE_REQUIRED',
            ],
            'items' => [],
        ];
    }
}
