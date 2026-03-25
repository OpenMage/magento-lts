<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Grid of packaging shipment
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging_Grid extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/shipment/packaging/grid.phtml');
    }

    /**
     * Return collection of shipment items
     *
     * @return array
     */
    public function getCollection()
    {
        if ($this->getShipment()->getId()) {
            $collection = Mage::getModel('sales/order_shipment_item')->getCollection()
                    ->setShipmentFilter($this->getShipment()->getId());
        } else {
            $collection = $this->getShipment()->getAllItems();
        }

        return $collection;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->getShipment()->getStoreId();
        $order = $this->getShipment()->getOrder();
        $address = $order->getShippingAddress();
        $shipperAddressCountryCode = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $storeId,
        );
        $recipientAddressCountryCode = $address->getCountryId();
        if ($shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }

        return false;
    }

    /**
     * Format price
     *
     * @param  float  $value
     * @return string
     */
    public function formatPrice($value)
    {
        return sprintf('%.2F', $value);
    }
}
