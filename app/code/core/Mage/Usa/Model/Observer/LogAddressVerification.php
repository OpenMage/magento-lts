<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

use Monolog\Level;

/**
 * USPS Address Verification Logger
 *
 * Observes checkout shipping address saves to log server-side
 * address verification results for analytics.
 *
 * Event: checkout_controller_onepage_save_shipping_address
 *
 * @package    Mage_Usa
 */
final class Mage_Usa_Model_Observer_LogAddressVerification implements Mage_Core_Observer_Interface
{
    private ?Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service $_addressService = null;

    private function _getAddressService(): Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service
    {
        if (!$this->_addressService instanceof Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service) {
            $this->_addressService = Mage::getModel('usa/shipping_carrier_usps_address_service');
        }

        return $this->_addressService;
    }

    public function execute(Varien_Event_Observer $observer): void
    {
        try {
            $addressService = $this->_getAddressService();
            if (!$addressService->isEnabled()) {
                return;
            }

            /** @var Mage_Sales_Model_Quote $quote */
            $quote = $observer->getEvent()->getQuote();
            if (!$quote) {
                return;
            }

            $shippingAddress = $quote->getShippingAddress();
            if (!$shippingAddress) {
                return;
            }

            // Only verify domestic US addresses
            if ($shippingAddress->getCountryId() !== 'US') {
                return;
            }

            // Build address data
            $street = $shippingAddress->getStreet();
            if (!is_array($street)) {
                $street = [(string) $street];
            }

            $addressData = [
                'street1' => $street[0] ?? '',
                'street2' => $street[1] ?? '',
                'city' => $shippingAddress->getCity(),
                'region' => $shippingAddress->getRegionCode(),
                'postcode' => $shippingAddress->getPostcode(),
            ];

            // Log verification attempt (don't block checkout if it fails)
            $result = $addressService->verifyFromArray($addressData);

            if ($result['success']) {
                // Store verification status on address for later reference
                $shippingAddress->setData('usps_verified', true);
                $shippingAddress->setData('usps_verification_status', $result['status']);

                if ($result['status'] === Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service::MATCH_CORRECTED) {
                    Mage::log(
                        sprintf(
                            'USPS Address Verification: Corrected address for quote %s',
                            $quote->getId(),
                        ),
                        Level::Info,
                        'usps_address_verification.log',
                    );
                }
            }

        } catch (Exception $exception) {
            // Never block checkout due to verification errors
            Mage::logException($exception);
        }
    }
}
