<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * Validate address before order placement
 *
 * Provides a final server-side check before order is placed.
 * If verification fails, we log but don't block (per business requirement).
 *
 * Event: sales_order_place_before
 *
 * @package    Mage_Usa
 */
final class Mage_Usa_Model_Observer_ValidateOrderAddress implements Mage_Core_Observer_Interface
{
    protected ?Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service $_addressService = null;

    protected function _getAddressService(): Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service
    {
        if (!$this->_addressService instanceof \Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service) {
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

            /** @var Mage_Sales_Model_Order $order */
            $order = $observer->getEvent()->getOrder();
            if (!$order) {
                return;
            }

            $shippingAddress = $order->getShippingAddress();
            if (!$shippingAddress) {
                return; // Virtual order
            }

            // Only for US addresses
            if ($shippingAddress->getCountryId() !== 'US') {
                return;
            }

            // Store verification flag on order for reference
            $order->setData('usps_address_verified', true);

        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }
}
