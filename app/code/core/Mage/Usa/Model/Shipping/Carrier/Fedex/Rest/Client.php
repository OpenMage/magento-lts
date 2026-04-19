<?php

declare(strict_types=1);

use Saloon\Exceptions\Request\RequestException;
use ShipStream\FedEx\Api\RatesAndTransitTimesV1\Dto\FullSchemaQuoteRate;
use ShipStream\FedEx\Api\ShipV1\Dto\FullSchemaCancelShipment;
use ShipStream\FedEx\Api\ShipV1\Dto\FullSchemaShip;
use ShipStream\FedEx\Api\TrackV1\Dto\FullSchemaTrackingNumbers;
use ShipStream\FedEx\Contracts\TokenCache;
use ShipStream\FedEx\Enums\Endpoint;
use ShipStream\FedEx\FedEx;

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client
{
    private FedEx $connector;

    public function __construct(
        string $clientId,
        string $clientSecret,
        bool $sandboxMode,
        ?TokenCache $tokenCache = null,
    ) {
        $this->connector = new FedEx(
            clientId: $clientId,
            clientSecret: $clientSecret,
            endpoint: $sandboxMode ? Endpoint::SANDBOX : Endpoint::PROD,
            tokenCache: $tokenCache ?? new Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_TokenManager(),
        );
    }

    public static function fromConnector(FedEx $connector): self
    {
        $client = (new ReflectionClass(self::class))->newInstanceWithoutConstructor();
        (function () use ($connector): void {
            $this->connector = $connector;
        })->call($client);

        return $client;
    }

    public function getRates(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->ratesTransitTimesV1()->rateAndTransitTimes(
                FullSchemaQuoteRate::deserialize($payload),
            ),
        );
    }

    public function track(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->trackV1()->trackByTrackingNumber(
                FullSchemaTrackingNumbers::deserialize($payload),
            ),
        );
    }

    public function processShipment(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->shipV1()->createShipment(
                FullSchemaShip::deserialize($payload),
            ),
        );
    }

    public function deleteShipment(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->shipV1()->cancelShipment(
                FullSchemaCancelShipment::deserialize($payload),
            ),
        );
    }

    private function send(Closure $call): array
    {
        try {
            $response = $call();
            $body = $response->json();
        } catch (RequestException $requestException) {
            try {
                $body = $requestException->getResponse()->json();
            } catch (JsonException $jsonException) {
                return ['errors' => [['message' => 'Could not parse client response', 'detail' => $jsonException->getMessage()]]];
            }
            return is_array($body) ? $body : ['errors' => [['message' => $requestException->getMessage()]]];
        } catch (JsonException $jsonException) {
            return ['errors' => [['message' => 'Could not parse client response', 'detail' => $jsonException->getMessage()]]];
        }


        return is_array($body) ? $body : [];
    }
}
