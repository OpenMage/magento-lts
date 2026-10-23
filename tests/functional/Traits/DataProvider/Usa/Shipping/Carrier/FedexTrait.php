<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Functional\Traits\DataProvider\Usa\Shipping\Carrier;

use Mage;
use Mage_Core_Helper_Measure_Length;
use Mage_Core_Helper_Measure_Weight;
use Mage_Shipping_Model_Rate_Request;
use Mage_Shipping_Model_Shipment_Request;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Container as Container;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Unitofmeasure as Uom;
use Varien_Object;

/**
 * @phpstan-type Address array{
 *     person_name: string,
 *     company_name: string,
 *     phone: string,
 *     street: string,
 *     city: string,
 *     state: string,
 *     postcode: string,
 *     country: string,
 *     residential: bool,
 * }
 * @phpstan-type CommodityItem array{name: string, qty: int, price: float}
 */
trait FedexTrait
{
    protected string $allowedMethods = 'FEDEX_GROUND,FEDEX_2_DAY,PRIORITY_OVERNIGHT,STANDARD_OVERNIGHT,'
        . 'INTERNATIONAL_PRIORITY,INTERNATIONAL_ECONOMY,INTERNATIONAL_FIRST,'
        . 'FEDEX_INTERNATIONAL_PRIORITY,FEDEX_INTERNATIONAL_PRIORITY_EXPRESS';

    protected string $originPostcode = '38116';

    protected string $originCountry = 'US';

    protected string $destPostcode = '90210';

    protected string $destCountry = 'US';

    protected float $defaultPackageWeight = 10.0;

    protected float $defaultPackageValue = 100.0;

    protected float $smartpostPackageWeight = 3.0;

    protected string $intlDestPostcode = 'E14 5AB';

    protected string $intlDestCountry = 'GB';

    protected string $intlShipMethod = 'FEDEX_INTERNATIONAL_PRIORITY';

    protected float $intlCustomsValue = 100.0;

    protected string $domesticShipMethod = 'FEDEX_GROUND';

    protected float $shipmentPackageWeight = 5.0;

    protected int $packageLength = 12;

    protected int $packageWidth = 9;

    protected int $packageHeight = 3;

    protected int $multipiecePackageCount = 2;

    protected string $trackingNumber = '794644746111';

    protected string $trackingNumberAlt = '122816215025810';

    /**
     * @return Address
     */
    protected function domesticShipper(): array
    {
        return [
            'person_name' => 'Sandbox Shipper',
            'company_name' => 'A Co LLC',
            'phone' => '9015551212',
            'street' => '3610 Hacks Cross Rd',
            'city' => 'Memphis',
            'state' => 'TN',
            'postcode' => '38125',
            'country' => 'US',
            'residential' => false,
        ];
    }

    /**
     * @return Address
     */
    protected function domesticRecipient(): array
    {
        return [
            'person_name' => 'Sandbox Recipient',
            'company_name' => 'B Co LLC',
            'phone' => '3105551212',
            'street' => '455 N Rexford Dr',
            'city' => 'Beverly Hills',
            'state' => 'CA',
            'postcode' => '90210',
            'country' => 'US',
            'residential' => false,
        ];
    }

    /**
     * @return Address
     */
    protected function ukRecipient(): array
    {
        return [
            'person_name' => 'Sandbox UK Recipient',
            'company_name' => 'C Co Ltd',
            'phone' => '442071234567',
            'street' => '1 Canada Square',
            'city' => 'London',
            'state' => '',
            'postcode' => 'E14 5AB',
            'country' => 'GB',
            'residential' => false,
        ];
    }

    /**
     * @return CommodityItem
     */
    protected function intlCommodityItem(): array
    {
        return [
            'name' => 'Cotton T-Shirt',
            'qty' => 1,
            'price' => 100.0,
        ];
    }

    protected function buildRateRequest(
        ?string $destPostcode = null,
        ?string $destCountry = null,
        ?float $weight = null,
    ): Mage_Shipping_Model_Rate_Request {
        $weight ??= $this->defaultPackageWeight;

        /** @var Mage_Shipping_Model_Rate_Request $request */
        $request = Mage::getModel('shipping/rate_request');

        return $request
            ->setOrigPostcode($this->originPostcode)
            ->setOrigCountryId($this->originCountry)
            ->setDestPostcode($destPostcode ?? $this->destPostcode)
            ->setDestCountryId($destCountry ?? $this->destCountry)
            ->setPackageWeight($weight)
            ->setPackagePhysicalValue($this->defaultPackageValue)
            ->setFreeMethodWeight($weight);
    }

