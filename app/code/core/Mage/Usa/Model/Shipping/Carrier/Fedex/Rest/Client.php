<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

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

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function getRates(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->ratesTransitTimesV1()->rateAndTransitTimes(
                FullSchemaQuoteRate::deserialize($payload),
            ),
        );
    }

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function track(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->trackV1()->trackByTrackingNumber(
                FullSchemaTrackingNumbers::deserialize($payload),
            ),
        );
    }

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function processShipment(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->shipV1()->createShipment(
                FullSchemaShip::deserialize($payload),
            ),
        );
    }

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function deleteShipment(array $payload): array
    {
        return $this->send(
            fn() => $this->connector->shipV1()->cancelShipment(
                FullSchemaCancelShipment::deserialize($payload),
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function send(Closure $call): array
    {
        try {
            try {
                $response = $call();
            } catch (RequestException $requestException) {
                $body = $requestException->getResponse()->json();
                return is_array($body) ? $body : ['errors' => [['message' => $requestException->getMessage()]]];
            }

            $body = $response->json();
        } catch (JsonException $jsonException) {
            return ['errors' => [['message' => 'Could not parse client response', 'detail' => $jsonException->getMessage()]]];
        }

        return is_array($body) ? $body : [];
    }
}
