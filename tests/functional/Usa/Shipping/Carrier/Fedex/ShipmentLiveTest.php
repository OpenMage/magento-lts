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
use Mage;
use Mage_Shipping_Model_Shipment_Request;
use Mage_Usa_Model_Shipping_Carrier_Fedex;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper;
use OpenMage\Tests\Functional\Usa\Shipping\Carrier\FedexTestCase;
use Varien_Object;

final class ShipmentLiveTest extends FedexTestCase
{
    /**
     * @return array<int, string>
     */
    #[Override]
    protected static function requiredEnv(): array
    {
        return ['FEDEX_CLIENT_ID', 'FEDEX_CLIENT_SECRET', 'FEDEX_ACCOUNT'];
    }

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Bailout if prod to avoid creating real labels.
        if (self::env('FEDEX_SANDBOX_MODE', '1') !== '1') {
            self::markTestSkipped(
                'Shipment test refuses to run against prod (FEDEX_SANDBOX_MODE != 1) '
                . 'to avoid minting real labels. Flip to sandbox to run it.',
            );
        }
    }

    /**
     * Tracking numbers returned by each successful shipment call within a test are
     * captured so tearDown can cancel them afterward avoiding stale shipments
     *
     * @var list<string>
     */
    private array $createdTrackingNumbers = [];

    private ?Mage_Usa_Model_Shipping_Carrier_Fedex $carrier = null;

    private ?string $originalAllowedMethods = null;

    protected function tearDown(): void
    {
        if ($this->carrier instanceof Mage_Usa_Model_Shipping_Carrier_Fedex && $this->createdTrackingNumbers !== []) {
            $data = array_map(
                static fn(string $trackingNumber): array => ['tracking_number' => $trackingNumber],
                $this->createdTrackingNumbers,
            );

            $this->carrier->rollBack($data);
        }

        if ($this->originalAllowedMethods !== null) {
            Mage::app()->getStore()->setConfig(
                'carriers/fedex/allowed_methods',
                $this->originalAllowedMethods,
            );
            $this->originalAllowedMethods = null;
        }

        $this->createdTrackingNumbers = [];
        $this->carrier = null;
        parent::tearDown();
    }

    public function testLiveShipmentReturnsTrackingAndLabel(): void
    {
        $this->carrier = $this->buildFedexCarrier();

        $request = $this->buildShipmentRequest(
            recipient: $this->domesticRecipient(),
            shippingMethod: $this->domesticShipMethod,
            customsValue: 0.0,
            items: [],
        );

        $this->assertShipmentSucceedsAndCancel($request);
    }

    public function testLiveSmartPostShipmentReturnsTrackingAndLabel(): void
    {
        $this->overrideAllowedMethods('SMART_POST,FEDEX_GROUND');

        $this->carrier = $this->buildFedexCarrier();

        $recipient = $this->domesticRecipient();
        $recipient['residential'] = true;

        $request = $this->buildShipmentRequest(
            recipient: $recipient,
            shippingMethod: 'SMART_POST',
            customsValue: 0.0,
            items: [],
        );

        $this->assertShipmentSucceedsAndCancel($request);
    }

    public function testLiveInternationalShipmentToUkReturnsTrackingAndLabel(): void
    {
        $this->carrier = $this->buildFedexCarrier();

        $request = $this->buildShipmentRequest(
            recipient: $this->ukRecipient(),
            shippingMethod: $this->intlShipMethod,
            customsValue: $this->intlCustomsValue,
            items: [$this->intlCommodityItem()],
        );

        $this->assertShipmentSucceedsAndCancel($request);
    }

    public function testLiveMultiPieceDomesticShipmentReturnsTrackingPerPackage(): void
    {
        $this->carrier = $this->buildFedexCarrier();

        $packageSpecs = array_fill(0, $this->multipiecePackageCount, [
            'weight' => $this->shipmentPackageWeight,
            'items' => [],
            'customs_value' => 0.0,
        ]);

        $request = $this->buildMultiPieceShipmentRequest(
            recipient: $this->domesticRecipient(),
            shippingMethod: $this->domesticShipMethod,
            packageSpecs: $packageSpecs,
        );

        $this->assertMultiPieceShipmentSucceedsAndCancel($request, $this->multipiecePackageCount);
    }

    public function testLiveMultiPieceInternationalShipmentReturnsTrackingPerPackage(): void
    {
        $this->carrier = $this->buildFedexCarrier();

        $packageSpecs = array_fill(0, $this->multipiecePackageCount, [
            'weight' => $this->shipmentPackageWeight,
            'items' => [$this->intlCommodityItem()],
            'customs_value' => $this->intlCustomsValue,
        ]);

        $request = $this->buildMultiPieceShipmentRequest(
            recipient: $this->ukRecipient(),
            shippingMethod: $this->intlShipMethod,
            packageSpecs: $packageSpecs,
        );

        $this->assertMultiPieceShipmentSucceedsAndCancel($request, $this->multipiecePackageCount);
    }

    private function assertShipmentSucceedsAndCancel(Mage_Shipping_Model_Shipment_Request $request): void
    {
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex::class, $this->carrier);

        $response = $this->carrier->requestToShipment($request);

        self::assertInstanceOf(Varien_Object::class, $response);

        $errors = $response->getErrors();
        $info = $response->getData('info');

        if (!is_array($info) || $info === []) {
            self::fail(sprintf(
                'FedEx shipment request returned no package info. Errors: %s',
                is_string($errors) && $errors !== '' ? $errors : '(none)',
            ));
        }

        self::assertCount(1, $info, 'Single-package request should produce exactly one info row.');

        $row = $info[0];
        self::assertIsArray($row);
        self::assertArrayHasKey('tracking_number', $row);
        self::assertArrayHasKey('label_content', $row);

        $trackingNumber = (string) $row['tracking_number'];
        self::assertNotSame('', $trackingNumber, 'FedEx did not return a tracking number.');
        self::assertNotSame('', (string) $row['label_content'], 'FedEx did not return label content.');

        // Track the shipment for tearDown sweep at end
        $this->createdTrackingNumbers[] = $trackingNumber;

        // Verify the /ship/v1/shipments/cancel round-trip actually succeeds —
        // Mage_Usa_Model_Shipping_Carrier_Fedex::rollBack() swallows errors
        // and never inspects the response, so running it alone can't confirm
        // FedEx actually cancelled the shipment. Call the REST client directly
        // and assert `cancelledShipment: true` came back.
        $cancelResult = $this->cancelShipment($trackingNumber);

        self::assertTrue(
            $cancelResult['cancelled'],
            sprintf(
                'FedEx did not confirm cancellation of %s. Message: %s; errors: %s',
                $trackingNumber,
                $cancelResult['message'] !== '' ? $cancelResult['message'] : '(none)',
                $cancelResult['errors'] === [] ? '(none)' : json_encode($cancelResult['errors']),
            ),
        );
        self::assertSame([], $cancelResult['errors'], 'FedEx cancel reply reported errors.');

        // Cancel succeeded — drop it from the tearDown retry list.
        $this->createdTrackingNumbers = array_values(array_diff(
            $this->createdTrackingNumbers,
            [$trackingNumber],
        ));
    }

    private function assertMultiPieceShipmentSucceedsAndCancel(
        Mage_Shipping_Model_Shipment_Request $request,
        int $expectedPackageCount,
    ): void {
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex::class, $this->carrier);

        $response = $this->carrier->requestToShipment($request);

        self::assertInstanceOf(Varien_Object::class, $response);

        $errors = $response->getErrors();
        $info = $response->getData('info');

        if (!is_array($info) || $info === []) {
            self::fail(sprintf(
                'FedEx multipiece shipment request returned no package info. Errors: %s',
                is_string($errors) && $errors !== '' ? $errors : '(none)',
            ));
        }

        self::assertCount(
            $expectedPackageCount,
            $info,
            sprintf(
                'Multipiece request should produce exactly %d info rows, got %d.',
                $expectedPackageCount,
                count($info),
            ),
        );

        $trackingNumbers = [];
        foreach ($info as $index => $row) {
            self::assertIsArray($row);
            self::assertArrayHasKey('tracking_number', $row);
            self::assertArrayHasKey('label_content', $row);

            $trackingNumber = (string) $row['tracking_number'];
            self::assertNotSame('', $trackingNumber, sprintf('Package %d returned no tracking number.', $index + 1));
            self::assertNotSame('', (string) $row['label_content'], sprintf('Package %d returned no label content.', $index + 1));

            $trackingNumbers[] = $trackingNumber;
        }

        self::assertCount(
            $expectedPackageCount,
            array_unique($trackingNumbers),
            sprintf('Expected %d distinct tracking numbers, got: %s', $expectedPackageCount, implode(',', $trackingNumbers)),
        );

        $this->createdTrackingNumbers = array_merge($this->createdTrackingNumbers, $trackingNumbers);

        foreach ($trackingNumbers as $trackingNumber) {
            $cancelResult = $this->cancelShipment($trackingNumber);

            self::assertTrue(
                $cancelResult['cancelled'],
                sprintf(
                    'FedEx did not confirm cancellation of %s. Message: %s; errors: %s',
                    $trackingNumber,
                    $cancelResult['message'] !== '' ? $cancelResult['message'] : '(none)',
                    $cancelResult['errors'] === [] ? '(none)' : json_encode($cancelResult['errors']),
                ),
            );
            self::assertSame([], $cancelResult['errors'], 'FedEx cancel reply reported errors.');
        }

        // Every package cancelled cleanly — drop them from the tearDown retry list.
        $this->createdTrackingNumbers = array_values(array_diff(
            $this->createdTrackingNumbers,
            $trackingNumbers,
        ));
    }

    private function overrideAllowedMethods(string $value): void
    {
        if ($this->originalAllowedMethods === null) {
            $this->originalAllowedMethods = (string) Mage::app()
                ->getStore()
                ->getConfig('carriers/fedex/allowed_methods');
        }

        Mage::app()->getStore()->setConfig('carriers/fedex/allowed_methods', $value);
    }

    /**
     * @return array{cancelled: bool, message: string, errors: list<array{severity: string, code: string, message: string}>}
     */
    private function cancelShipment(string $trackingNumber): array
    {
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex::class, $this->carrier);

        $builder = $this->carrier->getData('request_builder');
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder::class, $builder);

        $client = $this->carrier->getData('rest_client');
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client::class, $client);

        $mapper = $this->carrier->getData('response_mapper');
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper::class, $mapper);

        $payload = $builder->buildCancelShipmentPayload(
            (string) self::env('FEDEX_ACCOUNT', ''),
            $trackingNumber,
        );

        return $mapper->mapCancelReply($client->deleteShipment($payload));
    }
}
