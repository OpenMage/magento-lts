<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

use Carbon\Carbon;

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Responsemapper
{
    public const SEVERITY_ERROR = 'ERROR';

    public const SEVERITY_WARNING = 'WARNING';

    public const SEVERITY_NOTE = 'NOTE';

    /**
     * @param array<string, mixed> $json
     * @return array{
     *     rates: list<array{service_type: string, rated_type: string, currency: string, amount: float}>,
     *     alerts: list<array{severity: string, code: string, message: string}>,
     *     errors: list<array{severity: string, code: string, message: string}>
     * }
     */
    public function mapRateReply(array $json): array
    {
        $output = $json['output'] ?? $json;
        $rates = [];
        foreach ($output['rateReplyDetails'] ?? [] as $detail) {
            $serviceType = (string) ($detail['serviceType'] ?? '');
            if ($serviceType === '') {
                continue;
            }

            foreach ($detail['ratedShipmentDetails'] ?? [] as $ratedShipmentDetail) {
                $totalNetCharge = $ratedShipmentDetail['totalNetCharge'] ?? null;
                $currency = (string) ($ratedShipmentDetail['currency']
                    ?? $ratedShipmentDetail['totalNetChargeWithDutiesAndTaxes']['currency']
                    ?? '');

                if (is_array($totalNetCharge)) {
                    $amount = (float) ($totalNetCharge['amount'] ?? 0);
                    $currency = (string) ($totalNetCharge['currency'] ?? $currency);
                } else {
                    $amount = (float) ($totalNetCharge ?? 0);
                }

                $rates[] = [
                    'service_type' => $serviceType,
                    'rated_type' => (string) ($ratedShipmentDetail['rateType'] ?? ''),
                    'currency' => $currency,
                    'amount' => $amount,
                ];
            }
        }

        return [
            'rates' => $rates,
            'alerts' => $this->mapAlerts($output['alerts'] ?? []),
            'errors' => $this->mapErrors($json),
        ];
    }

    /**
     * @param array<string, mixed> $json
     * @return array{
     *     status: ?string,
     *     service: ?string,
     *     deliverydate: ?string,
     *     deliverytime: ?string,
     *     deliverylocation: ?string,
     *     shippeddate: ?string,
     *     signedby: ?string,
     *     weight: ?string,
     *     progressdetail: list<array{activity: string, deliverydate?: ?string, deliverytime?: ?string, deliverylocation?: string}>,
     *     errors: list<array{severity: string, code: string, message: string}>
     * }
     */
    public function mapTrackReply(array $json, string $trackingNumber): array
    {
        $output = $json['output'] ?? $json;
        $result = null;
        foreach ($output['completeTrackResults'] ?? [] as $complete) {
            if ((string) ($complete['trackingNumber'] ?? '') !== $trackingNumber) {
                continue;
            }

            $result = $complete['trackResults'][0] ?? null;
            break;
        }

        if ($result === null) {
            return [
                'status' => null,
                'service' => null,
                'deliverydate' => null,
                'deliverytime' => null,
                'deliverylocation' => null,
                'shippeddate' => null,
                'signedby' => null,
                'weight' => null,
                'progressdetail' => [],
                'errors' => $this->mapErrors($json),
            ];
        }

        $dateTimes = $this->indexDateTimes($result['dateAndTimes'] ?? []);
        $deliveryAt = $dateTimes['ACTUAL_DELIVERY'] ?? $dateTimes['ESTIMATED_DELIVERY'] ?? null;
        $shippedAt = $dateTimes['ACTUAL_PICKUP'] ?? $dateTimes['SHIP'] ?? null;

        $packaging = $result['packageDetails'] ?? [];
        $weight = null;
        if (isset($packaging['weightAndDimensions']['weight'][0])) {
            $weightItem = $packaging['weightAndDimensions']['weight'][0];
            $weight = trim(($weightItem['value'] ?? '') . ' ' . ($weightItem['unit'] ?? ''));
        }

        $deliveryLocation = $result['deliveryDetails']['actualDeliveryAddress']
            ?? $result['deliveryDetails']['estimatedDeliveryAddress']
            ?? null;

        return [
            'status' => (string) ($result['latestStatusDetail']['description'] ?? ''),
            'service' => (string) ($result['serviceDetail']['description'] ?? ''),
            'deliverydate' => $this->formatDateTimeString($deliveryAt, 'Y-m-d'),
            'deliverytime' => $this->formatDateTimeString($deliveryAt, 'H:i:s'),
            'deliverylocation' => is_array($deliveryLocation) ? $this->formatAddress($deliveryLocation) : null,
            'shippeddate' => $this->formatDateTimeString($shippedAt, 'Y-m-d'),
            'signedby' => (string) ($result['deliveryDetails']['receivedByName'] ?? ''),
            'weight' => $weight,
            'progressdetail' => $this->mapScanEvents($result['scanEvents'] ?? []),
            'errors' => $this->mapErrors($json),
        ];
    }

    /**
     * @param array<string, mixed> $json
     * @return array{
     *     tracking_number: string,
     *     master_tracking_number: string,
     *     label_content: ?string,
     *     errors: list<array{severity: string, code: string, message: string}>
     * }
     */
    public function mapShipReply(array $json): array
    {
        $output = $json['output'] ?? $json;
        $shipment = $output['transactionShipments'][0] ?? null;
        $piece = $shipment['pieceResponses'][0] ?? null;
        $label = $this->buildLabel($piece['packageDocuments'] ?? []);

        return [
            'tracking_number' => (string) ($piece['trackingNumber'] ?? $shipment['masterTrackingNumber'] ?? ''),
            'master_tracking_number' => (string) ($shipment['masterTrackingNumber'] ?? ''),
            'label_content' => $label,
            'errors' => $this->mapErrors($json),
        ];
    }

    /**
     * @param  array<string, mixed>                                                                                          $json
     * @return array{cancelled: bool, message: string, errors: list<array{severity: string, code: string, message: string}>}
     */
    public function mapCancelReply(array $json): array
    {
        $output = $json['output'] ?? $json;
        return [
            'cancelled' => (bool) ($output['cancelledShipment'] ?? false),
            'message' => (string) ($output['cancellationMessage'] ?? ''),
            'errors' => $this->mapErrors($json),
        ];
    }

    /**
     * @param  list<array<string, mixed>>                                   $alerts
     * @return list<array{severity: string, code: string, message: string}>
     */
    private function mapAlerts(array $alerts): array
    {
        $out = [];
        foreach ($alerts as $alert) {
            $out[] = [
                'severity' => $this->normalizeSeverity((string) ($alert['alertType'] ?? self::SEVERITY_NOTE)),
                'code' => (string) ($alert['code'] ?? ''),
                'message' => (string) ($alert['message'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>                                         $json
     * @return list<array{severity: string, code: string, message: string}>
     */
    private function mapErrors(array $json): array
    {
        $errors = [];
        foreach ($json['errors'] ?? [] as $error) {
            $errors[] = [
                'severity' => self::SEVERITY_ERROR,
                'code' => (string) ($error['code'] ?? ''),
                'message' => (string) ($error['message'] ?? ''),
            ];
        }

        return $errors;
    }

    private function normalizeSeverity(string $raw): string
    {
        return match (strtoupper($raw)) {
            'FAILURE', 'ERROR' => self::SEVERITY_ERROR,
            'WARNING' => self::SEVERITY_WARNING,
            default => self::SEVERITY_NOTE,
        };
    }

    /**
     * @param  list<array<string, mixed>> $entries
     * @return array<string, string>
     */
    private function indexDateTimes(array $entries): array
    {
        $out = [];
        foreach ($entries as $entry) {
            $type = (string) ($entry['type'] ?? '');
            $dateTime = (string) ($entry['dateTime'] ?? '');
            if ($type !== '' && $dateTime !== '') {
                $out[$type] = $dateTime;
            }
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>                                                                               $events
     * @return list<array{activity: string, deliverydate?: ?string, deliverytime?: ?string, deliverylocation?: string}>
     */
    private function mapScanEvents(array $events): array
    {
        $out = [];
        foreach ($events as $event) {
            $entry = [
                'activity' => (string) ($event['eventDescription'] ?? ''),
            ];

            $dateTime = $event['date'] ?? null;
            if (is_string($dateTime) && $dateTime !== '') {
                $entry['deliverydate'] = $this->formatDateTimeString($dateTime, 'Y-m-d');
                $entry['deliverytime'] = $this->formatDateTimeString($dateTime, 'H:i:s');
            }

            $location = $event['scanLocation'] ?? null;
            if (is_array($location) && $location !== []) {
                $entry['deliverylocation'] = $this->formatAddress($location);
            }

            $out[] = $entry;
        }

        return $out;
    }

    private function formatDateTimeString(?string $isoDateTime, string $format): ?string
    {
        if ($isoDateTime === null) {
            return null;
        }

        try {
            return Carbon::parse($isoDateTime)->format($format);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $address
     */
    private function formatAddress(array $address): string
    {
        $parts = array_filter(
            [
                $address['city'] ?? null,
                $address['stateOrProvinceCode'] ?? null,
                $address['countryCode'] ?? null,
            ],
            static fn($value): bool => $value !== null && $value !== '',
        );

        return implode(', ', array_map(strval(...), $parts));
    }

    /**
     * @param list<array<string, mixed>> $packageDocuments
     */
    private function buildLabel(array $packageDocuments): ?string
    {
        $label = null;

        foreach ($packageDocuments as $document) {
            if ((string) ($document['contentType'] ?? '') === 'LABEL') {
                $label = (string) ($document['encodedLabel'] ?? '');
                break;
            }
        }

        if ($label !== null) {
            $decodedLabel = base64_decode($label, true);
            $label = $decodedLabel !== false ? $decodedLabel : $label;
        }

        return $label;
    }
}
