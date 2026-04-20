<?php

declare(strict_types=1);

use Mage_Usa_Model_Shipping_Carrier_Fedex_UnitOfMeasure as Uom;

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_RequestBuilder
{
    public function buildRatePayload(Varien_Object $raw, string $currencyCode): array
    {
        $units = (string) $raw->getUnitOfMeasure();
        $requestedPackageLineItem = [
            'groupPackageCount' => 1,
            'weight' => [
                'units' => $units !== '' ? $units : Uom::WEIGHT_POUND,
                'value' => (float) $raw->getWeight(),
            ],
            'declaredValue' => [
                'amount' => (float) $raw->getValue(),
                'currency' => $currencyCode,
            ],
        ];

        return [
            'accountNumber' => ['value' => (string) $raw->getAccount()],
            'requestedShipment' => $this->buildRequestedShipment(
                $raw,
                $currencyCode,
                [$requestedPackageLineItem],
                1,
            ),
        ];
    }

    /**
     * Multi-piece rate payload for cubed shipments. Each container becomes one
     * `requestedPackageLineItems` entry with its own weight + dimensions. The
     * per-line-item `declaredValue` is split proportionally by weight; the last
     * container absorbs the rounding remainder so the sum equals
     * `$raw->getValue()` exactly. Zero shipment value / zero total weight omits
     * declaredValue entirely.
     *
     * @param object[] $containers
     */
    public function buildRatePayloadForContainers(
        Varien_Object $raw,
        string $currencyCode,
        array $containers,
        string $weightUnits = Uom::WEIGHT_POUND,
    ): array {
        if ($containers === []) {
            return $this->buildRatePayload($raw, $currencyCode);
        }

        $perItemAmounts = $this->distributeDeclaredValue($raw, $containers);

        $lineItems = [];
        foreach ($containers as $i => $container) {
            $lineItem = [
                'groupPackageCount' => 1,
                'weight' => [
                    'units' => $weightUnits,
                    'value' => (float) $container->getTotalWeight(),
                ],
                'dimensions' => [
                    'length' => (int) $container->getLength(),
                    'width' => (int) $container->getWidth(),
                    'height' => (int) $container->getHeight(),
                    'units' => Uom::DIMENSION_INCH,
                ],
            ];

            if (isset($perItemAmounts[$i])) {
                $lineItem['declaredValue'] = [
                    'amount' => $perItemAmounts[$i],
                    'currency' => $currencyCode,
                ];
            }

            $lineItems[] = $lineItem;
        }

        return [
            'accountNumber' => ['value' => (string) $raw->getAccount()],
            'requestedShipment' => $this->buildRequestedShipment(
                $raw,
                $currencyCode,
                $lineItems,
                count($containers),
            ),
        ];
    }

    /**
     * @param  object[]                          $containers
     * @return array<int,float>|array<int,never> indexed by container index when
     *                                           declaredValue should be emitted; empty otherwise
     */
    private function distributeDeclaredValue(Varien_Object $raw, array $containers): array
    {
        $total = (float) $raw->getValue();
        $totalWeight = array_sum(array_map(
            static fn($container) => (float) $container->getTotalWeight(),
            $containers,
        ));

        if ($total <= 0 || $totalWeight <= 0) {
            return [];
        }

        $amounts = [];
        $allocated = 0.0;
        $lastIndex = count($containers) - 1;

        foreach ($containers as $i => $container) {
            if ($i === $lastIndex) {
                $amounts[$i] = round($total - $allocated, 2);
                continue;
            }

            $share = round($total * ((float) $container->getTotalWeight() / $totalWeight), 2);
            $amounts[$i] = $share;
            $allocated += $share;
        }

        return $amounts;
    }

    private function buildRequestedShipment(
        Varien_Object $raw,
        string $currencyCode,
        array $lineItems,
        int $totalPackageCount
    ): array {
        $origCountry = (string) $raw->getOrigCountry();
        $destCountry = (string) $raw->getDestCountry();

        $requestedShipment = [
            'shipper' => [
                'address' => [
                    'postalCode' => (string) $raw->getOrigPostal(),
                    'countryCode' => $origCountry,
                ],
            ],
            'recipient' => [
                'address' => [
                    'postalCode' => (string) $raw->getDestPostal(),
                    'countryCode' => $destCountry,
                    'residential' => (bool) $raw->getResidenceDelivery(),
                ],
            ],
            'pickupType' => $this->mapDropoffType((string) $raw->getDropoffType()),
            'packagingType' => (string) $raw->getPackaging() ? (string) $raw->getPackaging() : 'YOUR_PACKAGING',
            'rateRequestType' => ['LIST', 'ACCOUNT'],
            'totalPackageCount' => $totalPackageCount,
            'shippingChargesPayment' => [
                'paymentType' => 'SENDER',
                'payor' => [
                    'responsibleParty' => [
                        'accountNumber' => ['value' => (string) $raw->getAccount()],
                        'address' => ['countryCode' => $origCountry],
                    ],
                ],
            ],
            'requestedPackageLineItems' => $lineItems,
        ];

        if ($origCountry !== '' && $destCountry !== '' && $origCountry !== $destCountry) {
            $totalWeight = array_sum(array_map(
                static fn(array $item): float => (float) $item['weight']['value'],
                $lineItems,
            ));
            $requestedShipment['customsClearanceDetail'] = [
                'commodities' => [[
                    'description' => 'Commodities',
                    'countryOfManufacture' => $origCountry,
                    'weight' => [
                        'units' => (string) $lineItems[0]['weight']['units'],
                        'value' => $totalWeight,
                    ],
                    'quantity' => 1,
                    'quantityUnits' => 'PCS',
                    'customsValue' => [
                        'amount' => (float) $raw->getValue(),
                        'currency' => $currencyCode,
                    ],
                ]],
            ];
        }

        $hubId = (string) $raw->getSmartpostHubid();
        if ($hubId !== '') {
            $requestedShipment['smartPostInfoDetail'] = [
                'indicia' => ((float) $raw->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'hubId' => $hubId,
            ];
        }

        return $requestedShipment;
    }

    public function buildTrackingPayload(string $trackingNumber): array
    {
        return [
            'includeDetailedScans' => true,
            'trackingInfo' => [
                ['trackingNumberInfo' => ['trackingNumber' => $trackingNumber]],
            ],
        ];
    }

    public function buildShipmentPayload(
        Varien_Object $request,
        string $dropoffType,
        string $accountNumber,
        string $storeCountryCode
    ): array {
        $packageParams = $request->getPackageParams();
        $weightUnits = $packageParams && $packageParams->getWeightUnits() === Zend_Measure_Weight::POUND ? Uom::WEIGHT_POUND : Uom::WEIGHT_KILOGRAM;
        $dimensionsUnits = $packageParams && $packageParams->getDimensionUnits() === Zend_Measure_Length::INCH ? Uom::DIMENSION_INCH : Uom::DIMENSION_CENTIMETER;

        $referenceData = $request->getReferenceData() !== null
            ? $request->getReferenceData() . $request->getPackageId()
            : $this->defaultReferenceData($request);

        $paymentType = $request->getIsReturn() ? 'RECIPIENT' : 'SENDER';

        $lineItem = [
            'sequenceNumber' => 1,
            'weight' => [
                'units' => $weightUnits,
                'value' => (float) $request->getPackageWeight(),
            ],
            'customerReferences' => [
                ['customerReferenceType' => 'CUSTOMER_REFERENCE', 'value' => $referenceData],
            ],
        ];

        if ($packageParams && ($packageParams->getLength() || $packageParams->getWidth() || $packageParams->getHeight())) {
            $lineItem['dimensions'] = [
                'length' => (int) $packageParams->getLength(),
                'width' => (int) $packageParams->getWidth(),
                'height' => (int) $packageParams->getHeight(),
                'units' => $dimensionsUnits,
            ];
        }

        if ($packageParams && $packageParams->getDeliveryConfirmation()) {
            $lineItem['packageSpecialServices'] = [
                'specialServiceTypes' => ['SIGNATURE_OPTION'],
                'signatureOptionType' => (string) $packageParams->getDeliveryConfirmation(),
            ];
        }

        $requestedShipment = [
            'shipper' => [
                'contact' => [
                    'personName' => (string) $request->getShipperContactPersonName(),
                    'companyName' => (string) $request->getShipperContactCompanyName(),
                    'phoneNumber' => (string) $request->getShipperContactPhoneNumber(),
                ],
                'address' => [
                    'streetLines' => array_values(array_filter([
                        (string) $request->getShipperAddressStreet1(),
                        (string) $request->getShipperAddressStreet2(),
                    ])),
                    'city' => (string) $request->getShipperAddressCity(),
                    'stateOrProvinceCode' => (string) $request->getShipperAddressStateOrProvinceCode(),
                    'postalCode' => (string) $request->getShipperAddressPostalCode(),
                    'countryCode' => (string) $request->getShipperAddressCountryCode(),
                ],
            ],
            'recipients' => [[
                'contact' => [
                    'personName' => (string) $request->getRecipientContactPersonName(),
                    'companyName' => (string) $request->getRecipientContactCompanyName(),
                    'phoneNumber' => (string) $request->getRecipientContactPhoneNumber(),
                ],
                'address' => [
                    'streetLines' => array_values(array_filter([
                        (string) $request->getRecipientAddressStreet1(),
                        (string) $request->getRecipientAddressStreet2(),
                    ])),
                    'city' => (string) $request->getRecipientAddressCity(),
                    'stateOrProvinceCode' => (string) $request->getRecipientAddressStateOrProvinceCode(),
                    'postalCode' => (string) $request->getRecipientAddressPostalCode(),
                    'countryCode' => (string) $request->getRecipientAddressCountryCode(),
                    'residential' => (bool) $request->getRecipientAddressResidential(),
                ],
            ]],
            'pickupType' => $this->mapDropoffType($dropoffType),
            'packagingType' => (string) $request->getPackagingType() ? (string) $request->getPackagingType() : 'YOUR_PACKAGING',
            'serviceType' => (string) $request->getShippingMethod(),
            'shippingChargesPayment' => [
                'paymentType' => $paymentType,
                'payor' => [
                    'responsibleParty' => [
                        'accountNumber' => ['value' => $accountNumber],
                        'address' => ['countryCode' => $storeCountryCode],
                    ],
                ],
            ],
            'labelSpecification' => [
                'labelFormatType' => 'COMMON2D',
                'imageType' => 'PNG',
                'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL',
            ],
            'rateRequestType' => ['ACCOUNT'],
            'totalPackageCount' => 1,
            'requestedPackageLineItems' => [$lineItem],
        ];

        if ($request->getShipperAddressCountryCode() !== $request->getRecipientAddressCountryCode()) {
            $customsValue = $packageParams ? (float) $packageParams->getCustomsValue() : 0.0;
            $unitPrice = 0.0;
            $itemsQty = 0;
            $itemsDesc = [];
            $countriesOfManufacture = [];
            foreach ((array) $request->getPackageItems() as $itemShipment) {
                $item = new Varien_Object($itemShipment);
                $unitPrice += (float) $item->getPrice();
                $itemsQty += (int) $item->getQty();
                $itemsDesc[] = (string) $item->getName();
            }

            $requestedShipment['customsClearanceDetail'] = [
                'dutiesPayment' => [
                    'paymentType' => $paymentType,
                    'payor' => [
                        'responsibleParty' => [
                            'accountNumber' => ['value' => $accountNumber],
                            'address' => ['countryCode' => $storeCountryCode],
                        ],
                    ],
                ],
                'commercialInvoice' => [
                    'customsValue' => [
                        'amount' => $customsValue,
                        'currency' => (string) $request->getBaseCurrencyCode(),
                    ],
                ],
                'commodities' => [[
                    'numberOfPieces' => 1,
                    'description' => implode(', ', array_filter($itemsDesc)),
                    'countryOfManufacture' => implode(',', array_unique($countriesOfManufacture)),
                    'weight' => [
                        'units' => $weightUnits,
                        'value' => (float) $request->getPackageWeight(),
                    ],
                    'quantity' => max(1, (int) ceil($itemsQty)),
                    'quantityUnits' => 'PCS',
                    'unitPrice' => [
                        'amount' => $unitPrice,
                        'currency' => (string) $request->getBaseCurrencyCode(),
                    ],
                    'customsValue' => [
                        'amount' => $customsValue,
                        'currency' => (string) $request->getBaseCurrencyCode(),
                    ],
                ]],
            ];
        }

        if ($request->getMasterTrackingId()) {
            $requestedShipment['masterTrackingId'] = ['trackingNumber' => (string) $request->getMasterTrackingId()];
        }

        return [
            'mergeLabelDocOption' => 'LABELS_AND_DOCS',
            'requestedShipment' => $requestedShipment,
            'labelResponseOptions' => 'LABEL',
            'accountNumber' => ['value' => $accountNumber],
        ];
    }

    public function buildCancelShipmentPayload(string $accountNumber, string $trackingNumber): array
    {
        return [
            'accountNumber' => ['value' => $accountNumber],
            'trackingNumber' => $trackingNumber,
            'deletionControl' => 'DELETE_ONE_PACKAGE',
        ];
    }

    private function defaultReferenceData(Varien_Object $request): string
    {
        $shipment = $request->getOrderShipment();
        $incrementId = ($shipment && $shipment->getOrder())
            ? (string) $shipment->getOrder()->getIncrementId()
            : '';

        return 'Order #' . $incrementId . ' P' . (string) $request->getPackageId();
    }

    private function mapDropoffType(string $dropoff): string
    {
        return match ($dropoff) {
            'REQUEST_COURIER' => 'CONTACT_FEDEX_TO_SCHEDULE',
            'DROP_BOX' => 'DROP_BOX',
            'BUSINESS_SERVICE_CENTER' => 'BUSINESS_SERVICE_CENTER',
            'STATION' => 'STATION',
            default => 'USE_SCHEDULED_PICKUP',
        };
    }
}
