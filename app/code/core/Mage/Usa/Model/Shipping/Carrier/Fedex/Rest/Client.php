<?php

declare(strict_types=1);

use Saloon\Exceptions\Request\RequestException;
use ShipStream\FedEx\Api\RatesAndTransitTimesV1\Dto\FullSchemaQuoteRate;
use ShipStream\FedEx\Api\ShipV1\Dto\FullSchemaCancelShipment;
use ShipStream\FedEx\Api\ShipV1\Dto\FullSchemaShip;
use ShipStream\FedEx\Api\TrackV1\Dto\FullSchemaTrackingNumbers;
use ShipStream\FedEx\FedEx;

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client
{
    public function __construct(
        private readonly FedEx $connector,
    ) {}

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
