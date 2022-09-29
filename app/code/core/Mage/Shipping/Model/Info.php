<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Shipping_Model_Info
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method int getOrderId()
 * @method string getProtectCode()
 * @method $this setProtectCode(string $value)
 * @method int getShipId()
 * @method int getTrackId()
 */
class Mage_Shipping_Model_Info extends Varien_Object
{
    /**
     * Tracking info
     *
     * @var array
     */
    protected $_trackingInfo = [];

    /**
     * Generating tracking info
     *
     * @param array $hash
     * @return $this
     */
    public function loadByHash($hash)
    {
        /** @var Mage_Shipping_Helper_Data $helper */
        $helper = Mage::helper('shipping');
        $data = $helper->decodeTrackingHash($hash);
        if (!empty($data)) {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);

            if ($this->getOrderId() > 0) {
                $this->getTrackingInfoByOrder();
            } elseif ($this->getShipId() > 0) {
                $this->getTrackingInfoByShip();
            } else {
                $this->getTrackingInfoByTrackId();
            }
        }
        return $this;
    }

    /**
     * Retrieve tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        return $this->_trackingInfo;
    }

    /**
     * Instantiate order model
     *
     * @return Mage_Sales_Model_Order|bool
     */
    protected function _initOrder()
    {
        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        if (!$order->getId() || $this->getProtectCode() !== $order->getProtectCode()) {
            return false;
        }

        return $order;
    }

    /**
     * Instantiate ship model
     *
     * @return Mage_Sales_Model_Order_Shipment|bool
     */
    protected function _initShipment()
    {
        /** @var Mage_Sales_Model_Order_Shipment $model */
        $model = Mage::getModel('sales/order_shipment');
        $ship = $model->load($this->getShipId());
        if (!$ship->getEntityId() || $this->getProtectCode() !== $ship->getProtectCode()) {
            return false;
        }

        return $ship;
    }

    /**
     * Retrieve all tracking by order id
     *
     * @return array
     */
    public function getTrackingInfoByOrder()
    {
        $shipTrack = [];
        $order = $this->_initOrder();
        if ($order) {
            $shipments = $order->getShipmentsCollection();
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            foreach ($shipments as $shipment) {
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos= [];
                foreach ($tracks as $track) {
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }

    /**
     * Retrieve all tracking by ship id
     *
     * @return array
     */
    public function getTrackingInfoByShip()
    {
        $shipTrack = [];
        $shipment = $this->_initShipment();
        if ($shipment) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos= [];
            foreach ($tracks as $track) {
                $trackingInfos[] = $track->getNumberDetail();
            }
            $shipTrack[$increment_id] = $trackingInfos;
        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }

    /**
     * Retrieve tracking by tracking entity id
     *
     * @return array
     */
    public function getTrackingInfoByTrackId()
    {
        $track = Mage::getModel('sales/order_shipment_track')->load($this->getTrackId());
        if ($track->getId() && $this->getProtectCode() === $track->getProtectCode()) {
            $this->_trackingInfo = [[$track->getNumberDetail()]];
        }
        return $this->_trackingInfo;
    }
}
