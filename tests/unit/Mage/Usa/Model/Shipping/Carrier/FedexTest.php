<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier;

use Override;
use Mage_Shipping_Model_Rate_Result;
use Mage_Shipping_Model_Tracking_Result;
use Mage_Shipping_Model_Tracking_Result_Error;
use ReflectionMethod;
use Mage_Core_Exception;
use ArrayObject;
use Mage;
use Mage_Shipping_Model_Rate_Request;
use Varien_Object;
use ReflectionProperty;
use Mage_Core_Helper_Measure_Weight;
use Mage_Core_Helper_Measure_Length;
use Mage_Usa_Model_Shipping_Carrier_Fedex as Subject;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client as RestClient;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Clientfactory as ClientFactory;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ClientfactoryInterface as ClientFactoryInterface;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\FedexTrait;
use PHPUnit\Framework\MockObject\MockObject;
use ShipStream\FedEx\Contracts\TokenCache;

final class FedexTest extends OpenMageTest
{
    use FedexTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    public function testContainerTypesMatchesCoreShape(): void
    {
        $all = self::$subject->getContainerTypesAll();
        self::assertIsArray($all);
        self::assertArrayHasKey('YOUR_PACKAGING', $all);
        self::assertArrayHasKey('FEDEX_ENVELOPE', $all);

        $filters = self::$subject->getContainerTypesFilter();
        self::assertIsArray($filters);
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

        self::assertInstanceOf(Mage_Shipping_Model_Rate_Result::class, $result);
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
        self::assertInstanceOf(Mage_Shipping_Model_Tracking_Result::class, $result);
        $trackings = $result->getAllTrackings();
        self::assertCount(1, $trackings);
        self::assertSame('Delivered', $trackings[0]->getStatus());
    }

