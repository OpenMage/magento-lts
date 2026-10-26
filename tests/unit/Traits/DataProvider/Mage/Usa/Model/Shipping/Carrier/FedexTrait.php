<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier;

use Varien_Object;

trait FedexTrait
{
    private function populateShipmentRequest(Varien_Object $request): void
    {
        $request->setShipperContactPersonName('Shipper');
        $request->setShipperContactCompanyName('Ship Co');
        $request->setShipperContactPhoneNumber('800-555-1212');
        $request->setShipperAddressStreet1('123 Ship St');
        $request->setShipperAddressCity('Memphis');
        $request->setShipperAddressStateOrProvinceCode('TN');
        $request->setShipperAddressPostalCode('38116');
        $request->setShipperAddressCountryCode('US');
        $request->setRecipientContactPersonName('Recipient');
        $request->setRecipientContactCompanyName('R Co');
        $request->setRecipientContactPhoneNumber('212-555-1212');
        $request->setRecipientAddressStreet1('1 Test Ave');
        $request->setRecipientAddressCity('Beverly Hills');
        $request->setRecipientAddressStateOrProvinceCode('CA');
        $request->setRecipientAddressPostalCode('90210');
        $request->setRecipientAddressCountryCode('US');
        $request->setShippingMethod('FEDEX_GROUND');
        $request->setStoreId(0);
    }
}
