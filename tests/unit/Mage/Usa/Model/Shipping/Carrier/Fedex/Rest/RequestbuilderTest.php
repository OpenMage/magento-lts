<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Varien_Object;
use Mage;
use Mage_Core_Helper_Measure_Weight;
use Mage_Core_Helper_Measure_Length;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Container as Container;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder as Requestbuilder;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest\RequestbuilderTrait;

final class RequestbuilderTest extends OpenMageTest
{
    use RequestbuilderTrait;

    private Requestbuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new Requestbuilder();
    }

    public function testBuildsGeneralRatePayload(): void
    {
        $payload = $this->builder->buildRatePayload($this->domesticRawRequest(), 'USD');

        self::assertSame('510510510', $payload['accountNumber']['value']);
        self::assertSame(['FDXE', 'FDXG', 'FXSP'], $payload['carrierCodes']);

        $rs = $payload['requestedShipment'];
        self::assertSame('38116', $rs['shipper']['address']['postalCode']);
        self::assertSame('US', $rs['shipper']['address']['countryCode']);
        self::assertSame('90210', $rs['recipient']['address']['postalCode']);
        self::assertFalse($rs['recipient']['address']['residential']);
        self::assertSame('YOUR_PACKAGING', $rs['packagingType']);
        self::assertSame(['LIST', 'ACCOUNT'], $rs['rateRequestType']);
        self::assertSame(1, $rs['totalPackageCount']);
        self::assertSame('USE_SCHEDULED_PICKUP', $rs['pickupType']);
        self::assertArrayNotHasKey('serviceType', $rs);
        self::assertArrayNotHasKey('smartPostInfoDetail', $rs);

        $line = $rs['requestedPackageLineItems'][0];
        self::assertSame(10.0, $line['weight']['value']);
        self::assertSame('LB', $line['weight']['units']);
        // declaredValue on rate line items silently drops SMART_POST from the
        // multi-service reply — so rate payloads never emit it.
        self::assertArrayNotHasKey('declaredValue', $line);

        self::assertArrayNotHasKey('customsClearanceDetail', $rs);
    }

    public function testRatePayloadCarrierCodesIncludeFxspSoFedExReturnsSmartPost(): void
    {
        $payload = $this->builder->buildRatePayload($this->domesticRawRequest(), 'USD');

        self::assertSame(['FDXE', 'FDXG', 'FXSP'], $payload['carrierCodes']);

        $rs = $payload['requestedShipment'];
        self::assertArrayNotHasKey('serviceType', $rs);
        self::assertArrayNotHasKey('smartPostInfoDetail', $rs);
    }

    public function testBuildsInternationalRatePayloadWithCustoms(): void
    {
        $raw = $this->domesticRawRequest()
            ->setDestCountry('CA')
            ->setDestPostal('H0H');

        $payload = $this->builder->buildRatePayload($raw, 'USD');
        $rs = $payload['requestedShipment'];

        $commodities = $rs['customsClearanceDetail']['commodities'];
        self::assertCount(1, $commodities);
        $commodity = $commodities[0];
        self::assertSame('Commodities', $commodity['description']);
        self::assertSame('US', $commodity['countryOfManufacture']);
        self::assertSame(10.0, $commodity['weight']['value']);
        self::assertSame('LB', $commodity['weight']['units']);
        self::assertSame(1, $commodity['quantity']);
        self::assertSame('PCS', $commodity['quantityUnits']);
        self::assertSame(100.0, $commodity['customsValue']['amount']);
        self::assertSame('USD', $commodity['customsValue']['currency']);
    }

    public function testMultiContainerInternationalPayloadSumsCommodityWeightFromLineItems(): void
    {
        $containers = [
            $this->container(8.0, 12, 10, 6),
            $this->container(2.0, 8, 6, 4),
        ];

        $raw = $this->domesticRawRequest()
            ->setDestCountry('CA')
            ->setDestPostal('H0H');

        $payload = $this->builder->buildRatePayloadForContainers($raw, 'USD', $containers);

        $rs = $payload['requestedShipment'];
        $lineWeightSum = array_sum(array_map(
            static fn(array $item): float => (float) $item['weight']['value'],
            $rs['requestedPackageLineItems'],
        ));
        self::assertSame(10.0, $lineWeightSum);

        $commodity = $rs['customsClearanceDetail']['commodities'][0];
        self::assertSame($lineWeightSum, $commodity['weight']['value']);
        self::assertSame('LB', $commodity['weight']['units']);
        self::assertSame(100.0, $commodity['customsValue']['amount']);
    }

    public function testBuildsMultiContainerGeneralRatePayload(): void
    {
        $containers = [
            $this->container(8.0, 12, 10, 6),
            $this->container(2.0, 8, 6, 4),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest(),
            'USD',
            $containers,
        );

        $rs = $payload['requestedShipment'];
        self::assertSame(2, $rs['totalPackageCount']);
        self::assertCount(2, $rs['requestedPackageLineItems']);

        $first = $rs['requestedPackageLineItems'][0];
        self::assertSame(8.0, $first['weight']['value']);
        self::assertSame('LB', $first['weight']['units']);
        self::assertSame(12, $first['dimensions']['length']);
        self::assertSame(10, $first['dimensions']['width']);
        self::assertSame(6, $first['dimensions']['height']);
        self::assertSame('IN', $first['dimensions']['units']);

        $second = $rs['requestedPackageLineItems'][1];
        self::assertSame(2.0, $second['weight']['value']);
        self::assertSame(8, $second['dimensions']['length']);

        self::assertArrayNotHasKey('sequenceNumber', $first);
        self::assertArrayNotHasKey('sequenceNumber', $second);
    }

    public function testMultiContainerPayloadAlsoIncludesCarrierCodesAtRoot(): void
    {
        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest(),
            'USD',
            [$this->container(5.0, 10, 10, 10)],
        );

        self::assertSame(['FDXE', 'FDXG', 'FXSP'], $payload['carrierCodes']);
    }

    public function testMultiContainerRatePayloadPropagatesWeightAndDimensionUnits(): void
    {
        $containers = [
            $this->container(8.0, 12, 10, 6),
            $this->container(2.0, 8, 6, 4),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest(),
            'USD',
            $containers,
            weightUnits: 'KG',
            dimensionUnits: 'CM',
        );

        foreach ($payload['requestedShipment']['requestedPackageLineItems'] as $lineItem) {
            self::assertSame('KG', $lineItem['weight']['units']);
            self::assertSame('CM', $lineItem['dimensions']['units']);
        }
    }

    public function testMultiContainerEmptyContainerListFallsBackToSinglePiece(): void
    {
        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest(),
            'USD',
            [],
        );

        $result = $payload['requestedShipment'];
        self::assertSame(1, $result['totalPackageCount']);
        self::assertCount(1, $result['requestedPackageLineItems']);
        self::assertArrayNotHasKey('dimensions', $result['requestedPackageLineItems'][0]);
    }

    public function testBuildsTrackingPayload(): void
    {
        $payload = $this->builder->buildTrackingPayload('123456789012');
        self::assertTrue($payload['includeDetailedScans']);
        self::assertIsArray($payload['trackingInfo']);
        self::assertSame('123456789012', $payload['trackingInfo'][0]['trackingNumberInfo']['trackingNumber']);
    }

    public function testBuildsCancelShipmentPayload(): void
    {
        $payload = $this->builder->buildCancelShipmentPayload('510510510', '794644746111');
        self::assertIsArray($payload['accountNumber']);
        self::assertSame('510510510', $payload['accountNumber']['value']);
        self::assertSame('794644746111', $payload['trackingNumber']);
        self::assertSame('DELETE_ONE_PACKAGE', $payload['deletionControl']);
    }

    public function testBuildsDomesticShipmentPayload(): void
    {
        $request = $this->shipmentRequest();
        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US');

        self::assertSame('510510510', $payload['accountNumber']['value']);
        $rs = $payload['requestedShipment'];
        self::assertSame('FEDEX_GROUND', $rs['serviceType']);
        self::assertSame('SENDER', $rs['shippingChargesPayment']['paymentType']);
        self::assertSame('USE_SCHEDULED_PICKUP', $rs['pickupType']);
        self::assertSame(1, $payload['requestedShipment']['totalPackageCount']);
        $line = $rs['requestedPackageLineItems'][0];
        self::assertSame(1, $line['sequenceNumber']);
        self::assertSame(15.0, $line['weight']['value']);
        self::assertArrayHasKey('customerReferences', $line);
        self::assertArrayNotHasKey('customsClearanceDetail', $rs);
    }

    public function testInternationalShipmentIncludesCustomsClearanceDetail(): void
    {
        $request = $this->shipmentRequest();
        $request->setRecipientAddressCountryCode('CA');
        $request->setBaseCurrencyCode('USD');

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US');

        $details = $payload['requestedShipment']['customsClearanceDetail'];
        self::assertSame('SENDER', $details['dutiesPayment']['paymentType']);
        self::assertSame(200.0, $details['totalCustomsValue']['amount']);
        self::assertSame('USD', $details['totalCustomsValue']['currency']);
        self::assertSame('Widget', $details['commodities'][0]['description']);
    }

    public function testSmartPostShipmentIncludesSmartPostInfoDetailWithParcelSelect(): void
    {
        $request = $this->shipmentRequest();
        $request->setShippingMethod('SMART_POST');
        $request->setPackageWeight(5.0);

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US', '5531');

        $rs = $payload['requestedShipment'];
        self::assertSame('SMART_POST', $rs['serviceType']);
        self::assertSame('PARCEL_SELECT', $rs['smartPostInfoDetail']['indicia']);
        self::assertSame('5531', $rs['smartPostInfoDetail']['hubId']);
    }

    public function testSmartPostShipmentSwitchesIndiciaToPresortedStandardBelowOneLb(): void
    {
        $request = $this->shipmentRequest();
        $request->setShippingMethod('SMART_POST');
        $request->setPackageWeight(0.5);

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US', '5531');

        self::assertSame('PRESORTED_STANDARD', $payload['requestedShipment']['smartPostInfoDetail']['indicia']);
    }

    public function testSmartPostShipmentOmitsInfoDetailWhenHubIdBlank(): void
    {
        $request = $this->shipmentRequest();
        $request->setShippingMethod('SMART_POST');

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US', '');

        self::assertArrayNotHasKey('smartPostInfoDetail', $payload['requestedShipment']);
    }

    public function testNonSmartPostShipmentOmitsInfoDetailEvenWhenHubIdProvided(): void
    {
        $request = $this->shipmentRequest();

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US', '5531');

        self::assertArrayNotHasKey('smartPostInfoDetail', $payload['requestedShipment']);
    }

    public function testShipmentPayloadTranslatesLegacySoapServiceTypeToRestCode(): void
    {
        $request = $this->shipmentRequest();
        $request->setShippingMethod('INTERNATIONAL_PRIORITY');

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US');

        self::assertSame('FEDEX_INTERNATIONAL_PRIORITY', $payload['requestedShipment']['serviceType']);
    }

    public function testShipmentPayloadPassesRetiredServiceCodeThroughUnchanged(): void
    {
        // Retired codes have no REST replacement — pass through so FedEx surfaces the error.
        $request = $this->shipmentRequest();
        $request->setShippingMethod('INTERNATIONAL_GROUND');

        $payload = $this->builder->buildShipmentPayload($request, 'REGULAR_PICKUP', '510510510', 'US');

        self::assertSame('INTERNATIONAL_GROUND', $payload['requestedShipment']['serviceType']);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function dropoffMappingProvider(): iterable
    {
        yield 'regular pickup (default)' => ['REGULAR_PICKUP', 'USE_SCHEDULED_PICKUP'];
        yield 'request courier' => ['REQUEST_COURIER', 'CONTACT_FEDEX_TO_SCHEDULE'];
        yield 'legacy drop box' => ['DROP_BOX', 'DROPOFF_AT_FEDEX_LOCATION'];
        yield 'legacy business service center' => ['BUSINESS_SERVICE_CENTER', 'DROPOFF_AT_FEDEX_LOCATION'];
        yield 'legacy station' => ['STATION', 'DROPOFF_AT_FEDEX_LOCATION'];
    }

    /**
     * @dataProvider dropoffMappingProvider
     */
    public function testShipmentPayloadMapsDropoffTypeToValidRestPickupType(string $configuredDropoff, string $expectedPickupType): void
    {
        $request = $this->shipmentRequest();

        $payload = $this->builder->buildShipmentPayload($request, $configuredDropoff, '510510510', 'US');

        self::assertSame($expectedPickupType, $payload['requestedShipment']['pickupType']);
    }

    private function container(float $totalWeight, int $length, int $width, int $height): Container
    {
        return (new Container())
            ->setTotalWeight($totalWeight)
            ->setLength($length)
            ->setWidth($width)
            ->setHeight($height);
    }

    private function domesticRawRequest(): Varien_Object
    {
        return (new Varien_Object())
            ->setAccount('510510510')
            ->setDropoffType('REGULAR_PICKUP')
            ->setPackaging('YOUR_PACKAGING')
            ->setOrigPostal('38116')
            ->setOrigCountry('US')
            ->setDestPostal('90210')
            ->setDestCountry('US')
            ->setWeight(10.0)
            ->setValue(100.0)
            ->setUnitOfMeasure('LB')
            ->setResidenceDelivery(false);
    }

    private function shipmentRequest(): Varien_Object
    {
        $request = Mage::getModel('shipping/shipment_request');
        $this->populateShipmentRequest($request);

        $request->setPackageId(1);
        $request->setReferenceData('REF-');
        $request->setShippingMethod('FEDEX_GROUND');
        $request->setPackagingType('YOUR_PACKAGING');
        $request->setPackageWeight(15.0);
        $request->setIsReturn(false);

        $packageParams = new Varien_Object([
            'weight_units' => Mage_Core_Helper_Measure_Weight::POUND,
            'dimension_units' => Mage_Core_Helper_Measure_Length::INCH,
            'length' => 10,
            'width' => 8,
            'height' => 4,
            'customs_value' => 200.0,
            'delivery_confirmation' => 'NO_SIGNATURE_REQUIRED',
        ]);
        $request->setPackageParams($packageParams);
        $request->setPackageItems([
            ['name' => 'Widget', 'price' => 50.0, 'qty' => 4, 'product_id' => 1],
        ]);

        return $request;
    }
}