    public function testGetResponseReturnsStatusesFromTrackingResult(): void
    {
        $restClient = $this->createMock(RestClient::class);
        $restClient->method('track')->willReturn([
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

        $fedex->getTracking('794644746111');

        self::assertStringContainsString('Delivered', $fedex->getResponse());
    }

    public function testGetResponseReturnsEmptyResponseWhenTrackingResultUnset(): void
    {
        $fedex = $this->fedexWithConfig(['title' => 'FedEx']);

        self::assertSame('Empty response', $fedex->getResponse());
    }

    public function testGetTrackingReturnsErrorResultWhenTrackingCredentialsMissing(): void
    {
        $fedex = $this->fedexWithConfig([
            'title' => 'FedEx',
            'tracking_client_id' => '',
            'tracking_client_secret' => '',
        ]);

        $result = $fedex->getTracking('794644746111');

        self::assertInstanceOf(Mage_Shipping_Model_Tracking_Result::class, $result);
        $errors = array_filter(
            $result->getAllTrackings(),
            static fn($entry) => $entry instanceof Mage_Shipping_Model_Tracking_Result_Error,
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

        $method = new ReflectionMethod(Subject::class, '_getTrackingRestClient');

        try {
            $method->invoke($fedex);
            self::fail('Expected Mage_Core_Exception when tracking credentials are missing');
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertStringContainsString($missingField, $mageCoreException->getMessage());
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

        /** @var ArrayObject<int, array{client_id: string, client_secret: string, sandbox_mode: bool}> $calls */
        $calls = new ArrayObject();
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

        /** @var ArrayObject<int, array{client_id: string, client_secret: string, sandbox_mode: bool}> $calls */
        $calls = new ArrayObject();
        $fedex->setData('rest_client_factory', $this->recordingFactory($calls, $stubClient));

        $fedex->getTracking('794644746111');

        self::assertSame(
            [['client_id' => 'track-id', 'client_secret' => 'track-secret', 'sandbox_mode' => false]],
            $calls->getArrayCopy(),
        );
    }

    public function testCreateRestClientDefaultsToClientfactory(): void
    {
        $fedex = $this->fedexWithConfig();
        $method = new ReflectionMethod(Subject::class, '_createRestClient');
        $client = $method->invoke($fedex, 'id', 'secret', true);

        self::assertInstanceOf(RestClient::class, $client);
        self::assertInstanceOf(ClientFactory::class, $fedex->getData('rest_client_factory'));
    }

    /**
     * @param ArrayObject<int, array{client_id: string, client_secret: string, sandbox_mode: bool}> $calls
     */
    private function recordingFactory(ArrayObject $calls, RestClient $stubClient): ClientFactoryInterface
    {
        return new class ($calls, $stubClient) implements ClientFactoryInterface {
            /** @var ArrayObject<int, array{client_id: string, client_secret: string, sandbox_mode: bool}> */
            private ArrayObject $calls;

            private readonly RestClient $stubClient;

            /**
             * @param ArrayObject<int, array{client_id: string, client_secret: string, sandbox_mode: bool}> $calls
             */
            public function __construct(
                ArrayObject $calls,
                RestClient $stubClient,
            ) {
                $this->calls = $calls;
                $this->stubClient = $stubClient;
            }

            public function create(
                string $clientId,
                string $clientSecret,
                bool $sandboxMode,
                ?TokenCache $tokenCache = null,
            ): RestClient {
                $this->calls[] = [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'sandbox_mode' => $sandboxMode,
                ];
                return $this->stubClient;
            }
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

    public function testDoShipmentRequestBuildsCustomsPayloadForInternationalDestination(): void
    {
        $payloads = [];
        $restClient = $this->createMock(RestClient::class);
        $restClient->expects(self::once())
            ->method('processShipment')
            ->willReturnCallback(function (array $payload) use (&$payloads): array {
                $payloads[] = $payload;
                return [
                    'output' => [
                        'transactionShipments' => [[
                            'masterTrackingNumber' => '794644746222',
                            'pieceResponses' => [[
                                'trackingNumber' => '794644746222',
                                'packageDocuments' => [[
                                    'contentType' => 'LABEL',
                                    'encodedLabel' => base64_encode('INTL-LBL'),
                                ]],
                            ]],
                        ]],
                    ],
                ];
            });

        $fedex = $this->fedexWithConfig([
            'account' => '510510510',
            'dropoff' => 'REGULAR_PICKUP',
            'title' => 'FedEx',
        ]);
        $fedex->setData('rest_client', $restClient);

        $request = $this->shipmentRequestVarien();
        $request->setShippingMethod('INTERNATIONAL_PRIORITY');
        $request->setRecipientAddressStreet1('1 Canada Square');
        $request->setRecipientAddressCity('London');
        $request->setRecipientAddressStateOrProvinceCode('');
        $request->setRecipientAddressPostalCode('E14 5AB');
        $request->setRecipientAddressCountryCode('GB');
        $request->setBaseCurrencyCode('USD');
        $request->getPackageParams()->setCustomsValue(250.0);
        $request->setPackageItems([
            ['name' => 'Widget', 'qty' => 1, 'price' => 250.0, 'country_of_manufacture' => ''],
        ]);

        $result = $this->invokeDoShipmentRequest($fedex, $request);

        self::assertSame('794644746222', $result->getTrackingNumber());
        self::assertSame('INTL-LBL', $result->getShippingLabelContent());

        self::assertCount(1, $payloads);
        $rs = $payloads[0]['requestedShipment'];
        // Legacy input → REST code on the wire.
        self::assertSame('FEDEX_INTERNATIONAL_PRIORITY', $rs['serviceType']);
        self::assertArrayHasKey('customsClearanceDetail', $rs);
        $customs = $rs['customsClearanceDetail'];
        self::assertSame(250.0, $customs['totalCustomsValue']['amount']);
        self::assertSame('USD', $customs['totalCustomsValue']['currency']);
        self::assertArrayNotHasKey('commercialInvoice', $customs);
        self::assertSame('Widget', $customs['commodities'][0]['description']);
        // Falls back to shipper country when items don't carry a country of
        // manufacture. Empty countryOfManufacture is a hard INVALID.INPUT
        // rejection from FedEx.
        self::assertSame('US', $customs['commodities'][0]['countryOfManufacture']);
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

    public function testCollectRatesFiresSingleCallWithCarrierCodesForSmartPost(): void
    {
        // A single general rate call with `carrierCodes: [FDXE, FDXG, FXSP]`
        // at the root is what makes FedEx include SMART_POST in the reply.
        // No SMART_POST-specific serviceType or smartPostInfoDetail is needed
        // — and eligibility checks (US destination, hubId config) do not
        // affect the rate call shape.
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
        self::assertSame(['FDXE', 'FDXG', 'FXSP'], $payloads[0]['carrierCodes']);

        $rs = $payloads[0]['requestedShipment'];
        self::assertArrayNotHasKey('serviceType', $rs);
        self::assertArrayNotHasKey('smartPostInfoDetail', $rs);
        self::assertArrayNotHasKey('declaredValue', $rs['requestedPackageLineItems'][0]);
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
            ->with(self::callback(static fn($p) => $p['trackingNumber'] === 'FIRST'));

        $fedex = $this->fedexWithConfig([
            'account' => '510510510',
            'dropoff' => 'REGULAR_PICKUP',
            'title' => 'FedEx',
        ]);
        $fedex->setData('rest_client', $restClient);

        $shipmentRequest = Mage::getModel('shipping/shipment_request');
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

    public function testGetCodeMethodReturnsDeprecatedLabelFallbackForLegacyServiceTypes(): void
    {
        self::assertSame('International Priority', self::$subject->getCode('method', 'FEDEX_INTERNATIONAL_PRIORITY'));
        self::assertSame('International Priority', self::$subject->getCode('method', 'INTERNATIONAL_PRIORITY'));
        self::assertSame('Europe First Priority', self::$subject->getCode('method', 'EUROPE_FIRST_INTERNATIONAL_PRIORITY'));
        self::assertSame('International Ground', self::$subject->getCode('method', 'INTERNATIONAL_GROUND'));
        self::assertSame('Freight', self::$subject->getCode('method', 'FEDEX_FREIGHT'));
        self::assertSame('National Freight', self::$subject->getCode('method', 'FEDEX_NATIONAL_FREIGHT'));
        self::assertFalse(self::$subject->getCode('method', 'NOT_A_SERVICE'));
    }

    public function testGetCodeMethodWithoutCodeExcludesDeprecatedKeys(): void
    {
        $methods = self::$subject->getCode('method');
        self::assertIsArray($methods);

        foreach (['INTERNATIONAL_PRIORITY', 'INTERNATIONAL_GROUND', 'EUROPE_FIRST_INTERNATIONAL_PRIORITY', 'FEDEX_FREIGHT', 'FEDEX_NATIONAL_FREIGHT'] as $deprecated) {
            self::assertArrayNotHasKey($deprecated, $methods);
        }

        self::assertArrayHasKey('FEDEX_INTERNATIONAL_PRIORITY', $methods);
    }

    public function testBuildRateResultSurfacesRateWhenAllowedMethodsStillContainsLegacyServiceCode(): void
    {
        $fedex = $this->fedexWithConfig([
            'active' => true,
            'allowed_methods' => 'INTERNATIONAL_PRIORITY',
            'title' => 'FedEx',
        ]);

        $mapped = [
            'rates' => [[
                'service_type' => 'FEDEX_INTERNATIONAL_PRIORITY',
                'rated_type' => 'ACCOUNT',
                'currency' => 'USD',
                'amount' => 42.0,
            ]],
            'alerts' => [],
            'errors' => [],
        ];

        $result = (new ReflectionMethod(Subject::class, '_buildRateResult'))->invoke($fedex, $mapped);

        self::assertInstanceOf(Mage_Shipping_Model_Rate_Result::class, $result);
        $rates = $result->getAllRates();
        self::assertCount(1, $rates);
        self::assertSame('FEDEX_INTERNATIONAL_PRIORITY', $rates[0]->getMethod());
    }

    public function testGetMethodPriceAppliesFreeShippingWhenFreeMethodIsLegacyAndReturnedCodeIsRest(): void
    {
        $fedex = $this->fedexWithConfig([
            'free_method' => 'INTERNATIONAL_PRIORITY',
            'free_shipping_enable' => '1',
            'free_shipping_subtotal' => '0',
        ]);
        (new ReflectionProperty(Subject::class, '_rawRequest'))->setValue($fedex, new Varien_Object(['base_subtotal_incl_tax' => 100.0]));

        self::assertSame('0.00', $fedex->getMethodPrice(15.0, 'FEDEX_INTERNATIONAL_PRIORITY'));
    }

    public function testTranslateLegacyServiceTypeIsNoOpForRetiredAndRestCodes(): void
    {
        self::assertSame('FEDEX_INTERNATIONAL_PRIORITY', Subject::translateLegacyServiceType('INTERNATIONAL_PRIORITY'));
        self::assertSame('FEDEX_INTERNATIONAL_PRIORITY', Subject::translateLegacyServiceType('FEDEX_INTERNATIONAL_PRIORITY'));
        self::assertSame('EUROPE_FIRST_INTERNATIONAL_PRIORITY', Subject::translateLegacyServiceType('EUROPE_FIRST_INTERNATIONAL_PRIORITY'));
        self::assertSame('FEDEX_FREIGHT', Subject::translateLegacyServiceType('FEDEX_FREIGHT'));
        self::assertSame('FEDEX_GROUND', Subject::translateLegacyServiceType('FEDEX_GROUND'));
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

        $configMap = array_map(strval(...), array_merge($defaults, $config));

        $fedex->method('getConfigData')->willReturnCallback(
            fn($field) => $configMap[$field] ?? false,
        );
        $fedex->method('getConfigFlag')->willReturnCallback(
            fn($field) => (bool) ((int) ($configMap[$field] ?? '0')),
        );

        return $fedex;
    }

    private function domesticRateRequest(): Mage_Shipping_Model_Rate_Request
    {
        return Mage::getModel('shipping/rate_request')
            ->setOrigPostcode('38116')
            ->setOrigCountryId('US')
            ->setDestPostcode('90210')
            ->setDestCountryId('US')
            ->setPackageWeight(10.0)
            ->setPackagePhysicalValue(100.0)
            ->setFreeMethodWeight(10.0);
    }

    private function rawRequest(Subject $fedex): Varien_Object
    {
        return (new ReflectionProperty(Subject::class, '_rawRequest'))->getValue($fedex);
    }

    private function shipmentRequestVarien(): Varien_Object
    {
        $request = new Varien_Object();
        $request->setPackageId(1);
        $request->setShippingMethod('FEDEX_GROUND');
        $request->setPackagingType('YOUR_PACKAGING');
        $request->setPackageWeight(15.0);
        $this->populateShipmentRequest($request);
        $request->setPackageParams(new Varien_Object([
            'weight_units' => Mage_Core_Helper_Measure_Weight::POUND,
            'dimension_units' => Mage_Core_Helper_Measure_Length::INCH,
            'length' => 10,
            'width' => 8,
            'height' => 4,
            'delivery_confirmation' => 'NO_SIGNATURE_REQUIRED',
        ]));
        $request->setPackageItems([]);

        return $request;
    }

    private function invokeDoShipmentRequest(Subject $fedex, Varien_Object $request): Varien_Object
    {
        return (new ReflectionMethod(Subject::class, '_doShipmentRequest'))->invoke($fedex, $request);
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
                'weight_units' => Mage_Core_Helper_Measure_Weight::POUND,
                'dimension_units' => Mage_Core_Helper_Measure_Length::INCH,
                'delivery_confirmation' => 'NO_SIGNATURE_REQUIRED',
            ],
            'items' => [],
        ];
    }
}
