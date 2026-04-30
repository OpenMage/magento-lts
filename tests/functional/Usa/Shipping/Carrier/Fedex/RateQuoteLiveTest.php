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
use Mage_Shipping_Model_Rate_Request;
use Mage_Shipping_Model_Rate_Result;
use Mage_Shipping_Model_Rate_Result_Error;
use Mage_Shipping_Model_Rate_Result_Method;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper;
use OpenMage\Tests\Functional\Usa\Shipping\Carrier\FedexTestCase;

final class RateQuoteLiveTest extends FedexTestCase
{
    private ?string $originalAllowedMethods = null;

    /**
     * @return array<int, string>
     */
    #[Override]
    protected static function requiredEnv(): array
    {
        return ['FEDEX_CLIENT_ID', 'FEDEX_CLIENT_SECRET', 'FEDEX_ACCOUNT'];
    }

    protected function tearDown(): void
    {
        if ($this->originalAllowedMethods !== null) {
            Mage::app()->getStore()->setConfig(
                'carriers/fedex/allowed_methods',
                $this->originalAllowedMethods,
            );
            $this->originalAllowedMethods = null;
        }

        parent::tearDown();
    }

    public function testLiveRateQuoteReturnsAtLeastOneMethod(): void
    {
        ['methods' => $methods, 'errors' => $errors] = $this->collectLiveRates($this->buildRateRequest());

        if ($methods === []) {
            self::fail(sprintf(
                "FedEx returned no rate methods. Errors:\n  - %s",
                $this->formatRateErrorMessages($errors),
            ));
        }

        foreach ($methods as $method) {
            self::assertSame('fedex', $method->getCarrier());
            self::assertNotSame('', (string) $method->getMethod());
            self::assertGreaterThan(0.0, (float) $method->getPrice());
        }
    }

    public function testLiveRateQuoteIncludesRequestedService(): void
    {
        $allowed = array_filter(array_map(
            trim(...),
            explode(',', $this->allowedMethods),
        ));

        ['methods' => $methods] = $this->collectLiveRates($this->buildRateRequest());

        $returnedMethods = array_map(
            static fn(Mage_Shipping_Model_Rate_Result_Method $method): string => (string) $method->getMethod(),
            $methods,
        );

        $intersect = array_values(array_intersect($allowed, $returnedMethods));
        self::assertNotEmpty(
            $intersect,
            sprintf(
                'None of the configured allowed_methods (%s) were returned by FedEx. Got: %s',
                implode(',', $allowed),
                $returnedMethods === [] ? '(empty)' : implode(',', $returnedMethods),
            ),
        );
    }

    public function testLiveRateQuoteToUkReturnsInternationalMethod(): void
    {
        ['methods' => $methods, 'errors' => $errors] = $this->collectLiveRates($this->buildRateRequest(
            destPostcode: $this->intlDestPostcode,
            destCountry: $this->intlDestCountry,
        ));

        if ($methods === []) {
            self::fail(sprintf(
                "FedEx returned no international rate methods for US→GB. Errors:\n  - %s",
                $this->formatRateErrorMessages($errors),
            ));
        }

        $returnedMethods = array_map(
            static fn(Mage_Shipping_Model_Rate_Result_Method $method): string => (string) $method->getMethod(),
            $methods,
        );

        $internationalMethods = array_values(array_filter(
            $returnedMethods,
            static fn(string $method): bool => str_starts_with($method, 'INTERNATIONAL_')
                || str_starts_with($method, 'FEDEX_INTERNATIONAL_'),
        ));

        self::assertNotEmpty(
            $internationalMethods,
            sprintf(
                'Expected at least one INTERNATIONAL_* service from US→GB, got: %s',
                implode(',', $returnedMethods),
            ),
        );

        foreach ($methods as $method) {
            self::assertSame('fedex', $method->getCarrier());
            self::assertGreaterThan(0.0, (float) $method->getPrice());
        }
    }

    public function testLiveRateQuoteReturnsSmartPostWhenAllowed(): void
    {
        $this->overrideAllowedMethods('SMART_POST,FEDEX_GROUND');

        ['methods' => $methods, 'errors' => $errors] = $this->collectLiveRates($this->buildRateRequest(
            weight: $this->smartpostPackageWeight,
        ));

        $returnedMethods = array_map(
            static fn(Mage_Shipping_Model_Rate_Result_Method $method): string => (string) $method->getMethod(),
            $methods,
        );

        if (!in_array('SMART_POST', $returnedMethods, true)) {
            self::fail(sprintf(
                "FedEx did not return a SMART_POST rate. Returned methods: %s. Errors:\n  - %s",
                $returnedMethods === [] ? '(empty)' : implode(',', $returnedMethods),
                $this->formatRateErrorMessages($errors),
            ));
        }

        foreach ($methods as $method) {
            if ((string) $method->getMethod() !== 'SMART_POST') {
                continue;
            }

            self::assertSame('fedex', $method->getCarrier());
            self::assertGreaterThan(0.0, (float) $method->getPrice());
        }
    }

