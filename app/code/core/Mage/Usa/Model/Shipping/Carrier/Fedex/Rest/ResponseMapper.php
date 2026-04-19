<?php

declare(strict_types=1);

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ResponseMapper
{
    public const SEVERITY_ERROR = 'ERROR';
    public const SEVERITY_WARNING = 'WARNING';
    public const SEVERITY_NOTE = 'NOTE';

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
        if (!empty($packaging['weightAndDimensions']['weight'][0])) {
            $weightItem = $packaging['weightAndDimensions']['weight'][0];
            $weight = trim(($weightItem['value'] ?? '') . ' ' . ($weightItem['unit'] ?? ''));
        }

        $deliveryLocation = $result['deliveryDetails']['actualDeliveryAddress']
            ?? $result['deliveryDetails']['estimatedDeliveryAddress']
            ?? null;

        return [
            'status' => (string) ($result['latestStatusDetail']['description'] ?? ''),
            'service' => (string) ($result['serviceDetail']['description'] ?? ''),
            'deliverydate' => $deliveryAt ? $this->formatDate($deliveryAt) : null,
            'deliverytime' => $deliveryAt ? $this->formatTime($deliveryAt) : null,
            'deliverylocation' => $deliveryLocation ? $this->formatAddress($deliveryLocation) : null,
            'shippeddate' => $shippedAt ? $this->formatDate($shippedAt) : null,
            'signedby' => (string) ($result['deliveryDetails']['receivedByName'] ?? ''),
            'weight' => $weight,
            'progressdetail' => $this->mapScanEvents($result['scanEvents'] ?? []),
            'errors' => $this->mapErrors($json),
        ];
    }

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

    public function mapCancelReply(array $json): array
    {
        $output = $json['output'] ?? $json;
        return [
            'cancelled' => (bool) ($output['cancelledShipment'] ?? false),
            'message' => (string) ($output['cancellationMessage'] ?? ''),
            'errors' => $this->mapErrors($json),
        ];
    }

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

    private function mapScanEvents(array $events): array
    {
        $out = [];
        foreach ($events as $event) {
            $dateTime = (string) ($event['date'] ?? '');
            $entry = [
                'activity' => (string) ($event['eventDescription'] ?? ''),
            ];

            if ($dateTime !== '') {
                $entry['deliverydate'] = $this->formatDate($dateTime);
                $entry['deliverytime'] = $this->formatTime($dateTime);
            }

            $location = $event['scanLocation'] ?? [];
            if ($location) {
                $entry['deliverylocation'] = $this->formatAddress($location);
            }

            $out[] = $entry;
        }

        return $out;
    }

    private function formatDate(string $iso): string
    {
        $timestamp = strtotime($iso);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }

    private function formatTime(string $iso): string
    {
        $timestamp = strtotime($iso);
        return $timestamp ? date('H:i:s', $timestamp) : '';
    }

    private function formatAddress(array $address): string
    {
        $parts = array_filter([
            $address['city'] ?? null,
            $address['stateOrProvinceCode'] ?? null,
            $address['countryCode'] ?? null,
        ]);

        return implode(', ', array_map('strval', $parts));
    }

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
            $label = base64_decode($label, true) ?: $label;
        }

        return $label;
    }
}