    /**
     * @param Address $shipper
     * @param Address $recipient
     */
    protected function shipmentRequest(
        array $shipper,
        array $recipient
    ): Mage_Shipping_Model_Shipment_Request {
        return Mage::getModel('shipping/shipment_request')
            ->setShipperContactPersonName($shipper['person_name'])
            ->setShipperContactCompanyName($shipper['company_name'])
            ->setShipperContactPhoneNumber($shipper['phone'])
            ->setShipperAddressStreet1($shipper['street'])
            ->setShipperAddressCity($shipper['city'])
            ->setShipperAddressStateOrProvinceCode($shipper['state'])
            ->setShipperAddressPostalCode($shipper['postcode'])
            ->setShipperAddressCountryCode($shipper['country'])
            ->setRecipientContactPersonName($recipient['person_name'])
            ->setRecipientContactCompanyName($recipient['company_name'])
            ->setRecipientContactPhoneNumber($recipient['phone'])
            ->setRecipientAddressStreet1($recipient['street'])
            ->setRecipientAddressCity($recipient['city'])
            ->setRecipientAddressStateOrProvinceCode($recipient['state'])
            ->setRecipientAddressPostalCode($recipient['postcode'])
            ->setRecipientAddressCountryCode($recipient['country'])
            ->setRecipientAddressResidential($recipient['residential']);
    }

    /**
     * @param Address             $recipient
     * @param list<CommodityItem> $items
     */
    protected function buildShipmentRequest(
        array $recipient,
        string $shippingMethod,
        float $customsValue,
        array $items,
    ): Mage_Shipping_Model_Shipment_Request {
        $weight = $this->shipmentPackageWeight;

        $packageParams = [
            'container' => 'YOUR_PACKAGING',
            'weight' => $weight,
            'weight_units' => Mage_Core_Helper_Measure_Weight::POUND,
            'dimension_units' => Mage_Core_Helper_Measure_Length::INCH,
            'length' => $this->packageLength,
            'width' => $this->packageWidth,
            'height' => $this->packageHeight,
            'customs_value' => $customsValue,
            'content_type' => '',
            'content_type_other' => '',
        ];

        return $this->shipmentRequest($this->domesticShipper(), $recipient)
            ->setShippingMethod($shippingMethod)
            ->setPackageWeight($weight)
            ->setBaseCurrencyCode('USD')
            ->setPackages([
                1 => [
                    'params' => $packageParams,
                    'items' => $items,
                ],
            ]);
    }

    /**
     * @param Address                                                                      $recipient
     * @param list<array{weight: float, items: list<CommodityItem>, customs_value: float}> $packageSpecs
     */
    protected function buildMultiPieceShipmentRequest(
        array $recipient,
        string $shippingMethod,
        array $packageSpecs,
    ): Mage_Shipping_Model_Shipment_Request {
        $packages = [];
        foreach ($packageSpecs as $i => $spec) {
            $packages[$i + 1] = [
                'params' => [
                    'container' => 'YOUR_PACKAGING',
                    'weight' => $spec['weight'],
                    'weight_units' => Mage_Core_Helper_Measure_Weight::POUND,
                    'dimension_units' => Mage_Core_Helper_Measure_Length::INCH,
                    'length' => $this->packageLength,
                    'width' => $this->packageWidth,
                    'height' => $this->packageHeight,
                    'customs_value' => $spec['customs_value'],
                    'content_type' => '',
                    'content_type_other' => '',
                ],
                'items' => $spec['items'],
            ];
        }

        return $this->shipmentRequest($this->domesticShipper(), $recipient)
            ->setShippingMethod($shippingMethod)
            ->setPackageWeight($packageSpecs[0]['weight'])
            ->setBaseCurrencyCode('USD')
            ->setPackages($packages);
    }

    protected function buildRawRateRequest(
        string $account,
        ?string $destPostcode = null,
        ?string $destCountry = null,
        ?float $value = null,
    ): Varien_Object {
        $raw = new Varien_Object();
        $raw->setAccount($account);
        $raw->setOrigPostal($this->originPostcode);
        $raw->setOrigCountry($this->originCountry);
        $raw->setDestPostal($destPostcode ?? $this->destPostcode);
        $raw->setDestCountry($destCountry ?? $this->destCountry);
        $raw->setValue($value ?? $this->defaultPackageValue);
        $raw->setUnitOfMeasure(Uom::WEIGHT_POUND);
        $raw->setResidenceDelivery(false);
        $raw->setPackaging('YOUR_PACKAGING');
        $raw->setDropoffType('REGULAR_PICKUP');

        return $raw;
    }

    /**
     * @return list<Container>
     */
    protected function buildRateContainers(?int $count = null, ?float $weight = null): array
    {
        $count ??= $this->multipiecePackageCount;
        $weight ??= $this->shipmentPackageWeight;

        $containers = [];
        for ($i = 0; $i < $count; $i++) {
            $containers[] = (new Container())
                ->setTotalWeight($weight)
                ->setLength($this->packageLength)
                ->setWidth($this->packageWidth)
                ->setHeight($this->packageHeight);
        }

        return $containers;
    }
}
