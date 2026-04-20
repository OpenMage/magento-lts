<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_RequestBuilder as RequestBuilder;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest\RequestBuilderTrait;

class RequestBuilderTest extends OpenMageTest
{
    use RequestBuilderTrait;

    private RequestBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new RequestBuilder();
    }

    public function testBuildsGeneralRatePayload(): void
    {
        $payload = $this->builder->buildRatePayload($this->domesticRawRequest(), 'USD');

        self::assertSame('510510510', $payload['accountNumber']['value']);

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
        self::assertSame(100.0, $line['declaredValue']['amount']);
        self::assertSame('USD', $line['declaredValue']['currency']);

        self::assertArrayNotHasKey('customsClearanceDetail', $rs);
    }

    public function testRatePayloadIncludesSmartPostInfoDetailWhenHubIdSet(): void
    {
        $raw = $this->domesticRawRequest()
            ->setWeight(0.5)
            ->setSmartpostHubid('5531');

        $payload = $this->builder->buildRatePayload($raw, 'USD');

        $rs = $payload['requestedShipment'];
        self::assertArrayNotHasKey('serviceType', $rs);
        self::assertSame('PRESORTED_STANDARD', $rs['smartPostInfoDetail']['indicia']);
        self::assertSame('5531', $rs['smartPostInfoDetail']['hubId']);
        self::assertSame(100.0, $rs['requestedPackageLineItems'][0]['declaredValue']['amount']);
    }

    public function testSmartPostIndiciaSwitchesToParcelSelectAtOneLb(): void
    {
        $raw = $this->domesticRawRequest()
            ->setWeight(1.0)
            ->setSmartpostHubid('5531');

        $payload = $this->builder->buildRatePayload($raw, 'USD');

        self::assertSame('PARCEL_SELECT', $payload['requestedShipment']['smartPostInfoDetail']['indicia']);
    }

    public function testRatePayloadOmitsSmartPostInfoDetailWhenHubIdBlank(): void
    {
        $payload = $this->builder->buildRatePayload($this->domesticRawRequest(), 'USD');

        self::assertArrayNotHasKey('smartPostInfoDetail', $payload['requestedShipment']);
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

    public function testMultiContainerGeneralDeclaredValueSumsExactly(): void
    {
        // value = 100, weight split 8 / 2 → first container ~80, last absorbs
        // the rounding remainder so the per-line-item amounts sum to 100.00 exactly.
        $containers = [
            $this->container(8.0, 12, 10, 6),
            $this->container(2.0, 8, 6, 4),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest()->setValue(100.0),
            'USD',
            $containers,
        );

        $items = $payload['requestedShipment']['requestedPackageLineItems'];
        $sum = $items[0]['declaredValue']['amount'] + $items[1]['declaredValue']['amount'];
        self::assertSame(100.0, round($sum, 2));
        self::assertSame('USD', $items[0]['declaredValue']['currency']);
    }

    public function testMultiContainerSplitAbsorbsRoundingDriftIntoLastItem(): void
    {
        // value = 10.00 across 3 equal containers → naive round = 3.33 + 3.33 + 3.33 = 9.99.
        // The remainder approach should give 3.33 + 3.33 + 3.34 = 10.00.
        $containers = [
            $this->container(1.0, 5, 5, 5),
            $this->container(1.0, 5, 5, 5),
            $this->container(1.0, 5, 5, 5),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest()->setValue(10.0),
            'USD',
            $containers,
        );

        $items = $payload['requestedShipment']['requestedPackageLineItems'];
        $sum = array_sum(array_map(static fn($i) => $i['declaredValue']['amount'], $items));
        self::assertSame(10.0, round($sum, 2));
        self::assertSame(3.33, $items[0]['declaredValue']['amount']);
        self::assertSame(3.33, $items[1]['declaredValue']['amount']);
        self::assertSame(3.34, $items[2]['declaredValue']['amount']);
    }

    public function testMultiContainerPayloadIncludesSmartPostInfoDetailWhenHubIdSet(): void
    {
        $containers = [
            $this->container(0.5, 5, 4, 2),
            $this->container(0.5, 5, 4, 2),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest()->setWeight(0.5)->setSmartpostHubid('5531'),
            'USD',
            $containers,
        );

        $rs = $payload['requestedShipment'];
        self::assertArrayNotHasKey('serviceType', $rs);
        self::assertSame('5531', $rs['smartPostInfoDetail']['hubId']);
        self::assertSame('PRESORTED_STANDARD', $rs['smartPostInfoDetail']['indicia']);
        $items = $rs['requestedPackageLineItems'];
        $sum = array_sum(array_map(static fn($i) => $i['declaredValue']['amount'], $items));
        self::assertSame(100.0, round($sum, 2));
    }

    public function testMultiContainerOmitsDeclaredValueWhenShipmentValueIsZero(): void
    {
        $containers = [
            $this->container(5.0, 10, 10, 10),
            $this->container(5.0, 10, 10, 10),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest()->setValue(0),
            'USD',
            $containers,
        );

        foreach ($payload['requestedShipment']['requestedPackageLineItems'] as $item) {
            self::assertArrayNotHasKey('declaredValue', $item);
        }
    }

    public function testMultiContainerOmitsDeclaredValueWhenContainerWeightsAreZero(): void
    {
        // Guard against divide-by-zero when containers report zero weight.
        $containers = [
            $this->container(0.0, 10, 10, 10),
            $this->container(0.0, 10, 10, 10),
        ];

        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest()->setValue(50.0),
            'USD',
            $containers,
        );

        foreach ($payload['requestedShipment']['requestedPackageLineItems'] as $item) {
            self::assertArrayNotHasKey('declaredValue', $item);
        }
    }

    public function testMultiContainerEmptyContainerListFallsBackToSinglePiece(): void
    {
        $payload = $this->builder->buildRatePayloadForContainers(
            $this->domesticRawRequest(),
            'USD',
            [],
        );

        $rs = $payload['requestedShipment'];
        self::assertSame(1, $rs['totalPackageCount']);
        self::assertCount(1, $rs['requestedPackageLineItems']);
        self::assertArrayNotHasKey('dimensions', $rs['requestedPackageLineItems'][0]);
    }

    public function testBuildsTrackingPayload(): void
    {
        $payload = $this->builder->buildTrackingPayload('123456789012');
        self::assertTrue($payload['includeDetailedScans']);
        self::assertSame('123456789012', $payload['trackingInfo'][0]['trackingNumberInfo']['trackingNumber']);
    }

    public function testBuildsCancelShipmentPayload(): void
    {
        $payload = $this->builder->buildCancelShipmentPayload('510510510', '794644746111');
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
        self::assertSame(200.0, $details['commercialInvoice']['customsValue']['amount']);
        self::assertSame('Widget', $details['commodities'][0]['description']);
    }

    private function container(float $totalWeight, int $length, int $width, int $height): \Varien_Object
    {
        return new \Varien_Object([
            'total_weight' => $totalWeight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
        ]);
    }

    private function domesticRawRequest(): \Varien_Object
    {
        return (new \Varien_Object())
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

    private function shipmentRequest(): \Varien_Object
    {
        $request = \Mage::getModel('shipping/shipment_request');
        $this->populateShipmentRequest($request);

        $request->setPackageId(1);
        $request->setReferenceData('REF-');
        $request->setShippingMethod('FEDEX_GROUND');
        $request->setPackagingType('YOUR_PACKAGING');
        $request->setPackageWeight(15.0);
        $request->setIsReturn(false);

        $packageParams = new \Varien_Object([
            'weight_units' => \Zend_Measure_Weight::POUND,
            'dimension_units' => \Zend_Measure_Length::INCH,
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
