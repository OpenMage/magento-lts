<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Shipping_Block_Tracking_Popup
 *
 * @method string getProtectCode()
 */
class Mage_Shipping_Block_Tracking_Popup extends Mage_Core_Block_Template
{
    /**
     * @deprecated after 1.3.2.3
     */
    protected $_track_id;
    /**
     * @deprecated after 1.3.2.3
     */
    protected $_order_id;
    /**
     * @deprecated after 1.3.2.3
     */
    protected $_ship_id;

    /**
     * @param int $oid
     * @return Mage_Shipping_Block_Tracking_Popup
     * @deprecated after 1.3.2.3
     */
    public function setOrderId($oid)
    {
        return $this->setData('order_id', $oid);
    }

    /**
     * @deprecated after 1.3.2.3
     */
    public function getOrderId()
    {
        return $this->_getData('order_id');
    }

    /**
     * @param int $oid
     * @return Mage_Shipping_Block_Tracking_Popup
     * @deprecated after 1.3.2.3
     */
    public function setShipId($oid)
    {
        return $this->setData('ship_id', $oid);
    }

    /**
     * @deprecated after 1.3.2.3
     */
    public function getShipId()
    {
        return $this->_getData('ship_id');
    }

    /**
     * @param string $tid
     * @return Mage_Shipping_Block_Tracking_Popup
     * @deprecated after 1.3.2.3
     */
    public function setTrackId($tid = '')
    {
        return $this->setData('track_id', $tid);
    }

    /**
     * @deprecated after 1.3.2.3
     */
    public function getTrackId()
    {
        return $this->_getData('track_id');
    }

     /**
      * @deprecated after 1.4.0.0-alpha3
      * Initialize order model instance
      *
      * @return Mage_Sales_Model_Order|false
      */
    protected function _initOrder()
    {
        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        if (!$order->getId() || $this->getProtectCode() != $order->getProtectCode()) {
            return false;
        }

        return $order;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Initialize ship model instance
     *
     * @return Mage_Sales_Model_Order_Shipment|false
     */
    protected function _initShipment()
    {
        $ship = Mage::getModel('sales/order_shipment')->load($this->getShipId());

        if (!$ship->getEntityId() || $this->getProtectCode() != $ship->getProtectCode()) {
            return false;
        }

        return $ship;
    }


    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var Mage_Shipping_Model_Info $info */
        $info = Mage::registry('current_shipping_info');

        return $info->getTrackingInfo();
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve all tracking by orders id
     *
     * @return array
     */
    public function getTrackingInfoByOrder()
    {
        $shipTrack = array();
        if ($order = $this->_initOrder()) {
            $shipments = $order->getShipmentsCollection();
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            foreach ($shipments as $shipment) {
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                foreach ($tracks as $track) {
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }
        return $shipTrack;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve all tracking by ship id
     *
     * @return array
     */
    public function getTrackingInfoByShip()
    {
        $shipTrack = array();
        if ($shipment = $this->_initShipment()) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos=array();
            foreach ($tracks as $track) {
                $trackingInfos[] = $track->getNumberDetail();
            }
            $shipTrack[$increment_id] = $trackingInfos;
        }
        return $shipTrack;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking by tracking entity id
     *
     * @return array
     */
    public function getTrackingInfoByTrackId()
    {
        $track = Mage::getModel('sales/order_shipment_track')->load($this->getTrackId());
        if ($this->getProtectCode() == $track->getProtectCode()) {
            return array(array($track->getNumberDetail()));
        }
        return array(array());
    }

    /**
     * Format given date and time in current locale without changing timezone
     *
     * @param string $date
     * @param string $time
     * @return string
     */
    public function formatDeliveryDateTime($date, $time)
    {
        return $this->formatDeliveryDate($date) . ' ' . $this->formatDeliveryTime($time);
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param string $date
     * @return string
     */
    public function formatDeliveryDate($date)
    {
        /* @var Mage_Core_Model_Locale $locale */
        $locale = Mage::app()->getLocale();
        $format = $locale->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        return $locale->date(strtotime($date), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param string $time
     * @param string $date
     * @return string
     */
    public function formatDeliveryTime($time, $date = null)
    {
        if (!empty($date)) {
            $time = $date . ' ' . $time;
        }
        
        /* @var Mage_Core_Model_Locale $locale */
        $locale = Mage::app()->getLocale();
        
        $format = $locale->getTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        return $locale->date(strtotime($time), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Is 'contact us' option enabled?
     *
     * @return boolean
     */
    public function getContactUsEnabled()
    {
        return (bool) Mage::getStoreConfig('contacts/contacts/enabled');
    }

    /**
     * @return string
     */
    public function getStoreSupportEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_support/email');
    }

    /**
     * @return string
     */
    public function getContactUs()
    {
        return $this->getUrl('contacts');
    }
}
