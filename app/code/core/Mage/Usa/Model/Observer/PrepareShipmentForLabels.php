<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * Prepare shipment for USPS label generation
 *
 * Marks shipments using USPS carriers as eligible for label creation.
 *
 * Event: sales_order_shipment_save_before
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Observer_PrepareShipmentForLabels implements Mage_Core_Observer_Interface
{
    public function execute(Varien_Event_Observer $observer): void
    {
        try {
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            $shipment = $observer->getEvent()->getShipment();
            if (!$shipment) {
                return;
            }

            $order = $shipment->getOrder();
            if (!$order) {
                return;
            }

            // Check if USPS is the carrier for this shipment
            $shippingMethod = $order->getShippingMethod();
            if (!str_starts_with($shippingMethod, 'usps_')) {
                return;
            }

            // Mark shipment as eligible for USPS label
            $shipment->setData('usps_label_eligible', true);

        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }
}