    public function testLiveMultiPieceDomesticRateQuoteReturnsRates(): void
    {
        $rates = $this->requestMultiPieceRates(
            destPostcode: null,
            destCountry: null,
        );

        self::assertNotEmpty(
            $rates['rates'],
            sprintf(
                "FedEx returned no multipiece rates for a US→US %d-package shipment. Errors:\n  - %s",
                $this->multipiecePackageCount,
                $this->formatErrorMessages($rates['errors']),
            ),
        );

        foreach ($rates['rates'] as $rate) {
            self::assertNotSame('', (string) $rate['service_type']);
            self::assertGreaterThan(0.0, (float) $rate['amount']);
        }
    }

    public function testLiveMultiPieceInternationalRateQuoteReturnsRates(): void
    {
        $rates = $this->requestMultiPieceRates(
            destPostcode: $this->intlDestPostcode,
            destCountry: $this->intlDestCountry,
        );

        self::assertNotEmpty(
            $rates['rates'],
            sprintf(
                "FedEx returned no multipiece rates for a US→GB %d-package shipment. Errors:\n  - %s",
                $this->multipiecePackageCount,
                $this->formatErrorMessages($rates['errors']),
            ),
        );

        $services = array_map(
            static fn(array $rate): string => $rate['service_type'],
            $rates['rates'],
        );

        $international = array_values(array_filter(
            $services,
            static fn(string $service): bool => str_starts_with($service, 'INTERNATIONAL_')
                || str_starts_with($service, 'FEDEX_INTERNATIONAL_'),
        ));

        self::assertNotEmpty(
            $international,
            sprintf(
                'Expected at least one INTERNATIONAL_* service from a US→GB multipiece quote, got: %s',
                $services === [] ? '(empty)' : implode(',', $services),
            ),
        );

        foreach ($rates['rates'] as $rate) {
            self::assertGreaterThan(0.0, (float) $rate['amount']);
        }
    }

    /**
     * `collectRates()` always emits a single-line-item rate payload, so the
     * multipiece builder is only reachable by driving the REST stack directly.
     *
     * @return array{
     *     rates: list<array{service_type: string, rated_type: string, currency: string, amount: float}>,
     *     alerts: list<array{severity: string, code: string, message: string}>,
     *     errors: list<array{severity: string, code: string, message: string}>
     * }
     */
    private function requestMultiPieceRates(?string $destPostcode, ?string $destCountry): array
    {
        $carrier = $this->buildFedexCarrier();

        $carrier->collectRates($this->buildRateRequest(
            destPostcode: $destPostcode,
            destCountry: $destCountry,
        ));

        $builder = $carrier->getData('request_builder');
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Requestbuilder::class, $builder);

        $client = $carrier->getData('rest_client');
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client::class, $client);

        $mapper = $carrier->getData('response_mapper');
        self::assertInstanceOf(Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper::class, $mapper);

        $raw = $this->buildRawRateRequest(
            account: (string) self::env('FEDEX_ACCOUNT', ''),
            destPostcode: $destPostcode,
            destCountry: $destCountry,
        );

        $payload = $builder->buildRatePayloadForContainers(
            $raw,
            'USD',
            $this->buildRateContainers(),
        );

        // Ensure that multiple requestedPackageLineItems survive to the API.
        self::assertSame($this->multipiecePackageCount, $payload['requestedShipment']['totalPackageCount']);
        self::assertCount(
            $this->multipiecePackageCount,
            $payload['requestedShipment']['requestedPackageLineItems'],
        );

        return $mapper->mapRateReply($client->getRates($payload));
    }

    /**
     * @return array{
     *     methods: list<Mage_Shipping_Model_Rate_Result_Method>,
     *     errors: list<Mage_Shipping_Model_Rate_Result_Error>,
     * }
     */
    private function collectLiveRates(Mage_Shipping_Model_Rate_Request $request): array
    {
        $result = $this->buildFedexCarrier()->collectRates($request);

        self::assertInstanceOf(
            Mage_Shipping_Model_Rate_Result::class,
            $result,
            'collectRates should return a Rate_Result against the live API',
        );

        $rates = $result->getAllRates();

        return [
            'methods' => array_values(array_filter(
                $rates,
                static fn($rate): bool => $rate instanceof Mage_Shipping_Model_Rate_Result_Method,
            )),
            'errors' => array_values(array_filter(
                $rates,
                static fn($rate): bool => $rate instanceof Mage_Shipping_Model_Rate_Result_Error,
            )),
        ];
    }

    /**
     * @param list<Mage_Shipping_Model_Rate_Result_Error> $errors
     */
    private function formatRateErrorMessages(array $errors): string
    {
        $messages = array_map(
            static fn(Mage_Shipping_Model_Rate_Result_Error $error): string => (string) $error->getErrorMessage(),
            $errors,
        );

        return implode("\n  - ", $messages !== [] ? $messages : ['(no error messages returned)']);
    }

    /**
     * @param list<array{severity: string, code: string, message: string}> $errors
     */
    private function formatErrorMessages(array $errors): string
    {
        $messages = array_map(
            static fn(array $error): string => $error['message'],
            $errors,
        );

        return implode("\n  - ", $messages !== [] ? $messages : ['(no error messages returned)']);
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
}
