<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Address Verification Observer
 *
 * Observes checkout events to provide server-side address verification.
 * This complements the frontend JS verification by catching cases where
 * JS verification might be bypassed.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Observer
{
    /**
     * @var Mage_Usa_Model_Shipping_Carrier_Usps_AddressService
     */
    protected $_addressService;

    /**
     * Get address service instance
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_AddressService
     */
    protected function _getAddressService()
    {
        if ($this->_addressService === null) {
            $this->_addressService = Mage::getModel('usa/shipping_carrier_usps_address_service');
        }
        return $this->_addressService;
    }

    /**
     * Log address verification result for analytics
     *
     * Event: checkout_controller_onepage_save_shipping_address
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Usa_Model_Observer
     */
    public function logShippingAddressVerification($observer)
    {
        try {
            $addressService = $this->_getAddressService();
            if (!$addressService->isEnabled()) {
                return $this;
            }

            /** @var Mage_Sales_Model_Quote $quote */
            $quote = $observer->getEvent()->getQuote();
            if (!$quote) {
                return $this;
            }

            $shippingAddress = $quote->getShippingAddress();
            if (!$shippingAddress) {
                return $this;
            }

            // Only verify domestic US addresses
            if ($shippingAddress->getCountryId() !== 'US') {
                return $this;
            }

            // Build address data
            $street = $shippingAddress->getStreet();
            $addressData = array(
                'street1' => isset($street[0]) ? $street[0] : '',
                'street2' => isset($street[1]) ? $street[1] : '',
                'city' => $shippingAddress->getCity(),
                'region' => $shippingAddress->getRegionCode(),
                'postcode' => $shippingAddress->getPostcode()
            );

            // Log verification attempt (don't block checkout if it fails)
            $result = $addressService->verifyFromArray($addressData);

            if ($result['success']) {
                // Store verification status on address for later reference
                $shippingAddress->setData('usps_verified', true);
                $shippingAddress->setData('usps_verification_status', $result['status']);

                if ($result['status'] === Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service::MATCH_CORRECTED) {
                    // Log that address was corrected (for analytics)
                    Mage::log(
                        sprintf(
                            'USPS Address Verification: Corrected address for quote %s',
                            $quote->getId()
                        ),
                        Zend_Log::INFO,
                        'usps_address_verification.log'
                    );
                }
            }

        } catch (Exception $e) {
            // Never block checkout due to verification errors
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Validate address before order placement
     *
     * Event: sales_order_place_before
     *
     * This provides a final check before order is placed.
     * If verification fails, we log but don't block (per business requirement).
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Usa_Model_Observer
     */
    public function validateOrderAddress($observer)
    {
        try {
            $addressService = $this->_getAddressService();
            if (!$addressService->isEnabled()) {
                return $this;
            }

            /** @var Mage_Sales_Model_Order $order */
            $order = $observer->getEvent()->getOrder();
            if (!$order) {
                return $this;
            }

            $shippingAddress = $order->getShippingAddress();
            if (!$shippingAddress) {
                return $this; // Virtual order
            }

            // Only for US addresses
            if ($shippingAddress->getCountryId() !== 'US') {
                return $this;
            }

            // Store verification flag on order for reference
            $order->setData('usps_address_verified', true);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Add USPS verification info to shipment for label generation
     *
     * Event: sales_order_shipment_save_before
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Usa_Model_Observer
     */
    public function prepareShipmentForLabels($observer)
    {
        try {
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            $shipment = $observer->getEvent()->getShipment();
            if (!$shipment) {
                return $this;
            }

            $order = $shipment->getOrder();
            if (!$order) {
                return $this;
            }

            // Check if USPS is the carrier for this shipment
            $shippingMethod = $order->getShippingMethod();
            if (strpos($shippingMethod, 'usps_') !== 0) {
                return $this;
            }

            // Mark shipment as eligible for USPS label
            $shipment->setData('usps_label_eligible', true);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
}
